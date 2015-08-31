<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Logger for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Logger;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Logger\Model\InputAction;
use Logger\Model\InputActionTable;
use Logger\Model\InputDrug;
use Logger\Model\InputDrugTable;


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

	public function onBootstrap(MvcEvent $e)
	{
		// You may not need to do this if you're doing it elsewhere in your
		// application
		$eventManager		= $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
	}

	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'Logger\Model\InputActionTable' =>  function($sm) {
					$tableGateway = $sm->get('LogInputActionTableGateway');
					$table = new InputActionTable($tableGateway);
					return $table;
				},
				'LogInputActionTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new InputAction());
					return new TableGateway('input_action', $dbAdapter, null, $resultSetPrototype);
				},
				'Logger\Model\InputDrugTable' =>  function($sm) {
					$tableGateway = $sm->get('LogInputDrugTableGateway');
					$table = new InputDrugTable($tableGateway);
					return $table;
				},
				'LogInputDrugTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new InputDrug());
					return new TableGateway('input_drug', $dbAdapter, null, $resultSetPrototype);
				},
			),
		);
	}
}
