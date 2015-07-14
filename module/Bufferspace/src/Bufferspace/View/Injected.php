<?php
namespace Bufferspace\View;

class Injected
{
	public	$patientlastname;
	public	$patientfirstname;
	public	$injectiontime;
	public	$activity;
	public	$operatorlastname;
	public	$operatorfirstname;

	public function	exchangeArray($data)
	{
		$this->patientlastname		= (!empty($data['patientlastname'])) ? $data['patientlastname'] : null;
		$this->patientfirstname		= (!empty($data['patientfirstname'])) ? $data['patientfirstname'] : null;
		$this->injectiontime		= (!empty($data['injectiontime'])) ? $data['injectiontime'] : null;
		$this->activity				= (!empty($data['activity'])) ? $data['activity'] : null;
		$this->operatorlastname		= (!empty($data['operatorlastname'])) ? $data['operatorlastname'] : null;
		$this->operatorfirstname	= (!empty($data['operatorfirstname'])) ? $data['operatorfirstname'] : null;
	}

	public function	toArray()
	{
		$data						= array();
		$data['patientlastname']	= $this->patientlastname;
		$data['patientfirstname']	= $this->patientfirstname;
		$data['injectiontime']		= $this->injectiontime;
		$data['activity']			= $this->activity;
		$data['operatorlastname']	= $this->operatorlastname;
		$data['operatorfirstname']	= $this->operatorfirstname;
		return $data;
	}
}