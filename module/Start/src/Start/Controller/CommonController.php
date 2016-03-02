<?php
namespace Start\Controller;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;

class CommonController extends AbstractActionController
{
	public function onDispatch(MvcEvent $e) {

		$oContainer = new Container('automate_setup');
		
		if (isset($oContainer->canWork) && $oContainer->canWork === false) {
			if ($this->getEvent()->getRouteMatch()->getParam('pagetype') == 'frontpage') {
				if ($this->getEvent()->getRouteMatch()->getMatchedRouteName() != 'operator') {
					$this->redirect()->toRoute('operator');
				}
			}
		}
		
		parent::onDispatch($e);
	}
}