<?php
namespace Logger\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Classe DAO de requêtage de la table patient_history
 * 
 * @author yohann.parisien
 *
 */
class PatientHistoryTable {
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * Création / Mise à jour des informations d'un objet PatientHistory
	 * 
	 * @param PatientHistory $patientHistory
	 * @return boolean
	 */
	public function save(PatientHistory $patientHistory)
	{
		return true;
		$data = array(
				'patient_id'					=> $patientHistory->patientId,
				'lastname'						=> $patientHistory->lastname,
				'firstname'						=> $patientHistory->firstname,
				'gender'						=> $patientHistory->gender,
				'birthdate'						=> $patientHistory->birthdate,
				'weight'						=> $patientHistory->weight,
				'height'						=> $patientHistory->height,
				'patient_type'					=> $patientHistory->patientType,
				'doctor_name'					=> $patientHistory->doctorName,
				'injection_type'				=> $patientHistory->injectionType,
				'injection_time'				=> $patientHistory->injectionTime,
				'injection_activity'			=> $patientHistory->injectionActivity,
				'injection_prescription_id'		=> $patientHistory->injectionPrescriptionId,
				'injection_dose_status'			=> $patientHistory->injectionDoseStatus,
				'injection_val_id	'			=> $patientHistory->injectionValId,
				'injection_location'			=> $patientHistory->injectionLocation,
				'injection_comments'			=> $patientHistory->injectionComments,
				'examination_name'				=> $patientHistory->examinationName,
				'examination_dci'				=> $patientHistory->examinationDCI,
				'examination_rate'				=> $patientHistory->examinationRate,
				'examination_min'				=> $patientHistory->examinationMin,
				'examination_max'				=> $patientHistory->examinationMax,
				'drug_name'						=> $patientHistory->drugName,
				'drug_dci'						=> $patientHistory->drugDCI,
				'drug_radionuclide_id'			=> $patientHistory->drugRadionuclideId,
				'drug_input_date'				=> $patientHistory->drugInputDate,
				'drug_input_operator_name'		=> $patientHistory->drugInputOperatorName,
				'drug_input_batchnum'			=> $patientHistory->drugInputBatchnum,
				'drug_input_calibration_time'	=> $patientHistory->drugInputCalibrationTime,
				'drug_input_expiration_time'	=> $patientHistory->drugInputExpirationTime,
				'drug_input_vialvol'			=> $patientHistory->drugInputVialvol,
				'drug_input_activity'			=> $patientHistory->drugInputActivity,
				'drug_input_activity_conc'		=> $patientHistory->drugInputActivityConc,
				'drug_input_activity_calib'		=> $patientHistory->drugInputActivityCalib,
				'patient_kit_serial'			=> $patientHistory->patientKitSerial,
				'source_kit_serial'				=> $patientHistory->sourceKitSerial,
				'radionuclide_id'				=> $patientHistory->radionuclideId,
				'radionuclide_code'				=> $patientHistory->radionuclideCode,
				'radionuclide_name'				=> $patientHistory->radionuclideName,
		);
	
		$this->tableGateway->insert($data);
	}
}