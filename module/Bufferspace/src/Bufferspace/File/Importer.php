<?php
namespace Bufferspace\File;

use Bufferspace\Model\Patient;
use Bufferspace\Model\Injection;

class	Importer
{
	protected	$_pathfile;
	protected	$_fd;
	protected	$_servicelocator;

	protected	$_header		= array();
	protected	$_datastruct	= array();
	protected	$_patients		= array();
	protected	$_checksum		= array();

	public function	__construct($servicelocator)
	{
		$this->_servicelocator = $servicelocator;
	}

	public function	getPathFile()	{ return $this->_pathfile; }
	public function	getFd()			{ return $this->_fd; }

	public function	getHeader()		{ return $this->_header; }
	public function	getDataStruct()	{ return $this->_datastruct; }
	public function	getPatients()	{ return $this->_patients; }
	public function	getChecksum()	{ return $this->_checksum; }

	public function	setPathFile($pathfile)
	{
		if(file_exists($pathfile))
		{
			$this->_pathfile = $pathfile;
		}
		else
		{
			throw(new \Exception("Invalid pathfile"));
		}
	}

	public function	loadFile($file)
	{
		if(file_exists($this->_pathfile . '/' . $file))
		{
			$this->_fd = fopen($this->_pathfile . '/' . $file, 'r');
			if($this->_fd === false)
			{
				throw new \Exception("Error during opening the file");
			}
			else
			{
				for($i = 0; $i < 4; $i++)
				{
					$aRead = fgetcsv($this->_fd, 0, ',', '"');
					$this->_header[$aRead[0]] = $aRead[1];
				}
				$this->_datastruct = fgetcsv($this->_fd, 0, ',', '"');
				while($aRead = fgetcsv($this->_fd, 0, ',', '"'))
				{
					if($aRead[0] == 'checksum')
					{
						$this->_checksum = $aRead[1];
						break;
					}
					else
					{
						$aCurPatient = array();
						foreach($aRead as $k=>$v)
						{
							$aCurPatient[$this->_datastruct[$k]] = $v;
						}
						$this->_patients[] = $aCurPatient;
					}
				}
			}
		}
		else
		{
			throw new \Exception("File not found at ".$this->_pathfile . '/' . $file);
		}
	}

	public function	fillDataBase()
	{
		$patientTable = $this->_servicelocator->get('Bufferspace\Model\PatientTable');
		$injectionTable = $this->_servicelocator->get('Bufferspace\Model\InjectionTable');


		foreach($this->_patients as $patient)
		{
			$name		= explode(',', $patient['PatientName']);
			$lastName	= $name[0];
			$firstName	= $name[1];

			$oPatient				= new Patient();
			$oPatient->patient_id	= $patient['PatientID'];
			$oPatient->lastname		= $lastName;
			$oPatient->firstname	= $firstName;
			$oPatient->gender		= $patient['Gender'];
			$oPatient->birthdate	= str_replace('/', '-', $patient['BirthDate']);
			$oPatient->age			= $patient['Age'];
			$oPatient->weight		= $patient['Weight'];
			$oPatient->height		= $patient['Height'];
			$oPatient->patienttype	= $patient['PatientType'];
			$oPatient->doctorname	= $patient['DoctorName'];
			$oPatient->injected		= false;
			$patientTable->savePatient($oPatient);

			$oInjection					= new Injection();
			$oInjection->patient_id 	= $oPatient->id;
			$oInjection->type			= $patient['Type'];
			$oInjection->injection_time	= $patient['Time'];
			$oInjection->activity		= $patient['Activity'];
			$oInjection->dose_status	= $patient['DoseStatus'];
			$oInjection->unique_id		= $patient['UniqueID'];
			$oInjection->vial_id		= $patient['VialID'];
			//$oInjection->location	= $patient['Emplacement'];
			$oInjection->location		= '';
			$oInjection->comments		= '';
			$injectionTable->saveInjection($oInjection);

		}
	}

	public function	cleanDataBase()
	{
		$patientTable = $this->_servicelocator->get('Bufferspace\Model\PatientTable');
		$injectionTable = $this->_servicelocator->get('Bufferspace\Model\InjectionTable');

		$aPatient = $patientTable->fetchAll();
		foreach($aPatient as $oPatient)
		{
			$patientTable->deletePatient($oPatient->id);
		}

		$aInjection = $injectionTable->fetchAll();
		foreach($aInjection as $oInjection)
		{
			$injectionTable->deleteInjection($oInjection->id);
		}
	}
}