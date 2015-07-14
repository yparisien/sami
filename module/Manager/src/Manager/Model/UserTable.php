<?php

namespace Manager\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select(array('visible'=>1));
		return $resultSet;
	}

	/**
	 *
	 * @param int $id
	 * @throws \Exception
	 * @return User
	 */
	public function getUser($id)
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
	 * @param string $login
	 * @return User
	 */
	public function	searchByLogin($login)
	{
		$rowset = $this->tableGateway->select(array('login' => $login));
		$row = $rowset->current();
		if (!$row)
		{
			return false;
		}
		return $row;
	}

	public function saveUser(User $user)
	{
		$data = array(
			'login'		=> $user->login,
			'password'	=> $user->password,
			'admin'		=> $user->admin,
			'firstname'	=> $user->firstname,
			'lastname'	=> $user->lastname,
			'visible' => $user->visible
		);

		$id = (int) $user->id;
		if ($id == 0)
		{
			$this->tableGateway->insert($data);
		}
		else
		{
			if ($this->getUser($id))
			{
				$this->tableGateway->update($data, array('id' => $id));
			}
			else
			{
				throw new \Exception('User id does not exist');
			}
		}
	}

	public function deleteUser($id)
	{
		$this->tableGateway->delete(array('id' => (int) $id));
	}
}
