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

class OperatorController extends AbstractActionController
{
	protected $systemTable;
	protected $drugTable;
	protected $examinationTable;

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

	public function indexAction()
	{
		$sm = $this->getServiceLocator();
		$config = $sm->get('Config');
		
		$aParam = array();
		
		$nbExams = $this->getExaminationTable()->count();
		
		// for the moment, retrieve steps status but use a clever way for final version
		$oContainer = new Container('automate_setup');
		$aParam['step'] = $oContainer;

		$ready = ($oContainer->drugspecified == true
			&& $oContainer->sourcekitscanned == true
			&& $oContainer->sourcekitloaded == true
			&& $oContainer->markedasended == false) ?
		true : false;

		$aParam['canInject'] = ($ready) ? true : false;
		$aParam['canUnload'] = ($ready || $oContainer->markedasended) ? true : false;
		$aParam['canExport'] = ($oContainer->fileloaded) ? true : false;
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
