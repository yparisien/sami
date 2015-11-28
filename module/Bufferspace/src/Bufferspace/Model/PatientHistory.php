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
	
	public static function generateByPatientId($patientId, ServiceLocatorInterface $sm) {
		/* @var $patientTable PatientTable */
		$patientTable = $sm->get('PatientTable');
		$patient = $patientTable->getPatient($patientId);
		
		/* @var $injectionTable InjectionTable */
		$injectionTable = $sm->get('InjectionTable');
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
	}
}