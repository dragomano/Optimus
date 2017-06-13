<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
else if(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

// Removing settings
//$smcFunc['db_query']('', "DELETE FROM {db_prefix}settings WHERE variable LIKE 'optimus_%'");

// Hooks
$hooks = array(
	'integrate_pre_include' => '$sourcedir/Subs-Optimus.php',
	'integrate_pre_load'    => 'load_optimus_hooks'
);

$call = 'remove_integration_function';

foreach ($hooks as $hook => $function)
	$call($hook, $function);
	
if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';
			
?>