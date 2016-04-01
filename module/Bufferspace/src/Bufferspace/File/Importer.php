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

	private $columnNames = array(
			'Type', 'Time', 'Activity', 'DoseStatus', 'UniqueID', 'VialID', 'PatientID', 'PatientName', 
			'Gender', 'BirthDate', 'Age', 'Weight', 'Height', 'PatientType', 'DoctorName', 'MedicamentID',
	);
	
	private $nbColumns;

	public function	__construct($servicelocator)
	{
		$this->_servicelocator = $servicelocator;
		$this->nbColumns = count($this->columnNames);
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
		$bCheckSumFound = false;
		$fileVersionFound = false;
		$compatibilityFound = false;
		$dateFound = false;
		$injectorIdFound = false;
		$line = 0;
		$fileContentPerLine = array();
		
		if(file_exists($this->_pathfile . '/' . $file))
		{
			$this->_fd = fopen($this->_pathfile . '/' . $file, 'r');
			if($this->_fd === false)
			{
				throw new \Exception("Error during opening the file");
			}
			else
			{
				//Récuperation complète du fichier
				while($aRead = fgetcsv($this->_fd, 0, ',', '"')) {
					$fileContentPerLine[] = $aRead;
				}
				fclose($this->_fd);
					
				//Récupération des 4 premières lignes
				for($i = 0; $i < 4; $i++, $line++)
				{
					$aRead = $fileContentPerLine[$line];
					
					$this->_header[$aRead[0]] = $aRead[1];
					if ($aRead[0] == 'file_version') {
						$fileVersionFound = true;
					} else if ($aRead[0] == 'compatibility_file_version') {
						$compatibilityFound = true;
					} else if ($aRead[0] == 'date') {
						$dateFound = true;
					} else if ($aRead[0] == 'injector_id') {
						$injectorIdFound = true;
					} 
				}

				//Récupération des noms des colonnes
				$this->_datastruct = $fileContentPerLine[$line];
				$line++;
				
				$nc = 0;
				foreach ($this->columnNames as $index => $value) {
					if (in_array($value, $this->_datastruct)) {
						unset($this->columnNames[$index]);
					}
				}
				
				if (count($this->columnNames)) {
					throw new \Exception("Missing column names : " . implode(', ', $this->columnNames));
				}
				
				while ($line < count($fileContentPerLine))
				{
					$aRead = $fileContentPerLine[$line];
				
					
					if($aRead[0] == 'checksum')
					{
						$bCheckSumFound = true;
						$this->_checksum = $aRead[1];
						break;
					}
					else
					{
						if (count($aRead) > 0 && count($aRead) != $this->nbColumns) {
							$part = array_slice($fileContentPerLine, $line - 1, 5);
							$currentRow = implode(",", $aRead);
							
							$message = "The line $line of the file seems to be invalid or not containing all datas." . PHP_EOL;
							$message .= "<br />" . PHP_EOL;
							$message .= "<div class=\"table-responsive\">". PHP_EOL;
							$message .= "<table class=\"table table-bordered\">" . PHP_EOL;
							
							foreach ($part as $p) {
								$comp = implode(",", $p);
								if ($comp == $currentRow) {
									$message .= "<tr><td><strong>" . PHP_EOL;
									$message .= utf8_encode(implode('</strong></td><td><strong>', $p));
									$message .= "</strong></td></tr>" . PHP_EOL;
								} else {
									$message .= "<tr><td>" . PHP_EOL;
									$message .= utf8_encode(implode('</td><td>', $p));
									$message .= "</td></tr>" . PHP_EOL;
								}
							}
							$message .= "</table>" . PHP_EOL;
							$message .= "</div>" . PHP_EOL;
							throw new \Exception($message);
						}
						
						$aCurPatient = array();
						foreach($aRead as $k=>$v)
						{
							$aCurPatient[$this->_datastruct[$k]] = $v;
						}
						$this->_patients[] = $aCurPatient;
					}
					$line++;
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
			$oInjection->dci			= $patient['MedicamentID'];			
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