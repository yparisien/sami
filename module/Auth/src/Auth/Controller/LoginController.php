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
use Zend\View\Model\JsonModel;

class LoginController extends AbstractActionController
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

	public function indexAction()
	{
		if($this->getAuthService()->hasIdentity()) // skip login form if already logged in
		{
			return $this->redirect()->toRoute('operator');
		}
		else // display form
		{
			return new ViewModel();
		}
	}

	public function	signinAction()
	{
		$oSetup = new Container('automate_setup');
		if($oSetup->issetup == true)
		{
			$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
			// grab datas from submitted form
			$sLogin = $this->getRequest()->getPost('login');
			$sPassword = $this->getRequest()->getPost('password');
			// inject them in auth service (db match against)
			$this->getAuthService()->getAdapter()
				->setIdentity($sLogin)
				->setCredential($sPassword);
			$result = $this->getAuthService()->authenticate();
			if($result->isValid()) // if credential is valid
			{
				$this->getSessionStorage()->storeAuth();
				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$action->action = $translate("Auth success");
				$this->getActionTable()->saveAction($action);

				return $this->redirect()->toRoute('operator');
			}
			else // then go back to the login form and display error msg
			{
				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = 0;
				$action->action = "Auth failure on login ".$sLogin;
				$this->getActionTable()->saveAction($action);

				$message = '';
				switch($result->getCode())
				{
					case Result::FAILURE_IDENTITY_NOT_FOUND:
						$message = $translate('No user matching');
						break;
					case Result::FAILURE_CREDENTIAL_INVALID:
						$message = $translate('Login and password doesn\'t match');
						break;
					default:
						$message= $translate('Error during the process, please retry later');
						break;
				}
				$this->flashmessenger()->addErrorMessage($message);

				return $this->redirect()->toRoute('log');
			}
		}
		else
		{
			return $this->redirect()->toRoute('home');
		}
	}

	public function changepwdAction()
	{
		if($this->getRequest()->isPost()) // form was submitted, process it then show form w/ statut msg
		{
			$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
			
			// retrieve datas from submitted form
			$old = $this->getRequest()->getPost('oldpwd');
			$new = $this->getRequest()->getPost('newpwd');
			$conf = $this->getRequest()->getPost('confpwd');

			// retrieve the current user datas (login, pwd)
			$sm = $this->getServiceLocator();
			$login = $sm->get('AuthService')->getIdentity();
			$user = $this->getUserTable()->searchByLogin($login);
			
			if($new == $conf) // if new and confirm pwd matched...
			{
				if(sha1($old) == $user->password) // and if the "old" pwd is correct (match with the current pwd)
				{
					$user->password = sha1($new);
					$this->getUserTable()->saveUser($user);
					$this->flashmessenger()->addSuccessMessage($translate('New password is setted.'));
					return $this->redirect()->toRoute('home');
				}
				else // bad "old" password, we don't change it
				{
					$this->flashmessenger()->addErrorMessage($translate('Old password don\'t match with current password please retry.'));
					return $this->redirect()->toRoute('log', array('action'=>'changepwd'));
				}
			}
			else // the pwd confirmation don't match, we don't change the pwd
			{
				$this->flashmessenger()->addErrorMessage($translate('New password is not confirmed, no match with confirmation, please retry.'));
				return $this->redirect()->toRoute('log', array('action'=>'changepwd'));
			}

		}
	}

	public function	logoutAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		// drop session's data about current user
		$this->getSessionStorage()->forgetMe();
		$this->getAuthService()->clearIdentity();

		// drop session's data about trys
		$oContainer = new Container('barcodetry');
		$oContainer->getManager()->getStorage()->clear('barcodetry');
		$oContainer->getManager()->getStorage()->clear('authtry');
		
		// add nice msg to explain all is clear (displayed into the login form)
		$this->flashmessenger()->addInfoMessage($translate("You have been logged out."));
		
		return $this->redirect()->toRoute('log');
	}
	
	public function	autologoutAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		$oModel = new JsonModel();
		
		// drop session's data about current user
		$this->getSessionStorage()->forgetMe();
		$this->getAuthService()->clearIdentity();
		
		// drop session's data about trys
		$oContainer = new Container('barcodetry');
		$oContainer->getManager()->getStorage()->clear('barcodetry');
		$oContainer->getManager()->getStorage()->clear('authtry');
		
		// return Message
		$oModel->setVariable('message', $translate("You have been logged out."));
		
		return $oModel;
	}
}