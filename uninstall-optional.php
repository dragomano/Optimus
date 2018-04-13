<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

$smcFunc['db_query']('', '
	DELETE FROM {db_prefix}settings
	WHERE variable LIKE {string:setting}',
	array(
		'setting' => 'optimus_%',
	)
);

?>