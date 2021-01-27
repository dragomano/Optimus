<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if (version_compare(PHP_VERSION, '5.6', '<'))
	die('This mod needs PHP 5.6 or greater. You will not be able to install/use this mod, contact your host and ask for a php upgrade.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

$smcFunc['db_query']('', "DELETE FROM {db_prefix}settings WHERE variable LIKE 'op_%' OR variable LIKE 'optimus_%'");

// Scheduled Tasks
if (empty($context['uninstalling'])) {
	$smcFunc['db_insert']('ignore',
		'{db_prefix}scheduled_tasks',
		array(
			'next_time'       => 'int',
			'time_offset'     => 'int',
			'time_regularity' => 'int',
			'time_unit'       => 'string',
			'disabled'        => 'int',
			'task'            => 'string'
		),
		array(0, 0, 1, 'w', 1, 'optimus_sitemap'),
		array('id_task')
	);
}

if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';
