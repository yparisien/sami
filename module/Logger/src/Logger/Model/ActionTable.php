<?php

namespace Logger\Model;

use Zend\Db\TableGateway\TableGateway;

class ActionTable
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

	public function getAction($id)
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

	public function saveAction(Action $action)
	{
		$data = array(
			'inputdate'			=> $action->inputdate,
			'userid'			=> $action->userid,
			'action'			=> $action->action,
			/*'batchnum'			=> $action->batchnum,
			'calibrationtime'	=> $action->calibrationtime,
			'expirationtime'	=> $action->expirationtime,
			'vialvol'			=> $action->vialvol,
			'activity'			=> $action->activity,
			'activityconc'		=> $action->activityconc,
			'activitycalib'		=> $action->activitycalib,*/
		);

		$id = (int) $action->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getAction($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Action id does not exist');
			}
		}
	}

	public function deleteAction($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}