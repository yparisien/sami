<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Bufferspace for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Bufferspace\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Config\Reader\Json;
use Zend\View\Helper\ViewModel;
use Manager\Robot\RobotService;
use Zend\View\Model\JsonModel;

class MonitorController extends AbstractActionController
{
	protected	$drugTable;
	protected	$injectionTable;
	protected	$inputdrugTable;
	protected	$patientTable;
	protected	$systemTable;
	protected	$userTable;
	protected	$viewinjectedTable;

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
	 * @return \Bufferspace\Model\InjectionTable
	 */
	public function getInjectionTable()
	{
		if(!$this->injectionTable)
		{
			$sm = $this->getServiceLocator();
			$this->injectionTable = $sm->get('Bufferspace\Model\InjectionTable');
		}
		return $this->injectionTable;
	}

	
	/**
	 *
	 * @return \Logger\Model\InputDrugTable
	 */
	public function getInputDrugTable()
	{
		if(!$this->inputdrugTable)
		{
			$sm = $this->getServiceLocator();
			$this->inputdrugTable = $sm->get('Logger\Model\InputDrugTable');
		}
		return $this->inputdrugTable;
	}
	
	/**
	 * 
	 * @return \Bufferspace\Model\PatientTable
	 */
	public function getPatientTable()
	{
		if(!$this->patientTable)
		{
			$sm = $this->getServiceLocator();
			$this->patientTable = $sm->get('Bufferspace\Model\PatientTable');
		}
		return $this->patientTable;
	}

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
	 * @return \Manager\Model\UserTable
	 */
	public function getUserTable()
	{
		if(!$this->userTable)
		{
			$sm = $this->getServiceLocator();
			$this->userTable = $sm->get('Manager\Model\UserTable');
		}
		return $this->userTable;
	}

	public function	getViewinjectedTable()
	{
		if(!$this->viewinjectedTable)
		{
			$sm = $this->getServiceLocator();
			$this->viewinjectedTable = $sm->get('Bufferspace\View\InjectedTable');
		}
		return $this->viewinjectedTable;

	}
	
	public function indexAction()
	{
		return array();
	}

	public function	patientlistAction()
	{
		$aParam = array(
			'injections'	=> $this->getViewinjectedTable()->fetchAll(),
			'unit'			=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
		);
		
		return $aParam;
	}

	/*
	 * Ajax's calls beyond this line
	 */
	public function	aloadpatientAction()
	{
		$patientId = $this->getRequest()->getPost('patientid');
		$oPatient = $this->getPatientTable()->getPatient($patientId);
		$oInjection = $this->getInjectionTable()->searchByPatientId($patientId);
		
		$oPatient->comments = $oInjection->comments;
		$oPatient->expeditornum = $oInjection->unique_id;
		
		$aParams = $oPatient->toArray();
		
		$result = new JsonModel($aParams);
		return $result;
	}

	public function	aloadinjectionAction()
	{
		/* @var $robotService RobotService  */
		$patientId = $this->getRequest()->getPost('patientid');
		$oInjection = $this->getInjectionTable()->searchByPatientId($patientId);
		
		if ($oInjection->activity > 0) {
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send(array('G_Patient.Input.ActToInj' => $oInjection->activity));
		}
		
		$aParams = $oInjection->toArray();
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function agetinjectedpatientsdetailsAction() {
		$aParams = array();
	
		$patientId = $this->params()->fromQuery('patientid', 0);
		
		if ($patientId > 0) {
			$patient = $this->getPatientTable()->getPatient($patientId);
			$injection = $this->getInjectionTable()->searchByPatientId($patientId);
			$operator = $this->getUserTable()->getUser($injection->operatorid);
			$inputDrug = $this->getInputDrugTable()->getInputDrug($injection->inputdrugid);
			$drug = $this->getDrugTable()->getDrug($injection->drugid);
			
			$aParams['error'] = 0;
			$aParams['patient'] = $patient;
			$aParams['injection'] = $injection;
			$aParams['operator'] = $operator;
			$aParams['inputdrug'] = $inputDrug;
			$aParams['drug'] = $drug;
		} else {
			$aParams['error'] = 1;
		}
		
		$result = new JsonModel($aParams);
		return $result;
	}
}
