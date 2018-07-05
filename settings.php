<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('PMX'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('PMX'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as PortaMx Forum\'s index.php and SSI.php files.');

if ((PMX == 'SSI') && !$user_info['is_admin'])
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
			'callable'        => 'Optimus::scheduledTask'
		),
		'data' => array(0, 0, 1, 'd', 1, 'optimus_sitemap'),
		'keys' => array('id_task')
	)
);

foreach ($rows as $row)
	$pmxcFunc['db_insert']($row['method'], $row['table_name'], $row['columns'], $row['data'], $row['keys']);

if (PMX == 'SSI')
	echo 'Database changes are complete! Please wait...';
