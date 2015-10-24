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
		$this->id			= (!empty($data['id'])) ? (int) $data['id'] : null;
		$this->login		= (!empty($data['login'])) ? $data['login'] : null;
		$this->password		= (!empty($data['password'])) ? $data['password'] : null;
		$this->admin		= (!empty($data['admin'])) ? (bool) $data['admin'] : null;
		$this->firstname	= (!empty($data['firstname'])) ? $data['firstname'] : null;
		$this->lastname		= (!empty($data['lastname'])) ? $data['lastname'] : null;
		$this->visible		= (!empty($data['visible'])) ? (bool) $data['visible'] : null;
	}
	
	public function toArray() {
		$tab = [];
		
		$tab['id'] = $this->id;
		$tab['login'] = $this->login;
		$tab['password'] = $this->password;
		$tab['admin'] = $this->admin;
		$tab['firstname'] = $this->firstname;
		$tab['lastname'] = $this->lastname;
		$tab['visible'] = $this->visible;
		
		return $tab;
	}
}