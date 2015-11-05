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
       	         
				file_get_contents("http://10.0.0.100/goform/ReadWrite", false, $context);
			}
		}
		else {
			foreach ($toWrite as $k => $v)
			{
				$log->debug('[SIMULATED] Send to robot [' . $k . '] : ' . $v);
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
	                
			$ret = @file_get_contents("http://10.0.0.100/goform/ReadWrite", false, $context);
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
				if (isset($fr->expirationtime) && $fr->expirationtime < $fr->calibrationtime) {
					$mRet = -1;
				} else if (isset($fr->activityconc) && $fr->activityconc <= 0) {
					$mRet = -2;
				} else if (isset($fr->activitycalib) && $fr->activitycalib <= 0) {
					$mRet = -3;
				} else if (isset($fr->vialvol) && $fr->vialvol > 15) {
					$mRet = -4;
				} else {
					$mRet = mt_rand(400, 500);
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
				$oInjection = new Container('injection_profile');
				$mRet = $fr->activitytoinj;
				if (isset($fr->confirmpatient) && $fr->confirmpatient === true) {
					$maxactivity = $this->getServiceLocator()->get('Manager\Model\SystemTable')->getSystem()->maxactivity;
					$exam = null;
					if ($oInjection->examinationid > 0) {
						$exam = $this->getServiceLocator()->get('Manager\Model\ExaminationTable')->getExamination($oInjection->examinationid);
					}
					if ($mRet > $maxactivity) {
						//Activite supérieure à celle du centre
						$mRet = -2;
					} else if ($exam != null && $mRet > $exam->max) {
						//Activité supérieure à celle de l'examen
						$mRet = -4;
					} else if ($mRet == 0 || ($exam != null && $mRet < $exam->min)) {
						//Activité inférieure à celle de l'examen
						$mRet = -3;
					} else if ($mRet > $fr->lastActDispo) {
						//Activité supérieure à celle disponible
						$mRet = -1;
					}
				}
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
				$progress = (isset($fr->injectionEvolutionStatus) && ($fr->injectionEvolutionStatus == 100)) ? 0 : $fr->injectionEvolutionStatus + 10;
				$fr->injectionEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case RobotConstants::MAINLOGIC_STATUS_RINSINGEVOLUTION:
				$fr = new Container('fake_robot');
				$progress = (isset($fr->rinsingEvolutionStatus) && ($fr->rinsingEvolutionStatus == 100)) ? 0 : $fr->rinsingEvolutionStatus + 10 ;
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
				$mRet = 5;
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
					'Isotopes[1].HalfLife' 		=> '360',
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
			default:
				die($variable);
				break;
		}
		
		
		return $mRet;
	}
}