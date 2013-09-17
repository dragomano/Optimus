<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
else if(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

// Removing settings
//$smcFunc['db_query']('', "DELETE FROM {db_prefix}settings WHERE variable LIKE 'optimus_%'");
$smcFunc['db_query']('', "DELETE FROM {db_prefix}scheduled_tasks WHERE task LIKE 'optimus_sitemap'");

// Hooks
$hooks = array(
	'integrate_pre_include' => '$sourcedir/Subs-Optimus.php',
	'integrate_admin_include' => '$sourcedir/Admin-Optimus.php',
	'integrate_load_theme' => 'optimus_home',
	'integrate_admin_areas' => 'optimus_admin_areas',
	'integrate_menu_buttons' => 'optimus_operations',
	'integrate_buffer' => 'optimus_buffer',
	'integrate_create_topic' => 'optimus_sitemap'
);

$call = 'remove_integration_function';

foreach ($hooks as $hook => $function)
	$call($hook, $function);
	
if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';
			
?>