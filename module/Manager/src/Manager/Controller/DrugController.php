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
use Manager\Model\Drug;
use Zend\View\Model\JsonModel;
use Start\Controller\CommonController;

/**
 * Controlleur des écrans superviseurs de création / modification / suppression des médicaments
 * 
 * @author yohann.parisien
 *
 */
class DrugController extends CommonController
{
	protected	$drugTable;
	protected	$radionuclideTable;
	protected	$vdrugTable;

	/**
	 * 
	 * @return \Manager\Model\RadionuclideTable
	 */
	public function getRadionuclideTable()
	{
		if(!$this->radionuclideTable)
		{
			$sm = $this->getServiceLocator();
			$this->radionuclideTable = $sm->get('Manager\Model\RadionuclideTable');
		}
		return $this->radionuclideTable;
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
	 * @return \Manager\View\VDrugTable
	 */
	public function getVDrugTable()
	{
		if(!$this->vdrugTable)
		{
			$sm = $this->getServiceLocator();
			$this->vdrugTable = $sm->get('Manager\View\VDrugTable');
		}
		return $this->vdrugTable;
	}

	/**
	 * Action d'affichage des médicaments
	 * 
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function	indexAction()
	{
		$aParam = array();
		$aParam['drugs'] = $this->getVDrugTable()->fetchAll();
		return new ViewModel($aParam);
	}

	/**
	 * Action d'ajout d'un médicament à la base de donnée
	 * 
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function	addAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oDrug = new Drug();
			$oDrug->name 			= $oRequest->getPost('name');
			$oDrug->radionuclideid 	= $oRequest->getPost('radionuclideid');
			$oDrug->dci 			= $oRequest->getPost('dci');
			$oDrug->dilutable 		= ($oRequest->getPost('dilutable', 0) == 1) ? true : false;
			$this->getDrugTable()->saveDrug($oDrug);
			
			$message = sprintf($translate("Drug (%s) has been created."), $oDrug->name);
			$this->flashMessenger()->addSuccessMessage($message);
			
			return $this->redirect()->toRoute('drug');
		}
		else
		{
			return new ViewModel(array(
				'radionuclides'	=> $this->getRadionuclideTable()->fetchAll(),
			));
		}
	}

	/**
	 * Action de modification d'un médicament à la base de donnée
	 * 
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function	editAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$drugId = $oRequest->getPost('id');
			$oDrug = $this->getDrugTable()->getDrug($drugId);
			$oDrug->name			= $oRequest->getPost('name');
			$oDrug->radionuclideid	= $oRequest->getPost('radionuclideid');
			$oDrug->dci				= $oRequest->getPost('dci');
			$oDrug->dilutable 		= ($oRequest->getPost('dilutable', 0) == 1) ? true : false;
			$this->getDrugTable()->saveDrug($oDrug);
			
			$message = sprintf($translate("Drug (%s) has been modified."), $oDrug->name);
			$this->flashMessenger()->addInfoMessage($message);
			
			return $this->redirect()->toRoute('drug');
		}
		else
		{
			$drugId = $this->params('id');
			return new ViewModel(array(
				'drug'			=> $this->getDrugTable()->getDrug($drugId),
				'radionuclides'	=> $this->getRadionuclideTable()->fetchAll(),
			));
		}
	}

	/**
	 * Action de suppression d'un médicament à la base de donnée
	 * 
	 * @return \Zend\Http\Response
	 */
	public function	deleteAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$oRequest = $this->getRequest();
		$drugId = $this->params('id');
		$vdrug = $this->getVDrugTable()->getVDrug($drugId);
		
		$this->getDrugTable()->deleteDrug($drugId);
		$message = sprintf($translate("Drug (%s) has been deleted."), $vdrug->drug_name);
		$this->flashMessenger()->addSuccessMessage($message);
		
		return $this->redirect()->toRoute('drug');
	}
	
	/**
	 * Action Ajax de vérification d'unicité du nom du medicament
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function acheckuniqueAction() {
		$aParams = array('error' => true, 'errorMessage' => 'This drug name already exists.');
		
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$drugName = $oRequest->getPost('name');
			$currentDrugId = (int) $oRequest->getPost('currentDrugId', 0);
			$oDrug = $this->getDrugTable()->getDrugByName($drugName);
			
			if ($oDrug === null || $oDrug->id == $currentDrugId) {
				$aParams = array('error' => false);
			}
		}
		$result = new JsonModel($aParams);
		return $result;
	}
}
