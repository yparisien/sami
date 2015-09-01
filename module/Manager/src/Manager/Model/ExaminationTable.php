<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ExaminationTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select(function (Select $select) {
     		$select->order('name ASC');
		});
		
		return $resultSet;
	}

	/**
	 * 
	 * @param integer $id
	 * @throws \Exception
	 * @return Examination
	 */
	public function getExamination($id)
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

	public function saveExamination(Examination $user)
	{
		$data = array(
			'name'		=> $user->name,
			'drugid'	=> $user->drugid,
			'rate'		=> $user->rate,
			'min'		=> $user->min,
			'max'		=> $user->max,
		);

		$id = (int) $user->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getExamination($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Examination id does not exist');
			}
		}
	}

	public function deleteExamination($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}