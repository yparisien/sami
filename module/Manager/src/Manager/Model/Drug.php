<?php
namespace Manager\Model;

class Drug
{
	public	$id;
	public	$name;
	public	$radionuclideid;
	public	$dci;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->name = (!empty($data['name'])) ? $data['name'] : null;
		$this->radionuclideid = (!empty($data['radionuclideid'])) ? $data['radionuclideid'] : null;
		$this->dci = (!empty($data['dci'])) ? $data['dci'] : null;
	}
}