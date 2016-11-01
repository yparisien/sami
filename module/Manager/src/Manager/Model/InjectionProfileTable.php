<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class InjectionProfileTable
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
	 * @return InjectionProfile
	 */
	public function getInjectionProfile($id)
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
	
	public function saveInjectionProfile(InjectionProfile $injectProfile)
	{
		$data = array(
			'code'	=> $injectProfile->code,
			'label'	=> $injectProfile->label,
		);

		$id = (int) $injectProfile->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getInjectionProfile($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Injection Profile id does not exist');
			}
		}
	}

	public function deleteInjectionProfile($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}