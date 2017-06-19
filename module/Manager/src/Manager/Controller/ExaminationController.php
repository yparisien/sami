<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Manager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Manager\Controller;

use Zend\View\Model\ViewModel;

use Manager\Model\Examination;
use Manager\Model\ExaminationTable;
use Manager\View\VExaminationTable;
use Start\Controller\CommonController;

/**
 * Controlleur des écrans superviseurs de création / modification / suppression des examens
 * 
 * @author yohann.parisien
 *
 */
class ExaminationController extends CommonController
{
	protected $drugTable;
	protected $examinationTable;
	protected $systemTable;
	protected $vexaminationTable;

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
	 * @return VExaminationTable 
	 */
	public function getVExaminationTable()
	{
		if(!$this->vexaminationTable)
		{
			$sm = $this->getServiceLocator();
			$this->vexaminationTable = $sm->get('Manager\View\VExaminationTable');
		}
		return $this->vexaminationTable;
	}

	/**
	 * Action d'affichage des examens
	 * 
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function	indexAction()
	{
		$aParam = array(
			'examinations'	=> $this->getVExaminationTable()->fetchAll(),
			'unit'			=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
		);
		return new ViewModel($aParam);
	}

	/**
	 * Action d'ajout d'un examen à la base de donnée
	 * 
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function	addAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oExamination = new Examination();
			$oExamination->name		= $oRequest->getPost('name');
			$oExamination->dci		= $oRequest->getPost('dci');
			$oExamination->rate		= $oRequest->getPost('rate');
			$oExamination->min		= $oRequest->getPost('min');
			$oExamination->max		= $oRequest->getPost('max');
			$this->getExaminationTable()->saveExamination($oExamination);
			
			$message = sprintf($translate("Examination (%s) has been created."), $oExamination->name);
			$this->flashMessenger()->addSuccessMessage($message);
			
			return $this->redirect()->toRoute('examination');
		}
		else
		{
			return new ViewModel(array(
				'unit'	=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
				'dcis'	=> $this->getDrugTable()->fetchAllDCI(),
			));
		}
	}

	/**
	 * Action de modification d'un examen à la base de donnée
	 * 
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function	editAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$examinationId = $oRequest->getPost('id');
			$oExamination = $this->getExaminationTable()->getExamination($examinationId);
			$oExamination->name		= $oRequest->getPost('name');
			$oExamination->dci		= $oRequest->getPost('dci');
			$oExamination->rate		= $oRequest->getPost('rate');
			$oExamination->min		= $oRequest->getPost('min');
			$oExamination->max		= $oRequest->getPost('max');
			$this->getExaminationTable()->saveExamination($oExamination);
			
			$message = sprintf($translate("Examination (%s) has been modified."), $oExamination->name);
			$this->flashMessenger()->addInfoMessage($message);
			
			return $this->redirect()->toRoute('examination');
		}
		else
		{
			$drugId = $this->params('id');
			return new ViewModel(array(
				'unit'			=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
				'examination'	=> $this->getExaminationTable()->getExamination($drugId),
				'dcis'			=> $this->getDrugTable()->fetchAllDCI(),
			));
		}
	}

	/**
	 * Action de suppression d'un examen à la base de donnée
	 * 
	 * @return \Zend\Http\Response
	 */
	public function	deleteAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		$oRequest = $this->getRequest();
		$examinationID = $this->params('id');
		$examination = $this->getVExaminationTable()->getVExamination($examinationID);
		
		$this->getExaminationTable()->deleteExamination($examinationID);
		$message = sprintf($translate("Examination (%s) has been deleted."), $examination->examination_name);
		$this->flashMessenger()->addSuccessMessage($message);
		
		return $this->redirect()->toRoute('examination');
	}
}
