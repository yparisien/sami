<?php
namespace Manager\Robot;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Session\Container;

class RobotService implements ServiceLocatorAwareInterface {
	
	protected $sm;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->sm = $serviceLocator;
	}
	
	public function getServiceLocator() {
		return $this->sm;
	}
	
	public function send($toWrite) {
		/* @var $log \Zend\Log\Logger */
		
		$log = $this->sm->get('Log\App');
		$cfg = $this->sm->get('Config');
		
		$simulated = isset($cfg['robot']['simulated']) ? $cfg['robot']['simulated'] : false;
		
		if (!$simulated)
		{
			foreach ($toWrite as $k => $v)
			{
				$postdata = http_build_query(array(
						"redirect" => "response.asp",
						"variable" => $k,
						"value" => $v,
						"write" => "Write"
				));
				
				$opts = array('http' => array(
						'method'  => 'POST',
       	                'header'  => 'Content-type: application/x-www-form-urlencoded',
       	                'content' => $postdata
       	             )
       	        );
				
       	        $context  = stream_context_create($opts);
       	        $log->info('Send to robot [' . $k . '] : ' . $v);
       	         
				file_get_contents($cfg['robot']['webservice']['read_write'], false, $context);
			}
		}
		else {
			foreach ($toWrite as $k => $v)
			{
				if ($k == RobotConstants::MAINLOGIC_CMD_INPUTSOFT_RINSINGSEQUENCE) {
					$fr = new Container('fake_robot');
					$fr->rinsingEvolutionStatus = 0;
				}
				if ($k == RobotConstants::MAINLOGIC_CMD_INPUTSOFT_INJECTIONSEQUENCE) {
					$fr = new Container('fake_robot');
					$fr->injectionEvolutionStatus = 0;
				}
				$log->debug('[SIMULATED] Send to robot [' . $k . '] : ' . $v);
				usleep(500);
			}
		}
		
		return true;
	}
	
	public function receive($variable) {
		/* @var $log \Zend\Log\Logger */
		
		$log = $this->sm->get('Log\App');
		$cfg = $this->sm->get('Config');
		$simulated = isset($cfg['robot']['simulated']) ? $cfg['robot']['simulated'] : false;
		
		if ($simulated) {
			$retsim = $this->fakeReceive($variable);
			$log->debug('[SIMULATED] Get from robot [' . $variable . '] : ' . $retsim);
			usleep(500);
			return $retsim;
		}
		else {
			$aData = array(
				"redirect" => "response.asp",
				"variable" => $variable,
				"value" => "",
				"read" => "Read",
			);
	
			$postdata = http_build_query($aData);
	        $opts = array(
	        	'http' => array(
	        		'method'  => 'POST',
	                'header'  => 'Content-type: application/x-www-form-urlencoded',
	                'content' => $postdata,
	        		'timeout' => 5,
				)
			);
	        $context  = stream_context_create($opts);
	                
			$ret = @file_get_contents($cfg['robot']['webservice']['read_write'], false, $context);
			$log->info('Get from robot [' . $variable . '] : ' . $ret);
			
			return $ret;
		}
	}
	
	private function fakeReceive($variable) {
		$mRet = null;
		$cfg = $this->sm->get('Config');
		switch ($variable) {
			case RobotConstants::MEDICAMENT_CALCULATION_CACTDISPO:
				$fr = new Container('fake_robot');
				if (isset($fr->expirationtime) && isset($fr->calibrationtime) && $fr->expirationtime->getTimestamp() < $fr->calibrationtime->getTimestamp()) {
					$mRet = -1;
				} else if (isset($fr->activityconc) && $fr->activityconc <= 0) {
					$mRet = -2;
				} else if (isset($fr->activitycalib) && $fr->activitycalib <= 0) {
					$mRet = -3;
				} else if (isset($fr->vialvol) && $fr->vialvol < $cfg['robot']['vialvol']['min'] && $fr->vialvol > $cfg['robot']['vialvol']['max']) {
					$mRet = -4;
				} else {
					$mRet = mt_rand(4000, 5000);
					$fr->lastActDispo = $mRet;
				}
				break;
			case RobotConstants::MEDICAMENT_CALCULATION_CACTPREV:
				$mRet = mt_rand(400, 500);
				break;
			case RobotConstants::MEDICAMENT_ACTUAL_ACTVOL:
				$fr = new Container('fake_robot');
				$mRet = $fr->activitycalib / $fr->vialvol;
				break;
			case RobotConstants::MEDICAMENT_ACTUAL_ACTDT:
				$fr = new Container('fake_robot');
				$mRet = $fr->activityconc * $fr->vialvol;
				break;
			case RobotConstants::PATIENT_ACTUAL_ACTTOINJ:
				$fr = new Container('fake_robot');
				$mRet = $fr->activitytoinj;
				break;
			case RobotConstants::MEDICAMENT_CONTROL_ACTVOL:
				$fr = new Container('fake_robot');
				$mRet = ($fr->activitycalib / $fr->vialvol) * 0.95;
				break;
			case RobotConstants::MEDICAMENT_CONTROL_ACTDT:
				$fr = new Container('fake_robot');
				$mRet = ($fr->activityconc * $fr->vialvol) * 0.85;
				break;
			case RobotConstants::MEDICAMENT_CONTROL_VOLUME:
				$fr = new Container('fake_robot');
				$mRet = 9.63;
				break;
			case RobotConstants::PATIENT_ACTUAL_VALIDATION:
				$fr = new Container('fake_robot');
				$oInjection = new Container('injection_profile');
// 				if (isset($fr->confirmpatient) && $fr->confirmpatient === true) {
					$maxactivity = $this->getServiceLocator()->get('Manager\Model\SystemTable')->getSystem()->maxactivity;
					$exam = null;
					$mRet = 1;
					if ($oInjection->examinationid > 0) {
						$exam = $this->getServiceLocator()->get('Manager\Model\ExaminationTable')->getExamination($oInjection->examinationid);
					}
					if ($fr->activitytoinj > $fr->lastActDispo) {
						//Activité supérieure à celle disponible
						$mRet = -1;
					}
					else if ($fr->activitytoinj > $maxactivity) {
						//Activite supérieure à celle du centre
						$mRet = -2;
					} else if ($exam != null && $fr->activitytoinj > $exam->max) {
						//Activité supérieure à celle de l'examen
						$mRet = -4;
					} else if ($fr->activitytoinj == 0 || ($exam != null && $fr->activitytoinj < $exam->min)) {
						//Activité inférieure à celle de l'examen
						$mRet = -3;
					}
// 				}
				break;
			case RobotConstants::MAINLOGIC_CMD_INPUTTRASYS_MEASUREDVALUE:
				$mRet = 100;
				break;
			case RobotConstants::MAINLOGIC_CMD_INPUTSOFT_INJECTSPEED:
				$mRet = 50;
				break;
			case RobotConstants::SUBPURGE_STATUS_EVOLUTION:
				$fr = new Container('fake_robot');
				$progress = (isset($fr->purgeEvolutionStatus) && ($fr->purgeEvolutionStatus == 100)) ? 0 : $fr->purgeEvolutionStatus + 25;
				$fr->purgeEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case RobotConstants::MAINLOGIC_STATUS_SAMPLINGEVOLUTION:
				$fr = new Container('fake_robot');
				$progress = (isset($fr->samplingEvolutionStatus) && ($fr->samplingEvolutionStatus == 100)) ? 0 : $fr->samplingEvolutionStatus + 20;
				$fr->samplingEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case RobotConstants::MAINLOGIC_STATUS_DILUTIONEVOLUTION:
				$fr = new Container('fake_robot');
				$progress = (isset($fr->dilutionEvolutionStatus) && ($fr->dilutionEvolutionStatus == 100)) ? 0 : $fr->dilutionEvolutionStatus + 20;
				$fr->dilutionEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case RobotConstants::MAINLOGIC_STATUS_INJECTIONEVOLUTION:
				$fr = new Container('fake_robot');
				$progress = (isset($fr->injectionEvolutionStatus) && ($fr->injectionEvolutionStatus >= 100)) ? 100 : $fr->injectionEvolutionStatus + 10;
				$fr->injectionEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case RobotConstants::MAINLOGIC_STATUS_RINSINGEVOLUTION:
				$fr = new Container('fake_robot');
				$progress = (isset($fr->rinsingEvolutionStatus) && ($fr->rinsingEvolutionStatus >= 100)) ? 100 : $fr->rinsingEvolutionStatus + 10 ;
				$fr->rinsingEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case RobotConstants::MAINLOGIC_PAR_SERIALNUMBERSAMI:
				$mRet = "DI-SAMI-V.0.1";
				break;
			case RobotConstants::MAINLOGIC_PAR_SERIALNUMBERACTIVI:
				$mRet = "SN-A12345-123";
				break;
			case RobotConstants::MAINLOGIC_PAR_SOFTWAREVERSION:
				$mRet = "Version 0.1a";
				break;
			case RobotConstants::MAINLOGIC_PAR_SYSTEMVERSION:
				$mRet = "Version 0.1b";
				break;
			case RobotConstants::MAINLOGIC_STATUS_RESTARTTYPE:
				$mRet = $cfg['robot']['simulation']['init']['restarttype'];
				break;
			case RobotConstants::ISOTOPES_NB:
				$mRet = 6;
				break;
			case stripos($variable, 'Isotopes['):
				$rnitems = [
					'Isotopes[0].ID_Isotope' 	=> '1',
					'Isotopes[0].ShortName' 	=> 'C11',
					'Isotopes[0].Name' 			=> 'Carbon 11',
					'Isotopes[0].HalfLife' 		=> '1222.8',
					'Isotopes[1].ID_Isotope' 	=> '2',
					'Isotopes[1].ShortName' 	=> 'T99',
					'Isotopes[1].Name' 			=> 'Technetium 99',
					'Isotopes[1].HalfLife' 		=> '21600',
					'Isotopes[2].ID_Isotope' 	=> '3',
					'Isotopes[2].ShortName' 	=> 'F18',
					'Isotopes[2].Name' 			=> 'Fluorine 18',
					'Isotopes[2].HalfLife' 		=> '109',
					'Isotopes[3].ID_Isotope' 	=> '4',
					'Isotopes[3].ShortName' 	=> 'TH201',
					'Isotopes[3].Name' 			=> 'Thallium 201',
					'Isotopes[3].HalfLife' 		=> '691200',
					'Isotopes[4].ID_Isotope' 	=> '5',
					'Isotopes[4].ShortName' 	=> 'I131',
					'Isotopes[4].Name' 			=> 'Iodine 131',
					'Isotopes[4].HalfLife' 		=> '692928',
					'Isotopes[5].ID_Isotope' 	=> '5',
					'Isotopes[5].ShortName' 	=> 'GE68',
					'Isotopes[5].Name' 			=> 'Germanium 68',
					'Isotopes[5].HalfLife' 		=> '2.339885e+07',
				];
				$mRet = $rnitems[$variable];
				break;
			case RobotConstants::MAINLOGIC_STATUS_ACTIVE:
				sleep($cfg['timeout']['robot']);
				$fr = new Container('fake_robot');
				$mRet = false;
				if ($fr->tryconnect == $cfg['robot']['simulation']['init']['trytogood']) {
					$mRet = true;
					$fr->tryconnect = 0;
				}
				$try = (isset($fr->tryconnect) && ($fr->tryconnect == 5)) ? 0 : $fr->tryconnect + 1 ;
				$fr->tryconnect = $try;
				break;
			case RobotConstants::MAINLOGIC_PAR_MEASUREUNIT:
				$mRet = $cfg['robot']['simulation']['init']['unit'];
				break;
			case RobotConstants::MAINLOGIC_STATUS_HASMEDICAMENTLOADED:
				$mRet = $cfg['robot']['simulation']['init']['hasmed'];
				break;
			case RobotConstants::MAINLOGIC_STATUS_GETMEDICAMENTLOADED:
				$mRet = $cfg['robot']['simulation']['init']['loadedmed'];
				break;
			case RobotConstants::MAINLOGIC_STATUS_HASKITSOURCESCANNED:
				$mRet = $cfg['robot']['simulation']['init']['sourcekitscanned'];
				break;
			case RobotConstants::MAINLOGIC_STATUS_GETSERIALKITSOURCE:
				$mRet = $cfg['robot']['simulation']['init']['sourcekitserial'];
				break;
			case RobotConstants::MAINLOGIC_STATUS_HASKITSOURCELOADED:
				$mRet = $cfg['robot']['simulation']['init']['loadedsourcekit'];
				$fr = new Container('fake_robot');
 				if ($fr->offsetExists('haskitsourceloaded') && $fr->haskitsourceloaded === true) {
 					$mRet = '1';
 				}
				break;
			case RobotConstants::MAINLOGIC_STATUS_ERROR:
				$mRet = $cfg['robot']['simulation']['init']['robotinerror'];
				break;
			case RobotConstants::MAINLOGIC_STATUS_ERRORID:
				$mRet = $cfg['robot']['simulation']['init']['roboterrorcode'];
				break;
			case RobotConstants::PATIENT_ACTUAL_PATIENTID:
				$mRet = $cfg['robot']['simulation']['init']['patientid'];
				break;
			case RobotConstants::MAINLOGIC_CMD_INPUTSOFT_VIALCONTROLRESULT:
				$fr = new Container('fake_robot');
				$as = new Container('automate_setup');
				if ($fr->vialcontroltry == 10 || $as->vialcontrolled === true) {
					$fr->vialcontroltry = 0;
					$mRet = $cfg['robot']['simulation']['process']['vial_ctrl_result'];
				} else {
					$fr->vialcontroltry += 1;
					$mRet = 0;
				}
				break;
			case RobotConstants::MAINLOGIC_STATUS_ISVIALCONTROLLABLE:
				$mRet = $cfg['robot']['simulation']['process']['is_vial_controllable'];
				break;
			case RobotConstants::MAINLOGIC_STATUS_HASDRUGLOADED:
				$mRet = $cfg['robot']['simulation']['process']['has_drug_loaded'];
				break;
			default:
				die($variable);
				break;
		}
		
		
		return $mRet;
	}
}