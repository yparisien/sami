<?php
namespace Bufferspace\Model;

/**
 * Classe modèle de la table tmp_injection
 * 
 * @author yohann.parisien
 *
 */
class Injection
{
	public	$id;
	public	$patient_id;
	public	$type;
	public  $injection_date;
	public	$injection_time;
	public	$activity;
	public	$dose_status;
	public	$unique_id;
	public	$vial_id;
	public	$location;
	public	$comments;
	public	$dci;
	
	/**
	 * Champs rempli durant l'examen
	 */
	public	$drugid;
	public	$inputdrugid;
	public	$examinationid;
	public	$operatorid;
	public	$patientkitid;

	public function	exchangeArray($data)
	{
		$this->id				= (!empty($data['id'])) ? (int) $data['id'] : null;
		$this->patient_id		= (!empty($data['patient_id'])) ? $data['patient_id'] : null;
		$this->type				= (!empty($data['type'])) ? $data['type'] : null;
		$this->injection_date	= (!empty($data['injection_date'])) ? $data['injection_date'] : null;
		$this->injection_time	= (!empty($data['injection_time'])) ? $data['injection_time'] : null;
		$this->activity			= (!empty($data['activity'])) ? (int) $data['activity'] : null;
		$this->dose_status		= (!empty($data['dose_status'])) ? $data['dose_status'] : null;
		$this->unique_id		= (!empty($data['unique_id'])) ? $data['unique_id'] : null;
		$this->vial_id			= (!empty($data['vial_id'])) ? $data['vial_id'] : null;
		$this->location			= (!empty($data['location'])) ? $data['location'] : null;
		$this->comments			= (!empty($data['comments'])) ? $data['comments'] : null;
		$this->dci				= (!empty($data['dci'])) ? $data['dci'] : null;
		$this->drugid			= (!empty($data['drugid'])) ? (int) $data['drugid'] : null;
		$this->inputdrugid		= (!empty($data['inputdrugid'])) ? (int) $data['inputdrugid'] : null;
		$this->examinationid	= (!empty($data['examinationid'])) ? (int) $data['examinationid'] : null;
		$this->operatorid		= (!empty($data['operatorid'])) ? (int) $data['operatorid'] : null;
		$this->patientkitid		= (!empty($data['patientkitid'])) ? (int) $data['patientkitid'] : null;
	}

	public function	toArray()
	{
		$data					= array();
		
		$data['id']				= $this->id;
		$data['patient_id']		= $this->patient_id;
		$data['type']			= $this->type;
		$data['injection_date']	= $this->injection_date;
		$data['injection_time']	= $this->injection_time;
		$data['activity']		= $this->activity;
		$data['dose_status']	= $this->dose_status;
		$data['unique_id']		= $this->unique_id;
		$data['vial_id']		= $this->vial_id;
		$data['location']		= $this->location;
		$data['comments']		= $this->comments;
		$data['dci']			= $this->dci;
		$data['drugid']			= $this->drugid;
		$data['inputdrugid']	= $this->inputdrugid;
		$data['examinationid']	= $this->examinationid;
		$data['operatorid']		= $this->operatorid;
		$data['patientkitid']	= $this->patientkitid;
		
		return $data;
	}
}