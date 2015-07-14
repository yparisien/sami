<?php
namespace Bufferspace\View;

class Export
{
	public	$type;
	public	$injectiontime;
	public	$activity;
	public	$dosestatus;
	public	$uniqueid;
	public	$batchnum;
	public	$patientid;
	public	$patientname;
	public	$gender;
	public	$birthdate;
	public	$age;
	public	$weight;
	public	$height;
	public	$patienttype;
	public	$doctorname;
	public	$emplacement;

	public function	exchangeArray($data)
	{
		$this->type				= (!empty($data['type'])) ? $data['type'] : null;
		$this->injectiontime	= (!empty($data['injectiontime'])) ? $data['injectiontime'] : null;
		$this->activity			= (!empty($data['activity'])) ? $data['activity'] : null;
		$this->dosestatus		= (!empty($data['dosestatus'])) ? $data['dosestatus'] : null;
		$this->uniqueid			= (!empty($data['uniqueid'])) ? $data['uniqueid'] : null;
		$this->batchnum			= (!empty($data['batchnum'])) ? $data['batchnum'] : null;
		$this->patientid		= (!empty($data['patientid'])) ? $data['patientid'] : null;
		$this->patientname		= (!empty($data['patientname'])) ? $data['patientname'] : null;
		$this->gender			= (!empty($data['gender'])) ? $data['gender'] : null;
		$this->birthdate		= (!empty($data['birthdate'])) ? $data['birthdate'] : null;
		$this->age				= (!empty($data['age'])) ? $data['age'] : null;
		$this->weight			= (!empty($data['weight'])) ? $data['weight'] : null;
		$this->height			= (!empty($data['height'])) ? $data['height'] : null;
		$this->patienttype		= (!empty($data['patienttype'])) ? $data['patienttype'] : null;
		$this->doctorname		= (!empty($data['doctorname'])) ? $data['doctorname'] : null;
		$this->emplacement		= (!empty($data['emplacement'])) ? $data['emplacement'] : null;
	}
}