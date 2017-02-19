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
	public	$controlled_activity;
	public	$controlled_volume;
	public	$controlled_actvol;
	public	$selected_activity;
	
	const SELECTED_ACTIVITY_CONTROLLED = 'controlled';
	const SELECTED_ACTIVITY_SETTED = 'setted';


	public function	exchangeArray($data)
	{
		$this->id 		 			= (!empty($data['id'])) 					? $data['id'] : null;
		$this->inputdate 			= (!empty($data['inputdate'])) 				? new \DateTime($data['inputdate']) : null;
		$this->userid 				= (!empty($data['userid'])) 				? $data['userid'] : null;
		$this->drugid 				= (!empty($data['drugid'])) 				? $data['drugid'] : null;
		$this->batchnum 			= (!empty($data['batchnum'])) 				? $data['batchnum'] : null;
		$this->calibrationtime 		= (!empty($data['calibrationtime']))		? new \DateTime($data['calibrationtime']) : null;
		$this->expirationtime 		= (!empty($data['expirationtime'])) 		? new \DateTime($data['expirationtime']) : null;
		$this->vialvol 				= (!empty($data['vialvol'])) 				? $data['vialvol'] : null;
		$this->activity 			= (!empty($data['activity'])) 				? $data['activity'] : null;
		$this->activityconc 		= (!empty($data['activityconc'])) 			? $data['activityconc'] : null;
		$this->activitycalib 		= (!empty($data['activitycalib']))	 		? $data['activitycalib'] : null;
		$this->controlled_activity 	= (!empty($data['controlled_activity']))	? $data['controlled_activity'] : null;
		$this->controlled_volume 	= (!empty($data['controlled_volume'])) 		? $data['controlled_volume'] : null;
		$this->controlled_actvol 	= (!empty($data['controlled_actvol']))		? $data['controlled_actvol'] : null;
		$this->selected_activity 	= (!empty($data['selected_activity']))		? $data['selected_activity'] : null;
	}
	
	public function toArray() {
		$data = [];
		
		$data['id'] 					= $this->id;
		$data['inputdate'] 				= $this->inputdate->format('Y-m-d H:i:s');
		$data['userid'] 				= $this->userid;
		$data['drugid'] 				= $this->drugid;
		$data['batchnum'] 				= $this->batchnum;
		$data['calibrationtime'] 		= $this->calibrationtime->format('Y-m-d H:i:s');
		$data['expirationtime'] 		= $this->expirationtime->format('Y-m-d H:i:s');
		$data['vialvol'] 				= $this->vialvol;
		$data['activity'] 				= $this->activity;
		$data['activityconc'] 			= $this->activityconc;
		$data['activitycalib'] 			= $this->activitycalib;
		$data['controlled_activity'] 	= $this->controlled_activity;
		$data['controlled_volume'] 		= $this->controlled_volume;
		$data['controlled_actvol'] 		= $this->controlled_actvol;
		$data['selected_activity'] 		= $this->selected_activity;
		
		return $data;
	}
}