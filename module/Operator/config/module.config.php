<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'Operator\Controller\Operator'		=> 'Operator\Controller\OperatorController',
			'Operator\Controller\Inputdata'		=> 'Operator\Controller\InputdataController',
			'Operator\Controller\Interactrobot'	=> 'Operator\Controller\InteractrobotController',
		),
	),
	'router' => array(
		'routes' => array(
			'operator' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/operator[/][:action]',
					'constraints' => array(
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'action'	 => '[a-zA-Z][a-zA-Z0-9_-]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Operator\Controller',
						'controller'	=> 'Operator',
						'action'		=> 'index',
					),
				),
			),
			'setup' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/setup[/][:action][/:confirm]',
					'constraints' => array(
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'action'	 => '[a-zA-Z][a-zA-Z0-9_-]*',
						'confirm'	 => '[0-1]',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Operator\Controller',
						'controller'	=> 'Inputdata',
						'action'		=> 'index',
					),
				),
			),
			'inject' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/inject[/][:action][/:confirm]',
					'constraints' => array(
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'action'	 => '[a-zA-Z][a-zA-Z0-9_-]*',
						'confirm'	 => '[0-1]',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Operator\Controller',
						'controller'	=> 'Interactrobot',
						'action'		=> 'index',
					),
				),
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'Operator' => __DIR__ . '/../view',
		),
	),
);
