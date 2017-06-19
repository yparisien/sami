<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/Manager for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Manager\Controller;

use Zend\View\Model\ViewModel;

use Start\Controller\CommonController;

/**
 * Controlleur de l'écran principal de la section Manager
 * 
 * @author yohann.parisien
 *
 */
class RadionuclideController extends CommonController
{
	protected $radionuclideTable;

	public function getRadionuclideTable()
	{
		if(!$this->radionuclideTable)
		{
			$sm = $this->getServiceLocator();
			$this->radionuclideTable = $sm->get('Manager\Model\RadionuclideTable');
		}
		return $this->radionuclideTable;
	}

	/**
	 * Liste l'ensemble des radionucléides présent en base
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function	indexAction()
	{
		$aParam = array();
		$aParam['radionuclides'] = $this->getRadionuclideTable()->fetchAll();
		return new ViewModel($aParam);
	}
}
