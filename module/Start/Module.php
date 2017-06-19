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
use Zend\Session\Container;

/**
 * 
 * 
 * @author yohann.parisien
 *
 */
class Module
{
	/**
	 * Chargement des ACL et de variables globales à l'initialisation du module
	 * 
	 * @param MvcEvent $e
	 */
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
		
		$oContainer = new Container('robot_config');
		
		if (!isset($oContainer->simulation)) {
			$oContainer->simulation = $config['robot']['simulation'];
		}
		
		/*
		 * Récupération des variables de configuration pour les stocker dans la session
		 */
		$oContainer->vialvol	= $config['robot']['vialvol'];
		$oContainer->simulated	= $config['robot']['simulated'];
		$oContainer->webservice = $config['robot']['webservice'];
		
		/*
		 * Assigne les variables concernant le clavier virtuel dans le layout 
		 */
		$layoutViewModel->virtualkeyboardEnable = $config['virtualkeyboard']['enable'];
		$layoutViewModel->virtualkeyboardSize = $config['virtualkeyboard']['size'];
		
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

	/**
	 * Initialisation des rôles ACL définis dans le fichier de configuration des rôles
	 * 
	 * @param MvcEvent $e
	 */
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
				if(!$acl->hasResource($resource)) {
					$acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
				}
			}
			//adding restrictions
			foreach ($allResources as $resource) {
				$acl->allow($role, $resource);
			}
		}

		//setting to view
		$e->getViewModel()->acl = $acl;
	}

	/**
	 * Determination des rôles ACL en fonction du profil de l'utilisateur
	 * 
	 * @param MvcEvent $e
	 */
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
		if ($e->getViewModel()->acl->hasResource($route) && !$e->getViewModel()->acl->isAllowed($userRole, $route))
		{
			$app = $e->getTarget();
			$route = $e->getRouteMatch();
			$e->setError('ACL_ACCESS_DENIED')->setParam('route', $route->getMatchedRouteName());
			$app->getEventManager()->trigger('dispatch.error', $e);
		}
	}

	/**
	 * Fonction de blocage des accès ACL
	 * 
	 * @param MvcEvent $e
	 */
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
		
		//Redirection vers la route pour forcer la deconnexion de l'utilisateur
		$urlToLogin = $e->getRouter()->assemble(array('action' => 'logout'), array('name' => 'log'));
		
		$response = $e->getResponse();
		$response->getHeaders()->addHeaderLine('Location', $urlToLogin);
		$response->setStatusCode(302);
		$response->sendHeaders();
		exit();
	}
}
