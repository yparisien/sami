<?php
namespace Bufferspace\View;

/**
 * Classe modèle de la vue view_toinject
 * 
 * @author yohann.parisien
 *
 */
class ToInject
{
	public	$patientId;
	public	$patientLastname;
	public	$patientFirstname;
	public	$patientGender;
	public  $patientWeight;
	public	$injectionActivity;
	public  $injectionDci;

	public function	exchangeArray($data)
	{
		$this->patientId			= (!empty($data['patientid']))			? $data['patientid']			: null;
		$this->patientLastname		= (!empty($data['patientlastname']))	? $data['patientlastname']		: null;
		$this->patientFirstname		= (!empty($data['patientfirstname']))	? $data['patientfirstname'] 	: null;
		$this->patientGender		= (!empty($data['patientgender']))		? $data['patientgender']		: null;
		$this->patientWeight		= (!empty($data['patientweight']))		? (int) $data['patientweight']	: null;
		$this->injectionActivity	= (!empty($data['injectionactivity']))	? $data['injectionactivity']	: null;
		$this->injectionDci			= (!empty($data['injectiondci'])) 		? $data['injectiondci'] 		: null;
	}

	public function	toArray()
	{
		$data						= array();
		
		$data['patientid']			= $this->patientId;
		$data['patientlastname']	= $this->patientLastname;
		$data['patientfirstname']	= $this->patientFirstname;
		$data['patientgender']		= $this->patientGender;
		$data['patientweight']		= $this->patientWeight;
		$data['injectionactivity']	= $this->injectionActivity;
		$data['injectiondci']		= $this->injectionDci;
		
		return $data;
	}
}