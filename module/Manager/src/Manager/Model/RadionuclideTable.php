<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;

class RadionuclideTable
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

	public function getRadionuclide($id)
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

	public function saveRadionuclide(Radionuclide $radionuclide)
	{
		$data = array(
			'name'			=> $radionuclide->name,
			'period'		=> $radionuclide->period,
			'coefficient'	=> $radionuclide->coefficient,
		);

		$id = (int) $radionuclide->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getRadionuclide($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Radionuclide id does not exist');
			}
		}
	}

	public function deleteRadionuclide($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}