<?php
namespace Bufferspace\View;

use Zend\Db\TableGateway\TableGateway;

/**
 * Classe DAO de la vue view_injected
 * 
 * @author yohann.parisien
 *
 */
class InjectedTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Récupération de tous les patients injectés
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
	
	/**
	 * Comptage du nombre de patients injectés
	 * 
	 * @return integer
	 */
	public function count()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet->count();
	}
}