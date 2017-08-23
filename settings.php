<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

// Some settings
$newSettings = array(
	'optimus_forum_index'     => $smcFunc['substr']($txt['forum_index'], 7),
	'optimus_description'     => $context['forum_name'],
	'optimus_templates'       => 'a:0:{}',
	'optimus_no_first_number' => 1,
	'optimus_sitemap_boards'  => 1,
	'optimus_sitemap_topics'  => 1,
	'optimus_meta'            => 'a:0:{}',
	'optimus_counters_css'    => '.copyright a>img {opacity: 0.3} .copyright a:hover>img {opacity: 1.0}',
	'optimus_ignored_actions' => 'admin,bookmarks,credits,helpadmin,pm,printpage'
);

$base = array();
foreach ($newSettings as $setting => $value) {
	if (!isset($modSettings[$setting]))
		$base[$setting] = $value;
}

if (empty($context['uninstalling']))
	updateSettings($base);

// Scheduled Tasks
$rows = array();
$rows[] = array(
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
);

if (!empty($rows) && empty($context['uninstalling']))
	foreach ($rows as $row)
		$smcFunc['db_insert']($row['method'], $row['table_name'], $row['columns'], $row['data'], $row['keys']);

if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';

?>