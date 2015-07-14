<?php
namespace Manager\Model;

class Radionuclide
{
	public	$id;
	public	$name;
	public	$period; // min
	public	$coefficient; // %

	public function	exchangeArray($data)
	{
		$this->id			= (!empty($data['id'])) ? $data['id'] : null;
		$this->name			= (!empty($data['name'])) ? $data['name'] : null;
		$this->period		= (!empty($data['period'])) ? $data['period'] : null;
		$this->coefficient	= (!empty($data['coefficient'])) ? $data['coefficient'] : null;
	}
}