<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module implements AutoloaderProviderInterface
{
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
			// if we're in a namespace deeper than one level we need to fix the \ in the path
					__NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
				),
			),
		);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function	getServiceConfig()
	{
		return array(
			'factories' => array(
				'Auth\Model\AuthStorage'	=> function($sm) {
					return new Model\AuthStorage();
				},
				'AuthService' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'user', 'login', 'password', 'SHA1(?)');
					$authService = new AuthenticationService();
					$authService->setAdapter($dbTableAuthAdapter);
					$authService->setStorage($sm->get('Auth\Model\AuthStorage'));
					return $authService;
				}
			),
		);
	}

	public function onBootstrap(MvcEvent $e)
	{
		// You may not need to do this if you're doing it elsewhere in your
		// application
		$eventManager		= $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
		$eventManager->attach('dispatch', array($this, 'checkLoginChangeLayout'));
	}

	public function	checkLoginChangeLayout(MvcEvent $e)
	{
		$sm = $e->getApplication()->getServiceManager();
		$controller = $e->getTarget();
		if ($controller->layout()->terminate() === false && $sm->get('AuthService')->hasIdentity()) {
			$identity = $sm->get('AuthService')->getIdentity();
			$oUser = $sm->get('Manager\Model\UserTable')->searchByLogin($identity);
			$controller->layout('layout/logged');
			$controller->layout()->login = $oUser->login;
			$controller->layout()->isAdmin = $oUser->admin;
		}
	}
}
