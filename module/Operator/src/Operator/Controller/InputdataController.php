<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Operator for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Operator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Validator\File\Size;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\NotExists;
use Zend\View\View;
use Operator\Model\Sourcekit;
use Operator\Model\Patientkit;
use Bufferspace\File\Importer;
use Bufferspace\Model\Patient;
use Bufferspace\Model\Injection;
use Logger\Model\Drug;
use Logger\Model\Action;
use Manager\Robot\RobotService;


class InputdataController extends AbstractActionController
{
	protected $actionTable;
	protected $drugTable;
	protected $examinationTable;
	protected $injectionTable;
	protected $logdrugTable;
	protected $patientTable;
	protected $patientkitTable;
	protected $radionuclideTable;
	protected $sourcekitTable;
	protected $systemTable;
	protected $userTable;

	/*
	 * Some handy functions / proxies for access to models
	 */
	public function getActionTable()
	{
		if(!$this->actionTable)
		{
			$sm = $this->getServiceLocator();
			$this->actionTable = $sm->get('Logger\Model\ActionTable');
		}
		return $this->actionTable;
	}

	public function getDrugTable()
	{
		if(!$this->drugTable)
		{
			$sm = $this->getServiceLocator();
			$this->drugTable = $sm->get('Manager\Model\DrugTable');
		}
		return $this->drugTable;
	}

	public function getExaminationTable()
	{
		if(!$this->examinationTable)
		{
			$sm = $this->getServiceLocator();
			$this->examinationTable = $sm->get('Manager\Model\ExaminationTable');
		}
		return $this->examinationTable;
	}

	public function getInjectionTable()
	{
		if(!$this->injectionTable)
		{
			$sm = $this->getServiceLocator();
			$this->injectionTable = $sm->get('Bufferspace\Model\InjectionTable');
		}
		return $this->injectionTable;
	}

	public function	getLogDrugTable()
	{
		if(!$this->logdrugTable)
		{
			$sm = $this->getServiceLocator();
			$this->logdrugTable = $sm->get('Logger\Model\DrugTable');
		}
		return $this->logdrugTable;
	}

	public function getPatientTable()
	{
		if(!$this->patientTable)
		{
			$sm = $this->getServiceLocator();
			$this->patientTable = $sm->get('Bufferspace\Model\PatientTable');
		}
		return $this->patientTable;
	}

	public function getPatientkitTable()
	{
		if(!$this->patientkitTable)
		{
			$sm = $this->getServiceLocator();
			$this->patientkitTable = $sm->get('Operator\Model\PatientkitTable');
		}
		return $this->patientkitTable;
	}

	public function getRadionuclideTable()
	{
		if(!$this->radionuclideTable)
		{
			$sm = $this->getServiceLocator();
			$this->radionuclideTable = $sm->get('Manager\Model\RadionuclideTable');
		}
		return $this->radionuclideTable;
	}

	public function getSourcekitTable()
	{
		if(!$this->sourcekitTable)
		{
			$sm = $this->getServiceLocator();
			$this->sourcekitTable = $sm->get('Operator\Model\SourcekitTable');
		}
		return $this->sourcekitTable;
	}

