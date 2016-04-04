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
		$errorID = null;
		$btnLogout = false;
		$btnOkSkip = false;
		
		/*@var $robotService \Manager\Robot\RobotService */
		$robotService = $this->sm->get('RobotService');
		$translate = $this->sm->get('viewhelpermanager')->get('translate');
		$error = (bool) $robotService->receive(RobotConstants::MAINLOGIC_STATUS_ERROR);
		
		if ($error === true) {
			$errorID = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_ERRORID);
			$operator = new Container('operator');
			if (!isset($operator->skippedErrors[$errorID])) {
				switch ($errorID) {
					case 1:
						$html = $this->errorCode1();
						$btnOkSkip = true;
						break;
					case 5:
						$html = $this->errorCode5();
						$btnLogout = true;
						break;
					case 6:
						$html = $this->errorCode6();
						$btnLogout = true;
						break;
					case 7:
						$html = $this->errorCode7();
						$btnLogout = true;
						break;
					case 11:
						$html = $this->errorCode11();
						$btnLogout = true;
						break;
					case 12:
						$html = $this->errorCode12();
						$btnLogout = true;
						break;
					case 13:
						$html = $this->errorCode13();
						$btnLogout = true;
						break;
					case 14:
						$html = $this->errorCode14();
						$btnLogout = true;
						break;
					case 15:
						$html = $this->errorCode15();
						$btnLogout = true;
						break;
					case 16:
						$html = $this->errorCode16();
						$btnLogout = true;
						break;
					case 17:
						$html = $this->errorCode17();
						$btnLogout = true;
						break;
					case 18:
						$html = $this->errorCode18();
						$btnLogout = true;
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
						$btnLogout = true;
						$html = $this->unknowErrorCode();
						break;
				}
			} else {
				$error = false;
			}
		}
		
		if ($modal === true && $error === true) {
			$view = new ViewModel();
			$view->setVariable('errorMessage', $html);
			$view->setVariable('errorID', $errorID);
			$view->setVariables(['btnLogout' => $btnLogout, 'btnOkSkip' => $btnOkSkip]);
			$view->setTemplate('error/robot/modalerror');
			$viewRender = $this->sm->get('ViewRenderer');
			$html = $viewRender->render($view);
		}
		
		return array('error' => $error, 'html' => $html, 'errorID' => $errorID);
	}
	
	private function errorCode1() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error1');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
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
	
	private function errorCode11() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error11');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode12() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error12');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode13() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error13');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode14() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error14');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode15() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error15');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode16() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error16');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode17() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error17');
	
		$viewRender = $this->sm->get('ViewRenderer');
		return $viewRender->render($view);
	}
	
	private function errorCode18() {
		$view = new ViewModel();
		$view->setTemplate('error/robot/error18');
	
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