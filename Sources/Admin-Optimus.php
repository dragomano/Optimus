<?php

/**
 * Admin-Optimus.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2017 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 1.9.6
 */

if (!defined('SMF'))
	die('Hacking attempt...');

function optimus_admin_areas(&$admin_areas)
{
	global $txt;
	
	$admin_areas['config']['areas']['optimus'] =
		array(
			'label'    => $txt['optimus_title'],
			'function' => create_function(null, 'optimus_area_settings();'),
			'icon'     => 'maintain.gif',
			'subsections' => array(
				'common'   => array($txt['optimus_common_title']),
				'extra'    => array($txt['optimus_extra_title']),
				'verify'   => array($txt['optimus_verification_title']),
				'counters' => array($txt['optimus_counters']),
				'robots'   => array($txt['optimus_robots_title']),
				'map'      => array($txt['optimus_sitemap_title'])
			)
		);
}

function optimus_area_settings()
{
	global $sourcedir, $context, $txt, $scripturl;

	require_once($sourcedir . '/ManageSettings.php');

	$context['page_title'] = $txt['optimus_main'];

	loadTemplate('Optimus', 'optimus');

	$subActions = array(
		'common'   => 'optimus_common_settings',
		'extra'    => 'optimus_extra_settings',
		'verify'   => 'optimus_verify_settings',
		'counters' => 'optimus_counters_settings',
		'robots'   => 'optimus_robots_settings',
		'map'      => 'optimus_map_settings'
	);

	loadGeneralSettingParameters($subActions, 'common');

	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['optimus_title'],
		'tabs' => array(
			'common' => array(
				'description' => $txt['optimus_common_desc'],
			),
			'extra' => array(
				'description' => $txt['optimus_extra_desc'],
			),
			'verify' => array(
				'description' => $txt['optimus_verification_desc'],
			),
			'counters' => array(
				'description' => $txt['optimus_counters_desc'],
			),
			'robots' => array(
				'description' => $txt['optimus_robots_desc'],
			),
			'map' => array(
				'description' => sprintf($txt['optimus_sitemap_desc'], $scripturl . '?action=admin;area=scheduledtasks;' . $context['session_var'] . '=' . $context['session_id']),
			),
		),
	);

	call_user_func($subActions[$_REQUEST['sa']]);
}

