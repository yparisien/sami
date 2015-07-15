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

class DrugController extends AbstractActionController
{
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
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oDrug = new Drug();
			$oDrug->name = $oRequest->getPost('name');
			$oDrug->radionuclideid = $oRequest->getPost('radionuclideid');
			$oDrug->dci = $oRequest->getPost('dci');
			$this->getDrugTable()->saveDrug($oDrug);
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
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$drugId = $oRequest->getPost('id');
			$oDrug = $this->getDrugTable()->getDrug($drugId);
			$oDrug->name			= $oRequest->getPost('name');
			$oDrug->radionuclideid	= $oRequest->getPost('radionuclideid');
			$oDrug->dci				= $oRequest->getPost('dci');
			$this->getDrugTable()->saveDrug($oDrug);
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
		$oRequest = $this->getRequest();
		$drugId = $this->params('id');
		$this->getDrugTable()->deleteDrug($drugId);
		return $this->redirect()->toRoute('drug');
	}
}
