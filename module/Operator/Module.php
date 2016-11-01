<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Operator for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Operator;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Operator\Model\Patientkit;
use Operator\Model\PatientkitTable;
use Operator\Model\Sourcekit;
use Operator\Model\SourcekitTable;

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

	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'Operator\Model\PatientkitTable' =>  function($sm) {
					$tableGateway = $sm->get('PatientkitTableGateway');
					$table = new PatientkitTable($tableGateway);
					return $table;
				},
				'PatientkitTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Patientkit());
					return new TableGateway('patientkit', $dbAdapter, null, $resultSetPrototype);
				},
				'Operator\Model\SourcekitTable' =>  function($sm) {
					$tableGateway = $sm->get('SourcekitTableGateway');
					$table = new SourcekitTable($tableGateway);
					return $table;
				},
				'SourcekitTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Sourcekit());
					return new TableGateway('sourcekit', $dbAdapter, null, $resultSetPrototype);
				},
			),
		);
	}

	public function onBootstrap(MvcEvent $e)
	{
		$layoutViewModel = $e->getApplication()->getMvcEvent()->getViewModel();
		$eventManager = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
		
		
		$appendOperatorJsTimeout = false;
		$matchRoute = $e->getRouter()->match($e->getRequest());
		if (!is_null($matchRoute)) {
			$timeout = $matchRoute->getParam('timeout', null);
			$disableLinks = $matchRoute->getParam('disable_links', false);
			
			$serviceManager = $e->getApplication()->getServiceManager();
			$config = $serviceManager->get('config');
			$layoutViewModel->disableRightClick = $config['disableRightClick'];
			
			if ($timeout == 'operator') {
				$appendOperatorJsTimeout = true;
				$layoutViewModel->operatorTimeout = $config['timeout']['operator'];
			}
			
			$layoutViewModel->disableLinks = $disableLinks;
			$layoutViewModel->appendOperatorJsTimeout = $appendOperatorJsTimeout;
		}
	}
}
