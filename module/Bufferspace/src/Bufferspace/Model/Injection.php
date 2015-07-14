<?php
namespace Bufferspace\Model;

class Injection
{
	public	$id;
	public	$patient_id;
	public	$type;
	public	$injection_time;
	public	$activity;
	public	$dose_status;
	public	$unique_id;
	public	$vial_id;
	public	$location;
	public	$comments;
	public	$drugid;
	public	$examinationid;
	public	$operatorid;

	public function	exchangeArray($data)
	{
		$this->id				= (!empty($data['id'])) ? $data['id'] : null;
		$this->patient_id		= (!empty($data['patient_id'])) ? $data['patient_id'] : null;
		$this->type				= (!empty($data['type'])) ? $data['type'] : null;
		$this->injection_time	= (!empty($data['injection_time'])) ? $data['injection_time'] : null;
		$this->activity			= (!empty($data['activity'])) ? $data['activity'] : null;
		$this->dose_status		= (!empty($data['dose_status'])) ? $data['dose_status'] : null;
		$this->unique_id		= (!empty($data['unique_id'])) ? $data['unique_id'] : null;
		$this->vial_id			= (!empty($data['vial_id'])) ? $data['vial_id'] : null;
		$this->location			= (!empty($data['location'])) ? $data['location'] : null;
		$this->comments			= (!empty($data['comments'])) ? $data['comments'] : null;
		$this->drugid			= (!empty($data['drugid'])) ? $data['drugid'] : null;
		$this->examinationid	= (!empty($data['examinationid'])) ? $data['examinationid'] : null;
		$this->operatorid		= (!empty($data['operatorid'])) ? $data['operatorid'] : null;
	}

	public function	toArray()
	{
		$data					= array();
		$data['id']				= $this->id;
		$data['patient_id']		= $this->patient_id;
		$data['type']			= $this->type;
		$data['injection_time']	= $this->injection_time;
		$data['activity']		= $this->activity;
		$data['dose_status']	= $this->dose_status;
		$data['unique_id']		= $this->unique_id;
		$data['vial_id']		= $this->vial_id;
		$data['location']		= $this->location;
		$data['comments']		= $this->comments;
		$data['drugid']			= $this->drugid;
		$data['examinationid']	= $this->examinationid;
		$data['operatorid']		= $this->operatorid;
		return $data;
	}
}