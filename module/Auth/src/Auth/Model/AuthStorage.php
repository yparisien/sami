<?php
namespace Auth\Model;

use Zend\Authentication\Storage;

class AuthStorage extends Storage\Session
{
	public function	storeAuth($time = 1209600)
	{
		$this->session->getManager()->rememberMe($time);
	}

	public function forgetMe()
	{
		$this->session->getManager()->forgetMe();
	}
}