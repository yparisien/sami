<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Manager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Operator\Controller;

use Zend\View\Model\ViewModel;

use Start\Controller\CommonController;

class ActivimeterController extends CommonController
{

	public function	indexAction()
	{
		$aParam = array();
		return new ViewModel($aParam);
	}
}
