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

class UserController extends AbstractActionController
{
	protected $userTable;

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
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oUser = new User();
			$oUser->lastname = $oRequest->getPost('lastname');
			$oUser->firstname = $oRequest->getPost('firstname');
			$oUser->login = $oRequest->getPost('login');
			$oUser->password = sha1($oRequest->getPost('password')); // @todo add test on pwd/confirm pwd fields
			$oUser->admin = ($oRequest->getPost('admin'))? 1 : 0;
			$oUser->visible = 1;
			$this->getUserTable()->saveUser($oUser);
			return $this->redirect()->toRoute('user');
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
			$userId = $oRequest->getPost('id');
			$oUser = $this->getUserTable()->getUser($userId);
			$oUser->lastname = $oRequest->getPost('lastname');
			$oUser->firstname = $oRequest->getPost('firstname');
			$oUser->login = $oRequest->getPost('login');
			$oUser->admin = ($oRequest->getPost('admin'))? 1 : 0;
			if ($oRequest->getPost('password') == $oRequest->getPost('password-confirm')) {
				$oUser->password = sha1($oRequest->getPost('password'));
			}
			$this->getUserTable()->saveUser($oUser);
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
		$oRequest = $this->getRequest();
		$userId = $this->params('id');
		$this->getUserTable()->deleteUser($userId);
		return $this->redirect()->toRoute('user');
	}
}
