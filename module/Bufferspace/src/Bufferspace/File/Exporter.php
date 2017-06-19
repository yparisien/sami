<?php
namespace Bufferspace\File;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Classe d'export / création du fichier csv
 * 
 * @author yohann.parisien
 *
 */
class	Exporter
{
	protected	$_pathfile;
	protected	$_fd;
	
	/**
	 * 
	 * @var ServiceLocatorInterface
	 */
	protected	$_servicelocator;

	protected	$_header		= array();
	protected	$_datastruct	= array();
	protected	$_patients		= array();
	protected	$_checksum		= array();
	
	protected	$patientTable;
	protected	$injectionTable;
	protected	$injectedTable;

	public function	__construct($servicelocator)
	{
		$this->_servicelocator = $servicelocator;
	}
	
	/**
	 *
	 * @return \Bufferspace\Model\InjectionTable
	 */
	public function getInjectionTable()
	{
		if(!$this->injectionTable)
		{
			$this->injectionTable = $this->_servicelocator->get('Bufferspace\Model\InjectionTable');
		}
		return $this->injectionTable;
	}
	
	/**
	 *
	 * @return \Bufferspace\Model\PatientTable
	 */
	public function getPatientTable()
	{
		if(!$this->patientTable)
		{
			$this->patientTable = $this->_servicelocator->get('Bufferspace\Model\PatientTable');
		}
		return $this->patientTable;
	}
	
	/**
	 *
	 * @return \Bufferspace\View\InjectedTable
	 */
	public function getInjectedTable()
	{
		if(!$this->injectedTable)
		{
			$this->injectedTable = $this->_servicelocator->get('Bufferspace\View\InjectedTable');
		}
		return $this->injectedTable;
	}


	public function	getPathFile()	{ return $this->_pathfile; }
	public function	getFd()			{ return $this->_fd; }

	public function	setHeader()		{ return $this->_header; }
	public function	setDataStruct()	{ return $this->_datastruct; }

	/**
	 * 
	 * @param string $pathfile
	 */
	public function	setPathFile($pathfile)
	{
		if(file_exists($pathfile))
		{
			$this->_pathfile = $pathfile;
		}
		else
		{
			throw(new \Exception("Invalid pathfile : " . $pathfile));
		}
	}

	/**
	 * Génération du fichier d'export
	 * 
	 * @param unknown $filename
	 * @return void
	 */
	public function	generateFile($filename)
	{
		if(file_exists($this->_pathfile . '/' . $filename))
		{
			unlink($this->_pathfile . '/' . $filename);
			return $this->generateFile($filename);
		}
		else
		{
			$fd = fopen($this->_pathfile.'/'.$filename, 'c');
			if($fd !== false)
			{
				fwrite($fd, "file_version,1\n");
				fwrite($fd, "compatibility_file_version,1\n");
				fwrite($fd, "date,".date('Y/m/d')."\n");
				fwrite($fd, "injector_id,1283485\n");
				fwrite($fd, "Type,Time,Activity,DoseStatus,UniqueID,VialID,PatientID,PatientName,Gender,BirthDate,Age,Weight,Height,PatientType,DoctorName,Emplacement\n");
				$exportline = $this->_servicelocator->get('Bufferspace\View\ExportTable')->fetchAll();
				foreach($exportline as $line)
				{
					$towrite = '';
					$towrite .= $line->type. ',';
					$towrite .= $line->injectiontime. ',';
					$towrite .= $line->activity. ',';
					$towrite .= $line->dosestatus. ',';
					$towrite .= $line->uniqueid. ',';
					$towrite .= $line->batchnum. ',';
					$towrite .= $line->patientid. ',';
					$towrite .= '"'.$line->patientname. '",';
					$towrite .= $line->gender. ',';
					$towrite .= $line->birthdate. ',';
					$towrite .= $line->age. ',';
					$towrite .= $line->weight. ',';
					$towrite .= $line->height. ',';
					$towrite .= $line->patienttype. ',';
					$towrite .= $line->doctorname. ',';
					$towrite .= $line->emplacement;
					$towrite .= "\n";
					fwrite($fd, $towrite);
				}
				fclose($fd);
				$checksum = md5_file($this->_pathfile.'/'.$filename);
				$fd = fopen($this->_pathfile.'/'.$filename, 'a');
				fwrite($fd, 'checksum,'.$checksum."\n");
				fclose($fd);
			}
		}
	}
	
	/**
	 * Nettoyage des tables tmp_patient & tmp_injection après création de l'export
	 */
	public function cleanDataBase()
	{
		$patientTable = $this->getPatientTable();
		$injectionTable = $this->getInjectionTable();

		$aoInjection = $injectionTable->fetchAll();
		
		foreach ($aoInjection as $oInjection) {
			$patientTable->deletePatient($oInjection->patient_id);
			$injectionTable->deleteInjection($oInjection->id);
		}
	}
}