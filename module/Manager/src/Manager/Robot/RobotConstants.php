<?php
namespace Manager\Robot;

/**
 * Classe des constantes de communication avec le robot
 * @author yohann.parisien
 *
 */
class RobotConstants {
	
	const ISOTOPES_N_IDISOTOPE						= 'Isotopes[@@n@@].ID_Isotope';
	const ISOTOPES_N_SHORTNAME						= 'Isotopes[@@n@@].ShortName';
	const ISOTOPES_N_NAME							= 'Isotopes[@@n@@].Name';
	const ISOTOPES_N_HALFLIFE						= 'Isotopes[@@n@@].HalfLife';
	const ISOTOPES_NB								= 'Isotopes.Nb';
	
	const MAINLOGIC_CMD_INPUTSOFT_EXIT 				= 'G_MainLogic.cmd.Input_Soft.Exit';
	const MAINLOGIC_CMD_INPUTSOFT_INJECTSPEED 		= 'G_MainLogic.cmd.Input_Soft.Inject_Speed';
	const MAINLOGIC_CMD_INPUTSOFT_LOADMEDICAMENT	= 'G_MainLogic.cmd.Input_Soft.Load_Medicament';
	const MAINLOGIC_CMD_INPUTSOFT_LOADSEQUENCE		= 'G_MainLogic.cmd.Input_Soft.Load_Sequence';
	const MAINLOGIC_CMD_INPUTSOFT_VALCONNECTIONKITP	= 'G_MainLogic.cmd.Input_Soft.Val_Connection_Kit_P';
	const MAINLOGIC_CMD_INPUTSOFT_KITPATIENTSERIAL	= 'G_MainLogic.cmd.Input_Soft.Kit_Patient_Serial';
	const MAINLOGIC_CMD_INPUTSOFT_KITSOURCESERIAL	= 'G_MainLogic.cmd.Input_Soft.Kit_Source_Serial';
	
	const MAINLOGIC_CMD_INPUTTRASYS_MEASUREDVALUE 	= 'G_MainLogic.cmd.Input_Trasys.Measured_Value';
	
	const MAINLOGIC_PAR_SERIALNUMBERSAMI 			= 'G_MainLogic.par.Serial_Number_SAMI';
	const MAINLOGIC_PAR_SERIALNUMBERACTIVI 			= 'G_MainLogic.par.Serial_Number_Activi';
	const MAINLOGIC_PAR_SOFTWAREVERSION 			= 'G_MainLogic.par.Software_Version';
	const MAINLOGIC_PAR_SYSTEMVERSION 				= 'G_MainLogic.par.System_Version';

	const MAINLOGIC_STATUS_DILUTIONEVOLUTION 		= 'G_MainLogic.status.Dilution_Evolution';
	const MAINLOGIC_STATUS_INJECTIONEVOLUTION 		= 'G_MainLogic.status.Injection_Evolution';
	const MAINLOGIC_STATUS_RESTARTTYPE 				= 'G_MainLogic.status.Restart_Type';
	const MAINLOGIC_STATUS_SAMPLINGEVOLUTION 		= 'G_MainLogic.status.Sampling_Evolution';
	
	const MEDICAMENT_ACTUAL_ACTVOL 					= 'G_Medicament.Actual.Act_Vol';
	const MEDICAMENT_ACTUAL_ACTDT 					= 'G_Medicament.Actual.Act_DT';
	const MEDICAMENT_CALCULATION_CACTDISPO 			= 'G_Medicament.Calculation.C_Act_Dispo';
	const MEDICAMENT_INPUT_ACT						= 'G_Medicament.Input.Act';
	const MEDICAMENT_INPUT_ACTVOL					= 'G_Medicament.Input.Act_Vol';
	const MEDICAMENT_INPUT_ACTDT					= 'G_Medicament.Input.Act_DT';
	const MEDICAMENT_INPUT_DTCALIB					= 'G_Medicament.Input.DT_Calib';
	const MEDICAMENT_INPUT_DTEND					= 'G_Medicament.Input.DT_End';
	const MEDICAMENT_INPUT_NAME						= 'G_Medicament.Input.Name';
	const MEDICAMENT_INPUT_NLOT						= 'G_Medicament.Input.N_Lot';
	const MEDICAMENT_INPUT_PERIOD					= 'G_Medicament.Input.Period';
	const MEDICAMENT_INPUT_VOL						= 'G_Medicament.Input.Vol';
	
	const PATIENT_ACTUAL_ACTTOINJ 					= 'G_Patient.Actual.ActToInj';
	
	const SUBPURGE_STATUS_EVOLUTION					= 'SubPurge.status.Evolution';
}
