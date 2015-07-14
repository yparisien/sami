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

class MonitorController extends AbstractActionController
{
	protected	$injectionTable;
	protected	$patientTable;
	protected	$systemTable;
	protected	$viewinjectedTable;

	public function getInjectionTable()
	{
		if(!$this->injectionTable)
		{
			$sm = $this->getServiceLocator();
			$this->injectionTable = $sm->get('Bufferspace\Model\InjectionTable');
		}
		return $this->injectionTable;
	}

	public function getPatientTable()
	{
		if(!$this->patientTable)
		{
			$sm = $this->getServiceLocator();
			$this->patientTable = $sm->get('Bufferspace\Model\PatientTable');
		}
		return $this->patientTable;
	}

	public function getSystemTable()
	{
		if(!$this->systemTable)
		{
			$sm = $this->getServiceLocator();
			$this->systemTable = $sm->get('Manager\Model\SystemTable');
		}
		return $this->systemTable;
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
		$aParams = $oPatient->toArray();
		$result = new \Zend\View\Model\JsonModel($aParams);
		return $result;
	}

	public function	aloadinjectionAction()
	{
		$patientId = $this->getRequest()->getPost('patientid');
		$oInjection = $this->getInjectionTable()->searchByPatientId($patientId);
		$aParams = $oInjection->toArray();
		$result = new \Zend\View\Model\JsonModel($aParams);
		return $result;
	}
}
