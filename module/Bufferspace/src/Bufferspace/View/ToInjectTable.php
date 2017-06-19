<?php
namespace Bufferspace\View;

use Zend\Db\TableGateway\TableGateway;

/**
 * Classe DAO de la vue view_toinject
 * 
 * @author yohann.parisien
 *
 */
class ToInjectTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Récupération de la liste des patients à injecter
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
}