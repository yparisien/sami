<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/Operator for the canonical source repository
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace Operator\Controller;

use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;

use Logger\Model\InputAction;
use Manager\Robot\RobotService;
use Manager\Robot\RobotConstants;
use Zend\View\Model\Zend\View\Model;
use Bufferspace\Model\PatientHistory;
use Start\Controller\CommonController;

class InteractrobotController extends CommonController
{
	protected $inputActionTable;
	protected $drugTable;
	protected $examinationTable;
	protected $injectionTable;
	protected $inputdrugTable;
	protected $patientTable;
	protected $patientkitTable;
	protected $radionuclideTable;
	protected $sourcekitTable;
	protected $systemTable;
	protected $userTable;
	protected $patienthistoryTable;

	
	/*
	 * Some handy functions / proxies for access to models
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
	 * @return \Bufferspace\Model\PatientHistoryTable
	 */
	public function getPatientHistoryTable()
	{
		if(!$this->patienthistoryTable)
		{
			$sm = $this->getServiceLocator();
			$this->patienthistoryTable = $sm->get('Bufferspace\Model\PatientHistoryTable');
		}
		return $this->patienthistoryTable;
	}

	public function readData($toRead)
	{
		$this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
		$this->getResponse()->setReasonPhrase("readData($toRead)");
		return ;
	}

	/*
	 * Actions methods below this line
	 */
	public function	indexAction()
	{
		return array();
	}

	public function purgeAction()
	{
		$aParams = array();
		
		if ($this->params()->fromQuery('launched', null))
		{
			$aParams['launched'] = true;
		}
		
		return new ViewModel($aParams);
	}

	public function	sampleAction()
	{
		$aParams = array();
		
		$injection = new Container('injection_profile');
		$patientId = $injection->patientid;
		
		if ($this->params()->fromQuery('launched', null))
		{
			$aParams['launched'] = true;
		}
		
		$aParams['patient'] = $this->getPatientTable()->getPatient($patientId)->toArray();
		$aParams['injection'] = $this->getInjectionTable()->searchByPatientId($patientId)->toArray();
		$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		
		return new ViewModel($aParams);
	}

