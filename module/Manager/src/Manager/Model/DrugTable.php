<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;

class DrugTable
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

	public function getDrug($id)
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

	public function saveDrug(Drug $drug)
	{
		$data = array(
			'name'				=> $drug->name,
			'radionuclideid'	=> $drug->radionuclideid,
			'dci'				=> $drug->dci,
		);

		$id = (int) $drug->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getDrug($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Drug id does not exist');
			}
		}
	}

	public function deleteDrug($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}