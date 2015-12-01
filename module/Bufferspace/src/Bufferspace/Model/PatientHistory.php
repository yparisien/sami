<?php
namespace Bufferspace\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * 
 * @author yohann.parisien
 *
 */
class PatientHistory {
	/*
	 * General variables
	 */
	public	$id;
	public	$patientId;
	public	$lastname;
	public	$firstname;
	public	$gender;
	public	$birthdate;
	public	$weight;
	public	$height;
	public	$patientType;
	public	$doctorName;
	
	/*
	 * Injection variables
	 */
	public	$injectionType;
	public	$injectionTime;
	public	$injectionActivity;
	public	$injectionPrescriptionId;
	public	$injectionDoseStatus;
	public	$injectionValId;
	public	$injectionLocation;
	public	$injectionComments;
	
	/*
	 * Examination variables
	 */
	public	$examinationName;
	public	$examinationDCI;
	public	$examinationRate;
	public	$examinationMin;
	public	$examinationMax;
	
	/*
	 * Drug variables
	 */
	public	$drugName;
	public	$drugDCI;
	public	$drugRadionuclideId;
	
	/*
	 * DrugInput variables
	 */
	public	$drugInputDate;
	public	$drugInputOperatorName;
	public	$drugInputBatchnum;
	public	$drugInputCalibrationTime;
	public	$drugInputExpirationTime;
	public	$drugInputVialvol;
	public	$drugInputActivity;
	public	$drugInputActivityConc;
	public	$drugInputActivityCalib;
	
	/*
	 * Kit variables
	 */
	public	$patientKitSerial;
	public	$sourceKitSerial;
	
	/*
	 * Radionuclide variables
	 */
	public	$radionuclideId;
	public	$radionuclideCode;
	public	$radionuclideName;
	
	public function	exchangeArray($data)
	{
		$this->id			= (!empty($data['id']))				? (int) $data['id']			: null;
		$this->patient_id	= (!empty($data['patient_id']))		? $data['patient_id']		: null;
		$this->lastname		= (!empty($data['lastname']))		? $data['lastname']			: null;
		$this->firstname	= (!empty($data['firstname']))		? $data['firstname']		: null;
		$this->gender		= (!empty($data['gender']))			? $data['gender']			: null;
		$this->birthdate	= (!empty($data['birthdate']))		? $data['birthdate']		: null;
		$this->age			= (!empty($data['age']))			? (int) $data['age']		: null;
		$this->weight		= (!empty($data['weight']))			? (float) $data['weight']	: null;
		$this->height		= (!empty($data['height']))			? (float) $data['height']	: null;
		$this->patienttype	= (!empty($data['patienttype']))	? $data['patienttype']		: null;
		$this->doctorname	= (!empty($data['doctorname']))		? $data['doctorname']		: null;
		
		//TODO TO COMPLETE
		
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
		
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
		
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
		
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
		
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
		
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
// 		$this->			= (!empty($data['']))				? (int) $data['']			: null;
	}
	
	public static function generateByPatientId($patientId, ServiceLocatorInterface $sm) {
		/* @var $patientTable PatientTable */
		$patientTable = $sm->get('Bufferspace\Model\PatientTable');
		$patient = $patientTable->getPatient($patientId);
		
		/* @var $injectionTable InjectionTable */
		$injectionTable = $sm->get('Bufferspace\Model\InjectionTable');
		$injection = $injectionTable->searchByPatientId($patientId);
		
		/*
		 * Create object
		 */
		$patientHistory = new PatientHistory();
		
		/*
		 * Fill object [Patient part]
		 */
		$patientHistory->patientId		= $patient->id;
		$patientHistory->lastname		= $patient->lastname;
		$patientHistory->firstname		= $patient->firstname;
		$patientHistory->gender			= $patient->gender;
		$patientHistory->birthdate		= $patient->birthdate;
		$patientHistory->weight			= $patient->weight;
		$patientHistory->height			= $patient->height;
		$patientHistory->patientType	= $patient->patienttype;
		$patientHistory->doctorName		= $patient->doctorname;
		
		/*
		 * Fill object [Injection part]
		 */
		$patientHistory->injectionType				= $injection->type;
		$patientHistory->injectionTime				= $injection->injection_time;
		$injection->injection_date;
// 		$patientHistory->injectionActivity			= $injection->activity;
// 		$patientHistory->injectionPrescriptionId	= $injection->;
		$patientHistory->injectionDoseStatus		= $injection->dose_status;
		$patientHistory->injectionValId				= $injection->vial_id;
		$patientHistory->injectionLocation			= $injection->location;
		$patientHistory->injectionComments			= $injection->comments;
		
		//TODO To Achieve
		
		return $patientHistory;
	}
}