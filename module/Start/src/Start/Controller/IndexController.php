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
use Zend\View\Model\JsonModel;
use Manager\Robot\RobotConstants;
use Manager\Robot\RobotService;
use Manager\Model\RadionuclideTable;
use Manager\Model\Radionuclide;
use Manager\Model\Drug;
use Logger\Model\InputDrug;
use Operator\Model\Sourcekit;

class IndexController extends AbstractActionController
{
	protected $authservice;
	protected $drugTable;
	protected $inputdrugTable;
	protected $sourcekitTable;
	protected $storage;
	protected $systemTable;

	
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
	
	/**
	 *
	 * @return \Manager\Model\SystemTable
	 */
	public function getSystemTable()
	{
		if(!$this->systemTable)
		{
			$sm = $this->getServiceLocator();
			$this->systemTable = $sm->get('Manager\Model\SystemTable');
		}
		return $this->systemTable;
	}
	
	
	/**
	 *
	 * @return \Manager\Model\DrugTable
	 */
	public function getDrugTable()
	{
		if(!$this->drugTable)
		{
			$sm = $this->getServiceLocator();
			$this->drugTable = $sm->get('Manager\Model\DrugTable');
		}
		return $this->drugTable;
	}
	
	/**
	 *
	 * @return \Logger\Model\InputDrugTable
	 */
	public function getInputdrugTable()
	{
		if(!$this->inputdrugTable)
		{
			$sm = $this->getServiceLocator();
			$this->inputdrugTable = $sm->get('Logger\Model\InputDrugTable');
		}
		return $this->inputdrugTable;
	}
	
	/**
	 *
	 * @return \Operator\Model\SourcekitTable
	 */
	public function getSourcekitTable()
	{
		if(!$this->sourcekitTable)
		{
			$sm = $this->getServiceLocator();
			$this->sourcekitTable = $sm->get('Operator\Model\SourcekitTable');
		}
		return $this->sourcekitTable;
	}
	
