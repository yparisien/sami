<?php
/**
 * Règles ACL en fonction des routes
 */
return array(
	'guest'		=> array(
		'auth',
		'home',
		'log',
	),
	'operator'	=> array(
		'inject',
		'operator',
		'setup',
		'bufferspace',
	),
	'manager'	=> array(
		'bufferspace',
		'drug',
		'examination',
		'manager',
		'radionuclide',
		'system',
		'user',
	),
);