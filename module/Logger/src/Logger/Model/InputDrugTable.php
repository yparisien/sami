<?php

namespace Logger\Model;

use Zend\Db\TableGateway\TableGateway;

class InputDrugTable
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
	 * Get a single InputDrug
	 * @param unknown $id
	 * @throws \Exception
	 * @return InputDrug
	 */
	public function getInputDrug($id)
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

	public function saveInputDrug(InputDrug &$inputdrug)
	{
		$data = array(
			'inputdate'			=> $inputdrug->inputdate,
			'userid'			=> $inputdrug->userid,
			'drugid'			=> $inputdrug->drugid,
			'batchnum'			=> $inputdrug->batchnum,
			'calibrationtime'	=> $inputdrug->calibrationtime,
			'expirationtime'	=> $inputdrug->expirationtime,
			'vialvol'			=> $inputdrug->vialvol,
			'activity'			=> $inputdrug->activity,
			'activityconc'		=> $inputdrug->activityconc,
			'activitycalib'		=> $inputdrug->activitycalib,
		);

		$id = (int) $inputdrug->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
			$inputdrug->id = $this->tableGateway->lastInsertValue;
		}
		else
		{
			if ($this->getInputDrug($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('InputDrug id does not exist');
			}
		}
	}
}