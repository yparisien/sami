<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'Bufferspace\Controller\Monitor' => 'Bufferspace\Controller\MonitorController',
			'Bufferspace\Controller\Exportbuffer' => 'Bufferspace\Controller\ExportbufferController',
		),
	),
	'router' => array(
		'routes' => array(
			'exportbuffer' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/extract[/][:action]',
					'constraints'	=> array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Bufferspace\Controller',
						'controller'	=> 'Exportbuffer',
						'action'		=> 'index',
					),
				),
			),
			'bufferspace' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/monitor[/][:action]',
					'constraints'	=> array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Bufferspace\Controller',
						'controller'	=> 'Monitor',
						'action'		=> 'index',
					),
				),
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'Bufferspace' => __DIR__ . '/../view',
		),
	),
);
