<?php
namespace Manager\Robot;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

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
		}
		
		return $mRet;
	}
}