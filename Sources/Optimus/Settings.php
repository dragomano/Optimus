<?php

namespace Bugo\Optimus;

/**
 * Settings.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class Settings
{
	/**
	 * Прописываем менюшку с настройками мода в админке
	 *
	 * @param array $admin_areas
	 * @return void
	 */
	public static function adminAreas(&$admin_areas)
	{
		global $txt;

		$admin_areas['config']['areas']['optimus'] = array(
			'label'    => $txt['optimus_title'],
			'function' => function() {
				self::settingActions();
			},
			'icon'     => 'maintain.gif',
			'subsections' => array(
				'base'     => array($txt['optimus_base_title']),
				'extra'    => array($txt['optimus_extra_title']),
				'favicon'  => array($txt['optimus_favicon_title']),
				'metatags' => array($txt['optimus_meta_title']),
				'counters' => array($txt['optimus_counters']),
				'robots'   => array($txt['optimus_robots_title'])
			)
		);
	}

	/**
	 * Ключевая функция, подключающая все остальные при их вызове
	 *
	 * @return void
	 */
	public static function settingActions()
	{
		global $context, $txt, $sourcedir, $scripturl;

		$context['page_title'] = $txt['optimus_main'];

		// Подключаем файл шаблона вместе с таблицами стилей
		loadTemplate('Optimus', array('admin', 'optimus'));

		$subActions = array(
			'base'     => 'baseSettings',
			'extra'    => 'extraSettings',
			'favicon'  => 'faviconSettings',
			'metatags' => 'metatagsSettings',
			'counters' => 'counterSettings',
			'robots'   => 'robotsSettings'
		);

		require_once($sourcedir . '/ManageSettings.php');
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
				)
			),
		);

		call_user_func(__CLASS__ . '::' . $subActions[$_REQUEST['sa']]);
	}

	/**
	 * Основные настройки мода
	 *
	 * @return void
	 */
	public static function baseSettings()
	{
		global $context, $txt, $scripturl, $modSettings, $smcFunc;

		$context['sub_template'] = 'base';
		$context['page_title'] .= ' - ' . $txt['optimus_base_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=base;save';

		$add_settings = [];
		if (!isset($modSettings['optimus_forum_index']))
			$add_settings['optimus_forum_index'] = $smcFunc['substr']($txt['forum_index'], 7);
		if (!isset($modSettings['optimus_no_first_number']))
			$add_settings['optimus_no_first_number'] = 1;
		if (!empty($add_settings))
			updateSettings($add_settings);

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

	/**
	 * Страница с настройками микроразметки
	 *
	 * @return void
	 */
	public static function extraSettings()
	{
		global $context, $txt, $scripturl, $settings, $modSettings;

		$context['page_title'] .= ' - ' . $txt['optimus_extra_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=extra;save';

		if (!isset($modSettings['optimus_og_image']))
			updateSettings(array('optimus_og_image' => $settings['images_url'] . '/thumbnail.gif'));

		$config_vars = array(
			array('title', 'optimus_extra_title'),
			array('check', 'optimus_open_graph'),
			array('text',  'optimus_og_image', 50, 'disabled' => !empty($modSettings['optimus_open_graph']) ? false : true),
			array('text', 'optimus_fb_appid', 40, 'disabled' => !empty($modSettings['optimus_open_graph']) ? false : true),
			array('text', 'optimus_tw_cards', 40, 'preinput' => '@')
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

	/**
	 * Управление фавиконкой форума
	 *
	 * @return void
	 */
	public static function faviconSettings()
	{
		global $context, $txt, $scripturl;

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

	/**
	 * Управление мета-тегами
	 *
	 * @return void
	 */
	public static function metatagsSettings()
	{
		global $context, $txt, $scripturl;

		$context['sub_template'] = 'metatags';
		$context['page_title'] .= ' - ' . $txt['optimus_meta_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=metatags;save';

		$config_vars = array();

		$meta = array();
		if (isset($_POST['custom_tag_name'])) {
			foreach ($_POST['custom_tag_name'] as $key => $value) {
				if (empty($value))
					unset($_POST['custom_tag_name'][$key], $_POST['custom_tag_value'][$key]);
				else
					$meta[$_POST['custom_tag_name'][$key]] = $_POST['custom_tag_value'][$key];
			}
		}

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			updateSettings(array('optimus_meta' => serialize($meta)));
			redirectexit('action=admin;area=optimus;sa=metatags');
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * Управление счетчиками
	 *
	 * @return void
	 */
	public static function counterSettings()
	{
		global $context, $txt, $scripturl, $modSettings;

		$context['sub_template'] = 'counters';
		$context['page_title'] .= ' - ' . $txt['optimus_counters'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=counters;save';

		$add_settings = [];
		if (!isset($modSettings['optimus_counters_css']))
			$add_settings['optimus_counters_css'] = '.copyright a>img {opacity: 0.3} .copyright a:hover>img {opacity: 1.0}';
		if (!isset($modSettings['optimus_ignored_actions']))
			$add_settings['optimus_ignored_actions'] = 'admin,bookmarks,credits,helpadmin,pm,printpage';
		if (!empty($add_settings))
			updateSettings($add_settings);

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

	/**
	 * Страница для изменения robots.txt
	 *
	 * @return void
	 */
	public static function robotsSettings()
	{
		global $context, $txt, $scripturl, $boarddir;

		$context['sub_template'] = 'robots';
		$context['page_title'] .= ' - ' . $txt['optimus_robots_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=robots;save';

		$common_rules_path = (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : $boarddir) . '/robots.txt';

		clearstatcache();

		$context['robots_txt_exists'] = file_exists($common_rules_path);
		$context['robots_content']    = $context['robots_txt_exists'] ? file_get_contents($common_rules_path) : '';

		self::robotsCreate();

		if (isset($_GET['save'])) {
			checkSession();

			if (isset($_POST['robots'])) {
				$common_rules = stripslashes($_POST['robots']);
				file_put_contents($common_rules_path, $common_rules);
			}

			redirectexit('action=admin;area=optimus;sa=robots');
		}
	}

	/**
	 * Генерация правил для файла robots.txt
	 *
	 * @return void
	 */
	private static function robotsCreate()
	{
		global $modSettings, $boardurl, $sourcedir, $boarddir, $context, $scripturl;

		clearstatcache();

		// SimplePortal
		$sp = isset($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 1 && function_exists('sportal_init');
		// Standalone mode
		$autosp = !empty($modSettings['sp_standalone_url']) ? substr($modSettings['sp_standalone_url'], strlen($boardurl)) : '';

		// PortaMx
		$pm = !empty($modSettings['pmx_frontmode']) && function_exists('PortaMx');
		// if (forum == community)
		$alias = !empty($modSettings['pmxsef_aliasactions']) && strpos($modSettings['pmxsef_aliasactions'], 'forum');

		// Is any SEF mod enabled?
		$pretty    = file_exists($sourcedir . '/PrettyUrls-Filters.php') && !empty($modSettings['pretty_enable_filters']);
		$simplesef = !empty($modSettings['simplesef_enable']) && file_exists($sourcedir . '/SimpleSEF.php');
		$sef       = $pretty || $simplesef;

		// Sitemap file exists?
		$map      = 'sitemap.xml';
		$path_map = $boardurl . '/' . $map;

		require_once($sourcedir . '/Optimus/libs/idna_convert.class.php');
		$idn = new \idna_convert(array('idn_version' => 2008));
		if (stripos($idn->encode($boardurl), 'xn--') !== false)
			$path_map = $idn->encode($boardurl) . '/' . $map;

		$temp_map = file_exists($boarddir . '/' . $map);
		$map      = $temp_map ? $path_map : '';
		$url_path = parse_url($boardurl, PHP_URL_PATH);

		$actions = array('msg','profile','help','search','mlist','sort','recent','register','groups','stats','unread','topicseen','showtopic','prev_next','imode','wap','all');

		$common_rules = [];
		$common_rules[] = "User-agent: *";

		// Special rules for Pretty URLs or SimpleSEF
		if ($sef) {
			$common_rules[] = "Disallow: " . $url_path . "/login/";

			foreach ($actions as $action)
				$common_rules[] = "Disallow: " . $url_path . "/*" . $action;
		}

		$common_rules[] = "Disallow: " . $url_path . "/*action";

		if (!empty($modSettings['queryless_urls']) || $sef)
			$common_rules[] = "";
		else
			$common_rules[] = "Disallow: " . $url_path . "/*topic=*.msg\nDisallow: " . $url_path . "/*topic=*.new";

		$common_rules[] = "Disallow: " . $url_path . "/*PHPSESSID";
		$common_rules[] = $sef ? "" : "Disallow: " . $url_path . "/*;";

		// Front page
		$common_rules[] = "Allow: " . $url_path . "/$";

		// Content
		if (!empty($modSettings['queryless_urls']))
			$common_rules[] = ($sef ? "" : "Allow: " . $url_path . "/*board*.html$\nAllow: " . $url_path . "/*topic*.html$");
		else
			$common_rules[] = ($sef ? "" : "Allow: " . $url_path . "/*board\nAllow: " . $url_path . "/*topic");

		// action=forum
		$common_rules[] = $sp ? "Allow: " . $url_path . "/*forum$" : "";

		// SimplePortal
		if (isset($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 3 && file_exists($boarddir . $autosp))
			$common_rules[] = "Allow: " . $url_path . $autosp;

		$common_rules[] = $sp ? "Allow: " . $url_path . "/*page*page" : "";

		// PortaMx
		$common_rules[] = $pm && $alias ? "Allow: " . $url_path . "/*forum$" : "";
		$common_rules[] = $pm && !$alias ? "Allow: " . $url_path . "/*community$" : "";

		// RSS
		$common_rules[] = !empty($modSettings['xmlnews_enable']) ? "Allow: " . $url_path . "/*.xml" : "";

		// Sitemap
		$common_rules[] = !empty($map) || file_exists($sourcedir . '/Sitemap.php') ? "Allow: " . $url_path . "/*sitemap" : "";

		// We have nothing to hide ;)
		$common_rules[] = "Allow: /*.css$\nAllow: /*.js$\nAllow: /*.png$\nAllow: /*.jpg$\nAllow: /*.gif$";

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
}
