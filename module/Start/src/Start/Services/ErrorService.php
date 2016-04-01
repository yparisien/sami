<?php

namespace Start\Services;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

use Manager\Robot\RobotConstants;

/**
 * 
 * @author yohann.parisien
 *
 */
class ErrorService implements ServiceLocatorAwareInterface {
	
	protected $sm;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->sm = $serviceLocator;
	}
	
	public function getServiceLocator() {
		return $this->sm;
	}
	
	public function checkErrorStatus($modal = false) {
		$oContainer = new Container('automate_setup');
		$oContainer->canWork = true;
		
		$error = false;
		$html = null;
		
		/*@var $robotService \Manager\Robot\RobotService */
		$robotService = $this->sm->get('RobotService');
		$translate = $this->sm->get('viewhelpermanager')->get('translate');
		$error = (bool) $robotService->receive(RobotConstants::MAINLOGIC_STATUS_ERROR);
		
		if ($error === true) {
			$errorID = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_ERRORID);
			switch ($errorID) {
				case 5:
					$html = $this->errorCode5();
					break;
				case 6:
					$html = $this->errorCode6();
					break;
				case 7:
					$html = $this->errorCode7();
					break;
				case 20:
					$oContainer->canWork = false;
					$html = $this->errorCode20();
					break;
				case 21:
					$oContainer->canWork = false;
					$html = $this->errorCode21();
					break;
				case 22:
					$oContainer->canWork = false;
					$html = $this->errorCode22();
					break;
				case 25:
					$oContainer->canWork = false;
					$html = $this->errorCode25();
					break;
				case 26:
					$oContainer->canWork = false;
					$html = $this->errorCode26();
					break;
				case 27:
					$oContainer->canWork = false;
					$html = $this->errorCode27();
					break;
				default:
					$oContainer->canWork = false;
					$html = $this->unknowErrorCode();
					break;
			}
		}
		
		if ($modal === true) {
			$view = new ViewModel();
			$view->setVariable('errorMessage', $html);
			$view->setTemplate('error/robot/modalerror');
			$viewRender = $this->sm->get('ViewRenderer');
			$html = $viewRender->render($view);
		}
		
		return array('error' => $error, 'html' => $html);
	}
	
	private function errorCode5() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error5');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode6() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error6');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode7() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error7');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode20() {
		$identity = $this->sm->get('AuthService')->getIdentity();
		$oUser = $this->sm->get('Manager\Model\UserTable')->searchByLogin($identity);
	
		$view = new ViewModel();
		$view->setTemplate('error/robot/error20');
		$view->setVariable('isAdmin', $oUser->admin);
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode21() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error21');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode22() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error22');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode25() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error25');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode26() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error26');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode27() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error27');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function unknowErrorCode() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/unknowError');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
}