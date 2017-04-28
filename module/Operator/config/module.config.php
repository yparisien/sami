<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'Operator\Controller\Activimeter'	=> 'Operator\Controller\ActivimeterController',
			'Operator\Controller\Inputdata'		=> 'Operator\Controller\InputdataController',
			'Operator\Controller\Interactrobot'	=> 'Operator\Controller\InteractrobotController',
			'Operator\Controller\Operator'		=> 'Operator\Controller\OperatorController',
		),
	),
	'router' => array(
		'routes' => array(
			'testprint' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/testprint',
					'defaults' => array(
						'__NAMESPACE__' => 'Operator\Controller',
						'controller'    => 'Operator',
						'action'        => 'testprint',
					),
				),
			),
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
						'timeout'		=> 'operator',
						'pagetype'		=> 'frontpage',
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
						'disable_links'	=> true,
						'pagetype'		=> 'frontpage',
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
						'disable_links'	=> true,
					),
				),
			),
			'activimeter' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/activimeter[/][:action][/:id]',
					'constraints' => array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'		=> '[0-9]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Manager\Controller',
						'controller'	=> 'Activimeter',
						'action'		=> 'index',
						'pagetype'		=> 'admin',
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
