<?php
namespace Manager\Model;

class InjectionProfile
{
	public	$id;
	public	$code;
	public	$label;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->code = (!empty($data['code'])) ? $data['code'] : null;
		$this->label = (!empty($data['label'])) ? $data['label'] : null;
	}
	
	public function toArray() {
		$data = [];
		
		$data['id'] = $this->id;
		$data['code'] = $this->code;
		$data['label'] = $this->label;
		
		return $data;
	}
}