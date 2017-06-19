<?php
namespace Logger\Model;

/**
 * Classe modèle de la table input_action
 * 
 * @author yohann.parisien
 *
 */
class InputAction
{
	/**
	 * 
	 * @var integer $id
	 */
	public	$id;
	
	/**
	 * 
	 * @var \DateTime $inputdate
	 */
	public	$inputdate;
	
	/**
	 * 
	 * @var integer $userid;
	 */
	public	$userid;
	
	/**
	 * 
	 * @var string $action
	 */
	public	$action;

	public function	exchangeArray($data)
	{
		$this->id = (!empty($data['id'])) ? $data['id'] : null;
		$this->inputdate = (!empty($data['inputdate'])) ? $data['inputdate'] : null;
		$this->userid = (!empty($data['userid'])) ? $data['userid'] : null;
		$this->action = (!empty($data['action'])) ? $data['action'] : null;
	}
}