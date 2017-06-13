<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin privileges required.');

// Hooks
$hooks = array(
	'integrate_pre_include' => '$sourcedir/Subs-Optimus.php',
	'integrate_pre_load'    => 'load_optimus_hooks'
);

$call = 'add_integration_function';

foreach ($hooks as $hook => $function)
	$call($hook, $function);

// Some settings
$newSettings = array(
	'optimus_portal_compat'   => 0,
	'optimus_forum_index'     => $smcFunc['substr']($txt['forum_index'], 7),
	'optimus_description'     => $context['forum_name'],
	'optimus_templates'       => 'a:0:{}',
    'optimus_no_first_number' => 0,
	'optimus_sitemap_boards'  => 1,
	'optimus_sitemap_topics'  => 1,
	'optimus_meta'            => 'a:0:{}',
	'optimus_counters_css'    => '.copyright a>img {opacity: 0.3} .copyright a:hover>img {opacity: 1.0} #footerarea ul li.copyright {line-height: normal; padding: 0}',
	'optimus_ignored_actions' => 'admin,bookmarks,credits,helpadmin,pm,printpage',
);

$base = array();
foreach ($newSettings as $setting => $value) {
	if (!isset($modSettings[$setting]))
		$base[$setting] = $value;
}
updateSettings($base);

if (SMF == 'SSI')
	echo 'Database changes are complete! Please wait...';

?>