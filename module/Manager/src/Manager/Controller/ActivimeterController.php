<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Manager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Manager\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ActivimeterController extends AbstractActionController
{

	public function	indexAction()
	{
		exec("echo 'p' | vncviewer -autopass 10.0.0.100:5900");
		$aParam = array();
		return new ViewModel($aParam);
	}
}
