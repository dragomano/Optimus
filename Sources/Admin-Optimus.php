<?php

/**
 * Admin-Optimus.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2018 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 1.9.7.3
 */

if (!defined('SMF'))
	die('Hacking attempt...');

function optimus_admin_areas(&$admin_areas)
{
	global $txt;

	$admin_areas['config']['areas']['optimus'] =
		array(
			'label'    => $txt['optimus_title'],
			'function' => 'optimus_area_settings',
			'icon'     => 'maintain.gif',
			'subsections' => array(
				'base'     => array($txt['optimus_base_title']),
				'extra'    => array($txt['optimus_extra_title']),
				'favicon'  => array($txt['optimus_favicon_title']),
				'metatags' => array($txt['optimus_meta_title']),
				'counters' => array($txt['optimus_counters']),
				'robots'   => array($txt['optimus_robots_title']),
				'sitemap'  => array($txt['optimus_sitemap_title']),
				'donate'   => array($txt['optimus_donate_title'])
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
		'base'     => 'optimus_base_settings',
		'extra'    => 'optimus_extra_settings',
		'favicon'  => 'optimus_favicon_settings',
		'metatags' => 'optimus_meta_settings',
		'counters' => 'optimus_counters_settings',
		'robots'   => 'optimus_robots_settings',
		'sitemap'  => 'optimus_sitemap_settings',
		'donate'   => 'optimus_donate_settings'
	);

	loadGeneralSettingParameters($subActions, 'base');

	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['optimus_title'],
		'tabs' => array(
			'base' => array(
				'description' => $txt['optimus_base_desc'],
			),
			'extra' => array(
				'description' => $txt['optimus_extra_desc'],
			),
			'favicon' => array(
				'description' => $txt['optimus_favicon_desc'],
			),
			'metatags' => array(
				'description' => $txt['optimus_meta_desc'],
			),
			'counters' => array(
				'description' => $txt['optimus_counters_desc'],
			),
			'robots' => array(
				'description' => $txt['optimus_robots_desc'],
			),
			'sitemap' => array(
				'description' => sprintf($txt['optimus_sitemap_desc'], $scripturl . '?action=admin;area=scheduledtasks;' . $context['session_var'] . '=' . $context['session_id']),
			),
			'donate' => array(
				'description' => $txt['optimus_donate_desc'],
			),
		),
	);

	call_user_func($subActions[$_REQUEST['sa']]);
}

