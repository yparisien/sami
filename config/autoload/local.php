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
		'simulated'		=> true, //Active (true) / Desactive (false) le mode de simulation
		'simulation'	=> array(
			'init' => array( //Paramètres de la séquence de démarrage
				'hasmed' 			=> false, //Un médicament est chargé (true) ou non (false) dans le robot
				'loadedmed'			=> 'GLU', //Nom DCI du médicament chargé dans le robot
				'loadedsourcekit'	=> true, //Kit source chargé (true) ou non (false)
				'sourcekitscanned'	=> true, //Kit source scanné (true) ou non (false)
				'sourcekitserial'	=> '90830284902384238904823098402398402', //Numéro de série du kit source chargé et scanné
				'unit'				=> 'mbq', //Unit de mesure du robot mbq ou mci
			),
		),
	),
	/**
	 * Configuration du clavier virtuel
	 */
	'virtualkeyboard' => array(
		'enable'	=> false, //Clavier virtuel activé (true) ou non (false)
		'size'		=> 1.55, //Taille du clavier
	),
	/**
	 * Configuration des imports/exports de fichiers
	 */
	'import_export' => array(
		'upload_path'			=> __DIR__ . '/../../public/tmp', //Dossier de réception des fichiers entrants
		'import_archive_path'	=> __DIR__ . '/../../data/import_archives', //Dossier d'archive des fichiers entrants
		'export_archive_path'	=> __DIR__ . '/../../data/export_archives', //Dossier d'archive des fichiers sortants
	),
	/**
	 * Configuration des locks-screen (en secondes)
	 */
	'timeout' => array(
			'operator'		=> 60 * 60, //Temps d'inactivité avec lock des écrans opérateurs
			'supervisor'	=> 60 * 60, //Temps d'inactivité avec lock des écrans superviseurs
	),
);
