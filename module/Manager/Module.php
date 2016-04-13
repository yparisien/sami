<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Manager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Manager;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Manager\Model\Drug;
use Manager\Model\DrugTable;
use Manager\Model\Examination;
use Manager\Model\ExaminationTable;
use Manager\Model\Radionuclide;
use Manager\Model\RadionuclideTable;
use Manager\Model\System;
use Manager\Model\SystemTable;
use Manager\Model\User;
use Manager\Model\UserTable;
use Manager\View\VDrug;
use Manager\View\VDrugTable;
use Manager\View\VExamination;
use Manager\View\VExaminationTable;
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
				'Manager\Model\DrugTable' =>  function($sm) {
					$tableGateway = $sm->get('DrugTableGateway');
					$table = new DrugTable($tableGateway);
					return $table;
				},
				'DrugTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Drug());
					return new TableGateway('drug', $dbAdapter, null, $resultSetPrototype);
				},
				'Manager\Model\ExaminationTable' =>  function($sm) {
					$tableGateway = $sm->get('ExaminationTableGateway');
					$table = new ExaminationTable($tableGateway);
					return $table;
				},
				'ExaminationTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Examination());
					return new TableGateway('examination', $dbAdapter, null, $resultSetPrototype);
				},
				'Manager\Model\RadionuclideTable' =>  function($sm) {
					$tableGateway = $sm->get('RadionuclideTableGateway');
					$table = new RadionuclideTable($tableGateway);
					return $table;
				},
				'RadionuclideTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Radionuclide());
					return new TableGateway('radionuclide', $dbAdapter, null, $resultSetPrototype);
				},
				'Manager\Model\SystemTable' =>  function($sm) {
					$tableGateway = $sm->get('SystemTableGateway');
					$table = new SystemTable($tableGateway);
					return $table;
				},
				'SystemTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new System());
					return new TableGateway('system', $dbAdapter, null, $resultSetPrototype);
				},
				'Manager\Model\UserTable' =>  function($sm) {
					$tableGateway = $sm->get('UserTableGateway');
					$table = new UserTable($tableGateway);
					return $table;
				},
				'UserTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new User());
					return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
				},
				'Manager\View\VDrugTable' =>  function($sm) {
					$tableGateway = $sm->get('VDrugTableGateway');
					$table = new VDrugTable($tableGateway);
					return $table;
				},
				'VDrugTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new VDrug());
					return new TableGateway('view_drug', $dbAdapter, null, $resultSetPrototype);
				},
				'Manager\View\VExaminationTable' =>  function($sm) {
					$tableGateway = $sm->get('VExaminationTableGateway');
					$table = new VExaminationTable($tableGateway);
					return $table;
				},
				'VExaminationTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new VExamination());
					return new TableGateway('view_examination', $dbAdapter, null, $resultSetPrototype);
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

		$appendSupervisorJsTimeout = false;
		$matchRoute = $e->getRouter()->match($e->getRequest());
		
		if (!is_null($matchRoute)) {
			$timeout = $matchRoute->getParam('timeout', null);
			$pagetype =  $matchRoute->getParam('pagetype', null);
			
			$serviceManager = $e->getApplication()->getServiceManager();
			$config = $serviceManager->get('config');
			
			if ($timeout == 'supervisor') {
				$appendSupervisorJsTimeout = true;
				$layoutViewModel->supervisorTimeout = $config['timeout']['supervisor'];
			}
			
			if ($pagetype == 'admin') {
				$layoutViewModel->isAdminPage = true;
			} else {
				$layoutViewModel->isAdminPage = false;
			}
			
			$layoutViewModel->appendSupervisorJsTimeout = $appendSupervisorJsTimeout;
		}
	}
}
