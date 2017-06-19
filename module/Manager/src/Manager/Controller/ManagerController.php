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

class ManagerController extends CommonController
{
	public function indexAction()
	{
		/* @var $radionuclideTable \Manager\Model\RadionuclideTable */
		/* @var $drugTable \Manager\Model\DrugTable */
		/* @var $examinationTable \Manager\Model\ExaminationTable */
		
		$sm = $this->getServiceLocator();
		$radioTable = $sm->get('Manager\Model\RadionuclideTable');
		$drugTable  = $sm->get('Manager\Model\DrugTable');
		$examTable  = $sm->get('Manager\Model\ExaminationTable');
		
		$noRadios = ($radioTable->count() > 0) ? false : true;
		$noDrugs  = ($drugTable->count() > 0)  ? false : true;
		$noExams  = ($examTable->count() > 0)  ? false : true;
		
		return new ViewModel(array("noRadioNuclides" => $noRadios, "noDrugs" => $noDrugs, "noExams" => $noExams));
	}
}

