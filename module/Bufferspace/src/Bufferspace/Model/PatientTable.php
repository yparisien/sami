<?php
namespace Bufferspace\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * 
 * @author yohann.parisien
 *
 */
class PatientTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Récupération de tous les patients
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	/**
	 * Récupération de tous les patients à injecter
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function	getToInject()
	{
		$rowset = $this->tableGateway->select(array('injected' => 0));
		return $rowset;
	}

	/**
	 * Récupération de tous les patiens injectés
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function	getInjected()
	{
		$rowset = $this->tableGateway->select(array('injected' => 1));
		return $rowset;
	}

	/**
	 * Récupéation d'un patient par id
	 * 
	 * @param integer $id
	 * @throws \Exception
	 * @return \Bufferspace\Model\Patient
	 */
	public function getPatient($id)
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row)
		{
			return null;
		}
		return $row;
	}
	
	/**
	 * Création / Mise à jour des informations d'un patient
	 * 
	 * @param Patient $patient
	 * @throws \Exception
	 */
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
		
		$data = $patient->toArray();
		
		$id = (int) $patient->id;
		if ($id == 0)
		{
			$ret = $this->tableGateway->insert($data);
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

	/**
	 * Suppression d'un patient par id
	 * 
	 * @param integer $id
	 */
	public function deletePatient($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}