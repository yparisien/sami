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
	public function indexAction()
	{
		return array();
	}

	public function agenfileAction()
	{
		$destPath = dirname(__DIR__) . '/../../../../public/tmp';
		$oContainer = new Container('automate_setup');
		$oContainer->fileloaded = false;

		$oExport = new Exporter($this->getServiceLocator());
		$oExport->setPathfile($destPath);
		$oExport->generateFile('schedule.csv');
		$ret = $oExport->historyPatient();
		
		if ($ret === true) {
			$oExport->cleanDataBase();
			$oContainer->fileexported = true;
		}

		return new JsonModel(array('succes' => 1));
	}
}
