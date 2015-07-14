<?php
namespace Manager\View;

class VDrug
{
	public	$id;
	public	$radionuclide_name;
	public	$drug_name;
	public	$dci;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->radionuclide_name = (!empty($data['radionuclide_name'])) ? $data['radionuclide_name'] : null;
		$this->drug_name = (!empty($data['drug_name'])) ? $data['drug_name'] : null;
		$this->dci = (!empty($data['dci'])) ? $data['dci'] : null;
	}
}