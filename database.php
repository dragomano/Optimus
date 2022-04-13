<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if (version_compare(PHP_VERSION, '7.4', '<'))
	die('This mod needs PHP 7.4 or greater. You will not be able to install/use this mod, contact your host and ask for a php upgrade.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

db_extend('packages');

$tables[] = array(
	'name'    => 'optimus_keywords',
	'columns' => array(
		array(
			'name'     => 'id',
			'type'     => 'int',
			'size'     => 10,
			'unsigned' => true,
			'auto'     => true
		),
		array(
			'name' => 'name',
			'type' => 'varchar',
			'size' => 255,
			'null' => false
		)
	),
	'indexes' => array(
		array(
			'type'    => 'primary',
			'columns' => array('id')
		),
		array(
			'type'    => 'unique',
			'columns' => array('name')
		)
	)
);

$tables[] = array(
	'name'    => 'optimus_log_keywords',
	'columns' => array(
		array(
			'name'     => 'keyword_id',
			'type'     => 'int',
			'size'     => 10,
			'unsigned' => true
		),
		array(
			'name'     => 'topic_id',
			'type'     => 'mediumint',
			'size'     => 8,
			'unsigned' => true
		),
		array(
			'name'     => 'user_id',
			'type'     => 'mediumint',
			'size'     => 8,
			'unsigned' => true
		)
	),
	'indexes' => array(
		array(
			'type'    => 'primary',
			'columns' => array('keyword_id', 'topic_id', 'user_id')
		)
	)
);

$tables[] = array(
	'name'    => 'optimus_search_terms',
	'columns' => array(
		array(
			'name'     => 'id_term',
			'type'     => 'int',
			'size'     => 11,
			'unsigned' => true,
			'auto'     => true
		),
		array(
			'name' => 'phrase',
			'type' => 'varchar',
			'size' => 255,
			'null' => false
		),
		array(
			'name'     => 'hit',
			'type'     => 'int',
			'size'     => 11,
			'default'  => 1,
			'unsigned' => true
		)
	),
	'indexes' => array(
		array(
			'type'    => 'primary',
			'columns' => array('id_term')
		)
	)
);

foreach($tables as $table)
	$smcFunc['db_create_table']('{db_prefix}' . $table['name'], $table['columns'], $table['indexes'], array(), 'ignore');

// Optimus description for topics table
$smcFunc['db_add_column'](
	'{db_prefix}topics',
	array(
		'name'    => 'optimus_description',
		'type'    => 'varchar',
		'size'    => 255,
		'default' => ''
	),
	array(),
	'do_nothing'
);

// Optimus og-image for boards table
$smcFunc['db_add_column'](
	'{db_prefix}boards',
	array(
		'name'    => 'optimus_og_image',
		'type'    => 'varchar',
		'size'    => 255,
		'default' => ''
	),
	array(),
	'do_nothing'
);

updateSettings(array('optimus_sitemap_enable' => 0));

if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';
