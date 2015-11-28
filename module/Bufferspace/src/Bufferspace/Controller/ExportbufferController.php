<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Bufferspace for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Bufferspace\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Bufferspace\File\Exporter;
use Zend\View\Helper\ViewModel;
use Zend\Session\Container;



class ExportbufferController extends AbstractActionController
{
	protected $inputfileTable;	
	
	/**
	 *
	 * @return \Logger\Model\InputFileTable
	 */
	public function getInputFileTable()
	{
		if(!$this->inputfileTable)
		{
			$sm = $this->getServiceLocator();
			$this->inputfileTable = $sm->get('Logger\Model\InputFileTable');
		}
		return $this->inputfileTable;
	}
	
	public function indexAction()
	{
		return array();
	}

	public function agenfileAction()
	{
		$destPath = dirname(__DIR__) . '/../../../../public/tmp';
		$oContainer = new Container('automate_setup');
		$oContainer->fileloaded = false;
		
		$inputFile = $this->getInputFileTable()->getLastInputFile();
		$filename = $inputFile->name;

		$oExport = new Exporter($this->getServiceLocator());
		$oExport->setPathfile($destPath);
		$oExport->generateFile($filename);
		
		$exportFileContent = file_get_contents($destPath . '/' . $filename);
		$inputFile->out = $exportFileContent;
		$inputFile->export_date = date('Y-m-d H:i:s');
		$this->getInputFileTable()->saveInputFile($inputFile);
		
		if ($ret === true) {
			$oExport->cleanDataBase();
			$oContainer->fileexported = true;
		}

		return new JsonModel(array('succes' => 1));
	}
}
