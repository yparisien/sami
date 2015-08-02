<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link		http://github.com/zendframework/Manager for the canonical source repository
 * @copyright	Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license		http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Manager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Manager\Model\User;
use Manager\Model\UserTable;

class UserController extends AbstractActionController
{
	protected $userTable;

	/**
	 * 
	 * @return UserTable object
	 */
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
		$aParam = array();
		$aParam['users'] = $this->getUserTable()->fetchAll();
		return new ViewModel($aParam);
	}

	public function	addAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$oRequest = $this->getRequest();
		
		if($oRequest->isPost())
		{
			if ($oRequest->getPost('password') != $oRequest->getPost('password-confirm')) {
				$message = $translate("Password confirmation is different than new password.");
				$this->flashMessenger()->addMessage($message);
				return $this->redirect()->toRoute('user', array('action' => 'add'));
			} else {
				$oUser = new User();
				$oUser->lastname = $oRequest->getPost('lastname');
				$oUser->firstname = $oRequest->getPost('firstname');
				$oUser->login = $oRequest->getPost('login');
				$oUser->password = sha1($oRequest->getPost('password'));
				$oUser->admin = ($oRequest->getPost('admin'))? 1 : 0;
				$oUser->visible = 1;
				
				$this->getUserTable()->saveUser($oUser);
				
				$message = sprintf($translate("Operator (%s) has been created."), $oUser->login);
				$this->flashMessenger()->addSuccessMessage($message);
				return $this->redirect()->toRoute('user');
			}
		}
		else
		{
			return array();
		}
	}

	public function	editAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$oRequest = $this->getRequest();
		
		if($oRequest->isPost())
		{
			if ($oRequest->getPost('password') != $oRequest->getPost('password-confirm')) {
				$message = $translate("Password confirmation is different than new password.");
				$this->flashMessenger()->addMessage($message);
				return $this->redirect()->toRoute('user', array('action' => 'edit', 'id' => $oRequest->getPost('id')));
			} else {
				$userId = $oRequest->getPost('id');
				$oUser = $this->getUserTable()->getUser($userId);
				$oUser->lastname = $oRequest->getPost('lastname');
				$oUser->firstname = $oRequest->getPost('firstname');
				$oUser->login = $oRequest->getPost('login');
				$oUser->admin = ($oRequest->getPost('admin'))? 1 : 0;
				if (strlen($oRequest->getPost('password')) > 0 && $oRequest->getPost('password') == $oRequest->getPost('password-confirm')) {
					$oUser->password = sha1($oRequest->getPost('password'));
				}
				$this->getUserTable()->saveUser($oUser);
				$message = sprintf($translate("Operator (%s) has been modified."), $oUser->login);
				$this->flashMessenger()->addInfoMessage($message);
			}
			
			return $this->redirect()->toRoute('user');
		}
		else
		{
			$userId = $this->params('id');
			return new ViewModel(array(
				'user'	=> $this->getUserTable()->getUser($userId),
			));
		}
	}

	public function	deleteAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		$oRequest = $this->getRequest();
		$userId = $this->params('id');
		$user = $this->getUserTable()->getUser($userId);
		
		//TODO Check user can
		
		$this->getUserTable()->deleteUser($userId);
		$message = sprintf($translate("Operator (%s) has been deleted."), $user->login);
		$this->flashMessenger()->addSuccessMessage($message);
		
		return $this->redirect()->toRoute('user');
	}
}
