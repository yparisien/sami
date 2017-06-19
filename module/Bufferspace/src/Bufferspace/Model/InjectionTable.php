<?php
namespace Bufferspace\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Classe DAO de requêtage de la table tmp_injection
 * 
 * @author yohann.parisien
 *
 */
class InjectionTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Récupère toutes les injections
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	/**
	 * Récupération d'une injection par id
	 * 
	 * @param integer $id
	 * @throws \Exception
	 * @return Injection
	 */
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
	 * Récupération d'une injection par id patient
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

	/**
	 * Création / Mise à jour des informations d'une injection
	 * 
	 * @param Injection $injection
	 * @throws \Exception
	 */
	public function saveInjection(Injection $injection)
	{
		$data = $injection->toArray();
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

	/**
	 * Suppression d'une injection par id
	 * 
	 * @param integer $id
	 */
	public function deleteInjection($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}