<?php
namespace Logger\Model;

class InputDrug
{
	public	$id;
	public	$inputdate;
	public	$userid;
	public	$drugid;
	public	$batchnum;
	public	$calibrationtime;
	public	$expirationtime;
	public	$vialvol;
	public	$activity;
	public	$activityconc;
	public	$activitycalib;


	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->inputdate = (!empty($data['inputdate'])) ? $data['inputdate'] : null;
		$this->userid = (!empty($data['userid'])) ? $data['userid'] : null;
		$this->drugid = (!empty($data['drugid'])) ? $data['drugid'] : null;
		$this->batchnum = (!empty($data['batchnum'])) ? $data['batchnum'] : null;
		$this->calibrationtime = (!empty($data['calibrationtime'])) ? $data['calibrationtime'] : null;
		$this->expirationtime = (!empty($data['expirationtime'])) ? $data['expirationtime'] : null;
		$this->vialvol = (!empty($data['vialvol'])) ? $data['vialvol'] : null;
		$this->activity = (!empty($data['activity'])) ? $data['activity'] : null;
		$this->activityconc = (!empty($data['activityconc'])) ? $data['activityconc'] : null;
		$this->activitycalib = (!empty($data['activitycalib'])) ? $data['activitycalib'] : null;
	}
}