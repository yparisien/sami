<?php

namespace Logger\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Classe DAO de la table input_file
 * 
 * @author yohann.parisien
 *
 */
class InputFileTable
{
	protected $tableGateway;

	/**
	 * 
	 * @param TableGateway $tableGateway
	 */
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Récupération de l'ensemble des InputFiles
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	/**
	 * Get a single InputFile by id
	 * 
	 * @param integer $id
	 * @throws \Exception
	 * @return InputFile
	 */
	public function getInputFile($id)
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
	 * Get last used InputFile
	 * 
	 * @throws \Exception
	 * @return InputFile
	 */
	public function getLastInputFile()
	{
		$rowset = $this->tableGateway->select(function (Select $select) {
			$where = new \Zend\Db\Sql\Where;
			$where->equalTo('deleted', 0);
			$where->AND->isNull('out');
			
			$select->where($where);
			$select->order('creation_date DESC')->limit(1);
		});
		
		$row = $rowset->current();
		if (!$row)
		{
			return null;
		}
		return $row;
	}

	/**
	 * Création / Mise à jour des informations d'un objet InputFile
	 * 
	 * @param InputFile $inputFile
	 * @throws \Exception
	 */
	public function saveInputFile(InputFile &$inputFile)
	{
		$data = array(
			'name'			=> $inputFile->name,
			'in'			=> $inputFile->in,
			'out'			=> $inputFile->out,
			'creation_date'	=> $inputFile->creation_date,
			'export_date'	=> $inputFile->export_date,
			'deleted'		=> $inputFile->deleted,
		);

		$id = (int) $inputFile->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
			$inputFile->id = $this->tableGateway->lastInsertValue;
		}
		else
		{
			if ($this->getInputFile($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('InputFile id does not exist');
			}
		}
		
		return true;
	}
}