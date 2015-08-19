<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Operator for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Operator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class OperatorController extends AbstractActionController
{
	protected $systemTable;

	public function getSystemTable()
	{
		if(!$this->systemTable)
		{
			$sm = $this->getServiceLocator();
			$this->systemTable = $sm->get('Manager\Model\SystemTable');
		}
		return $this->systemTable;
	}

	public function indexAction()
	{
		$aParam = array();

		// for the moment, retrieve steps status but use a clever way for final version
		$oContainer = new Container('automate_setup');
		$aParam['step'] = $oContainer;

		$ready = ($oContainer->drugspecified == true
			&& $oContainer->fileloaded == true
			&& $oContainer->sourcekitscanned == true
			&& $oContainer->sourcekitloaded == true
			&& $oContainer->markedasended == false) ?
		true : false;

		$aParam['canInject'] = ($ready) ? true : false;
		$aParam['canUnload'] = ($ready || $oContainer->markedasended) ? true : false;
		$aParam['canExport'] = ($oContainer->fileloaded) ? true : false;
		$aParam['needScan'] = true;

		return new ViewModel($aParam);
	}
	
}