	public function getSystemTable()
	{
		if(!$this->systemTable)
		{
			$sm = $this->getServiceLocator();
			$this->systemTable = $sm->get('Manager\Model\SystemTable');
		}
		return $this->systemTable;
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
		if($this->getRequest()->isPost()) // process the submitted form
		{
			$r = $this->getRequest();
			$user = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity());
			$aDrugData = array(
				'inputdate'			=> date('Y-m-d H:i:s'),
				'userid'			=> $user->id,
				'drugid'			=> $r->getPost('drugid'),
				'batchnum'			=> $r->getPost('batchnum'),
				'calibrationtime'	=> $r->getPost('calibrationtime'),
				'vialvol'			=> $r->getPost('vialvol'),
				'activity'			=> $r->getPost('activity'),
				'activityconc'		=> $r->getPost('activityconc'),
				'activitycalib'		=> $r->getPost('activitycalib'),
				'expirationtime'	=> $r->getPost('expirationtime'),
			);
			$logdrug = new Drug();
			$logdrug->exchangeArray($aDrugData);
			$this->getLogDrugTable()->saveDrug($logdrug);

			// log action
			$action = new Action();
			$action->inputdate = date('Y-m-d H:i:s');
			$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
			$action->action = "Specify drug profile #".$logdrug->id;
			$this->getActionTable()->saveAction($action);

			$drug = $this->getDrugTable()->getDrug($aDrugData['drugid']);
			$radionucleide = $this->getRadionuclideTable()->getRadionuclide($drug->radionuclideid);
			
			//Envoi a l'automate
			$bDrugData = array(
						"G_Medicament.Input.DT_Calib" => str_replace(" ", "-" ,"DT#" . $aDrugData['calibrationtime'] . ":00"),
						"G_Medicament.Input.Vol" => $aDrugData['vialvol'],
						"G_Medicament.Input.Act" => $aDrugData['activity'],
						"G_Medicament.Input.Act_Vol" => $aDrugData['activityconc'],
						"G_Medicament.Input.Act_DT" => $aDrugData['activitycalib'],
						"G_Medicament.Input.DT_End" => str_replace(" ", "-","DT#" . $aDrugData['expirationtime'] . ":00"),
						"G_Medicament.Input.N_Lot" => $aDrugData['batchnum'],
						"G_Medicament.Input.Name" => $drug->name,
						"G_Medicament.Input.Period" => $radionucleide->period * 60,
						"G_MainLogic.cmd.Input_Soft.Load_Medicament" => 0

					);
			
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send($bDrugData);


			// for the moment, store the log id but use a clever way for final version
			$oContainer = new Container('automate_setup');
			$oContainer->drugspecified = true;
			$oContainer->drugid = $logdrug->id;

			return $this->redirect()->toRoute('setup', array('action'=>'scankitsource'));
		}
		else // simply display the form
		{
			$aParam = array(
				'drugs'	=> $this->getDrugTable()->fetchAll(),
				'unit' => ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
			);
			return new ViewModel($aParam);
		}
	}

	public function loadpatientAction()
	{
		$aRetVal = array(
			'success'	=> 0,
			'msg'		=> '',
		);
		
		$destPath = dirname(__DIR__).'/../../../../public/tmp';
		if($this->getRequest()->isPost()) // du post en entrée, donc on traite un formulaire
		{
			if (class_exists('finfo', false) === false) {
				$aRetVal['msg'][] = "PHP Fileinfo extension is missing. Contact your administrator.";
				return new JsonModel($aRetVal);
			}
			
			$file		= $this->params()->fromFiles('SelectedFile');
			$size		= new Size(array('min'=>'252','max'=>'8MB')); // minimum bytes filesize
			$type		= new MimeType(array('text/csv','text/plain'));
			$exist		= new NotExists($destPath);
			$adapter	= new \Zend\File\Transfer\Adapter\Http();
			$adapter->setValidators(array($size,$type,$exist), $file['name']);
			if ($adapter->isValid())
			{
				$adapter->setDestination($destPath);
				if ($adapter->receive($file['name']))
				{
					// all is ok
					$aRetVal['success'] = 1;

					// parse it and load it
					$oImporter = new Importer($this->getServiceLocator());
					$oImporter->setPathFile($destPath);
					$oImporter->loadFile($file['name']);
					$oImporter->cleanDataBase();
					$oImporter->fillDataBase();

					// log action
					$action = new Action();
					$action->inputdate = date('Y-m-d H:i:s');
					$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
					$action->action = "Import new patients file";
					$this->getActionTable()->saveAction($action);

					// for the moment, store the log id but use a clever way for final version
					$oContainer = new Container('automate_setup');
					$oContainer->fileloaded = true;
					$oContainer->loadedfilename = $file['name'];
				}
				else
				{
					// error!
					$aRetVal['msg'] = "Can't store to ".$destPath."/".$file['name'];
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
				$aRetVal['msg'] = $error;
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
			$sourcekitId = $this->getRequest()->getPost('sourcekit-sn');
			$kit = $this->getSourcekitTable()->searchBySerialNumber($sourcekitId);
			if($kit) // si on trouve le kit en bdd, on retourne sur la page du scan
			{
				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$action->action = "Try to rescan sourcekit #".$kit->serialnumber;
				$this->getActionTable()->saveAction($action);
				return $this->redirect()->toRoute('setup', array('action'=>'scankitsource'));
			}
			else // sinon on valide la saisie et on passe à la suite
			{
				$login = $this->getServiceLocator()->get('AuthService')->getIdentity();
				$oUser = $this->getUserTable()->searchByLogin($login);

				$oKit = new Sourcekit();
				$oKit->serialnumber = $sourcekitId;
				$oKit->usedate = date("Y-m-d H:i:s");
				$oKit->operatorid = $oUser->id;
				$this->getSourcekitTable()->saveSourcekit($oKit);

				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$action->action = "Scan source barcode #".$oKit->id;
				$this->getActionTable()->saveAction($action);

				// for the moment, store the log id but use a clever way for final version
				$oContainer = new Container('automate_setup');
				$oContainer->sourcekitscanned = true;
				$oContainer->sourcekitbarcode = $sourcekitId;
				$oContainer->sourcekitid = $oKit->id;

				$robotService = $this->getServiceLocator()->get('RobotService');
				$robotService->send(array("G_MainLogic.cmd.Input_Soft.Load_Sequence" => 1));

				return $this->redirect()->toRoute('setup', array('action'=>'loadkitsource'));
			}
		}
		else // pas de post, on affiche simplement la page
		{
// Le kit source delphinnove est maintenant obligatoire
//			$system = $this->getSystemTable()->getSystem();
//			if($system->genuinekit)
//			{
				return array();
//			}
//			else
//			{
//				$oContainer = new Container('automate_setup');
//				$oContainer->sourcekitscanned = true;
//				return $this->redirect()->toRoute('setup', array('action'=>'loadkitsource'));
//			}
		}
	}

	public function loadkitsourceAction()
	{
		if($this->params('confirm'))
		{
			$action = new Action();
			$action->inputdate = date('Y-m-d H:i:s');
			$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
			$action->action = "User mark source kit loaded";
			$this->getActionTable()->saveAction($action);

			// for the moment, store the log id but use a clever way for final version
			$oContainer = new Container('automate_setup');
			$oContainer->sourcekitloaded = true;

			return $this->redirect()->toRoute('auth', array('action'=>'confirmauth'));
		}
		else
		{
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
				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$action->action = "Try to rescan patientkit #".$kit->serialnumber;
				$this->getActionTable()->saveAction($action);
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

				$oContainer = new Container('injection_profile');
				$oContainer->patientkitid = $oKit->id;

				return $this->redirect()->toRoute('setup', array('action'=>'loadkitpatient'));
			}
		}
		else // pas de post, on affiche simplement la page
		{
			$system = $this->getSystemTable()->getSystem();
			if($system->genuinekit)
			{
				return array();
			}
			else
			{
				return $this->redirect()->toRoute('setup', array('action'=>'loadkitpatient'));
			}
		}
	}

	public function	loadkitpatientAction()
	{
		/* @var $robotService RobotService  */
		if($this->params('confirm'))
		{
			$action = new Action();
			$action->inputdate = date('Y-m-d H:i:s');
			$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
			$action->action = "User mark patient kit plugged in";
			$this->getActionTable()->saveAction($action);
			
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send(array('G_MainLogic.cmd.Input_Soft.Val_Connection_Kit_P' => 1));
			
			return $this->redirect()->toRoute('inject', array('action'=>'purge'));
		}
		else
		{
			return array();
		}
	}

	public function readData($toRead)
	{
		$this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
		$this->getResponse()->setReasonPhrase("readData($toRead)");
		return ;
		// todo : rendre configuration l'adresse de l'automate
		$aData =  array("redirect" => "response.asp",
				"variable" => $toRead,
				"value" => "",
				"read" => "Read" );
                $postdata = http_build_query($aData);
                $opts = array('http' =>
                    array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $postdata
                    )
                );
                $context  = stream_context_create($opts);
                
		return file_get_contents("http://10.0.0.100/goform/ReadWrite", false, $context);
	}

	public function	checkperemptionAction()
	{
		//TODO Vérifier ce que fait cette méthode
		$aReferer = json_decode(file_get_contents('http://10.0.0.2/checkperemption.asp'), true);
		if($aReferer['isperempted'] == 1)
		{
			return new ViewModel();
		}
		else
		{
			return $this->redirect()->toRoute('setup', array('action'=>'selectpatient'));
		}
	}

	public function	selectpatientAction()
	{
		$aParam = array(
			'patients'	=>	$this->getPatientTable()->getToInject(),
		);
		return new ViewModel($aParam);
	}

	public function createpatientAction()
	{
		$sm = $this->getServiceLocator();
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oPatient = new Patient();
			$date = new \DateTime($oRequest->getPost('birthdate'));
			$now = new \DateTime();
			$interval = $now->diff($date);
			$age = (int)$interval->y;

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
			$oInjection->injection_time	= date('H:i:s');
			$oInjection->activity		= 0;
			$oInjection->dose_status	= '';
			$oInjection->unique_id		= $oRequest->getPost('expeditornum');
			$oInjection->vial_id		= '';
			$oInjection->location		= '';
			$oInjection->comments		= $oRequest->getPost('comment');

			$this->getInjectionTable()->saveInjection($oInjection);

			$oContainer = new Container('automate_setup');
			$drug = $this->getLogDrugTable()->getDrug($oContainer->drugid);

			$oInjection = new Container('injection_profile');
			$oInjection->drugid = $drug->drugid;
			$oInjection->examinationid = $oRequest->getPost('examinationid');
			$oInjection->patientid = $oPatient->id;
			$oInjection->operatorid = $this->getUserTable()->searchByLogin($sm->get('AuthService')->getIdentity())->id;
			//return $this->redirect()->toRoute('setup', array('action'=>'scankitpatient'));
			return $this->redirect()->toRoute('setup', array('action'=>'selectpatient'));
		}
		else
		{
			$oContainer = new Container('automate_setup');
			$drug = $this->getLogDrugTable()->getDrug($oContainer->drugid);
			$aParam = array(
				'examinations'	=> $this->getExaminationTable()->fetchAll(),
				'drugs'			=> $this->getDrugTable()->fetchAll(),
				'drugid'		=> $drug->drugid,
			);
			return new ViewModel($aParam);
		}
	}

	public function confirmpatientAction()
	{
		// on récup l'id en post et on la renvoit ds l'interface, on chargera les données en ajax
		$patientId = $this->getRequest()->getPost('patient-id');
		$oContainer = new Container('automate_setup');
		$drug = $this->getLogDrugTable()->getDrug($oContainer->drugid);
		$aParam = array(
			'patientid'		=> $patientId,
			'examinations'	=> $this->getExaminationTable()->fetchAll(),
			'drugs'			=> $this->getDrugTable()->fetchAll(),
			'drugid'		=> $drug->drugid,
			'unit' => ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi',
		);
		return new ViewModel($aParam);
	}

	public function	storecurrentpatientAction()
	{
		/* @var $robotService RobotService */
		$sm = $this->getServiceLocator();
		if($this->getRequest()->isPost())
		{
			$oRequest = $this->getRequest();
			$oPatient = $this->getPatientTable()->getPatient($oRequest->getPost('patient_id'));
			$oPatient->weight = $oRequest->getPost('weight');
			$this->getPatientTable()->savePatient($oPatient);

			$oInjection = $this->getInjectionTable()->searchByPatientId($oPatient->id);
			$oInjection->activity = $oRequest->getPost('activity');
			$this->getInjectionTable()->saveInjection($oInjection);

			$oContainer = new Container('automate_setup');
			$drug = $this->getLogDrugTable()->getDrug($oContainer->drugid);

			$oInjection = new Container('injection_profile');
			$oInjection->drugid = $drug->drugid;
			$oInjection->examinationid = $oRequest->getPost('examinationid');
			$oInjection->patientid = $oPatient->id;
			$oInjection->operatorid = $this->getUserTable()->searchByLogin($sm->get('AuthService')->getIdentity())->id;

			$oDrug = $this->getDrugTable()->getDrug($drug->drugid);
			$oExamination = $this->getExaminationTable()->getExamination($oRequest->getPost('examinationid'));
			
			// Envoi des infos a l'automate
			$dataToSend = array(
					'G_Patient.Input.Nom' => $oPatient->lastname,
					'G_Patient.Input.Prenom' => $oPatient->firstname,
					'G_Patient.Input.DateN' => $oPatient->birthdate,
					'G_Patient.Input.Ordonnancier' => $oRequest->getPost('expeditornum'),
					'G_Patient.Input.ActToInj' => $oRequest->activity,
					'G_Patient.Input.Med_Name' => $oDrug->name,
					'G_Patient.Input.Poids' => $oRequest->getPost('weight'),
					'G_Patient.Input.Type_Exam' => $oExamination->name,
					'G_Patient.Input.Taux' => $oExamination->rate,
					'G_Patient.Input.Taux_Min' => $oExamination->min,
					'G_Patient.Input.Taux_Max' => $oExamination->max,
					'G_MainLogic.cmd.Input_Soft.Load_Patient' => 0
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

	public function asetactivityAction()
	{
		/* @var $robotService RobotService  */
		
		if ($this->getRequest()->isPost()) // process the submitted form
		{
			$robotService = $this->getServiceLocator()->get('RobotService');
			$r = $this->getRequest();

			if ($r->getPost('min'))
			{
				$robotService->send(array('G_Patient.Calculation.Choice_Min' => 1));
			}
			if ($r->getPost('max'))
			{
				$robotService->send(array('G_Patient.Calculation.Choice_Max' => 1));
			}
			if ($r->getPost('norm'))
			{
				$robotService->send(array('G_Patient.Calculation.Choice_Reco' => 1));
			}
			if ($r->getPost('activity'))
			{
				$robotService->send(array('G_Patient.Input.ActToInj' => $r->getPost('activity')));
			}
			$activity = $robotService->receive('G_Patient.Actual.ActToInj');
			$result = new JsonModel(array('activity' => $activity));
   			return $result;
		}	
	}

	public function aupdatepatientAction()
	{
		/* @var $robotService RobotService  */

		if ($this->getRequest()->isPost()) // process the submitted form
		{
			$r = $this->getRequest();
			$aData = array();
			if ($r->getPost('lastname'))
			{
				$aData['G_Patient.Input.Nom'] = $r->getPost('lastname');
			}
			if ($r->getPost('firstname'))
			{
				$aData['G_Patient.Input.Prenom'] = $r->getPost('firstname');
			}
			if ($r->getPost('birthdate'))
			{
				$aData['G_Patient.Input.DateN'] = $r->getPost('birthdate');
			}
			if ($r->getPost('expeditornum'))
			{
				$aData['G_Patient.Input.Ordonnancier'] = $r->getPost('expeditornum');
			}
			if ($r->getPost('activity'))
			{
				$aData['G_Patient.Input.ActToInj'] = $r->getPost('activity');
			}
			if ($r->getPost('weight'))
			{
				$aData['G_Patient.Input.Poids'] = $r->getPost('weight');
			}
			
			$aData["G_MainLogic.cmd.Input_Soft.Load_Patient"] = 1;
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send($aData);
		}
		//TODO Vérifier si cette action est toujours appelé en POST ou non pour faire remonter le read dans le IF
		$activity = $robotService->receive('G_Patient.Actual.ActToInj');
		$result = new JsonModel(array("activity" => $activity));
   		
		return $result;
	}
	 
	public function aupdatedrugAction()
	{
		/* @var $robotService RobotService  */
		
		if($this->getRequest()->isPost()) // process the submitted form
		{
			$r = $this->getRequest();
			$drug = $this->getDrugTable()->getDrug($r->getPost('drugid'));
			$radionucleide = $this->getRadionuclideTable()->getRadionuclide($drug->radionuclideid);
			$aDrugData = array(
				"G_MainLogic.cmd.Input_Soft.Load_Medicament" => 1,
				'G_Medicament.Input.Name' => $drug->name,
				"G_Medicament.Input.Period" => $radionucleide->period * 60,
				'G_Medicament.Input.Vol' => $r->getPost('vialvol')
				);
			if ($r->getPost('activityconc'))
			{
				$aDrugData['G_Medicament.Input.Act_Vol'] = $r->getPost('activityconc');
			}
			if ($r->getPost('activitycalib'))
			{
				$aDrugData['G_Medicament.Input.Act_DT'] = $r->getPost('activitycalib');
			}
			if ($r->getPost('calibrationtime'))
			{
				$aDrugData['G_Medicament.Input.DT_Calib'] = str_replace(" ", "-" ,"DT#" . $r->getPost('calibrationtime') . ":00");
			}
			if ($r->getPost('batchnum'))
			{
				$aDrugData['G_Medicament.Input.N_Lot'] = $r->getPost('batchnum');
			}
			if ($r->getPost('expirationtime'))
			{
				$aDrugData['"G_Medicament.Input.DT_End"'] = str_replace(" ", "-","DT#" . $r->getPost('expirationtime') . ":00");
			}
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
		
		$r = $this->getRequest();
		if ($r->getPost('field') == "conc")
		{
			$activityConc = $robotService->receive('G_Medicament.Actual.Act_Vol');
			$aParams = array('success' => 1, "activityconc" => $activityConc);
		}
		if ($r->getPost('field') == "calib")
		{
			$activityCalib = $robotService->receive('G_Medicament.Actual.Act_DT');
			$aParams = array('success' => 1, "activitycalib" => $activityCalib);
		}

		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function	agetavailableactivityAction()
	{
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$activity = $robotService->receive('G_Medicament.Calculation.C_Act_Dispo');
		
		$aParams = array('time' => date('H:i:s'), 'activity' => $activity);
		$result = new JsonModel($aParams);

		return $result;
	}

	public function	agetavailableactivityatAction()
	{
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		
		$oTime= $this->getRequest()->getPost('wantedat');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Date_Prev' => $oTime, 'G_Medicament.Calculation.Cast_Prev_Activity' => 1));

		// make it active with real automate
		$aParams = array('time'=>date('H:i:s'), 'activity'=>$this->readData('G_Medicament.Calculation.C_Act_Prev'));
		//$aParams = array('time'=>date('H:m:i'),'activity'=>500);
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
					'redirect'	=> $this->url()->fromRoute('operator')
				);
			}
			else
			{
				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$action->action = "Try to rescan sourcekit #".$kit->serialnumber;
				$this->getActionTable()->saveAction($action);
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
			$robotService->send(array('G_Kit.Val_Kit_S' => 1));
			$aParams = array('success'=>1);
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
					'redirect'	=> $this->url()->fromRoute('operator')
				);
			}
			else
			{
				$action = new Action();
				$action->inputdate = date('Y-m-d H:i:s');
				$action->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
				$action->action = "Try to rescan patientkit #".$kit->serialnumber;
				$this->getActionTable()->saveAction($action);
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
			$robotService->send(array('G_Kit.Val_Kit_P' => 1));
			$aParams = array('success' => 1);
		}

		$result = new JsonModel($aParams);
		return $result;
	}

	public function	aloadexaminationAction()
	{
		/* @var $robotService RobotService  */
		
		$examinationId = $this->getRequest()->getPost('examinationid');
		$oExamination = $this->getExaminationTable()->getExamination($examinationId);

		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(
						'G_Patient.Input.Type_Exam' => $oExamination->name,
						'G_Patient.Input.Taux' => $oExamination->rate,
						'G_Patient.Input.Taux_Min' => $oExamination->min,
						'G_Patient.Input.Taux_Max' => $oExamination->max));
		
		$aParams = $oExamination->toArray();
		$result = new JsonModel($aParams);
		return $result;
	}
	public function	asetdemomodeAction()
	{
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.par.Demo_Mode' => 1));
		
		$result = new JsonModel(array());
		return $result;
	}
	public function	aunsetdemomodeAction()
	{
		/* @var $robotService RobotService  */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.par.Demo_Mode' => 0));
		
		$result = new JsonModel(array());
		return $result;
	}	

}
