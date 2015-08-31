<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/Operator for the canonical source repository
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace Operator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;

use Logger\Model\InputAction;
use Manager\Robot\RobotService;

class InteractrobotController extends AbstractActionController
{
	protected $inputActionTable;
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
	 * @return \Logger\Model\DrugTable
	 */
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
		return new ViewModel($aParams);
	}

	public function	sampleAction()
	{
		$aParams = array();
		$injection = new Container('injection_profile');
		$patientId = $injection->patientid;
		$aParams['patient'] = $this->getPatientTable()->getPatient($patientId)->toArray();
		$aParams['injection'] = $this->getInjectionTable()->searchByPatientId($patientId)->toArray();
		$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		return new ViewModel($aParams);
	}

	public function	dilutionAction()
	{
		$aParams = array();
		$injection = new Container('injection_profile');
		$patientId = $injection->patientid;
		$aParams['patient'] = $this->getPatientTable()->getPatient($patientId)->toArray();
		$aParams['injection'] = $this->getInjectionTable()->searchByPatientId($patientId)->toArray();
		$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		return new ViewModel($aParams);
	}

	public function	injectionAction()
	{
		$aParams = array();
		$injection = new Container('injection_profile');
		$patientId = $injection->patientid;
		$aParams['patient'] = $this->getPatientTable()->getPatient($patientId)->toArray();
		$aParams['injection'] = $this->getInjectionTable()->searchByPatientId($patientId)->toArray();
		$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		return new ViewModel($aParams);
	}

	public function	rinsingAction()
	{
		$aParams = array();
		$injection = new Container('injection_profile');
		$patientId = $injection->patientid;
		$aParams['patient'] = $this->getPatientTable()->getPatient($patientId)->toArray();
		$aParams['injection'] = $this->getInjectionTable()->searchByPatientId($patientId)->toArray();
		$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		return new ViewModel($aParams);
	}

	public function	patientdisconnectionAction()
	{
		$aParams = array();
		$injection = new Container('injection_profile');
		$setup = new Container('automate_setup');
		
		$drug = $this->getLogDrugTable()->getDrug($injection->drugid);

		$aParams['patient'] = $this->getPatientTable()->getPatient($injection->patientid)->toArray();
		$aParams['injection'] = $this->getInjectionTable()->searchByPatientId($injection->patientid)->toArray();
		$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		$aParams['examination'] = $this->getExaminationTable()->getExamination($injection->examinationid);
		$aParams['operator'] = $this->getUserTable()->getUser($injection->operatorid);
		$aParams['curdrug'] = $drug;
		$aParams['drug'] = $this->getDrugTable()->getDrug($drug->drugid);
		$aParams['patientkit'] = $this->getPatientkitTable()->getPatientkit($injection->patientkitid);
		$aParams['radionuclide'] = $this->getRadionuclideTable()->getRadionuclide($aParams['drug']->radionuclideid);
		$aParams['sourcekit'] = $this->getSourcekitTable()->getSourcekit($setup->sourcekitid);
		$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
		return new ViewModel($aParams);
	}

	public function	unloadAction()
	{
		if($this->params('confirm'))
		{
			$oContainer = new Container('automate_setup');
			$oContainer->drugspecified = false;
			$oContainer->drugid = 0;
			$oContainer->sourcekitscanned = false;
			$oContainer->sourcekitloaded = false;
			$oContainer->markedasended = false;

			$inputaction = new InputAction();
			$inputaction->inputdate = date('Y-m-d H:i:s');
			$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
			$inputaction->action = "User mark automate unloaded";
			$this->getInputActionTable()->saveInputAction($inputaction);
			return $this->redirect()->toRoute('operator');
		}
		else
		{
			return array();
		}
	}

	public function	endinjectAction()
	{
		$oContainer = new Container('automate_setup');
		if ($oContainer->sourcekitloaded) {
			$aParams = array();
			$aParams['unit'] = ($this->getSystemTable()->getSystem()->unit == 'mbq') ? 'MBq' : 'mCi';
			return new ViewModel($aParams);
		} else {
			$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
			$message = sprintf($translate("You are not authorized to perform this action : Access page '%s'"), $page);
			$this->flashmessenger()->addErrorMessage($message);
			$this->redirect()->toRoute('operator');
		}
	}

	/*
	 * Ajax's calls beyond this line
	 */
	public function	agetpurgeprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive("SubPurge.status.Evolution");
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
		return $result;
	}
	public function	alaunchpurgeAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Load_Purge' => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	public function	adisconnectpatientAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Val_Patient_Deconnection' => 1));
				
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	public function	agetsamplingprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive("G_MainLogic.status.Sampling_Evolution");
		
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
		return $result;
	}
	public function	alaunchsamplingAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Sampling_Sequence' => 1));

		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	public function	aadjustsamplingAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Adjust_Sampling' => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);

		return $result;
	}
	public function	agetsamplingactivityAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$activity = $robotService->receive('G_MainLogic.cmd.Input_Trasys.Measured_Value');
		
		$aParams = array("activity" => $activity);
		$result = new JsonModel($aParams);
		return $result;
	}
	public function	agetdilutionprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive("G_MainLogic.status.Dilution_Evolution");
		
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
		
		return $result;
	}
	public function	alaunchdilutionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Dilution_Sequence' => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	public function	agetinjectionprogressAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$progress = $robotService->receive("G_MainLogic.status.Injection_Evolution");
		
		$aParams = array("progress" => $progress);
		$result = new JsonModel($aParams);
		
		return $result;
	}
	public function	alaunchinjectionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Injection_Sequence' => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);

		return $result;
	}
	
	/* Pause ou rédémarre l'injection */
	public function	apauseinjectionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Stop_Injection' => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		return $result;
	}
	
	public function	aplayinjectionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Play_Injection' => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	aspeedinjectionAction()
	{
		/* @var $robotService RobotService */
		$speed = $this->getRequest()->getPost('speed');

		$robotService = $this->getServiceLocator()->get('RobotService');
		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Inject_Speed' => $speed));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}
	
	public function	agetspeedinjectionAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		$speed = $robotService->receive("G_MainLogic.cmd.Input_Soft.Inject_Speed");
		
		$aParams = array("speed" => $speed / 25);
		$result = new JsonModel($aParams);
		return $result;
	}

	public function	amarkpatientconnectedAction()
	{
		/* @var $robotService RobotService */
		$robotService = $this->getServiceLocator()->get('RobotService');
		
		$inputaction = new InputAction();
		$inputaction->inputdate = date('Y-m-d H:i:s');
		$inputaction->userid = $this->getUserTable()->searchByLogin($this->getServiceLocator()->get('AuthService')->getIdentity())->id;
		$inputaction->action = "User mark the patient connected to SAMI";
		$this->getInputActionTable()->saveInputAction($inputaction);

		$robotService->send(array('G_MainLogic.cmd.Input_Soft.Val_Patient_Connection' => 1));
		
		$aParams = array();
		$result = new JsonModel($aParams);
		
		return $result;
	}

	public function	aupdateactivityAction()
	{
		$newActivity = $this->getRequest()->getPost('activity');
		$inputaction = new InputAction();
		$inputaction->inputdate = date('Y-m-d H:i:s');
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
		$oInputDrug = $this->getLogDrugTable()->getDrug($injectionProfile->drugid);
		
		$oInjection = $this->getInjectionTable()->searchByPatientId($oPatient->id);
		$oInjection->injection_time = date('H:i:s'); // heure automate ou ihm?
		$oInjection->drugid = $oInputDrug->drugid;
		$oInjection->examinationid = $injectionProfile->examinationid;
		$oInjection->operatorid = $injectionProfile->operatorid;
		$this->getInjectionTable()->saveInjection($oInjection);
		
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
		$setup = new Container('automate_setup');
		$setup->markedasended = true;
		return new JsonModel(array('success'=>true));
	}
}
