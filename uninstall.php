<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('ELK'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('ELK'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as PortaMx Forum\'s index.php and SSI.php files.');

if ((ELK == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

$db = database();

$db->query('', "DELETE FROM {db_prefix}settings WHERE variable LIKE 'optimus_%'");
$db->query('', "DELETE FROM {db_prefix}scheduled_tasks WHERE task LIKE 'optimus_sitemap'");
