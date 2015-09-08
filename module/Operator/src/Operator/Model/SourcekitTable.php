<?php
namespace Operator\Model;

use Zend\Db\TableGateway\TableGateway;

class SourcekitTable
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

	public function getSourcekit($id)
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
	 * @param string $sn
	 * @return Sourcekit
	 */
	public function	searchBySerialNumber($sn)
	{
		//$sn = (int) $sn;
		$rowset = $this->tableGateway->select(array('serialnumber' => $sn));
		$row = $rowset->current();
		if (!$row)
		{
			//throw new \Exception("Could not find sn $sn");
			return false;
		}
		return $row;
	}

	public function saveSourcekit(Sourcekit &$sourcekit)
	{
		$data = array(
			'serialnumber'	=> $sourcekit->serialnumber,
			'usedate'		=> $sourcekit->usedate,
			'operatorid'	=> $sourcekit->operatorid,
		);

		$id = (int) $sourcekit->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
			$sourcekit->id = $this->tableGateway->lastInsertValue;
		}
		else
		{
			if ($this->getSourcekit($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('Sourcekit id does not exist');
			}
		}
	}

	public function deleteSourcekit($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}