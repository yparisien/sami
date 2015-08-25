<?php
return array(
	'db' => array(
		'username' => 'sami',
		'password' => 'sami',
	),
	'robot' => array(
		//Set to fasle when robot is connected
		'simulated' => true,
	),
	'virtualkeyboard' => array(
		'enable'	=> false,	
	),
	'import_export' => array(
		'upload_path'			=> __DIR__ . '/../../public/tmp',
		'import_archive_path'	=> __DIR__ . '/../../data/import_archives',
		'export_archive_path'	=> __DIR__ . '/../../data/export_archives',
	),
	'timeout' => array(
			'operator'		=> 10, 
			'supervisor'	=> 5,
	),
);
