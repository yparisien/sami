<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Session\Container;
use Logger\Model\Action;

class AuthController extends AbstractActionController
{
	protected $storage;
	protected $authservice;
	protected $actionTable;
	protected $userTable;

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

	public function getActionTable()
	{
		if(!$this->actionTable)
		{
			$sm = $this->getServiceLocator();
			$this->actionTable = $sm->get('Logger\Model\ActionTable');
		}
		return $this->actionTable;
	}

	public function getUserTable()
	{
		if(!$this->userTable)
		{
			$sm = $this->getServiceLocator();
			$this->userTable = $sm->get('Manager\Model\UserTable');
		}
		return $this->userTable;
	}

	public function	indexAction()
	{
		if($this->getAuthService()->hasIdentity())
		{
			return $this->redirect()->toRoute('operator');
		}
		else
		{
			return array();
		}
	}

	/*
	 * Traitement du formulaire de connexion + redirection
	 */
	public function	confirmauthAction()
	{
		$oContainer = new Container('automate_setup');
		$ready = ($oContainer->drugspecified == true
			&& $oContainer->fileloaded == true
			&& $oContainer->sourcekitscanned == true
			&& $oContainer->sourcekitloaded == true
			&& $oContainer->markedasended == false) ?
			true : false;
		if($ready)
		{
			$allowedtry = 3;
			$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');

			if($this->getRequest()->isPost())
			{
				$oContainer = new Container('authtry');
				$sLogin = $this->getRequest()->getPost('login');
				$sPassword = $this->getRequest()->getPost('password');
				$oUser = $this->getUserTable()->searchByLogin($sLogin);
				if($oUser && $oUser->password == sha1($sPassword)) // if credential is valid
				{
					$oContainer->confirmtrynumber = 0;
					$this->redirect()->toRoute('setup', array('action'=>'checkperemption'));
				}
				else
				{
					$oContainer->confirmtrynumber = (isset($oContainer->confirmtrynumber)) ? $oContainer->confirmtrynumber + 1 : 1;
					if($oContainer->confirmtrynumber >= $allowedtry)
					{
						// @todo fix it
						//$message = $translate("Too much failed try - You have been disconnected");
						//$this->flashmessenger()->addErrorMessage($message);
						$this->redirect()->toRoute('log', array('action'=>'logout'));
					}
					else
					{
						$message = sprintf($translate("Bad password - %s try(s) remaining"), $allowedtry - $oContainer->confirmtrynumber);
						$this->flashmessenger()->addErrorMessage($message);
						$this->redirect()->toRoute('auth', array('action'=>'confirmauth'));
					}
				}
			}
			else
			{
				$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$action->action = $translate("Confirm his/her identity");
				$this->getActionTable()->saveAction($action);

				$aParam = array(
					'current_user'	=> $this->getAuthService()->getIdentity(),
				);
				return new ViewModel($aParam);
			}
		}
		else
		{
			$this->redirect()->toRoute('operator');
		}
	}

	/*
	 * Page de changement d'utilisateur
	 */
	public function swapauthAction()
	{
		$allowedtry = 3;
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		if($this->getRequest()->isPost())
		{
			$this->getSessionStorage()->forgetMe();
			$oRequest = $this->getRequest();
			$sLogin = $this->getRequest()->getPost('login');
			$sPassword = $this->getRequest()->getPost('password');
			$oUser = $this->getUserTable()->searchByLogin($sLogin);
			$oContainer = new Container('authtry');
			if($oUser && $oUser->password == sha1($sPassword)) // if credential is valid
			{
				// on injecte les données dans le service auth
				$this->getAuthService()->getAdapter()
				->setIdentity($sLogin)
				->setCredential($sPassword);
				$result = $this->getAuthService()->authenticate();

				$oContainer->swaptrynumber = 0;
				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$action->action = $translate("Switching user successfull");
				$this->getActionTable()->saveAction($action);
				$this->getSessionStorage()->storeAuth();
				return $this->redirect()->toRoute('setup', array('action'=>'checkperemption'));
			}
			// sinon on retourne sur le formulaire avec une erreur
			else
			{
				$oContainer->swaptrynumber = (isset($oContainer->swaptrynumber)) ? $oContainer->swaptrynumber + 1 : 1;
				if($oContainer->swaptrynumber >= $allowedtry)
				{
					// @todo fix it
					//$message = $translate("Too much failed try - You have been disconnected");
					//$this->flashmessenger()->addErrorMessage($message);
					$this->redirect()->toRoute('log', array('action'=>'logout'));
				}
				else
				{
					$message = sprintf($translate("Bad credentials - %s try(s) remaining"), $allowedtry - $oContainer->swaptrynumber);
					$this->flashmessenger()->addErrorMessage($message);
					return $this->redirect()->toRoute('auth', array('action'=>'swapauth'));
				}
			}
		}
		else
		{
			return new ViewModel();
		}
	}

}