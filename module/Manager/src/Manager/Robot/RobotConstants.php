<?php
namespace Manager\Robot;

/**
 * Classe des constantes de communication avec le robot
 * @author yohann.parisien
 *
 */
class RobotConstants {
	
	const ISOTOPES_N_IDISOTOPE								= 'Isotopes[@@n@@].ID_Isotope';
	const ISOTOPES_N_SHORTNAME								= 'Isotopes[@@n@@].ShortName';
	const ISOTOPES_N_NAME									= 'Isotopes[@@n@@].Name';
	const ISOTOPES_N_HALFLIFE								= 'Isotopes[@@n@@].HalfLife';
	const ISOTOPES_NB										= 'Isotopes_Nb';
	
	const MAINLOGIC_CMD_INPUTSOFT_ADJUSTSAMPLING			= 'G_MainLogic.cmd.Input_Soft.Adjust_Sampling';
	const MAINLOGIC_CMD_INPUTSOFT_CHANGEDATETIME			= 'G_MainLogic.cmd.Input_Soft.ChangeDateTime';
	const MAINLOGIC_CMD_INPUTSOFT_DATETIMEIHM				= 'G_MainLogic.cmd.Input_Soft.DateTimeIHM';
	const MAINLOGIC_CMD_INPUTSOFT_DILUTIONSEQUENCE			= 'G_MainLogic.cmd.Input_Soft.Dilution_Sequence';
	const MAINLOGIC_CMD_INPUTSOFT_EXIT 						= 'G_MainLogic.cmd.Input_Soft.Exit';
	const MAINLOGIC_CMD_INPUTSOFT_INJECTIONSEQUENCE			= 'G_MainLogic.cmd.Input_Soft.Injection_Sequence';
	const MAINLOGIC_CMD_INPUTSOFT_INJECTSPEED 				= 'G_MainLogic.cmd.Input_Soft.Inject_Speed';
	const MAINLOGIC_CMD_INPUTSOFT_KITSOURCESERIAL			= 'G_MainLogic.cmd.Input_Soft.Kit_Source_Serial';
	const MAINLOGIC_CMD_INPUTSOFT_LOADMEDICAMENT			= 'G_MainLogic.cmd.Input_Soft.Load_Medicament';
	const MAINLOGIC_CMD_INPUTSOFT_LOADPATIENT				= 'G_MainLogic.cmd.Input_Soft.Load_Patient';
	const MAINLOGIC_CMD_INPUTSOFT_LOADPURGE					= 'G_MainLogic.cmd.Input_Soft.Load_Purge';
	const MAINLOGIC_CMD_INPUTSOFT_LOADSEQUENCE				= 'G_MainLogic.cmd.Input_Soft.Load_Sequence';
	const MAINLOGIC_CMD_INPUTSOFT_PLAYINJECTION				= 'G_MainLogic.cmd.Input_Soft.Play_Injection';
	const MAINLOGIC_CMD_INPUTSOFT_RINSINGSEQUENCE			= 'G_MainLogic.cmd.Input_Soft.Rinsing_Sequence';
	const MAINLOGIC_CMD_INPUTSOFT_SAMPLINGSEQUENCE			= 'G_MainLogic.cmd.Input_Soft.Sampling_Sequence';
	const MAINLOGIC_CMD_INPUTSOFT_STOPINJECTION				= 'G_MainLogic.cmd.Input_Soft.Stop_Injection';
	const MAINLOGIC_CMD_INPUTSOFT_VALCONNECTIONKITP			= 'G_MainLogic.cmd.Input_Soft.Val_Connection_Kit_P';
	const MAINLOGIC_CMD_INPUTSOFT_VALPATIENTCONNECTION		= 'G_MainLogic.cmd.Input_Soft.Val_Patient_Connection';
	const MAINLOGIC_CMD_INPUTSOFT_VALPATIENTDISCONNECTION	= 'G_MainLogic.cmd.Input_Soft.Val_Patient_Deconnection';
	const MAINLOGIC_CMD_INPUTSOFT_VALSAMPLING				= 'G_MainLogic.cmd.Input_Soft.Val_Sampling';
	
	const MAINLOGIC_CMD_INPUTTRASYS_MEASUREDVALUE 			= 'G_MainLogic.cmd.Input_Trasys.Measured_Value';
	
	const MAINLOGIC_PAR_SERIALNUMBERSAMI 					= 'G_MainLogic.par.Serial_Number_SAMI';
	const MAINLOGIC_PAR_SERIALNUMBERACTIVI 					= 'G_MainLogic.par.Serial_Number_Activi';
	const MAINLOGIC_PAR_SOFTWAREVERSION 					= 'G_MainLogic.par.Software_Version';
	const MAINLOGIC_PAR_SYSTEMVERSION 						= 'G_MainLogic.par.System_Version';
	const MAINLOGIC_PAR_MEASUREUNIT							= 'G_MainLogic.par.Measure_Unit';
	const MAINLOGIC_PAR_MAXACTIVITY							= 'G_MainLogic.par.Max_Act';
	//Faire le mode demo

