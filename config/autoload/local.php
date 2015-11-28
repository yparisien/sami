<?php
return array(
	/**
	 * Configuration de la base de donnée
	 */
	'db' => array(
		'username' => 'sami',
		'password' => 'sami',
	),
	/**
	 * Configuration du robot
	 */
	'robot' => array(
		'simulated'		=> true,												//Active (true) / Desactive (false) le mode de simulation
		'simulation'	=> array(
			'init' => array( 													//Paramètres de la séquence de démarrage
				'trytogood'			=> '1',										//Nb d'essai avant OK
				'robotinerror'		=> '0',										//Déamarrage du robot en erreur 1=Erreur 0=OK
				'roboterrorcode'	=> '2',										//Code de démarrage renvoyé par le robot en cas  d'erreur au démarrage
				'hasmed' 			=> '0',										//Un médicament est chargé (1) ou non (0) dans le robot
				'loadedmed'			=> 'Flucis',								//Nom du médicament chargé dans le robot
				'loadedsourcekit'	=> '0',										//Kit source chargé (1) ou non (0)
				'sourcekitscanned'	=> '0',										//Kit source scanné (1) ou non (0)
				'sourcekitserial'	=> '013366081202181517201231305010241115EG',//Numéro de série du kit source chargé et scanné
				'unit'				=> 'mbq',									//Unit de mesure du robot mbq ou mci
				'restarttype'		=> '8',										//Point de redemarrage
				'patientid'			=> '67',									//Id du patient chargé lors d'un redemarrage
			),
		),
	),
	/**
	 * Configuration du clavier virtuel
	 */
	'virtualkeyboard' => array(
		'enable'	=> true,	//Clavier virtuel activé (true) ou non (false)
		'size'		=> 1.55,	//Taille du clavier
	),
	/**
	 * Configuration des imports/exports de fichiers
	 */
	'import_export' => array(
		'upload_path'			=> __DIR__ . '/../../public/tmp',			//Dossier de réception des fichiers entrants
		'import_archive_path'	=> __DIR__ . '/../../data/import_archives', //Dossier d'archive des fichiers entrants
		'export_archive_path'	=> __DIR__ . '/../../data/export_archives', //Dossier d'archive des fichiers sortants
	),
	/**
	 * Configuration des locks-screen (en secondes)
	 */
	'timeout' => array(
			'operator'		=> 60 * 60, //Temps d'inactivité avec lock des écrans opérateurs
			'supervisor'	=> 60 * 60, //Temps d'inactivité avec lock des écrans superviseurs
			'robot'			=> 1, 		//Temps d'attente entre les essais de démarrage
	),
);
