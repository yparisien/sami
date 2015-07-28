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
		$cfg = $this->sm->get('Config');
		$simulated = isset($cfg['robot']['simulated']) ? $cfg['robot']['simulated'] : false;
		
		if (!$simulated)
		{
			foreach ($toWrite as $k => $v)
			{
	       	         $postdata = http_build_query(array ("redirect" => "response.asp",
								"variable" => $k,
								"value" => $v,
								"write" => "Write"));
	       	         $opts = array('http' =>
	       	             array(
	       	                 'method'  => 'POST',
	       	                 'header'  => 'Content-type: application/x-www-form-urlencoded',
	       	                 'content' => $postdata
	       	             )
	       	         );
	       	         $context  = stream_context_create($opts);
	       	         
			 file_get_contents("http://10.0.0.100/goform/ReadWrite", false, $context);
			}
		}
		return true;
	}
	
	public function receive($variable) {
		$cfg = $this->sm->get('Config');
		$simulated = isset($cfg['robot']['simulated']) ? $cfg['robot']['simulated'] : false;
		
		if ($simulated) {
			return $this->fakeReceive($variable);
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
	                        'content' => $postdata
				)
			);
	        $context  = stream_context_create($opts);
	                
			return file_get_contents("http://10.0.0.100/goform/ReadWrite", false, $context);
		}
	}
	
	private function fakeReceive($variable) {
		$mRet = null;
		
		
		switch ($variable) {
			case "G_Medicament.Calculation.C_Act_Dispo":
			case "G_Medicament.Actual.Act_Vol":
			case "G_Medicament.Actual.Act_DT":
				$mRet = mt_rand(400, 500);
				break;
			case "G_Patient.Actual.ActToInj":
				$mRet = 300;
				break;
			case "G_MainLogic.cmd.Input_Trasys.Measured_Value":
				$mRet = 100;
				break;
			case "G_MainLogic.cmd.Input_Soft.Inject_Speed":
				$mRet = 50;
				break;
			case "SubPurge.status.Evolution":
				$fr = new Container('fake_robot');
				$progress = (isset($fr->purgeEvolutionStatus) && ($fr->purgeEvolutionStatus == 100)) ? 0 : $fr->purgeEvolutionStatus + 25 ;
				$fr->purgeEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case "G_MainLogic.status.Sampling_Evolution":
				$fr = new Container('fake_robot');
				$progress = (isset($fr->samplingEvolutionStatus) && ($fr->samplingEvolutionStatus == 100)) ? 0 : $fr->samplingEvolutionStatus + 10 ;
				$fr->samplingEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case "G_MainLogic.status.Dilution_Evolution":
				$fr = new Container('fake_robot');
				$progress = (isset($fr->dilutionEvolutionStatus) && ($fr->dilutionEvolutionStatus == 100)) ? 0 : $fr->dilutionEvolutionStatus + 20 ;
				$fr->dilutionEvolutionStatus = $progress;
				$mRet = $progress;
				break;
			case "G_MainLogic.status.Injection_Evolution":
				$fr = new Container('fake_robot');
				$progress = (isset($fr->injectionEvolutionStatus) && ($fr->injectionEvolutionStatus == 100)) ? 0 : $fr->injectionEvolutionStatus + 2 ;
				$fr->injectionEvolutionStatus = $progress;
				$mRet = $progress;
				break;
		}
		
		return $mRet;
	}
}