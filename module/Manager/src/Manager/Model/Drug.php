<?php

namespace Manager\Model;

/**
 * Classe Modèle de la table drug
 * 
 * @author yohann.parisien
 *
 */
class Drug
{
	public	$id;
	public	$name;
	public	$radionuclideid;
	public	$dci;
	public  $dilutable;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->name = (!empty($data['name'])) ? $data['name'] : null;
		$this->radionuclideid = (!empty($data['radionuclideid'])) ? $data['radionuclideid'] : null;
		$this->dci = (!empty($data['dci'])) ? $data['dci'] : null;
		$this->dilutable = (!empty($data['dilutable']) && $data['dilutable'] == 1) ? true : false;
	}
	
	public function toArray() {
		$data = [];
		
		$data['id'] = $this->id;
		$data['name'] = $this->name;
		$data['radionuclideid'] = $this->radionuclideid;
		$data['dci'] = $this->dci;
		$data['dilutable'] = $this->dilutable;
		
		return $data;
	}
}