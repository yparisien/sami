<?php
namespace Logger\Model;

class InputAction
{
	public	$id;
	public	$inputdate;
	public	$userid;
	public	$action;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->inputdate = (!empty($data['inputdate'])) ? $data['inputdate'] : null;
		$this->userid = (!empty($data['userid'])) ? $data['userid'] : null;
		$this->action = (!empty($data['action'])) ? $data['action'] : null;
	}
}