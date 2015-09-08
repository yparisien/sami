<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link	  http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license	http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
	'version'	=> '0.1a',

	'router' => array(
		'routes' => array(
			'initrn' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/initrn',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'initrn',
					),
				),
			),
			'initping' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/initping',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'initping',
					),
				),
			),
			'inithml' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/inithml',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'inithml',
					),
				),
			),
			'initmu' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/initmu',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'initmu',
					),
				),
			),
			'initsp' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/initsp',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'initsp',
					),
				),
			),
			'initsks' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/initsks',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'initsks',
					),
				),
			),
			'initsd' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/initsd',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'initsd',
					),
				),
			),
			'home' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'index',
					),
				),
			),
			'restart' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'	=> '/restart',
					'defaults' => array(
						'controller' => 'Start\Controller\Index',
						'action'	 => 'restart',
					),
				),
			),
			// The following is a route to simplify getting started creating
			// new controllers and actions without needing to create a new
			// module. Simply drop new controllers in, and you can access them
			// using the path /application/:controller/:action
			'application' => array(
				'type'	=> 'Literal',
				'options' => array(
					'route'	=> '/start',
					'defaults' => array(
						'__NAMESPACE__' => 'Start\Controller',
						'controller'	=> 'Index',
						'action'		=> 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'default' => array(
						'type'	=> 'Segment',
						'options' => array(
							'route'	=> '/[:controller[/:action]]',
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action'	 => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
							),
						),
					),
				),
			),
		),
	),
	'service_manager' => array(
		'abstract_factories' => array(
			'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
			'Zend\Log\LoggerAbstractServiceFactory',
		),
		'aliases' => array(
			'translator' => 'MvcTranslator',
		),
	),
	'translator' => array(
		'locale' => 'en_US',
		'translation_file_patterns' => array(
			array(
				'type'	 => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.mo',
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'Start\Controller\Index'	=> 'Start\Controller\IndexController',
			'Start\Controller\Operator'	=> 'Start\Controller\OperatorController',
			'Start\Controller\Manager'	=> 'Start\Controller\ManagerController',
		),
	),
	'view_manager' => array(
		'display_not_found_reason'	=> true,
		'display_exceptions'		=> true,
		'doctype'					=> 'HTML5',
		'not_found_template'		=> 'error/404',
		'exception_template'		=> 'error/index',
		'template_map' => array(
			'layout/layout'			=> __DIR__ . '/../view/layout/layout.phtml',
			'layout/logged'			=> __DIR__ . '/../view/layout/logged.phtml',
			'start/index/index'		=> __DIR__ . '/../view/start/index/index.phtml',
			'error/403'				=> __DIR__ . '/../view/error/403.phtml',
			'error/404'				=> __DIR__ . '/../view/error/404.phtml',
			'error/index'			=> __DIR__ . '/../view/error/index.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
		'strategies'		=> array(
			'ViewJsonStrategy',
		),
	),
	// Placeholder for console routes
	'console' => array(
		'router' => array(
			'routes' => array(
			),
		),
	),
	// Configuration about the session
	'session'	=> array(
		'remember_me_seconds'	=> 86400,
		'use_cookies'			=> true,
		'cookie_httponly'		=> true,
	),
);
