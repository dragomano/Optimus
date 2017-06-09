<?php

global $context, $user_info, $smcFunc, $txt;

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF')) {
    require_once(dirname(__FILE__) . '/SSI.php');
} elseif (!defined('SMF')) {
    die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');
}

if ((SMF == 'SSI') && !$user_info['is_admin']) {
    die('Admin privileges required.');
}

// List settings
$mod_settings = array(
	'optimus_portal_compat'   => 0,
	'optimus_forum_index'     => $smcFunc['substr']($txt['forum_index'], 7),
	'optimus_description'     => $context['forum_name'],
	'optimus_templates'       => 'a:0:{}',
	'optimus_sitemap_topics'  => 1,
	'optimus_meta'            => 'a:0:{}',
	'optimus_ignored_actions' => 'admin,bookmarks,credits,helpadmin,pm,printpage',
);

// Update mod settings if applicable
foreach ($mod_settings as $new_setting => $new_value) {
    if (!isset($modSettings[$new_setting])) {
        updateSettings(array($new_setting => $new_value));
    }
}

if (SMF == 'SSI') {
    echo 'Database changes are complete! <a href="/">Return to the main page</a>.';
}
			
?>