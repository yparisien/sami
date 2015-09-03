<?php
namespace Manager\Model;

class Radionuclide
{
	public	$id;
	public	$code;
	public	$name;
	public	$period; // min
	public	$coefficient; // %

	public function	exchangeArray($data)
	{
		$this->id			= (!empty($data['id'])) ? $data['id'] : null;
		$this->code			= (!empty($data['code'])) ? $data['code'] : null;
		$this->name			= (!empty($data['name'])) ? $data['name'] : null;
		$this->period		= (!empty($data['period'])) ? $data['period'] : null;
		$this->coefficient	= (!empty($data['coefficient'])) ? $data['coefficient'] : null;
	}
	
	public function toArray() {
		$data = [];
		
		$data['id'] = $this->id;
		$data['code'] = $this->code;
		$data['name'] = $this->name;
		$data['period'] = $this->period;
		$data['coefficient'] = $this->coefficient;
		
		return $data;
	}
}