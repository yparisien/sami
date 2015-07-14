<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'Auth\Controller\Auth' => 'Auth\Controller\AuthController',
			'Auth\Controller\Login' => 'Auth\Controller\LoginController',
		),
	),
	'router' => array(
		'routes' => array(
			'log' => array(
				'type'	=> 'segment',
				'options' => array(
					'route'	=> '/login[/][:action][/:err]',
					'constraints'	=> array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
						'err'		=> '[0-9]*',
					),
					'defaults' => array(
						'controller'	=> 'Auth\Controller\Login',
						'action'		=> 'index',
					),
				),
			),
			'auth' => array(
				'type'	=> 'segment',
				'options'	=> array(
					'route'	=> '/auth[/][:action]',
					'constraints'	=> array(
						'action'	=> '[a-zA-Z][a-zA-Z0-9_-]*',
					),
					'defaults'	=> array(
						'controller'	=> 'Auth\Controller\Auth',
						'action'		=> 'index',
					),
				),
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'Auth' => __DIR__ . '/../view',
		),
	),
);
