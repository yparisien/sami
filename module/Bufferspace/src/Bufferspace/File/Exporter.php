<?php
namespace Bufferspace\File;

class	Exporter
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

	public function	setHeader()		{ return $this->_header; }
	public function	setDataStruct()	{ return $this->_datastruct; }

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
	
	public function historyPatient() {
		$bRet = false;
		
		$bRet = true;
		
		return $bRet;
	}
}