function optimus_common_settings()
{
	global $context, $txt, $scripturl;

	$context['sub_template'] = 'common';
	$context['page_title'] .= ' - ' . $txt['optimus_common_title'];
	$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=common;save';

	$config_vars = array(
		array('int', 'optimus_portal_compat'),
		array('text', 'optimus_portal_index'),
		array('text', 'optimus_forum_index'),
		array('text', 'optimus_description'),
		array('check', 'optimus_no_first_number'),
		array('check', 'optimus_board_description'),
		array('check', 'optimus_topic_description'),
		array('check', 'optimus_404_status')
	);

	$templates = array();
	foreach ($txt['optimus_templates'] as $name => $template) {
		$templates[$name] = array(
			'name' => isset($_POST['' . $name . '_name']) ? $_POST['' . $name . '_name'] : '',
			'page' => isset($_POST['' . $name . '_page']) ? $_POST['' . $name . '_page'] : '',
			'site' => isset($_POST['' . $name . '_site']) ? $_POST['' . $name . '_site'] : ''
		);
	}

	if (isset($_GET['save'])) {
		checkSession();
		$save_vars = $config_vars;
		saveDBSettings($save_vars);
		updateSettings(array('optimus_templates' => serialize($templates)));
		redirectexit('action=admin;area=optimus;sa=common');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_extra_settings()
{
	global $context, $txt, $scripturl, $modSettings, $settings;

	//$context['sub_template'] = 'extra';
	$context['page_title'] .= ' - ' . $txt['optimus_extra_title'];
	$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=extra;save';

	if (empty($modSettings['optimus_og_image'])) {
		updateSettings(array('optimus_og_image' => $settings['images_url'] . '/thumbnail.gif'));
	}

	$config_vars = array(
		array('title', 'optimus_extra_title'),
		array('check', 'optimus_remove_last_bc_item'),
		array('check', 'optimus_correct_prevnext'),
		array('check', 'optimus_open_graph'),
		array('text',  'optimus_og_image', 60, 'disabled' => !empty($modSettings['optimus_open_graph']) ? false : true),
		array('text', 'optimus_fb_appid', 40, 'disabled' => !empty($modSettings['optimus_open_graph']) ? false : true),
		array('text', 'optimus_tw_cards', 40, 'preinput' => '@'),
		array('check', 'optimus_json_ld')
	);

	if (isset($_GET['save'])) {
		checkSession();
		$save_vars = $config_vars;
		saveDBSettings($save_vars);
		redirectexit('action=admin;area=optimus;sa=extra');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_verify_settings()
{
	global $context, $txt, $scripturl;

	$context['sub_template'] = 'verify';
	$context['page_title'] .= ' - ' . $txt['optimus_verification_title'];
	$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=verify;save';

	$config_vars = array();

	$meta = array();
	foreach ($txt['optimus_search_engines'] as $engine => $data) {
		if (!empty($_POST['' . $engine . '_content'])) {
			$meta[$engine] = array(
				'name'    => isset($_POST['' . $engine . '_name']) ? $_POST['' . $engine . '_name'] : $data[0],
				'content' => $_POST['' . $engine . '_content']
			);
		}
	}

	if (isset($_GET['save'])) {
		checkSession();
		$save_vars = $config_vars;
		saveDBSettings($save_vars);
		updateSettings(array('optimus_meta' => serialize($meta)));
		redirectexit('action=admin;area=optimus;sa=verify');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_counters_settings()
{
	global $context, $txt, $scripturl;

	$context['sub_template'] = 'counters';
	$context['page_title'] .= ' - ' . $txt['optimus_counters'];
	$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=counters;save';

	$config_vars = array(
    	array('large_text', 'optimus_head_code'),
		array('large_text', 'optimus_stat_code'),
		array('large_text', 'optimus_count_code'),
		array('large_text', 'optimus_counters_css'),
		array('text', 'optimus_ignored_actions')
	);

	if (isset($_GET['save'])) {
		checkSession();
		$save_vars = $config_vars;
		saveDBSettings($save_vars);
		redirectexit('action=admin;area=optimus;sa=counters');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_robots_settings()
{
	global $context, $txt, $scripturl;

	$context['sub_template'] = 'robots';
	$context['page_title']  .= ' - ' . $txt['optimus_robots_title'];
	$context['post_url']     = $scripturl . '?action=admin;area=optimus;sa=robots;save';

	$common_rules_path = $_SERVER['DOCUMENT_ROOT'] . "/robots.txt";

	clearstatcache();
	
	$context['robots_txt_exists'] = file_exists($common_rules_path);
	$context['robots_content']    = $context['robots_txt_exists'] ? @file_get_contents($common_rules_path) : '';

	optimus_robots_create();

	if (isset($_GET['save'])) {
		checkSession();

		if (isset($_POST['robots'])) {
			$common_rules = stripslashes($_POST['robots']);
			file_put_contents($common_rules_path, $common_rules);
		}

		redirectexit('action=admin;area=optimus;sa=robots');
	}
}

function optimus_map_settings()
{
	global $context, $txt, $scripturl, $boarddir, $modSettings, $smcFunc, $sourcedir;

	$context['page_title'] .= ' - ' . $txt['optimus_sitemap_title'];
	$context['post_url']    = $scripturl . '?action=admin;area=optimus;sa=map;save';

	clearstatcache();

	$config_vars = array(
		array('title', 'optimus_sitemap_xml_link'),
		array('check', 'optimus_sitemap_enable'),
		array('check', 'optimus_sitemap_link',   'disabled' => file_exists($boarddir . '/sitemap.xml') ? false : true),
		array('check', 'optimus_sitemap_boards', 'disabled' => empty($modSettings['optimus_sitemap_enable']) ? true : false),
		array('int',   'optimus_sitemap_topics', 'disabled' => empty($modSettings['optimus_sitemap_enable']) ? true : false)
	);

	// Обновляем запись в Диспетчере задач
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}scheduled_tasks
		SET disabled = {int:disabled}
		WHERE task = {string:task}',
		array(
			'disabled' => !empty($modSettings['optimus_sitemap_enable']) ? 0 : 1,
			'task'     => 'optimus_sitemap',
		)
	);

	if (!empty($modSettings['optimus_sitemap_enable'])) {
		require_once($sourcedir . '/ScheduledTasks.php');
		CalculateNextTrigger('optimus_sitemap');
	}

	if (isset($_GET['save'])) {
		checkSession();
		$save_vars = $config_vars;
		saveDBSettings($save_vars);
		redirectexit('action=admin;area=optimus;sa=map');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_robots_create()
{
	global $smcFunc, $modSettings, $boardurl, $sourcedir, $boarddir, $context, $txt, $scripturl;

	$request = $smcFunc['db_query']('', '
		SELECT ps.permission
		FROM {db_prefix}permissions AS ps
		WHERE ps.id_group = -1',
		array()
	);

	$yes = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$yes[$row['permission']] = true;

	$smcFunc['db_free_result']($request);

	// SimplePortal
	$sp = isset($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 1 && function_exists('sportal_init');
	// Standalone mode (SimplePortal)
	$autosp = !empty($modSettings['sp_standalone_url']) ? substr($modSettings['sp_standalone_url'], strlen($boardurl)) : '';

	// PortaMx
	$pm = !empty($modSettings['pmx_frontmode']) && function_exists('PortaMx');
	// forum = community? (PortaMx)
	$alias = !empty($modSettings['pmxsef_aliasactions']) && strpos($modSettings['pmxsef_aliasactions'], 'forum');

	// Aeva Media
	$aeva = file_exists($sourcedir . '/Aeva-Subs.php') && isset($yes['aeva_access']);

	// SMF Gallery
	$gal = file_exists($sourcedir . '/Gallery2.php') && isset($yes['smfgallery_view']);

	// SMF Arcade
	$arc = file_exists($sourcedir . '/Subs-Arcade.php') && isset($yes['arcade_view']);

	// FAQ mod
	$faq = file_exists($sourcedir . '/Subs-Faq.php') && isset($yes['faqperview']);

	// PMXBlog
	$blog = file_exists($sourcedir . '/PmxBlog.php') && !empty($modSettings['pmxblog_enabled']);

	// SMF Project Tools
	$pj = file_exists($sourcedir . '/Project.php') && in_array('pj', $context['admin_features']) && isset($yes['project_access']);

	// Simple Classifieds
	$sc = file_exists($sourcedir . '/Classifieds/Classifieds-Subs.php') && isset($yes['view_classifieds']);

	// SC Light
	$scl = file_exists($sourcedir . '/Subs-SCL.php') && !empty($modSettings['scl_mode']);

	// Topic Rating Bar
	$trb = file_exists($sourcedir . '/Subs-TopicRating.php');

	// Downloads System
	$ds = file_exists($sourcedir . '/Downloads2.php') && isset($yes['downloads_view']);

	// SMF Links
	$sl = isset($txt['smflinks_menu']) && isset($yes['view_smflinks']);

	// Pretty URLs enabled?
	$pretty = file_exists($sourcedir . '/PrettyUrls-Filters.php') && !empty($modSettings['pretty_enable_filters']);

	// SimpleSEF enabled?
	$simplesef = !empty($modSettings['simplesef_enable']) && file_exists($sourcedir . '/SimpleSEF.php');

	$sef = $pretty || $simplesef;

	// Sitemap file exists?
	$map      = 'sitemap.xml';
	$path_map = $boardurl . '/' . $map;

	clearstatcache();

	$temp_map = file_exists($boarddir . '/' . $map);
	$map      = !$temp_map ? '' : $path_map;
	$url_path = @parse_url($boardurl, PHP_URL_PATH);

	$first_rules = array(
		"User-agent: MediaPartners-Google",
		"Allow: " . $url_path . "/",
		"|",
		substr($txt['lang_locale'], 0, 2) == 'ru' ? "User-agent: Baiduspider\nDisallow: " . $url_path . "/\n|" : "",
		"User-agent: *"
	);

	$common_rules = array(
		// Main
		"Allow: " . $url_path . "/$",
		// action=forum
		$sp ? "Allow: " . $url_path . "/*forum$" : "",
		// SimplePortal
		isset($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 3 && file_exists($boarddir . $autosp) ? "Allow: " . $url_path . $autosp : "",
		$sp ? "Allow: " . $url_path . "/*page*page" : "",
		// PortaMx
		$pm && $alias ? "Allow: " . $url_path . "/*forum$" : "",
		$pm && !$alias ? "Allow: " . $url_path . "/*community$" : "",
		// Aeva Media
		$aeva ? "Allow: " . $url_path . "/*media$\nAllow: " . $url_path . "/*media*album\nAllow: " . $url_path . "/*media*item\nAllow: " . $url_path . "/MGalleryItem.php?id" : "",
		// SMF Gallery mod
		$gal ? "Allow: " . $url_path . "/*gallery$\nAllow: " . $url_path . "/*gallery*cat\nAllow: " . $url_path . "/*gallery*view" : "",
		// RSS
		!empty($modSettings['xmlnews_enable']) ? "Allow: " . $url_path . "/*.xml" : "",
		// Sitemap
		!empty($map) || file_exists($sourcedir . '/Sitemap.php') ? "Allow: " . $url_path . "/*sitemap" : "",
		// SMF Arcade
		$arc ? "Allow: " . $url_path . "/*arcade$\nAllow: " . $url_path . "/*arcade*game" : "",
		// FAQ
		$faq ? "Allow: " . $url_path . "/*faq" : "",
		// PMXBlog
		$blog ? "Allow: " . $url_path . "/*pmxblog" : "",
		// Project Tools
		$pj ? "Allow: " . $url_path . "/*project\nAllow: " . $url_path . "/*issue" : "",
		// SC Light
		$scl ? "Allow: " . $url_path . "/*scl" : "",
		// Simple Classifieds
		$sc ? "Allow: " . $url_path . "/*bbs" : "",
		// Topic Rating Bar
		$trb ? "Allow: " . $url_path . "/*rating" : "",
		// Downloads System
		$ds ? "Allow: " . $url_path . "/*downloads" : "",
		// SMF Links
		$sl ? "Allow: " . $url_path . "/*links" : "",
		// We have nothing to hide ;)
		"Allow: /*.css\nAllow: /*.js\nAllow: /*.png\nAllow: /*.jpg\nAllow: /*.gif",

		// Special rules for Pretty URLs or SimpleSEF
		$sef ? "Disallow: " . $url_path . "/attachments/
Disallow: " . $url_path . "/avatars/
Disallow: " . $url_path . "/Packages/
Disallow: " . $url_path . "/Smileys/
Disallow: " . $url_path . "/Sources/
Disallow: " . $url_path . "/Themes/
Disallow: " . $url_path . "/login/
Disallow: " . $url_path . "/*msg
Disallow: " . $url_path . "/*profile
Disallow: " . $url_path . "/*help
Disallow: " . $url_path . "/*search
Disallow: " . $url_path . "/*mlist
Disallow: " . $url_path . "/*sort
Disallow: " . $url_path . "/*recent
Disallow: " . $url_path . "/*register
Disallow: " . $url_path . "/*groups
Disallow: " . $url_path . "/*stats
Disallow: " . $url_path . "/*unread
Disallow: " . $url_path . "/*topicseen
Disallow: " . $url_path . "/*showtopic
Disallow: " . $url_path . "/*prev_next
Disallow: " . $url_path . "/*imode
Disallow: " . $url_path . "/*wap
Disallow: " . $url_path . "/*all" : "",

		"Disallow: " . $url_path . "/*action",
		$sef ? "" : "Disallow: " . $url_path . "/*board=*wap\nDisallow: " . $url_path . "/*board=*imode\nDisallow: " . $url_path . "/*topic=*wap\nDisallow: " . $url_path . "/*topic=*imode",
		!empty($modSettings['queryless_urls']) || $sef ? "" : "Disallow: " . $url_path . "/*topic=*.msg\nDisallow: " . $url_path . "/*topic=*.new",
		$sef ? "" : "Disallow: " . $url_path . "/*;",
		"Disallow: " . $url_path . "/*PHPSESSID",
		// Content
		!empty($modSettings['queryless_urls'])
			? ($sef ? "" : "Allow: " . $url_path . "/*board*.html$\nAllow: " . $url_path . "/*topic*.html$")
			: ($sef ? "" : "Allow: " . $url_path . "/*board\nAllow: " . $url_path . "/*topic"),
		// Other pages are not needing
		$sef ? "" : "Disallow: " . $url_path . "/"
	);

	// Yandex only
	if (isset($txt['lang_dictionary']) && in_array($txt['lang_dictionary'], array('ru', 'uk'))) {
		$temp_rules = $common_rules;
		$common_rules[] = "|";
		$common_rules[] = "User-agent: Yandex";

		foreach ($temp_rules as $line) {
			if (!empty($line))
				$common_rules[] = $line;
		}

		$common_rules[] = "Clean-param: PHPSESSID";
		
		if (isset($_SERVER['HTTP_HOST']) || isset($_SERVER['SERVER_NAME'])) {
			$prefix = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https://' : '';
			$common_rules[] = "Host: " . $prefix . (empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST']);
		}
	}

	// Sitemap XML
	$sitemap = file_exists($sourcedir . '/Sitemap.php');
	$common_rules[] = !empty($map) || $sitemap ? "|" : "";
	$common_rules[] = !empty($map) ? "Sitemap: " . $map : "";
	$common_rules[] = $sitemap ? "Sitemap: " . $scripturl . "?action=sitemap;xml" : "";

	$common_rules = $first_rules + $common_rules;
	$new_robots   = array();
	
	foreach ($common_rules as $line) {
		if (!empty($line))
			$new_robots[] = $line;
	}

	$new_robots = implode("<br />", str_replace("|", "", $new_robots));
	$context['new_robots_content'] = parse_bbc('[code]' . $new_robots . '[/code]');
}
