<?php
namespace Manager\Model;

class System
{
	public	$id;
	public	$language;
	public	$mode;
	public	$unit;
	public	$genuinekit;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->language = (!empty($data['language'])) ? $data['language'] : null;
		$this->mode = (!empty($data['mode'])) ? $data['mode'] : null;
		$this->unit = (!empty($data['unit'])) ? $data['unit'] : null;
		$this->genuinekit = (!empty($data['genuinekit'])) ? $data['genuinekit'] : null;
	}
}