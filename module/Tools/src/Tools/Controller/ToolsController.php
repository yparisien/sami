<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Tools for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Tools\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ConsoleModel;
use Zend\View\Model\ViewModel;
use Bufferspace\File\Importer;

class ToolsController extends AbstractActionController
{
    public function indexAction()
    {
        return array();
    }

    public function	fileAction()
    {
    	$sm = $this->getServiceLocator();
    	$config = $sm->get('Config');
    	
    	//TODO Faire un nom dynamique pour archivage.
    	//Créer arbo de fichier par mois et par année
    	$archiveFileName = 'export.csv';
    	
		$oImporter = new Importer($sm);
		$oImporter->setPathFile($config['import_export']['export_archive_path']);
		$oImporter->loadFile($archiveFileName);
		$oImporter->cleanDataBase();
		$oImporter->fillDataBase();
		//$oImporter->cleanDataBase();
		
    	return new ViewModel(array('importer' => $oImporter));
    }
}
