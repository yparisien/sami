<?php

namespace Logger\Model;

use Zend\Db\TableGateway\TableGateway;

class InputActionTable
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

	public function getInputAction($id)
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

	public function saveInputAction(InputAction $action)
	{
		$data = array(
			'inputdate'			=> $action->inputdate,
			'userid'			=> $action->userid,
			'action'			=> $action->action,
		);

		$id = (int) $action->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getInputAction($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('InputAction id does not exist');
			}
		}
	}
}