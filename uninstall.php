<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('PMX'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('PMX'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as PortaMx Forum\'s index.php and SSI.php files.');

if ((PMX == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

$pmxcFunc['db_query']('', "DELETE FROM {db_prefix}settings WHERE variable LIKE 'optimus_%'");
$pmxcFunc['db_query']('', "DELETE FROM {db_prefix}scheduled_tasks WHERE task LIKE 'optimus_sitemap'");
