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
use Zend\Mvc\Controller\AbstractActionController;

class InjectionProfileController extends AbstractActionController
{
	protected	$injectionProfileTable;

	public function getInjectionProfileTable()
	{
		if(!$this->injectionProfileTable)
		{
			$sm = $this->getServiceLocator();
			$this->injectionProfileTable = $sm->get('Manager\Model\InjectionProfileTable');
		}
		return $this->injectionProfileTable;
	}


	public function	indexAction()
	{
		$aParam = array();
		return new ViewModel($aParam);
	}

	public function	addAction()
	{
// 		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
// 		if($this->getRequest()->isPost())
// 		{
// 			$oRequest = $this->getRequest();
// 			$oDrug = new Drug();
// 			$oDrug->name 			= $oRequest->getPost('name');
// 			$oDrug->radionuclideid 	= $oRequest->getPost('radionuclideid');
// 			$oDrug->dci 			= $oRequest->getPost('dci');
// 			$oDrug->dilutable 		= ($oRequest->getPost('dilutable', 0) == 1) ? true : false;
// 			$this->getDrugTable()->saveDrug($oDrug);
			
// 			$message = sprintf($translate("Drug (%s) has been created."), $oDrug->name);
// 			$this->flashMessenger()->addSuccessMessage($message);
			
// 			return $this->redirect()->toRoute('drug');
// 		}
// 		else
// 		{
// 			return new ViewModel(array(
// 				'radionuclides'	=> $this->getRadionuclideTable()->fetchAll(),
// 			));
// 		}
	}

	public function	editAction()
	{
// 		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
// 		if($this->getRequest()->isPost())
// 		{
// 			$oRequest = $this->getRequest();
// 			$drugId = $oRequest->getPost('id');
// 			$oDrug = $this->getDrugTable()->getDrug($drugId);
// 			$oDrug->name			= $oRequest->getPost('name');
// 			$oDrug->radionuclideid	= $oRequest->getPost('radionuclideid');
// 			$oDrug->dci				= $oRequest->getPost('dci');
// 			$oDrug->dilutable 		= ($oRequest->getPost('dilutable', 0) == 1) ? true : false;
// 			$this->getDrugTable()->saveDrug($oDrug);
			
// 			$message = sprintf($translate("Drug (%s) has been modified."), $oDrug->name);
// 			$this->flashMessenger()->addInfoMessage($message);
			
// 			return $this->redirect()->toRoute('drug');
// 		}
// 		else
// 		{
// 			$drugId = $this->params('id');
// 			return new ViewModel(array(
// 				'drug'			=> $this->getDrugTable()->getDrug($drugId),
// 				'radionuclides'	=> $this->getRadionuclideTable()->fetchAll(),
// 			));
// 		}
	}

	public function	deleteAction()
	{
// 		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
// 		$oRequest = $this->getRequest();
// 		$drugId = $this->params('id');
// 		$vdrug = $this->getVDrugTable()->getVDrug($drugId);
		
// 		if ($vdrug->nbExams == 0) {
// 			$this->getDrugTable()->deleteDrug($drugId);
// 			$message = sprintf($translate("Drug (%s) has been deleted."), $vdrug->drug_name);
// 			$this->flashMessenger()->addSuccessMessage($message);
// 		}
// 		else {
// 			$message = sprintf($translate("Drug (%s) can't be deleted. Already in use in %s examinations"), $vdrug->drug_name, $vdrug->nbExams);
// 			$this->flashMessenger()->addErrorMessage($message);
// 		}
		
// 		return $this->redirect()->toRoute('drug');
	}
}
