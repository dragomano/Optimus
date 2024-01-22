<?php

global $user_info, $smcFunc;

if (file_exists(dirname(__FILE__) . '/SSI.php') && ! defined('SMF'))
	require_once dirname(__FILE__) . '/SSI.php';
elseif (! defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if (version_compare(PHP_VERSION, '8.0', '<'))
	die('This mod needs PHP 8.0 or greater. You will not be able to install/use this mod, contact your host and ask for a php upgrade.');

if ((SMF === 'SSI') && ! $user_info['is_admin'])
	die('Admin privileges required.');

$tables[] = [
	'name'    => 'optimus_keywords',
	'columns' => [
		[
			'name'     => 'id',
			'type'     => 'int',
			'size'     => 10,
			'unsigned' => true,
			'auto'     => true
		],
		[
			'name' => 'name',
			'type' => 'varchar',
			'size' => 255,
			'null' => false
		]
	],
	'indexes' => [
		[
			'type'    => 'primary',
			'columns' => ['id']
		],
		[
			'type'    => 'unique',
			'columns' => ['name']
		]
	]
];

$tables[] = [
	'name'    => 'optimus_log_keywords',
	'columns' => [
		[
			'name'     => 'keyword_id',
			'type'     => 'int',
			'size'     => 10,
			'unsigned' => true
		],
		[
			'name'     => 'topic_id',
			'type'     => 'mediumint',
			'size'     => 8,
			'unsigned' => true
		],
		[
			'name'     => 'user_id',
			'type'     => 'mediumint',
			'size'     => 8,
			'unsigned' => true
		]
	],
	'indexes' => [
		[
			'type'    => 'primary',
			'columns' => ['keyword_id', 'topic_id', 'user_id']
		]
	]
];

$tables[] = [
	'name'    => 'optimus_search_terms',
	'columns' => [
		[
			'name'     => 'id_term',
			'type'     => 'int',
			'size'     => 11,
			'unsigned' => true,
			'auto'     => true
		],
		[
			'name' => 'phrase',
			'type' => 'varchar',
			'size' => 255,
			'null' => false
		],
		[
			'name'     => 'hit',
			'type'     => 'int',
			'size'     => 11,
			'default'  => 1,
			'unsigned' => true
		]
	],
	'indexes' => [
		[
			'type'    => 'primary',
			'columns' => ['id_term']
		]
	]
];

db_extend('packages');

foreach ($tables as $table) {
	$smcFunc['db_create_table']('{db_prefix}' . $table['name'], $table['columns'], $table['indexes']);
}

// Optimus description for topics table
$smcFunc['db_add_column'](
	'{db_prefix}topics',
	[
		'name'    => 'optimus_description',
		'type'    => 'varchar',
		'size'    => 255,
		'default' => ''
	],
	[],
	'do_nothing'
);

// Optimus og-image for boards table
$smcFunc['db_add_column'](
	'{db_prefix}boards',
	[
		'name'    => 'optimus_og_image',
		'type'    => 'varchar',
		'size'    => 255,
		'default' => ''
	],
	[],
	'do_nothing'
);

updateSettings(['optimus_sitemap_enable' => 0]);

if (SMF === 'SSI')
	echo 'Database changes are complete! Please wait...';
