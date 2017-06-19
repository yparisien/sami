<?php
namespace Start\Controller;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Controlleur commun 
 * 
 * @author yohann.parisien
 *
 */
class CommonController extends AbstractActionController
{
	protected $defaultVariables;
	
	public function onDispatch(MvcEvent $e) {

		$oContainer = new Container('automate_setup');
		
		/*
		 * Si l'automate n'est pas en mesure de travailler, 
		 * redirection systématique vers l'écran principal Opérateur
		 */
		if (isset($oContainer->canWork) && $oContainer->canWork === false) {
			if ($this->getEvent()->getRouteMatch()->getParam('pagetype') == 'frontpage') {
				if ($this->getEvent()->getRouteMatch()->getMatchedRouteName() != 'operator') {
					$this->redirect()->toRoute('operator');
				}
			}
		}
		
		$config = $this->getServiceLocator()->get('Config');

		$this->defaultVariables = array();
		
		$oContainer = new Container('robot_config');

		/*
		 * En mode simulé : Assignation des variables de simulation de la session dans le layout
		 * Permet d'affiche les valeurs des variables de simulation dans le formulaire de la console
		 */
		if ($config['robot']['simulated'] === true) {
			$this->defaultVariables['initTrytogood']		= $oContainer->simulation['init']['trytogood'];
			$this->defaultVariables['initRobotinerror']		= $oContainer->simulation['init']['robotinerror'];
			$this->defaultVariables['initRoboterrorcode']	= $oContainer->simulation['init']['roboterrorcode'];
			$this->defaultVariables['initHasmed']			= $oContainer->simulation['init']['hasmed'];
			$this->defaultVariables['initLoadedmed']		= $oContainer->simulation['init']['loadedmed'];
			$this->defaultVariables['initLoadedsourcekit']	= $oContainer->simulation['init']['loadedsourcekit'];
			$this->defaultVariables['initSourcekitscanned']	= $oContainer->simulation['init']['sourcekitscanned'];
			$this->defaultVariables['initUnit'] 			= $oContainer->simulation['init']['unit'];
			$this->defaultVariables['initRestarttype']		= $oContainer->simulation['init']['restarttype'];
			$this->defaultVariables['initPatientid']		= $oContainer->simulation['init']['patientid'];
		}
		
		$view = $e->getViewModel();
		$view->setVariables($this->defaultVariables);
		
		parent::onDispatch($e);
	}
	
}