	const MAINLOGIC_STATUS_ACTIVE 							= 'G_MainLogic.status.Active';
	const MAINLOGIC_STATUS_DILUTIONEVOLUTION 				= 'G_MainLogic.status.Dilution_Evolution';
	const MAINLOGIC_STATUS_ERROR							= 'G_MainLogic.status.Error';
	const MAINLOGIC_STATUS_ERRORID							= 'G_MainLogic.status.ErrorID';
	const MAINLOGIC_STATUS_GETMEDICAMENTLOADED 				= 'G_MainLogic.status.Get_Medicament_Loaded';
	const MAINLOGIC_STATUS_GETSERIALKITSOURCE 				= 'G_MainLogic.status.Get_Serial_KitSource';
	const MAINLOGIC_STATUS_HASKITSOURCELOADED 				= 'G_MainLogic.status.Has_KitSource_Loaded';
	const MAINLOGIC_STATUS_HASKITSOURCESCANNED 				= 'G_MainLogic.status.Has_KitSource_Scanned';
	const MAINLOGIC_STATUS_HASMEDICAMENTLOADED 				= 'G_MainLogic.status.Has_Medicament_Loaded';
	const MAINLOGIC_STATUS_INJECTIONEVOLUTION 				= 'G_MainLogic.status.Injection_Evolution';
	const MAINLOGIC_STATUS_RESTARTTYPE 						= 'G_MainLogic.status.Restart_Type';
	const MAINLOGIC_STATUS_SAMPLINGEVOLUTION 				= 'G_MainLogic.status.Sampling_Evolution';
	
	const MEDICAMENT_ACTUAL_ACTVOL 							= 'G_Medicament.Actual.Act_Vol';
	const MEDICAMENT_ACTUAL_ACTDT 							= 'G_Medicament.Actual.Act_DT';
	const MEDICAMENT_CALCULATION_CACTDISPO 					= 'G_Medicament.Calculation.C_Act_Dispo';
	const MEDICAMENT_INPUT_ACT								= 'G_Medicament.Input.Act';
	const MEDICAMENT_INPUT_ACTVOL							= 'G_Medicament.Input.Act_Vol';
	const MEDICAMENT_INPUT_ACTDT							= 'G_Medicament.Input.Act_DT';
	const MEDICAMENT_INPUT_DCI								= 'G_Medicament.Input.DCI';
	const MEDICAMENT_INPUT_DTCALIB							= 'G_Medicament.Input.DT_Calib';
	const MEDICAMENT_INPUT_DTEND							= 'G_Medicament.Input.DT_End';
	const MEDICAMENT_INPUT_NAME								= 'G_Medicament.Input.Name';
	const MEDICAMENT_INPUT_NLOT								= 'G_Medicament.Input.N_Lot';
	const MEDICAMENT_INPUT_PERIOD							= 'G_Medicament.Input.Period';
	const MEDICAMENT_INPUT_VOL								= 'G_Medicament.Input.Vol';
	const MEDICAMENT_INPUT_RADIONUCLIDEID					= 'G_Medicament.Input.ID';
	
	const PATIENT_ACTUAL_ACTTOINJ 							= 'G_Patient.Actual.ActToInj';
	const PATIENT_ACTUAL_PATIENTID							= 'G_Patient.Actual.Patient_ID';
	
	const KIT_VAL_KITSOURCE									= 'G_Kit.Val_Kit_S';
	
	const SUBPURGE_STATUS_EVOLUTION							= 'SubPurge.status.Evolution';
	
	const ERROR_CODE_1										= 'Fiole non conforme';
	const ERROR_CODE_2										= 'Erreur sur les informations pour le calcul de l\'activité volumique';
	const ERROR_CODE_3										= 'Erreur sur les informations pour le calcul de l\'activité total à heure de calibration';
	const ERROR_CODE_4										= 'Erreur sur les informations pour le calcul du volume de la fiole';
	
	const ERROR_CODE_5										= 'Disjoncteur général tombé';
	const ERROR_CODE_6										= 'Disjoncteur général tombé et batterie faible. Injection interdite !';
	const ERROR_CODE_7										= 'Batterie faible laisser recharger pour pouvoir injecter';
	
	const ERROR_CODE_11										= 'Problème sur carte X67 SM 2436';
	const ERROR_CODE_12										= 'Problème sur carte x67 DM 1321';
	const ERROR_CODE_13										= 'Problème sur carte DI9371';
	const ERROR_CODE_14										= 'Problème sur carte DO9322';
	const ERROR_CODE_15										= 'Problème sur carte BT9100';
	
	const ERROR_CODE_60										= 'Temps de monté trop long de l\'ascenseur';
	const ERROR_CODE_61										= 'Temps de descente trop long';
	const ERROR_CODE_62										= 'ARU enclenché pendant le déplacement de l\'ascenseur';
	
	const ERROR_CODE_65										= 'Impossible de mettre le moteur 3 voies en fonctionnement';
	const ERROR_CODE_66										= 'Impossible de faire la mise à zéro du moteur 3 voies';
	const ERROR_CODE_67										= 'Défaut déplacement 3 voies NACL vers FDG lors de la purge';
	const ERROR_CODE_68										= 'Défaut déplacement 3 voies FDG vers NACL lors de la purge';
	
	const ERROR_CODE_70										= 'Impossible de mettre le moteur tire seringue en fonctionnement';
	const ERROR_CODE_71										= 'Impossible de faire la mise à zéro du moteur tire seringue';
	const ERROR_CODE_72										= 'Défaut lors du prélèvement du NACL pendant la purge';
	const ERROR_CODE_73										= 'Défaut lors de la purge de l\'aiguille';
	const ERROR_CODE_74										= 'Défaut lors de la purge de la partie patient du kit source';
	const ERROR_CODE_75										= 'Défaut lors du prélèvement NACL pour purge kit patient';
	const ERROR_CODE_76										= 'Défaut lors de la pruge du kit patient';
}
