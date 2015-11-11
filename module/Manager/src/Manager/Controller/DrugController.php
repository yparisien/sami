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
use Manager\Model\Radionuclide;
use Zend\View\Model\JsonModel;

class DrugController extends AbstractActionController
{
	//TODO Rajouter une colonne killed car on ne pas perdre des données du fait que tous les input drug sont sauvegardés
	protected	$drugTable;
	protected	$radionuclideTable;
	protected	$vdrugTable;

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

	public function getVDrugTable()
	{
		if(!$this->vdrugTable)
		{
			$sm = $this->getServiceLocator();
			$this->vdrugTable = $sm->get('Manager\View\VDrugTable');
		}
		return $this->vdrugTable;
	}


	public function	indexAction()
	{
		$aParam = array();
		$aParam['drugs'] = $this->getVDrugTable()->fetchAll();
		return new ViewModel($aParam);
	}

	public function	addAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oDrug = new Drug();
			$oDrug->name = $oRequest->getPost('name');
			$oDrug->radionuclideid = $oRequest->getPost('radionuclideid');
			$oDrug->dci = $oRequest->getPost('dci');
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

	public function	deleteAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$oRequest = $this->getRequest();
		$drugId = $this->params('id');
		$vdrug = $this->getVDrugTable()->getVDrug($drugId);
		
// 		if ($vdrug->nbExams == 0) {
			$this->getDrugTable()->deleteDrug($drugId);
			$message = sprintf($translate("Drug (%s) has been deleted."), $vdrug->drug_name);
			$this->flashMessenger()->addSuccessMessage($message);
// 		}
// 		else {
// 			$message = sprintf($translate("Drug (%s) can't be deleted. Already in use in %s examinations"), $vdrug->drug_name, $vdrug->nbExams);
// 			$this->flashMessenger()->addErrorMessage($message);
// 		}
		
		return $this->redirect()->toRoute('drug');
	}
	
	public function acheckuniqueAction() {
		$aParams = array('error' => true, 'errorMessage' => 'This drug name already exists.');
		
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$drugName = $oRequest->getPost('name');
			$oDrug = $this->getDrugTable()->getDrugByName($drugName);
			if ($oDrug === null) {
				$aParams = array('error' => false);
			}
		}
		$result = new JsonModel($aParams);
		return $result;
	}
}
