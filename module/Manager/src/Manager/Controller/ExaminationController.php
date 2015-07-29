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
use Zend\View\Model\ViewModel;
use Manager\Model\Drug;
use Manager\Model\Examination;
use Manager\View\VExamination;
use Manager\Model\ExaminationTable;

class ExaminationController extends AbstractActionController
{
	protected $drugTable;
	protected $examinationTable;
	protected $systemTable;
	protected $vexaminationTable;

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
	 * @return ExaminationTable
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

	public function getSystemTable()
	{
		if(!$this->systemTable)
		{
			$sm = $this->getServiceLocator();
			$this->systemTable = $sm->get('Manager\Model\SystemTable');
		}
		return $this->systemTable;
	}

	public function getVExaminationTable()
	{
		if(!$this->vexaminationTable)
		{
			$sm = $this->getServiceLocator();
			$this->vexaminationTable = $sm->get('Manager\View\VExaminationTable');
		}
		return $this->vexaminationTable;
	}

	public function	indexAction()
	{
		$aParam = array(
			'examinations'	=> $this->getVExaminationTable()->fetchAll(),
			'unit'			=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
		);
		return new ViewModel($aParam);
	}

	public function	addAction()
	{
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oExamination = new Examination();
			$oExamination->name		= $oRequest->getPost('name');
			$oExamination->drugid	= $oRequest->getPost('drugid');
			$oExamination->rate		= $oRequest->getPost('rate');
			$oExamination->min		= $oRequest->getPost('min');
			$oExamination->max		= $oRequest->getPost('max');
			$this->getExaminationTable()->saveExamination($oExamination);
			return $this->redirect()->toRoute('examination');
		}
		else
		{
			return new ViewModel(array(
				'unit'	=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
				'drugs'	=> $this->getDrugTable()->fetchAll(),
			));
		}
	}

	public function	editAction()
	{
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$examinationId = $oRequest->getPost('id');
			$oExamination = $this->getExaminationTable()->getExamination($examinationId);
			$oExamination->name		= $oRequest->getPost('name');
			$oExamination->drugid	= $oRequest->getPost('drugid');
			$oExamination->rate		= $oRequest->getPost('rate');
			$oExamination->min		= $oRequest->getPost('min');
			$oExamination->max		= $oRequest->getPost('max');
			$this->getExaminationTable()->saveExamination($oExamination);
			return $this->redirect()->toRoute('examination');
		}
		else
		{
			$drugId = $this->params('id');
			return new ViewModel(array(
				'unit'			=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
				'examination'	=> $this->getExaminationTable()->getExamination($drugId),
				'drugs'			=> $this->getDrugTable()->fetchAll(),
			));
		}
	}

	public function	deleteAction()
	{
		$oRequest = $this->getRequest();
		$examinationID = $this->params('id');
		$this->getExaminationTable()->deleteExamination($examinationID);
		return $this->redirect()->toRoute('examination');
	}
}
