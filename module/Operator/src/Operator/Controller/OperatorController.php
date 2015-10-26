<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Operator for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Operator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Manager\Robot\RobotService;
use Manager\Robot\RobotConstants;

class OperatorController extends AbstractActionController
{
	protected $systemTable;
	protected $drugTable;
	protected $examinationTable;
	protected $injectedTable;

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
	 * 
	 * @return \Manager\Model\ExaminationTable
	 */
	public function getExaminationTable()
	{
		if(!$this->examinationTable)
		{
			$sm = $this->getServiceLocator();
			$this->examinationTable = $sm->get('Manager\Model\ExaminationTable');
		}
		return $this->examinationTable;
	}	
	
	/**
	 *
	 * @return \Manager\Model\DrugTable
	 */
	public function getDrugTable()
	{
		if(!$this->drugTable)
		{
			$sm = $this->getServiceLocator();
			$this->drugTable = $sm->get('Manager\Model\DrugTable');
		}
		return $this->drugTable;
	}
	
	/**
	 *
	 * @return \Bufferspace\View\InjectedTable
	 */
	public function getInjectedTable()
	{
		if(!$this->injectedTable)
		{
			$sm = $this->getServiceLocator();
			$this->injectedTable = $sm->get('Bufferspace\View\InjectedTable');
		}
		return $this->injectedTable;
	}

	public function indexAction()
	{
		/* @var $robotService RobotService  */
		$oContainer = new Container('automate_setup');
		$sm = $this->getServiceLocator();
		$config = $sm->get('Config');
		$aParam = array();
		
		$nbExams = $this->getExaminationTable()->count();
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotCanInject = (bool) $robotService->receive(RobotConstants::MAINLOGIC_STATUS_HASKITSOURCELOADED);
		if ($robotCanInject === true) {
			$oContainer->sourcekitloaded = true;
		}
		
		//TODO Continuer à doubler la vérif automate
		
		// for the moment, retrieve steps status but use a clever way for final version
		$aParam['step'] = $oContainer;

		$ready = (
			$oContainer->drugspecified			== true 
			&&	$oContainer->sourcekitscanned	== true 
			&&	$oContainer->sourcekitloaded	== true 
			&&	$oContainer->markedasended		== false 
			&&	$robotCanInject == true
		) ? true : false;
		
		if ($oContainer->sourcekitloaded === true && $robotCanInject === false) {
			//Rajouter log incoherence etat session etat robot
			$oContainer->sourcekitloaded = false;
		}

		$aParam['canInject'] = ($ready) ? true : false;
		$aParam['canUnload'] = ($ready || $oContainer->markedasended) ? true : false;
		$aParam['canExport'] = (!$oContainer->fileexported) ? true : false;
		$aParam['needScan'] = true;
		$aParam['hasExams'] = ($nbExams > 0) ? true : false;
		
		if ($oContainer->drugspecified == true) {
			$drug = $this->getDrugTable()->getDrug($oContainer->drugid);
			$aParam['selectedDrugName'] = '[' . $drug->dci . ']' . ' ' . $drug->name;
		}
		
		return new ViewModel($aParam);
	}
	
	
	public function testprintAction() {
		$aParam = array();
		
		$drug = array('name' => 'GLUSCAN');
		$injection = array('activity' => 100, 'unique_id' => '345345');
		$radionuclide = array('code' => 'F18');
		$curdrug = array('batchnum' => 1738273);
		$operator = array('lastname' => 'HENRI', 'firstname' => 'Michel');
		
		$aParam['timeInjection'] = new \DateTime();
		$aParam['drug'] = $drug;
		$aParam['injection'] = $injection;
		$aParam['unit'] = 'MBq';
		$aParam['radionuclide'] = $radionuclide;
		$aParam['curdrug'] = $curdrug;
		$aParam['radionuclide'] = $radionuclide;
		$aParam['operator'] = $operator;
		
		return new ViewModel($aParam);
	}
}
