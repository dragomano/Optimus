<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

db_extend('packages');
db_extend('extra');

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

foreach($tables as $table)
	$smcFunc['db_create_table']('{db_prefix}' . $table['name'], $table['columns'], $table['indexes'], array(), 'ignore');

// Optimus description for topics table
$smcFunc['db_add_column'](
	'{db_prefix}topics',
	array(
		'name'    => 'optimus_description',
		'type'    => 'varchar',
		'size'    => 255,
		'null'    => true,
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
		'null'    => true,
		'default' => ''
	),
	array(),
	'do_nothing'
);

if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';