	public function	injectionAction()
	{
		$aParams = array();
		
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VALSAMPLING => 1));

		$injection = new Container('injection_profile');
		$patientId = $injection->patientid;
		
		if ($this->params()->fromQuery('launched', null))
		{
			$aParams['launched'] = true;
		}
		
		$aParams['patient'] 	= $this->getPatientTable()->getPatient($patientId)->toArray();
		$aParams['injection']	= $this->getInjectionTable()->searchByPatientId($patientId)->toArray();
		$aParams['unit'] 		= ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		
		return new ViewModel($aParams);
	}

	public function	patientdisconnectionAction()
	{
		$aParams = array();
		
		$injection = new Container('injection_profile');
		$setup = new Container('automate_setup');
		
		$inputdrug = $this->getInputDrugTable()->getInputDrug($injection->inputdrugid);
		
		if ($this->params()->fromQuery('launched', null))
		{
			$aParams['launched'] = true;
		}

		$aParams['patient'] 	= $this->getPatientTable()->getPatient($injection->patientid)->toArray();
		$aParams['injection'] 	= $this->getInjectionTable()->searchByPatientId($injection->patientid)->toArray();
		$aParams['unit'] 		= ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		$aParams['examination'] = $this->getExaminationTable()->getExamination($injection->examinationid)->toArray();
		$aParams['operator'] 	= $this->getUserTable()->getUser($injection->operatorid)->toArray();
		$aParams['curdrug'] 	= $inputdrug->toArray();
		$aParams['drug'] 		= $this->getDrugTable()->getDrug($inputdrug->drugid)->toArray();
		$aParams['radionuclide']= $this->getRadionuclideTable()->getRadionuclide($aParams['drug']['radionuclideid'])->toArray();
		$aParams['sourcekit'] 	= $this->getSourcekitTable()->getSourcekit($setup->sourcekitid)->toArray();
		$aParams['unit'] 		= ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		$aParams['patientkit'] = $this->getPatientkitTable()->getPatientkit($injection->patientkitid)->toArray();

		return new ViewModel($aParams);
	}

	public function	unloadAction()
	{
		if($this->params('confirm'))
		{
			
			return $this->redirect()->toRoute('operator');
		}
		else
		{
			/* @var $robotService RobotService */
			$robotService = $this->getServiceLocator()->get('RobotService');
			
			$oContainer = new Container('automate_setup');
			$oContainer->drugspecified = false;
			$oContainer->drugid = 0;
			$oContainer->sourcekitscanned = false;
			$oContainer->sourcekitloaded = false;
			$oContainer->vialcontrolled = false;
			$oContainer->vialisdilutable = false;
			$oContainer->vialdilutabled = false;

			$inputaction = new InputAction();
			$inputaction->inputdate = new \DateTime();
			$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
			$inputaction->action = "User mark automate unloaded";
			$this->getInputActionTable()->saveInputAction($inputaction);
			
			$sm = $this->getServiceLocator();
			$cfg = $sm->get('Config');
			$simulated = isset($cfg['robot']['simulated']) ? $cfg['robot']['simulated'] : false;
			
			$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_UNLOAD => 1));
				
			if ($simulated === true) {
				$fr = new Container('fake_robot');
				$fr->haskitsourceloaded = false;
			}
			
			return array();
		}
	}

	public function	endinjectAction()
	{
		$oContainer = new Container('automate_setup');
		if ($oContainer->markedasended === false) {
			$aParams = array();
			$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
			return new ViewModel($aParams);
		} else {
			$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
			$page = "End Inject";
			$message = sprintf($translate("You are not authorized to perform this action : Access page '%s'"), $page);
			$this->flashmessenger()->addErrorMessage($message);
			return $this->redirect()->toRoute('operator');
		}
	}

	/*
	 * Ajax's calls beyond this line
	 */
	
	public function	agetpurgeprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive(RobotConstants::SUBPURGE_STATUS_EVOLUTION);
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function	alaunchpurgeAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_LOADPURGE => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	adisconnectpatientAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VALPATIENTDISCONNECTION => 1));
				
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	agetsamplingprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_SAMPLINGEVOLUTION);
		
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function	alaunchsamplingAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_SAMPLINGSEQUENCE => 1));

		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	aadjustsamplingAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_ADJUSTSAMPLING => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);

		return $result;
	}
	
	public function	agetsamplingactivityAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$activity = $robotService->receive(RobotConstants::MAINLOGIC_CMD_INPUTTRASYS_MEASUREDVALUE);
		
		$aParams = array("activity" => $activity);
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function	agetdilutionprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_DILUTIONEVOLUTION);
		
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
		
		return $result;
	}
	public function	alaunchdilutionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_DILUTIONSEQUENCE => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	agetinjectionprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_INJECTIONEVOLUTION);
		
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	agetrinsingprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive(RobotConstants::MAINLOGIC_STATUS_RINSINGEVOLUTION);
	
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
	
		return $result;
	}
	
	public function	alaunchinjectionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_INJECTIONSEQUENCE => 1));
		
		$aParams = array("error" => false);
		$result = new JsonModel($aParams);

		return $result;
	}
	
	public function	alaunchrinsingAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_RINSINGSEQUENCE => 1));
	
		$aParams = array("error" => false);
		$result = new JsonModel($aParams);
	
		return $result;
	}
	
	/* Pause ou rédémarre l'injection */
	public function	apauseinjectionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_STOPINJECTION => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function	aplayinjectionAction()
	{
		/* @var $robotService RobotService */
		$speed = $this->getRequest()->getPost('speed');
		
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(
				RobotConstants::MAINLOGIC_CMD_INPUTSOFT_INJECTSPEED => $speed,
				RobotConstants::MAINLOGIC_CMD_INPUTSOFT_PLAYINJECTION => 1,
		));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	aspeedinjectionAction()
	{
		/* @var $robotService RobotService */
		$speed = $this->getRequest()->getPost('speed');

		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_INJECTSPEED => $speed));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	agetspeedinjectionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$speed = $robotService->receive(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_INJECTSPEED);
		
		$aParams = array("speed" => $speed);
		$result = new JsonModel($aParams);
		return $result;
	}

	public function	amarkpatientconnectedAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		
		$inputaction = new InputAction();
		$inputaction->inputdate = new \DateTime();
		$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
		$inputaction->action = "User mark the patient connected to SAMI";
		$this->getInputActionTable()->saveInputAction($inputaction);

		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VALPATIENTCONNECTION => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}

	public function	aupdateactivityAction()
	{
		$newActivity = $this->getRequest()->getPost('activity');
		$inputaction = new InputAction();
		$inputaction->inputdate = new \DateTime();
		$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
		$inputaction->action = "New injected activity value: ".$newActivity;
		$this->getInputActionTable()->saveInputAction($inputaction);

		$injectionProfile = new Container('injection_profile');
		$injection = $this->getInjectionTable()->searchByPatientId($injectionProfile->patientid);
		$injection->activity = $newActivity;
		$this->getInjectionTable()->saveInjection($injection);

		$aParams = array();
		$result = new JsonModel($aParams);
		return $result;
	}

	public function	amarkasinjectedAction()
	{
		$injectionProfile = new Container('injection_profile');
		$oPatient = $this->getPatientTable()->getPatient($injectionProfile->patientid);
		$oPatient->injected = true;
		$this->getPatientTable()->savePatient($oPatient);
		
		$oInputDrug = $this->getInputDrugTable()->getInputDrug($injectionProfile->inputdrugid);
		
		$oInjection = $this->getInjectionTable()->searchByPatientId($oPatient->id);
		$oInjection->injection_time = date('H:i:s'); // heure automate ou ihm?
		$oInjection->drugid = $oInputDrug->drugid;
		$oInjection->inputdrugid = $oInputDrug->id;
		$oInjection->examinationid = $injectionProfile->examinationid;
		$oInjection->operatorid = $injectionProfile->operatorid;
		$oInjection->injection_date = date('Y-m-d');
		$this->getInjectionTable()->saveInjection($oInjection);
		
		$patientHistory = PatientHistory::generateByPatientId($oPatient->id, $this->serviceLocator);
		$this->getPatientHistoryTable()->save($patientHistory);
		
		return new JsonModel(array('success'=>true));
	}

	public function	storelocationAction()
	{
		$injectionProfile = new Container('injection_profile');
		$oInjection = $this->getInjectionTable()->searchByPatientId($injectionProfile->patientid);
		$oInjection->location = $this->getRequest()->getPost('location');
		$this->getInjectionTable()->saveInjection($oInjection);
		return new JsonModel(array('success'=>true, 'location'=>$oInjection->location));
	}

	public function	amarkendinjectAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_ENDINJECTION => 1));
		
		$setup = new Container('automate_setup');
		$setup->markedasended = true;
		
		
		return new JsonModel(array('success'=>true));
	}
	
	public function	alaunchcheckvialAction()
	{
		$success = false;
		$automateSetup = new Container('automate_setup');
		
		if ($automateSetup->vialcontrolled === false) {
			/* @var $robotService RobotService */
			$robotService = $this->getServiceLocator()->get('RobotService');
			$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VIALCONTROL => 1));
			$success = true;
		}
		
		return new JsonModel(array('success' => $success));
	}
	
	public function	agetcheckvialstatusAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$automateSetup = new Container('automate_setup');
	
		$success = false;
		$status = 'unknow';
		$actuals = null;
		$controls = null;
		$mustChoose = null;
		
		if ($automateSetup->vialcontrolled === false) {
			$vialControlResult = $robotService->receive(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VIALCONTROLRESULT);
			
			if ($vialControlResult == 0) {
				$status = 'inprogress';
				$success = true;
			} else if ($vialControlResult == 1) {
				$status = 'error';
				$success = false;
			} else if ($vialControlResult == 2) {
				$inputDrug = $this->getInputDrugTable()->getInputDrug($automateSetup->inputdrugid);
				
				$actualActVol = (float) $robotService->receive(RobotConstants::MEDICAMENT_ACTUAL_ACTVOL);
				$actualActDt = (float) $robotService->receive(RobotConstants::MEDICAMENT_ACTUAL_ACTDT);
				$actualVol = (float) $inputDrug->vialvol;
				
				$actuals = array('actvol' => $actualActVol, 'actdt' => $actualActDt, 'vol' => (float) $actualVol);

				$controlActVol = (float) $robotService->receive(RobotConstants::MEDICAMENT_CONTROL_ACTVOL);
				$controlActDt = (float) $robotService->receive(RobotConstants::MEDICAMENT_CONTROL_ACTDT);
				$controlVol = (float) $robotService->receive(RobotConstants::MEDICAMENT_CONTROL_VOLUME);
				
				$inputDrug->controlled_activity = $controlActDt;
				$inputDrug->controlled_volume 	= $controlVol;
				$inputDrug->controlled_actvol 	= $controlActVol;
				
				$this->getInputDrugTable()->saveInputDrug($inputDrug);
				
				$controls = array('actvol' => $controlActVol, 'actdt' => $controlActDt, 'vol' => (float) $controlVol);
				
				//TODO Voir avec Michel et Matthieu les seuils
				$diffActVol = (1 - (min($actualActVol, $controlActVol) / max($actualActVol, $controlActVol))) * 100;
				$diffActDt = (1 - (min($actualActDt, $controlActDt) / max($actualActDt, $controlActDt))) * 100;
				$diffVol = (1 - (min($actualVol, $controlVol) / max($actualVol, $controlVol))) * 100;
				if ($diffActDt > 10 || $diffActDt > 10 || $diffVol > 10) {
					$mustChoose = true;
				} else {
					$mustChoose = false;
					$automateSetup->vialcontrolled = true;
					$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VIALCONTROLSELECT => 1));
				}
				
				$status = 'done';
				$success = true;
			}
			
		}
	
		return new JsonModel(array('success' => $success, 'status' => $status, 'actuals' => $actuals, 'controls' => $controls, 'mustchoose' => $mustChoose));
	}
	
	
	public function	arejectoracceptvialresultAction() 
	{
		$automateSetup = new Container('automate_setup');
	
		if ($automateSetup->vialcontrolled === false) {
			/* @var $robotService RobotService */
			$robotService = $this->getServiceLocator()->get('RobotService');
			$inputDrug = $this->getInputDrugTable()->getInputDrug($automateSetup->inputdrugid);
			$accept = (int) $this->getRequest()->getPost('accept', 0);
			$robotService->send(array(RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VIALCONTROLSELECT => $accept));
			$automateSetup->vialcontrolled = true;
		}
	
		return new JsonModel(array('success' => true));
	}
	
	public function agetcheckresultsAction() {
		$automateSetup = new Container('automate_setup');
		
		if ($automateSetup->vialcontrolled === true) {
			return new JsonModel(array('success' => true));
		}
		return new JsonModel(array('success' => false));
	}
	
	public function avioldilutionprogessAction()
	{
		//TODO Temp code
		sleep(5);
		$automateSetup = new Container('automate_setup');
		$automateSetup->vialdilutabled = true;
		return new JsonModel(array('success' => true));
	}
}
