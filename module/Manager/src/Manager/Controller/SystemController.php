<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Manager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Manager\Controller;

use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

use Manager\Robot\RobotService;
use Manager\Robot\RobotConstants;

use Start\Controller\CommonController;

class SystemController extends CommonController
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

	/**
	 * Action affichant l'ensemble des paramètres relatifs au système
	 * 
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction()
	{
		$oSystem = $this->getSystemTable()->getSystem();

		return new ViewModel(array(
			'locale'		=> $oSystem->language,
			'unit'			=> ($oSystem->unit == 'mbq') ? 'MBq' : 'mCi',
			'maxactivity'	=> $oSystem->maxactivity,
		));
	}

	/* AJAX CALLS BEYOND THIS LINE */

	/**
	 * Retrive datas about robot throught http request
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function	agetsystemAction()
	{
		//TODO Appels multiples: vérifier leur utilité et la possibilité d'en faire plusieurs
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		
		$aParams = array();
		$actions = array(
				"serialnumber"			=> RobotConstants::MAINLOGIC_PAR_SERIALNUMBERSAMI, 
				"activimeterversion"	=> RobotConstants::MAINLOGIC_PAR_SERIALNUMBERACTIVI, 
				"softwareversion"		=> RobotConstants::MAINLOGIC_PAR_SOFTWAREVERSION, 
				"systemversion"			=> RobotConstants::MAINLOGIC_PAR_SYSTEMVERSION,
		);
		
		foreach ($actions as $k => $v)
		{
			$aParams[$k] = $robotService->receive($v);
		}
		
		$result = new JsonModel($aParams);
		return $result;
	}

	/**
	 * Action Ajax de changement de la langue du système
	 * 
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
	 * Action Ajax de réglage de l'activité maximum
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function	asetmaxactivityAction()
	{
		$oSystem = $this->getSystemTable()->getSystem();
		$oSystem->maxactivity = $this->getRequest()->getPost("maxactivity");
		$this->getSystemTable()->saveSystem($oSystem);
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_PAR_MAXACTIVITY => $oSystem->maxactivity));
		
		return new JsonModel(array('success'=>1));
	}
}
