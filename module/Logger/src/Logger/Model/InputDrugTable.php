<?php

namespace Logger\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

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
	 * @param integer $id
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
	
	/**
	 * Get a single InputDrug
	 * @param integer $drugid
	 * @throws \Exception
	 * @return InputDrug
	 */
	public function getLastByDrugId($drugid)
	{
		$drugid  = (int) $drugid;
		$rowset = $this->tableGateway->select(function (Select $select) use ($drugid) {
     		$select->where->equalTo('drugid', $drugid);
			$select->order('inputdate DESC')->limit(1);
		});
		$row = $rowset->current();
		if (!$row)
		{
			throw new \Exception("Could not find lastest row with drugid $drugid");
		}
		return $row;
	}

	public function saveInputDrug(InputDrug &$inputdrug)
	{
		$data = array(
			'inputdate'			=> $inputdrug->inputdate->format('c'),
			'userid'			=> $inputdrug->userid,
			'drugid'			=> $inputdrug->drugid,
			'batchnum'			=> $inputdrug->batchnum,
			'calibrationtime'	=> $inputdrug->calibrationtime->format('c'),
			'expirationtime'	=> $inputdrug->expirationtime->format('c'),
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