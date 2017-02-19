<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Operator for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

//TODO Transformer les appels robots

namespace Operator\Controller;

use Zend\Session\Container;
use Zend\Session\Zend\Session;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Validator\File\NotExists;
use Zend\Validator\File\Size;
use Zend\Validator\File\MimeType;
use Zend\View\View;

use Bufferspace\File\Importer;
use Bufferspace\Model\Injection;
use Bufferspace\Model\InjectionTable;
use Bufferspace\Model\Patient;
use Bufferspace\Model\PatientTable;
use Logger\Model\InputAction;
use Logger\Model\InputDrug;
use Logger\Model\InputDrugTable;
use Logger\Model\InputFile;
use Logger\Model\Logger\Model;
use Manager\Model\ExaminationTable;
use Manager\Robot\RobotConstants;
use Manager\Robot\RobotService;
use Manager\Model\User;
use Operator\Model\Sourcekit;
use Operator\Model\Patientkit;
use Start\Controller\CommonController;

class InputdataController extends CommonController
{
	protected $inputActionTable;
	protected $drugTable;
	protected $examinationTable;
	protected $injectionTable;
	protected $inputdrugTable;
	protected $inputfileTable;
	protected $patientTable;
	protected $patientkitTable;
	protected $radionuclideTable;
	protected $sourcekitTable;
	protected $systemTable;
	protected $userTable;
	protected $viewToInjectTable;

	/*
	 * Some handy functions / proxies for access to models
	 */
	
	/**
	 * 
	 * @return \Logger\Model\InputActionTable
	 */
	public function getInputActionTable()
	{
		if(!$this->inputActionTable)
		{
			$sm = $this->getServiceLocator();
			$this->inputActionTable = $sm->get('Logger\Model\InputActionTable');
		}
		return $this->inputActionTable;
	}
	
