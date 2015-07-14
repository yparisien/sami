<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;

class SystemTable
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

	public function getSystem($id = 1)
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

	public function saveSystem(System $system)
	{
		$data = array(
			'language'		=> $system->language,
			'mode'			=> $system->mode,
			'unit'			=> $system->unit,
			'genuinekit'	=> $system->genuinekit,
		);

		$id = (int) $system->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getSystem($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('System id does not exist');
			}
		}
	}

	public function deleteSystem($id)
	{
		if($id != 1) // can't remove the root system profile
		{
			$this->tableGateway->delete(array('id' => (int) $id));
		}
	}
}