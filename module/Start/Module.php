<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Start;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		// initialize acl handlers
		$this->initAcl($e);
		$e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));
		$e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'blockAccess'), -999);

		// initialize locales string
		$eventManager	= $e->getApplication()->getEventManager();
		$oSystem		= $e->getApplication()->getServiceManager()->get('Manager\Model\SystemTable')->getSystem();
		$e->getApplication()->getServiceManager()->get('translator')->setLocale($oSystem->language);

		//get the layout
		$layoutViewModel = $e->getApplication()->getMvcEvent()->getViewModel();
		
		// initialize global configuration values
		$serviceManager = $e->getApplication()->getServiceManager();
		$config = $serviceManager->get('config');
		$layoutViewModel->virtualkeyboardEnable = $config['virtualkeyboard']['enable'];
		
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	public function initAcl(MvcEvent $e)
	{

		$acl = new \Zend\Permissions\Acl\Acl();
		$roles = include __DIR__ . '/config/module.acl.roles.php';
		$allResources = array();
		foreach ($roles as $role => $resources) {

			$role = new \Zend\Permissions\Acl\Role\GenericRole($role);
			$acl->addRole($role);

			$allResources = array_merge($resources, $allResources);

			//adding resources
			foreach ($resources as $resource) {
				// Edit 4
				if(!$acl->hasResource($resource))
					$acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
			}
			//adding restrictions
			foreach ($allResources as $resource) {
				$acl->allow($role, $resource);
			}
		}
		//testing
		//var_dump($acl->isAllowed('admin','home'));
		//true

		//setting to view
		$e->getViewModel()->acl = $acl;

	}

	public function checkAcl(MvcEvent $e)
	{
		$route = $e->getRouteMatch()->getMatchedRouteName();
		$sm = $e->getApplication()->getServiceManager();

		$userRole = 'guest'; // default auth level
		if ($sm->get('AuthService')->hasIdentity())
		{
			$identity = $sm->get('AuthService')->getIdentity();
			$oUser = $sm->get('Manager\Model\UserTable')->searchByLogin($identity);
			if($oUser->admin)
			{
				$userRole = 'manager';
			}
			else
			{
				$userRole = 'operator';
			}
		}
		else
		{
			$userRole = 'guest';
		}
		//if (!$e->getViewModel()->acl->isAllowed($userRole, $route)) {
		if ($e->getViewModel()->acl->hasResource($route) && !$e->getViewModel()->acl->isAllowed($userRole, $route))
		{
			$app = $e->getTarget();
			$route = $e->getRouteMatch();
			$e->setError('ACL_ACCESS_DENIED')
				->setParam('route', $route->getMatchedRouteName());
			$app->getEventManager()->trigger('dispatch.error', $e);
		}
	}

	public function	blockAccess(MvcEvent $e)
	{
		$error = $e->getError();

		if (empty($error) || $error != "ACL_ACCESS_DENIED") {
			return;
		}

		$result = $e->getResult();

		if ($result instanceof StdResponse) {
			return;
		}

		$baseModel = new ViewModel();
		$baseModel->setTemplate('layout/layout');

		$model = new ViewModel();
		$model->setTemplate('error/403');

		$baseModel->addChild($model);
		$baseModel->setTerminal(true);

		$e->setViewModel($baseModel);

		$response = $e->getResponse();
		$response->setStatusCode(403);

		$e->setResponse($response);
		$e->setResult($baseModel);

		return false;
	}
}
