<?php
namespace Manager\Model;

class System
{
	public	$id;
	public	$language;
	public	$unit;
	public	$genuinekit;
	public	$maxactivity;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? (int) $data['id'] : null;
		$this->language = (!empty($data['language'])) ? $data['language'] : null;
		$this->unit = (!empty($data['unit'])) ? $data['unit'] : null;
		$this->genuinekit = true;
		$this->maxactivity = (!empty($data['maxactivity'])) ? (int) $data['maxactivity'] : 0;
	}
}