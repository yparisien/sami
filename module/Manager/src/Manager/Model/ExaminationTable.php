<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Classe DAO de la table examination
 * 
 * @author yohann.parisien
 *
 */
class ExaminationTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Récupération de l'ensemble des examens
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select(function (Select $select) {
     		$select->order('name ASC');
		});
		
		return $resultSet;
	}

	/**
	 * Récupération d'un examen par id
	 * 
	 * @param integer $id
	 * @throws \Exception
	 * @return Examination
	 */
	public function getExamination($id)
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
	 * Ajout / Modification d'un examen en base de donnée
	 * 
	 * @param Examination $user
	 * @throws \Exception
	 */
	public function saveExamination(Examination $user)
	{
		$data = array(
			'name'		=> $user->name,
			'dci'		=> $user->dci,
			'rate'		=> $user->rate,
			'min'		=> $user->min,
			'max'		=> $user->max,
		);

		$id = (int) $user->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getExamination($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Examination id does not exist');
			}
		}
	}

	/**
	 * Suppression d'un examen par id
	 * 
	 * @param unknown $id
	 */
	public function deleteExamination($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
	
	/**
	 * Comptage du nombre d'examens en base de donnée
	 * 
	 * @return number
	 */
	public function count()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet->count();
	}
}