<?php
namespace Operator\Model;

use Zend\Db\TableGateway\TableGateway;

class PatientkitTable
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

	public function getPatientkit($id)
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

	public function	searchBySerialNumber($sn)
	{
		//$sn = (int) $sn;
		$rowset = $this->tableGateway->select(array('serialnumber' => $sn));
		$row = $rowset->current();
		if (!$row)
		{
			//throw new \Exception("Could not find sn $sn");
			return false;
		}
		return $row;
	}

	public function savePatientkit(Patientkit &$patientkit)
	{
		$data = array(
			'serialnumber'	=> $patientkit->serialnumber,
			'usedate'		=> $patientkit->usedate,
			'operatorid'	=> $patientkit->operatorid,
		);

		$id = (int) $patientkit->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
			$patientkit->id = $this->tableGateway->lastInsertValue;
		}
		else
		{
			if ($this->getPatientkit($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Patientkit id does not exist');
			}
		}
	}

	public function deletePatientkit($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}