<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('ELK'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('ELK'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as PortaMx Forum\'s index.php and SSI.php files.');

if ((ELK == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

if (version_compare(PHP_VERSION, '5.6', '<'))
	die('This mod needs PHP 5.6 or greater. You will not be able to install/use this mod, contact your host and ask for a php upgrade.');

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
			'task'            => 'string'
		),
		'data' => array(0, 0, 1, 'd', 1, 'optimus_sitemap'),
		'keys' => array('id_task')
	)
);

$db = database();

foreach ($rows as $row)
	$db->insert($row['method'], $row['table_name'], $row['columns'], $row['data'], $row['keys']);

if (ELK == 'SSI')
	echo 'Database changes are complete! Please wait...';
