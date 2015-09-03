<?php
namespace Operator\Model;

class Sourcekit
{
	public	$id;
	public	$serialnumber;
	public	$usedate;
	public	$operatorid;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->serialnumber = (!empty($data['serialnumber'])) ? $data['serialnumber'] : null;
		$this->usedate = (!empty($data['usedate'])) ? $data['usedate'] : null;
		$this->operatorid = (!empty($data['operatorid'])) ? $data['operatorid'] : null;
	}
	
	public function toArray() {
		$data = [];
		
		$data['id'] = $this->id;
		$data['serialnumber'] = $this->serialnumber;
		$data['usedate'] = $this->usedate;
		$data['operatorid'] = $this->operatorid;
		
		return $data;
	}
}