	/**
	 *
	 * @return \Logger\Model\InputFileTable
	 */
	public function getInputFileTable()
	{
		if(!$this->inputfileTable)
		{
			$sm = $this->getServiceLocator();
			$this->inputfileTable = $sm->get('Logger\Model\InputFileTable');
		}
		return $this->inputfileTable;
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
	 * @return \Manager\Model\ExaminationTable
	 */
	public function getExaminationTable()
	{
		if(!$this->examinationTable)
		{
			$sm = $this->getServiceLocator();
			$this->examinationTable = $sm->get('Manager\Model\ExaminationTable');
		}
		return $this->examinationTable;
	}

	/**
	 * 
	 * @return \Bufferspace\Model\InjectionTable
	 */
	public function getInjectionTable()
	{
		if(!$this->injectionTable)
		{
			$sm = $this->getServiceLocator();
			$this->injectionTable = $sm->get('Bufferspace\Model\InjectionTable');
		}
		return $this->injectionTable;
	}

	/**
	 * 
	 * @return \Logger\Model\InputDrugTable
	 */
	public function	getInputDrugTable()
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
	 * @return \Bufferspace\Model\PatientTable
	 */
	public function getPatientTable()
	{
		if(!$this->patientTable)
		{
			$sm = $this->getServiceLocator();
			$this->patientTable = $sm->get('Bufferspace\Model\PatientTable');
		}
		return $this->patientTable;
	}

	/**
	 * 
	 * @return \Operator\Model\PatientkitTable
	 */
	public function getPatientkitTable()
	{
		if(!$this->patientkitTable)
		{
			$sm = $this->getServiceLocator();
			$this->patientkitTable = $sm->get('Operator\Model\PatientkitTable');
		}
		return $this->patientkitTable;
	}

	/**
	 * 
	 * @return \Manager\Model\RadionuclideTable
	 */
	public function getRadionuclideTable()
	{
		if(!$this->radionuclideTable)
		{
			$sm = $this->getServiceLocator();
			$this->radionuclideTable = $sm->get('Manager\Model\RadionuclideTable');
		}
		return $this->radionuclideTable;
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
	 * @return \Manager\Model\UserTable
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
	
	/**
	 * 
	 * @return \Bufferspace\View\ToInjectTable
	 */
	public function	getViewToInjectTable()
	{
		if(!$this->viewToInjectTable)
		{
			$sm = $this->getServiceLocator();
			$this->viewToInjectTable = $sm->get('Bufferspace\View\ToInjectTable');
		}
		return $this->viewToInjectTable;
	
	}

	/*
	 * Actions methods below this line
	 */
	public function indexAction()
	{
		return $this->redirect()->toRoute('operator');
	}

	public function drugAction()
	{
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');

		if($this->getRequest()->isPost()) // process the submitted form
		{
			$r = $this->getRequest();
			$user = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity());
			$aDrugData = array(
				'inputdate'			=> 'now',
				'userid'			=> $user->id,
				'drugid'			=> $r->getPost('drugid'),
				'batchnum'			=> $r->getPost('batchnum'),
				'calibrationtime'	=> $r->getPost('calibrationdate'),
				'vialvol'			=> $r->getPost('vialvol'),
				'activity'			=> $r->getPost('activity'),
				'activityconc'		=> $r->getPost('activityconc'),
				'activitycalib'		=> $r->getPost('activitycalib'),
				'expirationtime'	=> $r->getPost('expirationdate'),
			);
			
			$inputdrug = new InputDrug();
			$inputdrug->exchangeArray($aDrugData);
			$this->getInputDrugTable()->saveInputDrug($inputdrug);
			
			// log action
			$inputaction = new InputAction();
			$inputaction->inputdate = new \DateTime();
			$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
			$inputaction->action = "Specify inputdrug #".$inputdrug->id;
			$this->getInputActionTable()->saveInputAction($inputaction);

			$drug = $this->getDrugTable()->getDrug($aDrugData['drugid']);
			
			//Envoi a l'automate
			$bDrugData = array(
				RobotConstants::MAINLOGIC_CMD_INPUTSOFT_LOADMEDICAMENT => 0,
			);
			$robotService->send($bDrugData);

			//MAJ de la session
			$oContainer = new Container('automate_setup');
			$oContainer->drugspecified = true;
			$oContainer->drugid = $drug->id;
			$oContainer->inputdrugid = $inputdrug->id;

			return $this->redirect()->toRoute('operator');
		}
		else // simply display the form
		{
			$config = $this->getServiceLocator()->get('Config');
			
			$robotService->send(array(
					RobotConstants::MAINLOGIC_CMD_INPUTSOFT_LOADMEDICAMENT => 1,
			));
			
			$aParam = array(
				'drugs'			=> $this->getDrugTable()->fetchAll(),
				'unit' 			=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
				'locale'		=> explode('_', $this->getSystemTable()->getSystem()->language)[0],
				'vialvol_min'	=> $config['robot']['vialvol']['min'],
				'vialvol_max'	=> $config['robot']['vialvol']['max'],
			);
			return new ViewModel($aParam);
		}
	}

	public function unloadpatientAction() {
		$sm = $this->getServiceLocator();
		$config = $sm->get('Config');
		$error = false;
		$errorMessage = null;
		
		$oImporter = new Importer($this->getServiceLocator());
		$oImporter->cleanDataBase();
		
		$oContainer = new Container('automate_setup');
		$oContainer->fileloaded = false;
		$oContainer->fileexported = false;
		$oContainer->markedasended = false;
		$oContainer->loadedfilename = null;
		
		// log action
		$inputaction = new InputAction();
		$inputaction->inputdate = new \DateTime();
		$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
		$inputaction->action = "Cancel of import file";
		$this->getInputActionTable()->saveInputAction($inputaction);
		
		$inputfile = $this->getInputFileTable()->getLastInputFile();
		if ($inputfile) {
			$inputfile->deleted = 1;
			$this->getInputFileTable()->saveInputFile($inputfile);
		}
		
		$aRetVal = array('error' => $error, 'errorMessage' => $errorMessage);
		
		return new JsonModel($aRetVal);
	}
	
	public function loadpatientAction()
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$sm = $this->getServiceLocator();
		$config = $sm->get('Config');

		$aRetVal = array(
			'success'	=> 1,
			'msg'		=> '',
		);
		
		$destPath = $config['import_export']['upload_path'];
		if($this->getRequest()->isPost()) // du post en entrée, donc on traite un formulaire
		{
			if (class_exists('finfo', false) === false) {
				$aRetVal['msg'] = $translate("PHP Fileinfo extension is missing. Contact your administrator.");
				$aRetVal['success'] = 0;
				return new JsonModel($aRetVal);
			}
			
			$file		= $this->params()->fromFiles('SelectedFile');
			$size		= new Size(array('min'=>'252','max'=>'8MB')); // minimum bytes filesize
			$type		= new MimeType(array('text/csv','text/plain'));
			$exist		= new NotExists($destPath);
			$adapter	= new \Zend\File\Transfer\Adapter\Http();
// 			$adapter->setValidators(array($size,$type,$exist), $file['name']);
			$adapter->setValidators(array($exist), $file['name']);
			
			$todayString = date('Ymd');
			$pattern = '#^' . $todayString . '\d{4}#';
			preg_match($pattern, $file['name'], $matches);
			
			if (count($matches) == 0) {
				$aRetVal['success'] = 0;
				$aRetVal['msg'] = $translate("Bad file format, filename must be named '" . $todayString . "HHMM.csv' ");
			}
			else if ($adapter->isValid())
			{
				//DEPRECATED
				$adapter->setDestination($destPath);
				if ($adapter->receive($file['name']))
				{
					// parse it and load it
					$oImporter = new Importer($this->getServiceLocator());
					$oImporter->setPathFile($destPath);
					try {
						$oImporter->loadFile($file['name']);
						$oImporter->cleanDataBase();
						$oImporter->fillDataBase();
	
						// log action
						$inputaction = new InputAction();
						$inputaction->inputdate = new \DateTime();
						$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
						$inputaction->action = "Import new patients file";
						$this->getInputActionTable()->saveInputAction($inputaction);
						
						$now = new \DateTime();
						$inputFile = new InputFile();
						$inputFile->name = $file['name'];
						$inputFile->in = file_get_contents($destPath . '/' . $file['name']);
						$inputFile->creation_date = $now->format('Y-m-d H:i:s');
						$inputFile->deleted = 0;
						$this->getInputFileTable()->saveInputFile($inputFile);
	
						// for the moment, store the log id but use a clever way for final version
						$oContainer = new Container('automate_setup');
						$oContainer->fileloaded = true;
						$oContainer->fileexported = false;
						$oContainer->markedasended = false;
						$oContainer->loadedfilename = $file['name'];
						
						//Copy file too archive
						$source = realpath($destPath . DIRECTORY_SEPARATOR . $file['name']);
						$pathname = $config['import_export']['import_archive_path'] . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');
	
						if (!is_dir($pathname)) {
							if (@mkdir($pathname, 0777, true) === false) {
								$aRetVal['success'] = 0;
								$aRetVal['msg'] = $translate("Can't create directory " . $pathname);
							}
						}
						
						if (is_dir($pathname) === true) {
							$archive = realpath($pathname) . DIRECTORY_SEPARATOR . basename($file['name'], '.csv') . '_' . date('Ymd-His') . '.csv';
							if (@copy($source, $archive) === true) {
								//Delete file
								if (file_exists($source)) {
									unlink($source);
								}
							}
							else {
								$aRetVal['success'] = 0;
								$aRetVal['msg'] = $translate("Can't copy file " . $source . " to " . $archive);
							}
						}
					} catch (\Exception $e) {
						$aRetVal['success'] = 0;
						$aRetVal['msg'] = $e->getMessage();
					}
				}
				else
				{
					$dataError = $adapter->getMessages();
					$error = array();
					foreach($dataError as $key=>$row)
					{
						$error[] = $row;
					}
				
					$aRetVal['success'] = 0;
					$aRetVal['msg'] = $error;
					$aRetVal['msg'] .= $translate("Can't store to " . $destPath . "/" . $file['name']);
				}
			} else {
				if ($adapter->getErrors()[0] == NotExists::DOES_EXIST) {
					$aRetVal['success'] = 0;
					$aRetVal['msg'] = $translate("File " . realpath($destPath) . DIRECTORY_SEPARATOR  . $file['name'] . " already exists. Try to delete it.");
				} else {
					$aRetVal['success'] = 0;
					$msg = '';
					foreach ($adapter->getErrors() as $error) {
						$msg = $error . ', ';
					}
					$aRetVal['msg'] = substr($msg, 0, -2);
				}
			}
			
			if ($aRetVal['success'] == 0) {
				if (file_exists($destPath . DIRECTORY_SEPARATOR . $file['name'])) {
					unlink($destPath . DIRECTORY_SEPARATOR . $file['name']);
				}
			}
			
			return new JsonModel($aRetVal);
		}
		else // pas de post, on affiche simplement la page
		{
			return array();
		}
	}

