<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

// Scheduled Tasks
$rows = array(
	array(
		'method' => 'ignore',
		'table_name' => '{db_prefix}scheduled_tasks',
		'columns' => array(
			'next_time'       => 'int',
			'time_offset'     => 'int',
			'time_regularity' => 'int',
			'time_unit'       => 'string',
			'disabled'        => 'int',
			'task'            => 'string',
			'callable'        => 'string'
		),
		'data' => array(0, 0, 1, 'd', 1, 'optimus_sitemap', 'Optimus::scheduledTask'),
		'keys' => array('id_task')
	)
);

foreach ($rows as $row)
	$smcFunc['db_insert']($row['method'], $row['table_name'], $row['columns'], $row['data'], $row['keys']);

if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';
