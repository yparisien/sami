<?php
namespace Logger\Model;

class InputFile
{
	public	$id;
	public	$name;
	public	$in;
	public	$out;
	public	$creation_date;
	public	$export_date;
	public  $deleted;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->name = (!empty($data['name'])) ? $data['name'] : null;
		$this->in = (!empty($data['in'])) ? $data['in'] : null;
		$this->out = (!empty($data['out'])) ? $data['out'] : null;
		$this->creation_date = (!empty($data['creation_date'])) ? $data['creation_date'] : null;
		$this->export_date = (!empty($data['export_date'])) ? $data['export_date'] : null;
		$this->deleted = (!empty($data['deleted'])) ? $data['deleted'] : null;
	}
	
	public function toArray() {
		$data = [];
		
		$data['id'] = $this->id;
		$data['name'] = $this->name;
		$data['in'] = $this->in;
		$data['out'] = $this->out;
		$data['creation_date'] = $this->creation_date;
		$data['export_date'] = $this->export_date;
		$data['deleted'] = $this->deleted;
		
		return $data;
	}
}