<?php
namespace Bufferspace\Model;

use Zend\Db\TableGateway\TableGateway;

class InjectionTable
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

	public function getInjection($id)
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

	/**
	 * 
	 * @param integer $patientId
	 * @throws \Exception
	 * @return Injection
	 */
	public function searchByPatientId($patientId)
	{
		//$patientId  = (int) $patientId;
		$rowset = $this->tableGateway->select(array('patient_id' => $patientId));
		$row = $rowset->current();
		if (!$row)
		{
			throw new \Exception("Could not find row for patientid $patientId");
		}
		return $row;

	}

	public function saveInjection(Injection $injection)
	{
		$data = array(
			'patient_id'		=> $injection->patient_id,
			'type'				=> $injection->type,
			'injection_time'	=> $injection->injection_time,
			'activity'			=> $injection->activity,
			'dose_status'		=> $injection->dose_status,
			'unique_id'			=> $injection->unique_id,
			'vial_id'			=> $injection->vial_id,
			'location'			=> $injection->location,
			'comments'			=> $injection->comments,
			'dci'				=> $injection->dci,
			'drugid'			=> $injection->drugid,
			'inputdrugid'		=> $injection->inputdrugid,
			'examinationid'		=> $injection->examinationid,
			'operatorid'		=> $injection->operatorid,
		);

		$id = (int) $injection->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getInjection($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Injection id does not exist');
			}
		}
	}

	public function deleteInjection($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}