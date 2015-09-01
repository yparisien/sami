<?php
namespace Manager\Model;

class Examination
{
	public	$id;
	public	$name;
	public	$drugid;
	public	$rate; // mbq/kg
	public	$min;
	public	$max;

	public function	exchangeArray($data)
	{
		$this->id		= (!empty($data['id'])) ? $data['id'] : null;
		$this->name		= (!empty($data['name'])) ? $data['name'] : null;
		$this->drugid	= (!empty($data['drugid'])) ? $data['drugid'] : null;
		$this->rate		= (!empty($data['rate'])) ? $data['rate'] : null;
		$this->min		= (!empty($data['min'])) ? $data['min'] : null;
		$this->max		= (!empty($data['max'])) ? $data['max'] : null;
	}

	public function	toArray()
	{
		$data			= array();
		
		$data['id']		= $this->id;
		$data['name']	= $this->name;
		$data['drugid']	= $this->drugid;
		$data['rate']	= $this->rate;
		$data['min']	= $this->min;
		$data['max']	= $this->max;
		
		return $data;
	}
}