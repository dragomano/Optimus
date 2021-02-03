<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if (version_compare(PHP_VERSION, '5.6', '<'))
	die('This mod needs PHP 5.6 or greater. You will not be able to install/use this mod, contact your host and ask for a php upgrade.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

// Hooks
$hooks = array(
	'integrate_pre_include' => '$sourcedir/Optimus/app.php'
);

if (!empty($context['uninstalling']))
	$call = 'remove_integration_function';
else
	$call = 'add_integration_function';

foreach ($hooks as $hook => $function)
	$call($hook, $function);

if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';
