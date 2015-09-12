<?php
return array(
	'db' => array(
		'username' => 'sami',
		'password' => 'sami',
	),
	'robot' => array(
		//Set to fasle when robot is connected
		'simulated' => true,
		'simulation' => array(
			'init' => array(
				'hasmed' 			=> true,
				'loadedmed'			=> 'GLU',
				'loadedsourcekit'	=> true,	
				'sourcekitscanned'	=> true,
				'sourcekitserial'	=> '90830284902384238904823098402398402',
				'unit'				=> 'mbq',
			),
// 			'init' => array(
// 				'hasmed' 			=> false,
// 				'loadedmed'			=> null,
// 				'loadedsourcekit'	=> false,
// 				'sourcekitscanned'	=> false,
// 				'sourcekitserial'	=> null,
// 				'unit'				=> null,
// 			),
		),
	),
	'virtualkeyboard' => array(
		'enable'	=> false,
		'size'		=> 1.75,	
	),
	'import_export' => array(
		'upload_path'			=> __DIR__ . '/../../public/tmp',
		'import_archive_path'	=> __DIR__ . '/../../data/import_archives',
		'export_archive_path'	=> __DIR__ . '/../../data/export_archives',
	),
	'timeout' => array(
			'operator'		=> 3 * 60, 
			'supervisor'	=> 1 * 60,
	),
);
