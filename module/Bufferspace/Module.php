<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Bufferspace for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Bufferspace;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Bufferspace\Model\Injection;
use Bufferspace\Model\InjectionTable;
use Bufferspace\Model\Patient;
use Bufferspace\Model\PatientTable;
use Bufferspace\View\Export;
use Bufferspace\View\ExportTable;
use Bufferspace\View\Injected;
use Bufferspace\View\InjectedTable;
use Bufferspace\View\ToInjectTable;
use Bufferspace\View\ToInject;
use Bufferspace\View\Bufferspace\View;

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
				'Bufferspace\Model\PatientTable' =>  function($sm) {
					$tableGateway = $sm->get('PatientTableGateway');
					$table = new PatientTable($tableGateway);
					return $table;
				},
				'PatientTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Patient());
					return new TableGateway('tmp_patient', $dbAdapter, null, $resultSetPrototype);
				},
				'Bufferspace\Model\InjectionTable' =>  function($sm) {
					$tableGateway = $sm->get('InjectionTableGateway');
					$table = new InjectionTable($tableGateway);
					return $table;
				},
				'InjectionTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Injection());
					return new TableGateway('tmp_injection', $dbAdapter, null, $resultSetPrototype);
				},
				'Bufferspace\View\ExportTable' =>  function($sm) {
					$tableGateway = $sm->get('ExportTableGateway');
					$table = new ExportTable($tableGateway);
					return $table;
				},
				'ExportTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Export());
					return new TableGateway('view_export', $dbAdapter, null, $resultSetPrototype);
				},
				'Bufferspace\View\InjectedTable' =>  function($sm) {
					$tableGateway = $sm->get('InjectedTableGateway');
					$table = new InjectedTable($tableGateway);
					return $table;
				},
				'InjectedTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Injected());
					return new TableGateway('view_injected', $dbAdapter, null, $resultSetPrototype);
				},
				'Bufferspace\View\ToInjectTable' =>  function($sm) {
					$tableGateway = $sm->get('ToInjectTableGateway');
					$table = new ToInjectTable($tableGateway);
					return $table;
				},
				'ToInjectTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new ToInject());
					return new TableGateway('view_toinject', $dbAdapter, null, $resultSetPrototype);
				},
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
	}
}