	public function scankitsourceAction()
	{
		/* @var $robotService RobotService  */
		
		if($this->getRequest()->isPost()) // du post en entrée, donc on traite un formulaire
		{
			$sourcekitSN = $this->getRequest()->getPost('sourcekit-sn');
			$kit = $this->getSourcekitTable()->searchBySerialNumber($sourcekitSN);
			if($kit) // si on trouve le kit en bdd, on retourne sur la page du scan
			{
				$inputaction = new InputAction();
				$inputaction->inputdate = new \DateTime();
				$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$inputaction->action = "Try to rescan sourcekit #".$kit->serialnumber;
				$this->getInputActionTable()->saveInputAction($inputaction);
				return $this->redirect()->toRoute('setup', array('action'=>'scankitsource'));
			}
			else // sinon on valide la saisie et on passe à la suite
			{
				$login = $this->getServiceLocator()->get('AuthService')->getIdentity();
				$oUser = $this->getUserTable()->searchByLogin($login);

				$oKit = new Sourcekit();
				$oKit->serialnumber = $sourcekitSN;
				$oKit->usedate = date("Y-m-d H:i:s");
				$oKit->operatorid = $oUser->id;
				$oKit = $this->getSourcekitTable()->saveSourcekit($oKit);

				$inputaction = new InputAction();
				$inputaction->inputdate = new \DateTime();
				$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$inputaction->action = "Scan source barcode #".$oKit->id;
				$this->getInputActionTable()->saveInputAction($inputaction);

				// for the moment, store the log id but use a clever way for final version
				$oContainer = new Container('automate_setup');
				$oContainer->sourcekitscanned = true;
				$oContainer->sourcekitbarcode = $sourcekitSN;
				$oContainer->sourcekitid = $oKit->id;

				$robotService = $this->getServiceLocator()->get('RobotService');
				$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_KITSOURCESERIAL => $oKit->serialnumber));
				$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_LOADKITSOURCE => 1));
				
				return $this->redirect()->toRoute('operator');
			}
		}
		else
		{
			return array();
		}
	}

	public function loadkitsourceAction()
	{
		$robotService = $this->getServiceLocator()->get('RobotService');
		if($this->params('confirm'))
		{
			
			// for the moment, store the log id but use a clever way for final version
			$oContainer = new Container('automate_setup');
			$oContainer->sourcekitloaded = true;

			$drug = $this->getDrugTable()->getDrug($oContainer->drugid);
			
			$oContainer->vialcontrolled = false;
			$oContainer->vialdilutabled = false;
			$oContainer->vialisdilutable = $drug->dilutable;
			
			$sm = $this->getServiceLocator();
			$cfg = $sm->get('Config');
			$simulated = isset($cfg['robot']['simulated']) ? $cfg['robot']['simulated'] : false;
			
			if ($simulated === true) {
				$fr = new Container('fake_robot');
				$fr->haskitsourceloaded = true;
			}

			return $this->redirect()->toRoute('operator');
		}
		else
		{
			$inputaction = new InputAction();
			$inputaction->inputdate = new \DateTime();
			$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
			$inputaction->action = "User start loading source & kit.";
			$this->getInputActionTable()->saveInputAction($inputaction);
	
			$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_LOADSEQUENCE => 1));

			return array();
		}
	}

	public function	scankitpatientAction()
	{
		if($this->getRequest()->isPost()) // du post en entrée, donc on traite un formulaire
		{
			$patientkitId = $this->getRequest()->getPost('patientkit-sn');
			$kit = $this->getPatientkitTable()->searchBySerialNumber($patientkitId);
			
			if($kit) // si on trouve le kit en bdd, on retourne sur la page du scan
			{
				$inputaction = new InputAction();
				$inputaction->inputdate = new \DateTime();
				$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$inputaction->action = "Try to rescan patientkit #".$kit->serialnumber;
				$this->getInputActionTable()->saveInputAction($inputaction);
				return $this->redirect()->toRoute('setup', array('action'=>'scankitpatient'));
			}
			else // sinon c'est cool, on peut foncer
			{
				$login = $this->getServiceLocator()->get('AuthService')->getIdentity();
				$oUser = $this->getUserTable()->searchByLogin($login);

				$oKit = new Patientkit();
				$oKit->serialnumber = $patientkitId;
				$oKit->usedate = date("Y-m-d H:i:s");
				$oKit->operatorid = $oUser->id;
				$this->getPatientkitTable()->savePatientkit($oKit);

				//Recuperation de l'injection pour l'informer du kitpatient en base et en session
				$oContainer = new Container('injection_profile');
				$oContainer->patientkitid = $oKit->id;
				$injection = $this->getInjectionTable()->searchByPatientId($oContainer->patientid);
				$injection->patientkitid = $oKit->id;
				$this->getInjectionTable()->saveInjection($injection);
				
				return $this->redirect()->toRoute('setup', array('action'=>'loadkitpatient'));
			}
		}
		else // pas de post, on affiche simplement la page
		{
			return array();
		}
	}

	public function	loadkitpatientAction()
	{
		/* @var $robotService RobotService  */
		if($this->params('confirm'))
		{
			$inputaction = new InputAction();
			$inputaction->inputdate = new \DateTime();
			$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
			$inputaction->action = "User mark patient kit plugged in";
			$this->getInputActionTable()->saveInputAction($inputaction);
			
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VALCONNECTIONKITP => 1));
			
			return $this->redirect()->toRoute('inject', array('action'=>'purge'));
		}
		else
		{
			return array();
		}
	}

	public function confirmauthorityAction() {
		if($this->getRequest()->isPost()) {
			$aParams = array('message' => null, 'error' => false);
			
			$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
			$oRequest = $this->getRequest();
		
			$superviseur = $this->getUserTable()->searchByLogin($oRequest->getPost('login'));
		
			if ($superviseur === false) {
				$message = sprintf($translate("The user (%s) is unknow."), $oRequest->getPost('login'));
				$aParams['message'] = $message;
				$aParams['error'] = true;
			} else if ($superviseur instanceof User) {
				if ($superviseur->admin === true && $superviseur->visible === true) {
					if ($superviseur->password == sha1($oRequest->getPost('password'))) {
						$activity = $oRequest->getPost('activity');
						/*
						 * Enregistre l'action en base de donnée
						 */
						$action = new InputAction();
						$action->inputdate = new \DateTime();
						$action->userid = $superviseur->id;
						$action->action = sprintf($translate("Maximum activity of center reachead. Activity setted: %s. Overpass by %s %s"), $activity, $superviseur->firstname, $superviseur->lastname);
						$this->getInputActionTable()->saveInputAction($action);
					} else {
						$message = sprintf($translate("Authentification failed. Bad password"), $oRequest->getPost('login'));
						$aParams['message'] = $message;
						$aParams['error'] = true;
					}
				} else {
					$message = sprintf($translate("The user (%s) is not autorized to perform this action."), $oRequest->getPost('login'));
					$aParams['message'] = $message;
					$aParams['error'] = true;
				}
			}
			
			return new JsonModel($aParams);
		}
	} 
	
	public function	checkperemptionAction()
	{
		$oContainer = new Container('automate_setup');
		$oAuthtry = new Container('authtry');
		$inputdrugid = $oContainer->inputdrugid;
		$inputDrug = $this->getInputDrugTable()->getInputDrug($inputdrugid);
		
		$now = new \DateTime();
		$dateExpiration = $inputDrug->expirationtime;
		
		$perempted = ($now->getTimestamp() > $dateExpiration->getTimestamp());
		
 		if($perempted)
		{
			if($this->getRequest()->isPost()) {
				$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
				$oRequest = $this->getRequest();
				
				$superviseur = $this->getUserTable()->searchByLogin($oRequest->getPost('login'));

				if ($superviseur === false) {
					$message = sprintf($translate("The user (%s) is unknow."), $oRequest->getPost('login'));
					$this->flashMessenger()->addErrorMessage($message);
					return $this->redirect()->toRoute('setup', array('action'=>'checkperemption'));
				} else if ($superviseur instanceof User) {
					if ($superviseur->admin === true && $superviseur->visible === true) {
						if ($superviseur->password == sha1($oRequest->getPost('password'))) {
							$oAuthtry->peremptiontrynumber = 0;
							/*
							 * Enregistre l'action en base de donnée
							 */
							$action = new InputAction();
							$action->inputdate = new \DateTime();
							$action->userid = $superviseur->id;
							$action->action = sprintf($translate("Peremption limit reachead. Overpass by %s %s"), $superviseur->firstname, $superviseur->lastname);
							$this->getInputActionTable()->saveInputAction($action);
							
							return $this->redirect()->toRoute('setup', array('action'=>'selectpatient'));
						} else {
							$oAuthtry->peremptiontrynumber = $oAuthtry->peremptiontrynumber + 1;
							
							if ($oAuthtry->peremptiontrynumber == 3) {
								return $this->redirect()->toRoute('log', array('action'=>'logout'));
							}
							
							$message = sprintf($translate("Authentification failed. Bad password, %s try(s) remaining"), (3 - $oAuthtry->peremptiontrynumber));
							$this->flashMessenger()->addErrorMessage($message);
							return $this->redirect()->toRoute('setup', array('action'=>'checkperemption'));
						}
					} else {
						$message = sprintf($translate("The user (%s) is not autorized to perform this action."), $oRequest->getPost('login'));
						$this->flashMessenger()->addErrorMessage($message);
						return $this->redirect()->toRoute('setup', array('action'=>'checkperemption'));
					}
				}
				return new ViewModel();
				
			}
			else {
				$inputaction = new InputAction();
				$inputaction->inputdate = new \DateTime();
				$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$inputaction->action = "Peremption date reached.";
				$this->getInputActionTable()->saveInputAction($inputaction);
				
				return new ViewModel();
			}
		}
		else
		{
			return $this->redirect()->toRoute('setup', array('action'=>'selectpatient'));
		}
	}

	public function	selectpatientAction()
	{
		/* @var $patient \Bufferspace\View\ToInject */
		$oContainer = new Container('automate_setup');
		$drug = $this->getDrugTable()->getDrug($oContainer->drugid);
		
		$patientsC = array();
		$patientsNC = array();
		$patients = $this->getViewToInjectTable()->fetchAll();
		
		foreach ($patients as $patient) {
			if (strtolower($drug->dci) == strtolower($patient->injectionDci)) {
				$patientsC[] = $patient;
			} else {
				$patientsNC[] = $patient;
			}
		}
		
		$aParam = array(
			'patients_compatible'	=> $patientsC,
			'patients_nocompatible'	=> $patientsNC,
		);
		
		return new ViewModel($aParam);
	}

	public function createpatientAction()
	{
		$sm = $this->getServiceLocator();
		if($this->getRequest()->isPost())
		{
			$oExaminationTable = $this->getExaminationTable();
			$oRequest = $this->getRequest();
			$oPatient = new Patient();
			$date = new \DateTime($oRequest->getPost('birthdate'));
			$now = new \DateTime();
			$interval = $now->diff($date);
			$age = (int)$interval->y;
			$examination = $oExaminationTable->getExamination($oRequest->getPost('examination'));
			

			$oPatient->id = null;
			$oPatient->patient_id = 0;
			$oPatient->lastname = strtoupper($oRequest->getPost('lastname'));
			$oPatient->firstname = ucfirst(strtolower($oRequest->getPost('firstname')));
			$oPatient->gender = $oRequest->getPost('gender');
			$oPatient->birthdate = $oRequest->getPost('birthdate');
			$oPatient->age = $age;
			$oPatient->weight = $oRequest->getPost('weight');
			$oPatient->height = 0;
			$oPatient->injected = false;
			$this->getPatientTable()->savePatient($oPatient);

			$oInjection = new Injection();
			$oInjection->patient_id 	= $oPatient->id;
			$oInjection->type			= 'infuse';
			$oInjection->injection_date	= date('Y-m-d');
			$oInjection->injection_time	= date('H:i:s');
			$oInjection->activity		= 0;
			$oInjection->dose_status	= '';
			$oInjection->unique_id		= $oRequest->getPost('expeditornum');
			$oInjection->vial_id		= '';
			$oInjection->location		= '';
			$oInjection->comments		= $oRequest->getPost('comment');
			$oInjection->dci			= $examination->dci;
			$oInjection->examinationid	= $examination->id;

			$this->getInjectionTable()->saveInjection($oInjection);

			$oInjection = new Container('injection_profile');
			$oInjection->operatorid = $this->getUserTable()->searchByLogin($sm->get('AuthService')->getIdentity())->id;
			return $this->redirect()->toRoute('setup', array('action'=>'selectpatient'));
		}
		else
		{
			$oContainer = new Container('automate_setup');
			$inputdrug = $this->getInputDrugTable()->getInputDrug($oContainer->inputdrugid);
			$drug = $this->getDrugTable()->getDrug($inputdrug->drugid);
			
			$examinations = $this->getExaminationTable()->fetchAll();
			$dcis = $this->getDrugTable()->fetchAllDCI();
			
			$compatibleExams = [];
			$otherExams = [];
			
			foreach ($examinations as $exam) {
				if ($drug->dci == $exam->dci) {
					$compatibleExams[] = $exam;
				} else {
					$otherExams[] = $exam;
				}
			}
			
			$aParam = array(
				'examinations'	=> array('compatible' => $compatibleExams, 'others' => $otherExams),
				'dcis'			=> $dcis,
			);
			
			return new ViewModel($aParam);
		}
	}

	public function confirmpatientAction()
	{
		$sm = $this->getServiceLocator();
		
		$patientId = $this->getRequest()->getPost('patient-id');
		$oInjection = $this->getInjectionTable()->searchByPatientId($patientId);
		$oContainer = new Container('automate_setup');
		
		$inputdrug = $this->getInputDrugTable()->getInputDrug($oContainer->inputdrugid);
		$drug = $this->getDrugTable()->getDrug($inputdrug->drugid);
		
		$oContainer = new Container('injection_profile');
		$oContainer->inputdrugid = $oInjection->inputdrugid;
		$oContainer->drugid = $oInjection->drugid;
		$oContainer->examinationid = $oInjection->examinationid;
		$oContainer->patientid = $patientId;
		$oContainer->operatorid = $this->getUserTable()->searchByLogin($sm->get('AuthService')->getIdentity())->id;
		
		$examinations = $this->getExaminationTable()->fetchAll();
		
		$compatibleExams = [];
		$otherExams = [];
		
		foreach ($examinations as $exam) {
			if ($drug->dci == $exam->dci) {
				$compatibleExams[] = $exam;
			} else {
				$otherExams[] = $exam;
			}
		}
		
		$aParam = array(
			'patientid'		=> $patientId,
			'examinations'	=> array('compatible' => $compatibleExams, 'others' => $otherExams),
			'machinedrugid'	=> $inputdrug->drugid,
			'unit' 			=> ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
		);
		
		
		return new ViewModel($aParam);
	}

	public function	storecurrentpatientAction()
	{
		/* @var $robotService RobotService */
		$sm = $this->getServiceLocator();
		if($this->getRequest()->isPost())
		{
			$oContainer = new Container('automate_setup');
			$operatorId = $this->getUserTable()->searchByLogin($sm->get('AuthService')->getIdentity())->id;
			
			$oRequest = $this->getRequest();
			$oPatient = $this->getPatientTable()->getPatient($oRequest->getPost('patient_id'));
			$oExamination = $this->getExaminationTable()->getExamination($oRequest->getPost('examinationid'));
			$oDrug = $this->getDrugTable()->getDrug($oContainer->drugid);
			
			$oPatient->lastname = $oRequest->getPost('lastname');
			$oPatient->birthdate = $oRequest->getPost('birthdate');
			$oPatient->firstname = $oRequest->getPost('firstname');
			$oPatient->weight = $oRequest->getPost('weight');
			$this->getPatientTable()->savePatient($oPatient);

			$oInjection = $this->getInjectionTable()->searchByPatientId($oPatient->id);
			$oInjection->activity = $oRequest->getPost('activity');
			$oInjection->examinationid = $oRequest->getPost('examinationid');
			$oInjection->drugid = $oDrug->id;
			$oInjection->inputdrugid = $oContainer->inputdrugid;
			$oInjection->operatorid = $operatorId;
			$this->getInjectionTable()->saveInjection($oInjection);

			$oInjection = new Container('injection_profile');
			$oInjection->inputdrugid = $oContainer->inputdrugid;
			$oInjection->drugid = $oContainer->drugid;
			$oInjection->examinationid = $oRequest->getPost('examinationid');
			$oInjection->patientid = $oPatient->id;
			$oInjection->operatorid = $operatorId;

			
			// Envoi des infos a l'automate
			$dataToSend = array(
					RobotConstants::MAINLOGIC_CMD_INPUTSOFT_LOADPATIENT => 0
			);
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send($dataToSend);

			return $this->redirect()->toRoute('setup', array('action'=>'scankitpatient'));
		}
		else
		{
			return $this->redirect()->toRoute('setup', array('action'=>'selectpatient'));
		}
	}

	/*
	 * Ajax's calls beyond this line
	 */

	public function aunloaddrugAction() {
		/* @var $robotService RobotService  */
		$oContainer = new Container('automate_setup');
	
		$inputaction = new InputAction();
		$inputaction->inputdate = new \DateTime();
		$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
		$inputaction->action = "Remove selected inputdrug #".$oContainer->inputdrugid;
		$this->getInputActionTable()->saveInputAction($inputaction);
	
		$oContainer->drugspecified = false;
		$oContainer->drugid = null;
		$oContainer->inputdrugid = null;
	
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_EXIT => 1));
		$aParams = array('success' => 1);
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function aunloadsourcekitAction() {
		/* @var $robotService RobotService  */
	
		$oContainer = new Container('automate_setup');
		$oldSourceKitId = $oContainer->sourcekitid;
		$oContainer->sourcekitscanned = false;
		$oContainer->sourcekitbarcode = null;
		$oContainer->sourcekitid = null;
	
		$inputaction = new InputAction();
		$inputaction->inputdate = new \DateTime();
		$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
		$inputaction->action = "Remove selected source kit #" . $oldSourceKitId;
		$this->getInputActionTable()->saveInputAction($inputaction);
	
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(
				RobotConstants::MAINLOGIC_CMD_INPUTSOFT_LOADKITSOURCE => 0,
				RobotConstants::MAINLOGIC_CMD_INPUTSOFT_EXIT => 1,
		));
		$aParams = array('success' => 1);
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function asetactivityAction()
	{
		/* @var $robotService RobotService  */
		$sm = $this->getServiceLocator();
		$cfg = $sm->get('Config');
		$simulated = isset($cfg['robot']['simulated']) ? $cfg['robot']['simulated'] : false;
		$oContainer = new Container('injection_profile');
		$fr = new Container('fake_robot');
// 		$fr->confirmpatient = false;
		
		if ($this->getRequest()->isPost()) // process the submitted form
		{
			$robotService = $this->getServiceLocator()->get('RobotService');
			$r = $this->getRequest();
			
			if ($simulated) {
				$exam = null;
				if ($oContainer->examinationid) {
					$exam = $this->getExaminationTable()->getExamination($oContainer->examinationid);
				}
				$p = $this->getPatientTable()->getPatient($oContainer->patientid);
			}

			if ($r->getPost('min'))
			{
				if ($simulated) {
					$fr->activitytoinj = $exam->min;
				}
				$robotService->send(array(RobotConstants::PATIENT_CALCULATION_CHOICEMIN => 1));
			}
			if ($r->getPost('max'))
			{
				if ($simulated) {
					$fr->activitytoinj = $exam->max;
				}
				$robotService->send(array(RobotConstants::PATIENT_CALCULATION_CHOICEMAX => 1));
			}
			if ($r->getPost('norm'))
			{
				if ($simulated) {
					$fr->activitytoinj = $exam->rate * $p->weight;
					if ($fr->activitytoinj > $exam->max) {
						$fr->activitytoinj = $exam->max;
					}
				}
				$robotService->send(array(RobotConstants::PATIENT_CALCULATION_CHOICERECO => 1));
			}
			if ($r->getPost('activity') !== null)
			{
				$fr->activitytoinj = $r->getPost('activity');
// 				if ($simulated && stripos($this->getRequest()->getServer('HTTP_REFERER'), '/setup/confirmpatient') !== false) {
// 					$fr->confirmpatient = true;
// 				}
				$robotService->send(array(RobotConstants::PATIENT_INPUT_ACTTOINJ => $r->getPost('activity')));
			}
			
			$result = new JsonModel(array("error" => false));
			return $result;
		}
	}
	
	public function agetactivityAction() {
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$oContainer = new Container('injection_profile');
		
		$activityIsValid = (int) $robotService->receive(RobotConstants::PATIENT_ACTUAL_VALIDATION);
			
		$activity = $robotService->receive(RobotConstants::PATIENT_ACTUAL_ACTTOINJ);
			
		$result = new JsonModel(array('activity' => $activity, 'activityisvalid' => $activityIsValid));
		if ($activityIsValid == 1) {
			$oInjection = $this->getInjectionTable()->searchByPatientId($oContainer->patientid);
			$oInjection->activity = $activity;
			$this->getInjectionTable()->saveInjection($oInjection);
		}
			
   		return $result;
	}

	public function aupdatepatientAction()
	{
		/* @var $robotService RobotService  */
		$ret = array("error" => false);

		if ($this->getRequest()->isPost()) // process the submitted form
		{
			$r = $this->getRequest();
			$aData = array();
			$patientId = 0;
			$oPatient = null;
			$oInjection = null;
			
			if ($r->getPost('patientid')) {
				$aData[RobotConstants::PATIENT_INPUT_PATIENTID] = $r->getPost('patientid');
				$patientId = $r->getPost('patientid');
			} else {
				$oContainer = new Container('injection_profile');
				$patientId = $oContainer->patientid;
			}
			
			//MAJ du patient et de l'injection
			if ($patientId > 0) {
				$oPatient = $this->getPatientTable()->getPatient($patientId);
				$oInjection = $this->getInjectionTable()->searchByPatientId($patientId);
			}
			
			if ($r->getPost('lastname'))
			{
				$aData[RobotConstants::PATIENT_INPUT_NOM] = $r->getPost('lastname');
				$oPatient->lastname = $r->getPost('lastname');
			}
			if ($r->getPost('firstname'))
			{
				$aData[RobotConstants::PATIENT_INPUT_PRENOM] = $r->getPost('firstname');
				$oPatient->firstname = $r->getPost('firstname');
			}
			if ($r->getPost('birthdate'))
			{
				$aData[RobotConstants::PATIENT_INPUT_DATENAISSANCE] = $r->getPost('birthdate');
				$oPatient->birthdate = $r->getPost('birthdate');
			}
			if ($r->getPost('expeditornum'))
			{
				$aData[RobotConstants::PATIENT_INPUT_ORDONNANCIER] = $r->getPost('expeditornum');
				$oInjection->unique_id = $r->getPost('expeditornum');
			}
			if ($r->getPost('weight'))
			{
				$aData[RobotConstants::PATIENT_INPUT_POIDS] = $r->getPost('weight');
				$oPatient->weight = $r->getPost('weight');
			}
			
			$aData[RobotConstants::MAINLOGIC_CMD_INPUTSOFT_LOADPATIENT] = 1;
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send($aData);

			if ($oPatient instanceof Patient) {
				$this->getPatientTable()->savePatient($oPatient);
			}
			
			if ($oInjection instanceof Injection) {
				$this->getInjectionTable()->saveInjection($oInjection);
			}
		}

		$result = new JsonModel($ret);
   		
		return $result;
	}
	 
	public function aupdatedrugAction()
	{
		/* @var $robotService RobotService  */
		
		if($this->getRequest()->isPost()) // process the submitted form
		{
			$fr = new Container('fake_robot');
			$aDrugData = array();
			$r = $this->getRequest();
			if ($r->getPost('drugid')) {
				$drug = $this->getDrugTable()->getDrug($r->getPost('drugid'));
				$radionucleide = $this->getRadionuclideTable()->getRadionuclide($drug->radionuclideid);
				$aDrugData [RobotConstants::MEDICAMENT_INPUT_ID] = $drug->radionuclideid;
				$aDrugData [RobotConstants::MEDICAMENT_INPUT_NAME] = $drug->name;
				$aDrugData [RobotConstants::MEDICAMENT_INPUT_DCI] = $drug->dci;
				$aDrugData [RobotConstants::MEDICAMENT_INPUT_PERIOD] = $radionucleide->period;
			}
			if ($r->getPost('vialvol'))
			{
				$fr->vialvol = $r->getPost('vialvol');
				$aDrugData[RobotConstants::MEDICAMENT_INPUT_VOL] = $r->getPost('vialvol');
			}
			if ($r->getPost('activityconc'))
			{
				$fr->activityconc = $r->getPost('activityconc');
				$aDrugData[RobotConstants::MEDICAMENT_INPUT_ACTVOL] = $r->getPost('activityconc');
			}
			if ($r->getPost('activitycalib'))
			{
				$fr->activitycalib = $r->getPost('activitycalib');
				$aDrugData[RobotConstants::MEDICAMENT_INPUT_ACTDT] = $r->getPost('activitycalib');
			}
			if ($r->getPost('calibrationtime'))
			{
				$fr->calibrationtime = $r->getPost('calibrationtime');
				$aDrugData[RobotConstants::MEDICAMENT_INPUT_DTCALIB] = "DT#" . date("Y-m-d") . "-" . $r->getPost('calibrationtime') . ":00";
			}
			if ($r->getPost('batchnum'))
			{
				$aDrugData[RobotConstants::MEDICAMENT_INPUT_NLOT] = $r->getPost('batchnum');
			}
			if ($r->getPost('expirationtime'))
			{
				$fr->expirationtime = \DateTime::createFromFormat('Y-m-d H:i:s', $r->getPost('expirationtime'));
				$aDrugData[RobotConstants::MEDICAMENT_INPUT_DTEND] = str_replace(" ", "-","DT#" . $r->getPost('expirationtime'));
			}
			$fr->activityconfirm = null;
			
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send($aDrugData);
		}
		$result = new JsonModel(array());
   		
		return $result;
	}


	public function	arecalcactivityAction()
	{
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$aParams = array();
		
		$r = $this->getRequest();
		if ($r->getPost('field') == "conc")
		{
			$activityConc = $robotService->receive(RobotConstants::MEDICAMENT_ACTUAL_ACTVOL);
			$aParams = array('success' => 1, "activityconc" => $activityConc);
		}
		if ($r->getPost('field') == "calib")
		{
			$activityCalib = $robotService->receive(RobotConstants::MEDICAMENT_ACTUAL_ACTDT);
			$aParams = array('success' => 1, "activitycalib" => $activityCalib);
		}
		if ($r->getPost('field') == "vialvol")
		{
			$activityCalib = $robotService->receive(RobotConstants::MEDICAMENT_ACTUAL_ACTDT);
			$aParams = array('success' => 1, "activitycalib" => $activityCalib);
		}

		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function	agetavailableactivityAction()
	{
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$activity = $robotService->receive(RobotConstants::MEDICAMENT_CALCULATION_CACTDISPO);
		
		$aParams = array('time' => date('H:i:s'), 'activity' => $activity);
		$result = new JsonModel($aParams);

		return $result;
	}

	public function	agetavailableactivityatAction()
	{
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		
		$oTime= $this->getRequest()->getPost('wantedat');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_DATEPREV => $oTime, RobotConstants::MEDICAMENT_CALCULATION_CASTPREVACTIVITY => 1));

		// make it active with real automate
		$aParams = array('time'=>date('H:i:s'), 'activity' => $robotService->receive(RobotConstants::MEDICAMENT_CALCULATION_CACTPREV));
		$result = new JsonModel($aParams);
		return $result;
	}

	public function	acheckSourcekitAction()
	{
		/* @var $robotService RobotService  */
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$aParams = '';
		$sourcekitId = $this->getRequest()->getPost('sourcekit-sn');
		$kit = $this->getSourcekitTable()->searchBySerialNumber($sourcekitId);
		if($kit) // si le kit existe, on bloque
		{
			$oContainer = new Container('barcodetry');
			$oContainer->sourcekittry = (isset($oContainer->sourcekittry)) ? $oContainer->sourcekittry + 1 : 1;
			if($oContainer->sourcekittry >= 3)
			{
				$aParams = array(
					'success'	=> -1,
					'redirect'	=> $this->url()->fromRoute('log', array('action'=>'logout')),
				);
			}
			else
			{
				$inputaction = new InputAction();
				$inputaction->inputdate = new \DateTime();
				$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$inputaction->action = "Try to rescan sourcekit #".$kit->serialnumber;
				$this->getInputActionTable()->saveInputAction($inputaction);
				$oUser = $this->getUserTable()->getUser($kit->operatorid);
				$aParams = array(
					'success'	=> 0,
					'msg'		=> sprintf($translate('Source kit already used by %s %s at %s, %s try remaining'),$oUser->firstname,$oUser->lastname,$kit->usedate,3-$oContainer->sourcekittry),
				);
			}
		}
		else // sinon on laisse le formulaire être soumis
		{
			$robotService = $this->getServiceLocator()->get('RobotService');
			$aParams = array('success' => 1);
		}

		$result = new JsonModel($aParams);
		return $result;
	}

	public function	acheckPatientkitAction()
	{
		/* @var $robotService RobotService  */
		
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
		$aParams = '';
		$patientkitId = $this->getRequest()->getPost('patientkit-sn');
		$kit = $this->getPatientkitTable()->searchBySerialNumber($patientkitId);
		if($kit) // si le kit existe, on bloque
		{
			$oContainer = new Container('barcodetry');
			$oContainer->patientkittry = (isset($oContainer->patientkittry)) ? $oContainer->patientkittry + 1 : 1;

			if($oContainer->patientkittry >= 3)
			{
				$aParams = array(
					'success'	=> -1,
					'redirect'	=> $this->url()->fromRoute('log', array('action'=>'logout')),
				);
			}
			else
			{
				$inputaction = new InputAction();
				$inputaction->inputdate = new \DateTime();
				$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$inputaction->action = "Try to rescan patientkit #".$kit->serialnumber;
				$this->getInputActionTable()->saveInputAction($inputaction);
				$oUser = $this->getUserTable()->getUser($kit->operatorid);
				$aParams = array(
					'success'	=> 0,
					'msg'		=> sprintf($translate('Patient kit already used by %s %s at %s, %s try remaining'), $oUser->firstname, $oUser->lastname, $kit->usedate, 3 - $oContainer->patientkittry),
				);
			}
		}
		else // sinon on laisse le formulaire être soumis
		{
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send(array(RobotConstants::KIT_VALKITP => 1));
			$aParams = array('success' => 1);
		}

		$result = new JsonModel($aParams);
		return $result;
	}

	public function	aloadexaminationAction()
	{
		/* @var $robotService RobotService  */
		$aParams = array();
		$oContainer = new Container('automate_setup');
		

		$examinationId = $this->getRequest()->getPost('examinationid');
		$oExamination = $this->getExaminationTable()->getExamination($examinationId);
		$oDrug = $this->getDrugTable()->getDrug($oContainer->drugid);
		
		if ($oExamination->dci == $oDrug->dci) {
			$aParams['error'] = false;
			$aParams['examination'] = $oExamination->toArray();
			
			$robotService = $this->getServiceLocator()->get('RobotService');
			
			$inputExam = array(
				RobotConstants::PATIENT_INPUT_TYPEEXAM	=> 'typeexam',
				RobotConstants::PATIENT_INPUT_DCI		=> $oExamination->dci,
			);
			$robotService->send($inputExam);
			
			$inputTaux = array(
				RobotConstants::PATIENT_INPUT_TAUX		=> $oExamination->rate,
				RobotConstants::PATIENT_INPUT_TAUXMIN	=> $oExamination->min,
				RobotConstants::PATIENT_INPUT_TAUXMAX	=> $oExamination->max,
			);
			$robotService->send($inputTaux);
			
			$oInjection = new Container('injection_profile');
			$oInjection->examinationid = $examinationId;
		} else {
			$aParams['error'] = true;
		}
		
		$result = new JsonModel($aParams);
		return $result;
	}
	
	//TODO Change par des actions individuelles et faire un log dans la table action
	public function ainputsoftexitAction() {
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_EXIT => 1));
		$aParams = array('success' => 1);
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function acancelpatientAction() {
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_EXIT => 1));
		$aParams = array('success' => 1);
		$result = new JsonModel($aParams);
		return $result;
	}
}
