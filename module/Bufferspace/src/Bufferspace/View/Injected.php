<?php
namespace Bufferspace\View;

class Injected
{
	public	$patientid;
	public	$patientlastname;
	public	$patientfirstname;
	public	$injectionid;
	public	$injectiondate;
	public	$injectiontime;
	public	$activity;
	public	$operatorlastname;
	public	$operatorfirstname;

	public function	exchangeArray($data)
	{
		$this->patientid			= (!empty($data['patientid']))			? $data['patientid']		: null;
		$this->patientlastname		= (!empty($data['patientlastname']))	? $data['patientlastname']	: null;
		$this->patientfirstname		= (!empty($data['patientfirstname']))	? $data['patientfirstname']	: null;
		$this->injectionid			= (!empty($data['injectionid']))		? $data['injectionid']		: null;
		$this->injectiondate		= (!empty($data['injectiontdate']))		? $data['injectiondate']	: null;
		$this->injectiontime		= (!empty($data['injectiontime']))		? $data['injectiontime']	: null;
		$this->activity				= (!empty($data['activity']))			? $data['activity']			: null;
		$this->operatorlastname		= (!empty($data['operatorlastname']))	? $data['operatorlastname']	: null;
		$this->operatorfirstname	= (!empty($data['operatorfirstname']))	? $data['operatorfirstname']: null;
	}

	public function	toArray()
	{
		$data						= array();
		$data['patientid']			= $this->patientid;
		$data['patientlastname']	= $this->patientlastname;
		$data['patientfirstname']	= $this->patientfirstname;
		$data['injectionid']		= $this->injectionid;
		$data['injectiondate']		= $this->injectiondate;
		$data['injectiontime']		= $this->injectiontime;
		$data['activity']			= $this->activity;
		$data['operatorlastname']	= $this->operatorlastname;
		$data['operatorfirstname']	= $this->operatorfirstname;
		return $data;
	}
}