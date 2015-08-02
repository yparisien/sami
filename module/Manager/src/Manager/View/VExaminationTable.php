<?php

namespace Manager\View;

use Zend\Db\TableGateway\TableGateway;

class VExaminationTable
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
	
	/**
	 * 
	 * @param integer $id
	 * @throws \Exception
	 * @return VExamination object
	 */
	public function getVExamination($id)
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row)
		{
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
}