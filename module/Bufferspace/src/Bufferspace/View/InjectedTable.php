<?php
namespace Bufferspace\View;

use Zend\Db\TableGateway\TableGateway;

class InjectedTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
	
	public function count()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet->count();
	}
}