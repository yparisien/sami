<?php
namespace Manager\View;

class VExamination
{
	public	$id;
	public	$examination_name;
	public	$drug_name;
	public	$rate;
	public	$min;
	public	$max;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->examination_name = (!empty($data['examination_name'])) ? $data['examination_name'] : null;
		$this->drug_name = (!empty($data['drug_name'])) ? $data['drug_name'] : null;
		$this->rate = (!empty($data['rate'])) ? $data['rate'] : null;
		$this->min = (!empty($data['min'])) ? $data['min'] : null;
		$this->max = (!empty($data['max'])) ? $data['max'] : null;
	}
}