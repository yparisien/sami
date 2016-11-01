<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

//TODO Rajouter une colonne killed car on ne pas perdre des données du fait que tous les input drug sont sauvegardés

class DrugTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAllDCI()
	{
		$resultSet = $this->tableGateway->select(function (Select $select) {
		
			// Select columns and count the forums.
			$select->columns(array(
				'dci',
			));
			
			// Group by the category name.
			$select->group('dci');
		});
		return $resultSet;
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
	 * @return Drug
	 */
	public function getDrug($id)
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
	 * @param string $dci
	 * @throws \Exception
	 * @return Drug
	 */
	public function getDrugByDci($dci)
	{
		$rowset = $this->tableGateway->select(array('dci' => $dci));
		$row = $rowset->current();
		if (!$row)
		{
			throw new \Exception("Could not find drug with dci $dci");
		}
		return $row;
	}
	
	/**
	 *
	 * @param string $name
	 * @throws \Exception
	 * @return Drug
	 */
	public function getDrugByName($name)
	{
		$rowset = $this->tableGateway->select(array('name' => $name));
		$row = $rowset->current();
		if (!$row)
		{
			return null;
		}
		return $row;
	}

	public function saveDrug(Drug $drug)
	{
		$data = array(
			'name'				=> $drug->name,
			'radionuclideid'	=> $drug->radionuclideid,
			'dci'				=> $drug->dci,
			'dilutable'			=> $drug->dilutable === true ? 1 : 0,
		);

		$id = (int) $drug->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getDrug($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Drug id does not exist');
			}
		}
	}

	public function deleteDrug($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
	
	public function count()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet->count();
	}
}