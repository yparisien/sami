<?php
namespace Bufferspace\View;

use Zend\Db\TableGateway\TableGateway;

/**
 * Classe DAO de la vue view_export
 * 
 * @author yohann.parisien
 *
 */
class ExportTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Récupération de la liste de informations pour la constructio du fichier d'export (patient + injection)
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
}