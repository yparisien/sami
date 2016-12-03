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
use Zend\I18n\View\Helper\Translate;

class IndexController extends CommonController
{
	protected $authservice;
	protected $drugTable;
	protected $inputdrugTable;
	protected $sourcekitTable;
	protected $storage;
	protected $systemTable;

	private	$errorMessageTemplate;	
	
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
	 * Check la présence d'erreur sur chaque écran
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function checkerrorstatusAction() {
		/*@var $errorService \Start\Services\ErrorService */
		$errorService = $this->getServiceLocator()->get('Start\Services\ErrorService');
		
		$ret = $errorService->checkErrorStatus(true);
		
		$jsonModel = new JsonModel();
		$jsonModel->setVariables($ret);
		
		return $jsonModel;
	}
	
	
	
	/**
	 * Initialisation Ping (Vérification activité automate) (STEP 1)
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initpingAction() {
		/* @var $robotService RobotService */
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$active = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_ACTIVE);
		
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('active', (!$active) ? false : true);
		
		if ($active) {
			$now = new \DateTime();
			$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_DATETIMEIHM => "DT#" . $now->format('Y-m-d-H:i:s')));
			$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_CHANGEDATETIME => 1));
		}
		
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
		$errorMessage = null;
		
		$nbRN = $robotService->receive(RobotConstants::ISOTOPES_NB);
		
		if ($nbRN < 1) {
			$error = true;
			$errorMessage = $translate("No radionuclide found in the robot. Contact support.");
		}
		else {
			for ($n = 0; $n < $nbRN; $n++) {
				$keyID 			= str_ireplace('@@n@@', $n, RobotConstants::ISOTOPES_N_IDISOTOPE);
				$keyShortName 	= str_ireplace('@@n@@', $n, RobotConstants::ISOTOPES_N_SHORTNAME);
				$keyName 		= str_ireplace('@@n@@', $n, RobotConstants::ISOTOPES_N_NAME);
				$keyPeriod 		= str_ireplace('@@n@@', $n, RobotConstants::ISOTOPES_N_HALFLIFE);
				
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
				$rn->period = $period;
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
		$measureUnit = $robotService->receive(RobotConstants::MAINLOGIC_PAR_MEASUREUNIT);

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
		
		$hasMed = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_HASMEDICAMENTLOADED);
		
		if ($hasMed === '1') {
			$hasMed = true;
			$medname = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_GETMEDICAMENTLOADED);
			
			try {
				$drug = $this->getDrugTable()->getDrugByName($medname);
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
		} else if ($hasMed === '0') {
			$hasMed = false;
			$oContainer->drugspecified = false;
			$oContainer->drugid = null;
			$oContainer->inputdrugid = null;
		} else {
			$error = true;
			$errorMessage = sprintf($translate('Dont know if a drug is loaded (%s).'), $hasMed);
			$hasMed = null;
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
		/* @var $translate \Zend\I18n\View\Helper\Translate */
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
	
		$error = false;
		$errorMessage = null;
	
		$oContainer = new Container('automate_setup');
	
		$hasScan = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_HASKITSOURCESCANNED);
		if ($hasScan === '1') {
			$hasScan = true;
			$serial = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_GETSERIALKITSOURCE);
				
			$sourcekit = $this->getSourcekitTable()->searchBySerialNumber($serial);
				
			if ($sourcekit instanceof Sourcekit) {
				$oContainer->sourcekitscanned = true;
				$oContainer->sourcekitbarcode = $sourcekit->serialnumber;
			}
			
			//TODO Rajouter verif retour robot et comparer entrée base sinon erreur
		} else if ($hasScan === '0') {
			$hasScan = false;
			$oContainer->sourcekitscanned = false;
			$oContainer->sourcekitbarcode = null;
		} else {
			$error = true;
			$errorMessage = sprintf($translate("Don't know if kitsource has been scanned (%s)."), $hasScan);
			$hasScan = null;
		}
	
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
		$jsonModel->setVariable('hasscan', $hasScan);
		$jsonModel->setVariable('errorMessage', $errorMessage);
	
		return $jsonModel;
	}
	
	/**
	 * Initialisation Kit Source Chargé (Vérification Kit source chargé) (STEP 6)
	 *
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initlksAction() {
		/* @var $robotService RobotService */
		/* @var $translate \Zend\I18n\View\Helper\Translate */
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
	
		$error = false;
		$errorMessage = null;
	
		$oContainer = new Container('automate_setup');
	
		$isLoaded = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_HASKITSOURCELOADED);
		if ($isLoaded === '1') {
			$sourcekit = $this->getSourcekitTable()->searchBySerialNumber($oContainer->sourcekitbarcode);
			$oContainer->sourcekitid = $sourcekit->id;
			$oContainer->sourcekitloaded = true;
			//TODO Rajouter les etats de vérifs kit controllé et kit dilué 
		} else if ($isLoaded === '0') {
			$oContainer->sourcekitid = null;
			$oContainer->sourcekitloaded = false;
		} else {
			$error = true;
			$errorMessage = sprintf($translate("Don't know if kitsource & source has been loaded (%s)."), $isLoaded);
		}
	
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
		$jsonModel->setVariable('errorMessage', $errorMessage);
	
		return $jsonModel;
	}
	
	/**
	 * Initialisation de la position de démarrage du système
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initspAction() {
		/* @var $robotService RobotService */
		/* @var $translate \Zend\I18n\View\Helper\Translate */
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		
		$error = false;
		$errorMessage = null;
		
		$restartType = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_RESTARTTYPE);
		if (is_numeric($restartType)) {
			$oContainer = new Container('automate_setup');
			$oContainer->startposition = (int) $restartType;
			if ($oContainer->startposition > 7) {
				$oContainer->startposition = 0;
				$restartType = 0;
			}
		} else {
			$error = true;
			$errorMessage = sprintf($translate("Don't know if kitsource & source has been loaded (%s)."), $restartType);
		}
		
		$jsonModel = new JsonModel();
		$jsonModel->setVariable('error', $error);
		$jsonModel->setVariable('errorMessage', $errorMessage);
		$jsonModel->setVariable('restartType', $restartType);
		
		return $jsonModel;
	}	

	/**
	 * 
	 * @return \Zend\View\Model\JsonModel
	 */
	public function initsdAction() {
		$error = false;
		$errorMessage = null;

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
		$oSetup->fileexported = false;
		$oSetup->fileloaded = false;
		$oSetup->loadedfilename = "";
		$oSetup->sourcekitscanned = false;
		$oSetup->sourcekitbarcode = "";
		$oSetup->sourcekitid = 0;
		$oSetup->sourcekitloaded = false;
		$oSetup->markedasended = false;
		$oSetup->issetup = false;
		$oSetup->vialcontrolled = false;
		$oSetup->vialisdilutable = false;
		$oSetup->vialdilutabled = false;
		
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
		$oAuthtry->peremptiontrynumber = 0;
		
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
			$oContainer->fileexported = false;
			$oContainer->loadedfilename = "";
			$oContainer->sourcekitscanned = false;
			$oContainer->sourcekitbarcode = "";
			$oContainer->sourcekitid = 0;
			$oContainer->sourcekitloaded = false;
			$oContainer->markedasended = false;
			$oContainer->issetup = false;
			$oContainer->vialcontrolled = false;
			$oContainer->vialisdilutable = false;
			$oContainer->vialdilutabled = false;

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
			$oAuthtry->peremptiontrynumber = 0;

			$oBarcodetry = new Container('barcodetry');
			$oBarcodetry->sourcekittry = 0;
			$oBarcodetry->patientkittry = 0;

			$viewModel = new ViewModel();
			$viewModel->setVariables(array(
				'version' => $config['version'],
			));
			
			return $viewModel;
		}
	}
	
	public function skiperrorAction() {
		
		if($this->getRequest()->isPost()) {
			$errorID = $this->getRequest()->getPost('errorID');
			$operator = new Container('operator');
			$operator->skippedErrors[$errorID] = $errorID;
		}
		
		$jsonModel = new JsonModel();
		return $jsonModel;
	}
}