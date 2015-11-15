<?php

namespace Logger\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
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
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}

	/**
	 * Get a single InputFile
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
	 * Get a single InputFile
	 * @throws \Exception
	 * @return InputFile
	 */
	public function getLastInputFile()
	{
		$rowset = $this->tableGateway->select(function (Select $select) {
     		$select->where->isNull('out')->and->equalTo('deleted', 0);
			$select->order('creation_date DESC')->limit(1);
		});
		$row = $rowset->current();
		if (!$row)
		{
			throw new \Exception("Could not find lastest row");
		}
		return $row;
	}

	/**
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
	}
}