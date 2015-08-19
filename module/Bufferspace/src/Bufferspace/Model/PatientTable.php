<?php
namespace Bufferspace\Model;

use Zend\Db\TableGateway\TableGateway;

class PatientTable
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

	public function	getToInject()
	{
		$rowset = $this->tableGateway->select(array('injected' => 0));
		return $rowset;
	}

	public function	getInjected()
	{
		$rowset = $this->tableGateway->select(array('injected' => 1));
		return $rowset;
	}

	/**
	 * Get Patient
	 * @param integer $id
	 * @throws \Exception
	 * @return Patient
	 */
	public function getPatient($id)
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

	public function savePatient(Patient &$patient)
	{
		$data = array(
			'patient_id'	=> $patient->patient_id,
			'lastname'		=> $patient->lastname,
			'firstname'		=> $patient->firstname,
			'gender'		=> $patient->gender,
			'birthdate'		=> $patient->birthdate,
			'age'			=> $patient->age,
			'weight'		=> $patient->weight,
			'height'		=> $patient->height,
			'injected'		=> $patient->injected,
		);

		$id = (int) $patient->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
			$patient->id = $this->tableGateway->lastInsertValue;
		}
		else
		{
			if ($this->getPatient($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Patient id does not exist');
			}
		}
	}

	public function deletePatient($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}