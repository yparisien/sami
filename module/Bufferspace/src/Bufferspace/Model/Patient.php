<?php
namespace Bufferspace\Model;

class Patient
{
	public	$id;
	public	$patient_id;
	public	$lastname;
	public	$firstname;
	public	$gender;
	public	$birthdate;
	public	$age;
	public	$weight;
	public	$height;
	public	$patienttype;
	public	$doctorname;
	public	$injected;
	public	$comments;

	public function	exchangeArray($data)
	{
		$this->id			= (!empty($data['id']))				? $data['id']			: null;
		$this->patient_id	= (!empty($data['patient_id']))		? $data['patient_id']	: null;
		$this->lastname		= (!empty($data['lastname']))		? $data['lastname']		: null;
		$this->firstname	= (!empty($data['firstname']))		? $data['firstname']	: null;
		$this->gender		= (!empty($data['gender']))			? $data['gender']		: null;
		$this->birthdate	= (!empty($data['birthdate']))		? $data['birthdate']	: null;
		$this->age			= (!empty($data['age']))			? $data['age']			: null;
		$this->weight		= (!empty($data['weight']))			? $data['weight']		: null;
		$this->height		= (!empty($data['height']))			? $data['height']		: null;
		$this->patienttype	= (!empty($data['patienttype']))	? $data['patienttype']	: null;
		$this->doctorname	= (!empty($data['doctorname']))		? $data['doctorname']	: null;
		$this->injected		= (!empty($data['injected']))		? $data['injected']		: null;
		$this->comments		= (!empty($data['comments']))		? $data['comments']		: null;
	}

	/**
	 * @deprecated No used
	 * @param array $data
	 */
	public function	overloadData($data)
	{
		$aField = array('id', 'patient_id', 'lastname', 'firstname', 'gender', 'birthdate', 'age', 'weight', 'height', 'patienttype', 'doctorname', 'injected', 'comments');
		foreach($aField as $field)
		{
			if(array_key_exists($field, $data) && !empty($data[$field]))
			{
				$this->$field = $data[$field];
			}
		}
	}

	public function	toArray()
	{
		$data				= array();
		$data['id']			= $this->id;
		$data['patient_id']	= $this->patient_id;
		$data['lastname']	= $this->lastname;
		$data['firstname']	= $this->firstname;
		$data['gender']		= $this->gender;
		$data['birthdate']	= $this->birthdate;
		$data['age']		= $this->age;
		$data['weight']		= $this->weight;
		$data['height']		= $this->height;
		$data['patienttype']= $this->patienttype;
		$data['doctorname']	= $this->doctorname;
		$data['injected']	= $this->injected;
		$data['comments']	= $this->comments;
		return $data;
	}
}