function optimus_base_settings()
{
	global $context, $txt, $scripturl;

	$context['sub_template'] = 'base';
	$context['page_title'] .= ' - ' . $txt['optimus_base_title'];
	$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=base;save';

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
		redirectexit('action=admin;area=optimus;sa=base');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_extra_settings()
{
	global $context, $txt, $scripturl, $modSettings, $settings;

	$context['page_title'] .= ' - ' . $txt['optimus_extra_title'];
	$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=extra;save';

	if (empty($modSettings['optimus_og_image'])) {
		updateSettings(array('optimus_og_image' => $settings['images_url'] . '/thumbnail.gif'));
	}

	$config_vars = array(
		array('title', 'optimus_extra_title'),
		array('check', 'optimus_open_graph'),
		array('text',  'optimus_og_image', 50, 'disabled' => !empty($modSettings['optimus_open_graph']) ? false : true),
		array('text', 'optimus_fb_appid', 40, 'disabled' => !empty($modSettings['optimus_open_graph']) ? false : true),
		array('text', 'optimus_tw_cards', 40, 'preinput' => '@'),
		array('check', 'optimus_json_ld')
	);

	if (isset($_GET['save'])) {
		$_POST['optimus_tw_cards'] = str_replace('@', '', $_POST['optimus_tw_cards']);

		checkSession();

		$save_vars = $config_vars;
		saveDBSettings($save_vars);

		redirectexit('action=admin;area=optimus;sa=extra');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_favicon_settings()
{
	global $context, $txt, $scripturl, $modSettings, $settings;

	$context['sub_template'] = 'favicon';
	$context['page_title'] .= ' - ' . $txt['optimus_favicon_title'];
	$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=favicon;save';

	$config_vars = array(
		array('text', 'optimus_favicon_api_key'),
		array('large_text', 'optimus_favicon_text')
	);

	if (isset($_GET['save'])) {
		checkSession();

		$save_vars = $config_vars;
		saveDBSettings($save_vars);

		redirectexit('action=admin;area=optimus;sa=favicon');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_meta_settings()
{
	global $context, $txt, $scripturl;

	$context['sub_template'] = 'meta';
	$context['page_title'] .= ' - ' . $txt['optimus_meta_title'];
	$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=meta;save';

	$config_vars = array();

	$meta = array();
	if (isset($_POST['custom_tag_name'])) {
		foreach ($_POST['custom_tag_name'] as $key => $value) {
			if (empty($value)) {
				unset($_POST['custom_tag_name'][$key], $_POST['custom_tag_value'][$key]);
			}
			else
				$meta[$_POST['custom_tag_name'][$key]] = $_POST['custom_tag_value'][$key];
		}
	}

	if (isset($_GET['save'])) {
		checkSession();

		$save_vars = $config_vars;
		saveDBSettings($save_vars);

		updateSettings(array('optimus_meta' => serialize($meta)));
		redirectexit('action=admin;area=optimus;sa=meta');
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
	$context['robots_content']    = $context['robots_txt_exists'] ? file_get_contents($common_rules_path) : '';

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

function optimus_sitemap_settings()
{
	global $context, $txt, $scripturl, $modSettings, $smcFunc, $sourcedir;

	$context['page_title'] .= ' - ' . $txt['optimus_sitemap_title'];
	$context['post_url']    = $scripturl . '?action=admin;area=optimus;sa=sitemap;save';

	$config_vars = array(
		array('title', 'optimus_sitemap_xml_link'),
		array('check', 'optimus_sitemap_enable'),
		array('check', 'optimus_sitemap_link'),
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
			'task'     => 'optimus_sitemap'
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

		redirectexit('action=admin;area=optimus;sa=sitemap');
	}

	prepareDBSettingContext($config_vars);
}

function optimus_donate_settings()
{
	global $context, $txt;

	$context['sub_template'] = 'donate';
	$context['page_title']  .= ' - ' . $txt['optimus_donate_title'];
}

function optimus_robots_create()
{
	global $smcFunc, $modSettings, $boardurl, $sourcedir, $boarddir, $context, $scripturl;

	$request = $smcFunc['db_query']('', '
		SELECT permission
		FROM {db_prefix}permissions
		WHERE id_group = -1',
		array()
	);

	$guest_access = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$guest_access[$row['permission']] = true;

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
	$aeva = file_exists($sourcedir . '/Aeva-Subs.php') && isset($guest_access['aeva_access']);

	// SMF Gallery
	$gal = file_exists($sourcedir . '/Gallery2.php') && isset($guest_access['smfgallery_view']);

	// SMF Arcade
	$arc = file_exists($sourcedir . '/Subs-Arcade.php') && isset($guest_access['arcade_view']);

	// FAQ mod
	$faq = file_exists($sourcedir . '/Subs-Faq.php') && isset($guest_access['faqperview']);

	// PMXBlog
	$blog = file_exists($sourcedir . '/PmxBlog.php') && !empty($modSettings['pmxblog_enabled']);

	// SMF Project Tools
	$pj = file_exists($sourcedir . '/Project.php') && in_array('pj', $context['admin_features']) && isset($guest_access['project_access']);

	// Simple Classifieds
	$sc = file_exists($sourcedir . '/Classifieds/Classifieds-Subs.php') && isset($guest_access['view_classifieds']);

	// SC Light
	$scl = file_exists($sourcedir . '/Subs-SCL.php') && !empty($modSettings['scl_mode']);

	// Topic Rating Bar
	$trb = file_exists($sourcedir . '/Subs-TopicRating.php');

	// Downloads System
	$ds = file_exists($sourcedir . '/Downloads2.php') && isset($guest_access['downloads_view']);

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
	$map      = $temp_map ? $path_map : '';
	$url_path = parse_url($boardurl, PHP_URL_PATH);

	$common_rules = array(
		"User-agent: *",

		// Special rules for Pretty URLs or SimpleSEF
		$sef ? "Disallow: " . $url_path . "/attachments/
Disallow: " . $url_path . "/avatars/
Disallow: " . $url_path . "/Packages/
Disallow: " . $url_path . "/Smileys/
Disallow: " . $url_path . "/Sources/
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
		"Disallow: " . $url_path . "/*PHPSESSID",
		$sef ? "" : "Disallow: " . $url_path . "/*;",
		// Front page
		"Allow: " . $url_path . "/$",
		// Content
		!empty($modSettings['queryless_urls'])
		? ($sef ? "" : "Allow: " . $url_path . "/*board*.html$\nAllow: " . $url_path . "/*topic*.html$")
		: ($sef ? "" : "Allow: " . $url_path . "/*board\nAllow: " . $url_path . "/*topic"),
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
		// We have nothing to hide ;)
		"Allow: /*.css$\nAllow: /*.js$\nAllow: /*.png$\nAllow: /*.jpg$\nAllow: /*.gif$"
	);

	// Sitemap XML
	$sitemap = file_exists($sourcedir . '/Sitemap.php');
	$common_rules[] = !empty($map) || $sitemap ? "|" : "";
	$common_rules[] = !empty($map) ? "Sitemap: " . $map : "";
	$common_rules[] = $sitemap ? "Sitemap: " . $scripturl . "?action=sitemap;xml" : "";

	$new_robots = array();

	foreach ($common_rules as $line) {
		if (!empty($line))
			$new_robots[] = $line;
	}

	$new_robots = implode("<br />", str_replace("|", "", $new_robots));
	$context['new_robots_content'] = parse_bbc('[code]' . $new_robots . '[/code]');
}
