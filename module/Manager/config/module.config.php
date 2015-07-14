<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'Manager\Controller\Activimeter'	=> 'Manager\Controller\ActivimeterController',
			'Manager\Controller\Drug'			=> 'Manager\Controller\DrugController',
			'Manager\Controller\Examination'	=> 'Manager\Controller\ExaminationController',
			'Manager\Controller\Injector'		=> 'Manager\Controller\InjectorController',
			'Manager\Controller\Manager'		=> 'Manager\Controller\ManagerController',
			'Manager\Controller\Radionuclide'	=> 'Manager\Controller\RadionuclideController',
			'Manager\Controller\System'			=> 'Manager\Controller\SystemController',
			'Manager\Controller\User'			=> 'Manager\Controller\UserController',
		),
	),
	'router' => array(
		'routes' => array(
			'manager' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/manager[/][:action]',
					'constraints' => array(
						'action'		=> '[a-zA-Z][a-zA-Z0-9_-]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Manager\Controller',
						'controller'	=> 'Manager',
						'action'		=> 'index',
					),
				),
			),
			'user' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/user[/][:action][/:id]',
					'constraints' => array(
						'action'		=> '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'			=> '[0-9]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Manager\Controller',
						'controller'	=> 'User',
						'action'		=> 'index',
					),
				),
			),
			'radionuclide' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/radionuclide[/][:action][/:id]',
					'constraints' => array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'		=> '[0-9]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Manager\Controller',
						'controller'	=> 'Radionuclide',
						'action'		=> 'index',
					),
				),
			),
			'drug' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/drug[/][:action][/:id]',
					'constraints'		=> array(
						'action'		=> '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'			=> '[0-9]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Manager\Controller',
						'controller'	=> 'Drug',
						'action'		=> 'index',
					),
				),
			),
			'examination' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/examination[/][:action][/:id]',
					'constraints' => array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'		=> '[0-9]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Manager\Controller',
						'controller'	=> 'Examination',
						'action'		=> 'index',
					),
				),
			),
			'system' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/system[/][:action][/:id]',
					'constraints' => array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'		=> '[0-9]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Manager\Controller',
						'controller'	=> 'System',
						'action'		=> 'index',
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
					),
				),
			),
			'injector' => array(
				'type'	=> 'segment',
				'options' => array(
					// Change this to something specific to your module
					'route'	=> '/injector[/][:action][/:id]',
					'constraints' => array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'		=> '[0-9]*',
					),
					'defaults' => array(
						'__NAMESPACE__' => 'Manager\Controller',
						'controller'	=> 'Injector',
						'action'		=> 'index',
					),
				),
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'Manager' => __DIR__ . '/../view',
		),
	),
	'service_manager'	=> array(
		'aliases'		=> array(
			'Zend\Authentification\AuthentificationService'	=> 'my_auth_service',
		),
		'invokables'	=> array(
			'my_auth_service'	=> 'Zend\Authentication\AuthenticationService',
			'RobotService'		=> 'Manager\Robot\RobotService'
		),
	),
);
