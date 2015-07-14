<?php
namespace Manager\Model;

class User
{
	public	$id;
	public	$login;
	public	$password;
	public	$admin = 0;
	public	$firstname;
	public	$lastname;
	public	$visible;

	public function	exchangeArray($data)
	{
		$this->id			= (!empty($data['id'])) ? $data['id'] : null;
		$this->login		= (!empty($data['login'])) ? $data['login'] : null;
		$this->password		= (!empty($data['password'])) ? $data['password'] : null;
		$this->admin		= (!empty($data['admin'])) ? $data['admin'] : null;
		$this->firstname	= (!empty($data['firstname'])) ? $data['firstname'] : null;
		$this->lastname		= (!empty($data['lastname'])) ? $data['lastname'] : null;
		$this->visible		= (!empty($data['visible'])) ? $data['visible'] : null;
	}
}