	/**
	 * Initialisation Ping (Vérification activité automate) (STEP 1)
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initpingAction() {
		/* @var $robotService RobotService */
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$active = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_ACTIVE);
		
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('active', (!$active) ? false : true);
		
		return $jsonModel;
	}
	
	
	/**
	 * Initialisation des radionucléides (STEP 2)
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initrnAction() {
		/* @var $robotService RobotService */
		/* @var $rnTable RadionuclideTable */
		$config = $this->getServiceLocator()->get('config');
		$robotService = $this->getServiceLocator()->get('RobotService');
		
		$rnTable = $this->getServiceLocator()->get('Manager\Model\RadionuclideTable');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		$error = false;
		$errorMessage = '';
		
		$nbRN = $robotService->receive(RobotConstants::ISOTOPES_NB);
		
		if ($nbRN < 1) {
			$error = true;
			$errorMessage = $translate("No radionuclide found in the robot. Contact support.");
		}
		else {
			for ($n = 0; $n < $nbRN; $n++) {
				$keyID 			= str_ireplace('@@n@@', $n+1, RobotConstants::ISOTOPES_N_IDISOTOPE);
				$keyShortName 	= str_ireplace('@@n@@', $n+1, RobotConstants::ISOTOPES_N_SHORTNAME);
				$keyName 		= str_ireplace('@@n@@', $n+1, RobotConstants::ISOTOPES_N_NAME);
				$keyPeriod 		= str_ireplace('@@n@@', $n+1, RobotConstants::ISOTOPES_N_HALFLIFE);
				
				$id = $robotService->receive($keyID);
 				$shortName = $robotService->receive($keyShortName);
				$name = $robotService->receive($keyName);
				$period = $robotService->receive($keyPeriod);
// 				$coefficient = $robotService->receive("Isotopes[$n].coefficient");
				$coefficient = 1;
				
				$rn = new Radionuclide();
 				$rn->id = (int) $id;
 				$rn->code = $shortName;
				$rn->name = $name;
				$rn->period = (int) $period;
				$rn->coefficient = (float) $coefficient;
				//Todo Modifier le saveRadionuclide
				$rnTable->saveRadionuclide($rn);
			}
		}
		
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
		$jsonModel->setVariable('errorMessage', $errorMessage);
		
		return $jsonModel;
	}
	
	/**
	 * Initialisation Unité de mesure (STEP 3)
	 *
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initmuAction() {
		/* @var $robotService RobotService */
	
		$error = false;
		$robotService = $this->getServiceLocator()->get('RobotService');
		$measureUnit = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_MEASUREUNIT);

		$oSystem = $this->getSystemTable()->getSystem();
		
		if ($measureUnit == 'mbq' || $measureUnit == 'mci') {
			$oSystem->unit = $measureUnit;
			$this->getSystemTable()->saveSystem($oSystem);
		} else {
			$error = true;
		}
		
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
	
		return $jsonModel;
	}

	/**
	 * Initialisation présence Médicament (Vérification médicament chargé) (STEP 4 4.1)
	 *
	 * @return \Zend\View\Model\JsonModel
	 */
	public function inithmlAction() {
		/* @var $robotService RobotService */
		/* @var $translate \Zend\I18n\View\Helper\Translate */
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
	
		$error = false;
		$errorMessage = null;
	
		$oContainer = new Container('automate_setup');
		
		$hasMed = (bool) $robotService->receive(RobotConstants::MAINLOGIC_STATUS_HASMEDICAMENTLOADED);
		if ($hasMed) {
			$medcode = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_GETMEDICAMENTLOADED);
			
			try {
				$drug = $this->getDrugTable()->getDrugByDci($medcode);
				$inputdrug = $this->getInputdrugTable()->getLastByDrugId($drug->id);
			} catch (\Exception $e) {
				$error = true;
				$errorMessage = sprintf($translate('%s drug not found in database.'), $medcode);
				$drug = null;
				$inputdrug = null;
			}
			
			if ($drug instanceof Drug && $inputdrug instanceof InputDrug) {
				$oContainer->drugspecified = true;
				$oContainer->drugid = $drug->id;
				$oContainer->inputdrugid = $inputdrug->id;
			}
		}
	
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
		$jsonModel->setVariable('hasmed', $hasMed);
		$jsonModel->setVariable('errorMessage', $errorMessage);
	
		return $jsonModel;
	}
	
	/**
	 * Initialisation Kit Source Scanné (Vérification Kit source scanné) (STEP 5 5.1)
	 *
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initsksAction() {
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
	
		$error = false;
	
		$oContainer = new Container('automate_setup');
	
		$hasScan = (bool) $robotService->receive(RobotConstants::MAINLOGIC_STATUS_HASKITSOURCESCANNED);
		if ($hasScan) {
			$serial = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_GETSERIALKITSOURCE);
				
			$sourcekit = $this->getSourcekitTable()->searchBySerialNumber($serial);
				
			if ($sourcekit instanceof Sourcekit) {
				$oContainer->sourcekitscanned = true;
				$oContainer->sourcekitbarcode = $sourcekit->serialnumber;
			}
		}
	
		// 		'sourcekitid' => string '1' (length=1)
		// 		'sourcekitloaded' => boolean true
	
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
		$jsonModel->setVariable('hasscan', $hasScan);
	
		return $jsonModel;
	}
	
	/**
	 * Initialisation Kit Source Chargé (Vérification Kit source chargé) (STEP 6)
	 *
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initlksAction() {
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
	
		$error = false;
	
		$oContainer = new Container('automate_setup');
	
		$isLoaded = (bool) $robotService->receive(RobotConstants::MAINLOGIC_STATUS_HASKITSOURCELOADED);
		if ($isLoaded) {
			$sourcekit = $this->getSourcekitTable()->searchBySerialNumber($oContainer->sourcekitbarcode);
			$oContainer->sourcekitid = $sourcekit->id;
			$oContainer->sourcekitloaded = true;
		}
	
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
	
		return $jsonModel;
	}
	
	/**
	 * Initialisation de la position de démarrage du système
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initspAction() {
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		
		$error = false;
		$errorMessage = '';
		
		$restartType = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_RESTARTTYPE);
		//TODO Sauvegarder le type pour renvoyer vers le bon démarrage
		
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
		$jsonModel->setVariable('errorMessage', $errorMessage);
		
		return $jsonModel;
	}	

	/**
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initsdAction() {
		$error = false;
		$errorMessage = '';

		//TODO Check that all inits are OK

		if ($error === false) {
			$oContainer = new Container('automate_setup');
			$oContainer->issetup = true;
		}
		
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
		$jsonModel->setVariable('errorMessage', $errorMessage);
	
		return $jsonModel;
	}

	/**
	 * 
	 * @return \Zend\Http\Response
	 */
	public function restartAction() {
		//Reinitilise les session du setup Automate à vide
		$oSetup = new Container('automate_setup');
		$oSetup->drugspecified = false;
		$oSetup->drugid = 0;
		$oSetup->inputdrugid = 0;
		$oSetup->fileloaded = false;
		$oSetup->loadedfilename = "";
		$oSetup->sourcekitscanned = false;
		$oSetup->sourcekitbarcode = "";
		$oSetup->sourcekitid = 0;
		$oSetup->sourcekitloaded = false;
		$oSetup->markedasended = false;
		$oSetup->issetup = false;
		
		//Reinitialisation du patient en cours d'injection
		$oInject = new Container('injection_profile');
		$oInject->drugid = 0;
		$oInject->inputdrugid = 0;
		$oInject->examinationid = 0;
		$oInject->patientid = 0;
		$oInject->operatorid = 0;
		$oInject->patientkitid = 0;
		
		//Reinitialisation du nombre d'essais d'authentification
		$oAuthtry = new Container('authtry');
		$oAuthtry->confirmtrynumber = 0;
		$oAuthtry->swaptrynumber = 0;
		
		//Reinitialisation du nombre d'essais de scan codes barres
		$oBarcodetry = new Container('barcodetry');
		$oBarcodetry->sourcekittry = 0;
		$oBarcodetry->patientkittry = 0;
		
		return $this->redirect()->toRoute('home');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction()
	{
		$oContainer = new Container('automate_setup');
		$config = $this->getServiceLocator()->get('config');

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
			$oContainer->inputdrugid = 0;
			$oContainer->fileloaded = false;
			$oContainer->loadedfilename = "";
			$oContainer->sourcekitscanned = false;
			$oContainer->sourcekitbarcode = "";
			$oContainer->sourcekitid = 0;
			$oContainer->sourcekitloaded = false;
			$oContainer->markedasended = false;
			$oContainer->issetup = false;

			$oInject = new Container('injection_profile');
			$oInject->drugid = 0;
			$oInject->inputdrugid = 0;
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
			$viewModel->setVariables(array(
				'version' 					=> $config['version'],
			));
			
			return $viewModel;
		}
	}
}