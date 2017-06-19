<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Classe DAO de la table radionuclide
 * 
 * @author yohann.parisien
 *
 */
class RadionuclideTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Liste l'ensemble des radionuclides
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	/**
	 * Récupération d'un radionuclide par id
	 * 
	 * @param integer $id
	 * @return \Manager\Model\Radionuclide
	 */
	public function getRadionuclide($id)
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		return $row;
	}

	/**
	 * Création / Modification d'un radionuclide
	 * 
	 * @param Radionuclide $radionuclide
	 */
	public function saveRadionuclide(Radionuclide $radionuclide)
	{
		$data = array(
			'id'			=> (int) $radionuclide->id,
			'code'			=> $radionuclide->code,
			'name'			=> $radionuclide->name,
			'period'		=> $radionuclide->period,
			'coefficient'	=> (float) $radionuclide->coefficient,
		);

		if ($this->getRadionuclide($radionuclide->id))
		{
			$this->tableGateway->update($data, array('id' => $radionuclide->id));
		}
		else
		{
			$this->tableGateway->insert($data);
		}
	}
	
	/**
	 * Comptage des radionuclides en base de donnée
	 * 
	 * @return number
	 */
	public function count()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet->count();
	}
}