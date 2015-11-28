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

	/**
	 * 
	 * @param unknown $id
	 * @return \Manager\Model\Radionuclide
	 */
	public function getRadionuclide($id)
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		return $row;
	}

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
			//TODO Rajouter log fichier
			$this->tableGateway->update($data, array('id' => $radionuclide->id));
		}
		else
		{
			//TODO Rajouter log fichier
			$this->tableGateway->insert($data);
		}
	}
	
	public function count()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet->count();
	}
}