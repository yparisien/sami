<?php

namespace Logger\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Classe DAO de la table input_action
 * 
 * @author yohann.parisien
 *
 */
class InputActionTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Récupération d'un input_action par id
	 * 
	 * @param unknown $id
	 * @throws \Exception
	 * @return ArrayObject|NULL
	 */
	public function getInputAction($id)
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
	 *  Création / Mise à jour des informations d'une injection
	 * 
	 * @param InputAction $action
	 * @throws \Exception
	 */
	public function saveInputAction(InputAction $action)
	{
		$data = array(
			'inputdate'			=> $action->inputdate->format('Y-m-d H:i:s'),
			'userid'			=> $action->userid,
			'action'			=> $action->action,
		);

		$id = (int) $action->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getInputAction($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('InputAction id does not exist');
			}
		}
	}
}