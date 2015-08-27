<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Manager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Manager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ConsoleModel;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

use Manager\Robot\RobotService;

class SystemController extends AbstractActionController
{
	protected $systemTable;

	/**
	 * 
	 * @return \Manager\Model\SystemTable
	 */
	public function getSystemTable()
	{
		if(!$this->systemTable)
		{
			$sm = $this->getServiceLocator();
			$this->systemTable = $sm->get('Manager\Model\SystemTable');
		}
		return $this->systemTable;
	}

	public function indexAction()
	{
		$oSystem = $this->getSystemTable()->getSystem();

		return new ViewModel(array(
			'locale'		=> $oSystem->language,
			'unit'			=> $oSystem->unit,
			'genuinekit'	=> $oSystem->genuinekit,
			'maxactivity'	=> $oSystem->maxactivity,
		));
	}

	/* AJAX CALLS BEYOND THIS LINE */

	/**
	 * Retrive datas about robot throught http request
	 * @return \Zend\View\Model\JsonModel
	 */
	public function	agetsystemAction()
	{
		//TODO Appels multiples: vérifier leur utilité et la possibilité d'en faire plusieurs
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		
		$aParams = array();
		$actions = array(
				"serialnumber"			=> "G_MainLogic.par.Serial_Number_SAMI", 
				"activimeterversion"	=> "G_MainLogic.par.Serial_Number_Activi", 
				"softwareversion"		=> "G_MainLogic.par.Software_Version", 
				"systemversion"			=> "G_MainLogic.par.System_Version",
		);
		
		foreach ($actions as $k => $v)
		{
			$aParams[$k] = $robotService->receive($v);
		}
		
		$result = new JsonModel($aParams);
		return $result;
	}

	/**
	 * @todo insert http call to robot (set locale?)
	 * @return \Zend\View\Model\JsonModel
	 */
	public function	asetlocalsystemAction()
	{
		$oSystem = $this->getSystemTable()->getSystem();
		$oSystem->language = $this->getRequest()->getPost("language");
		$this->getSystemTable()->saveSystem($oSystem);
		return new JsonModel(array('success'=>1));
	}


	/**
	 * @todo insert http call to robot (set mode)
	 * @return \Zend\View\Model\JsonModel
	 */
	public function	asetgenuinekitAction()
	{
		$oSystem = $this->getSystemTable()->getSystem();
		$oSystem->genuinekit = $this->getRequest()->getPost("genuinekit");
		$this->getSystemTable()->saveSystem($oSystem);
		return new JsonModel(array('success'=>1));
	}
	
	/**
	 * @return \Zend\View\Model\JsonModel
	 */
	public function	asetmaxactivityAction()
	{
		$oSystem = $this->getSystemTable()->getSystem();
		$oSystem->maxactivity = $this->getRequest()->getPost("maxactivity");
		$this->getSystemTable()->saveSystem($oSystem);
		return new JsonModel(array('success'=>1));
	}
}
