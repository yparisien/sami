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
use Manager\Model\Radionuclide;

class RadionuclideController extends AbstractActionController
{
	protected $radionuclideTable;

	public function getRadionuclideTable()
	{
		if(!$this->radionuclideTable)
		{
			$sm = $this->getServiceLocator();
			$this->radionuclideTable = $sm->get('Manager\Model\RadionuclideTable');
		}
		return $this->radionuclideTable;
	}

	public function	indexAction()
	{
		$aParam = array();
		$aParam['radionuclides'] = $this->getRadionuclideTable()->fetchAll();
		return new ViewModel($aParam);
	}

	public function	addAction()
	{
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oUser = new Radionuclide();
			$oUser->name = $oRequest->getPost('name');
			$oUser->period = $oRequest->getPost('period');
			$oUser->coefficient = $oRequest->getPost('coefficient');
			$this->getRadionuclideTable()->saveRadionuclide($oUser);
			return $this->redirect()->toRoute('radionuclide');
		}
		else
		{
			return array();
		}
	}

	public function	editAction()
	{
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$radionuclideId = $oRequest->getPost('id');
			$oRadionuclide = $this->getRadionuclideTable()->getRadionuclide($radionuclideId);
			$oRadionuclide->name = $oRequest->getPost('name');
			$oRadionuclide->period = $oRequest->getPost('period');
			$oRadionuclide->coefficient = $oRequest->getPost('coefficient');
			$this->getRadionuclideTable()->saveRadionuclide($oRadionuclide);
			return $this->redirect()->toRoute('radionuclide');
		}
		else
		{
			$radionuclideId = $this->params('id');
			return new ViewModel(array(
				'radionuclide'	=> $this->getRadionuclideTable()->getRadionuclide($radionuclideId),
			));
		}
	}

	public function	deleteAction()
	{
		$oRequest = $this->getRequest();
		$radionuclideId = $this->params('id');
		$this->getRadionuclideTable()->deleteRadionuclide($radionuclideId);
		return $this->redirect()->toRoute('radionuclide');
	}
}
