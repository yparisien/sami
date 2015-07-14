<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Start\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	protected $storage;
	protected $authservice;

	public function getAuthService()
	{
		if(!$this->authservice) {
			$this->authservice = $this->getServiceLocator()
				->get('AuthService');
		}
		return $this->authservice;
	}

	public function getSessionStorage()
	{
		if(!$this->storage) {
			$this->storage = $this->getServiceLocator()
				->get('Auth\Model\AuthStorage');
		}
		return $this->storage;
	}

	public function indexAction()
	{
		$oContainer = new Container('automate_setup');

		// on skip le chargement si déjà setup
		if(isset($oContainer->issetup) && $oContainer->issetup == true)
		{
			return $this->redirect()->toRoute('log');
		}
		// sinon on affiche le chargement
		else
		{
			// on initialise divers trucs en session
			$oContainer->drugspecified = false;
			$oContainer->drugid = 0;
			$oContainer->fileloaded = false;
			$oContainer->loadedfilename = "";
			$oContainer->sourcekitscanned = false;
			$oContainer->sourcekitbarcode = "";
			$oContainer->sourcekitid = 0;
			$oContainer->sourcekitloaded = false;
			$oContainer->markedasended = false;
			$oContainer->issetup = true;

			$oInject = new Container('injection_profile');
			$oInject->drugid = 0;
			$oInject->examinationid = 0;
			$oInject->patientid = 0;
			$oInject->operatorid = 0;
			$oInject->patientkitid = 0;

			$oAuthtry = new Container('authtry');
			$oAuthtry->confirmtrynumber = 0;
			$oAuthtry->swaptrynumber = 0;

			$oBarcodetry = new Container('barcodetry');
			$oBarcodetry->sourcekittry = 0;
			$oBarcodetry->patientkittry = 0;

			$viewModel = new ViewModel();
			$config = $this->getServiceLocator()->get('config');
			$viewModel->setVariables(array('version'=>$config['version']));
			return $viewModel;
		}
	}
}