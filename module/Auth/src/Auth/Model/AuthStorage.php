<?php
namespace Auth\Model;

use Zend\Authentication\Storage;

/**
 * 
 * @author yohann.parisien
 *
 */
class AuthStorage extends Storage\Session
{
	public function	storeAuth($time = 7200)
	{
		$this->session->getManager()->rememberMe($time);
	}

	public function forgetMe()
	{
		$this->session->getManager()->forgetMe();
	}
}