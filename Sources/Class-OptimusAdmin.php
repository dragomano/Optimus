<?php

/**
 * Class-OptimusAdmin.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2018 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 0.1
 */

if (!defined('PMX'))
	die('Hacking attempt...');

class OptimusAdmin
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

		loadCSSFile('optimus.css', array('minimize' => true), 'pmx_admin');

		$counter = array_search('modsettings', array_keys($admin_areas['config']['areas']));

		$admin_areas['config']['areas'] = array_merge(
			array_slice($admin_areas['config']['areas'], 0, $counter, true),
			array(
				'optimus' => array(
					'label'    => $txt['optimus_title'],
					'function' => function(){self::settingActions();},
					'icon'     => 'optimus',
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
				)
			),
			array_slice($admin_areas['config']['areas'], $counter, count($admin_areas['config']['areas']), true)
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

		loadTemplate('Optimus');

		$subActions = array(
			'base'     => array('OptimusAdmin', 'baseSettings'),
			'extra'    => array('OptimusAdmin', 'extraSettings'),
			'favicon'  => array('OptimusAdmin', 'faviconSettings'),
			'metatags' => array('OptimusAdmin', 'metatagsSettings'),
			'counters' => array('OptimusAdmin', 'counterSettings'),
			'robots'   => array('OptimusAdmin', 'robotsSettings'),
			'sitemap'  => array('OptimusAdmin', 'sitemapSettings'),
			'donate'   => array('OptimusAdmin', 'donateSettings')
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
				),
				'sitemap' => array(
					'description' => sprintf($txt['optimus_sitemap_desc'], $scripturl . '?action=admin;area=scheduledtasks;' . $context['session_var'] . '=' . $context['session_id']),
				),
				'donate' => array(
					'description' => $txt['optimus_donate_desc'],
				),
			),
		);

		call_helper($subActions[$_REQUEST['sa']]);
	}

	/**
	 * Основные настройки мода
	 *
	 * @return void
	 */
	public static function baseSettings()
	{
		global $context, $txt, $scripturl, $modSettings;

		$context['page_title'] .= ' - ' . $txt['optimus_base_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=base;save';

		if (empty($modSettings['optimus_forum_index']))
			updateSettings(array('optimus_forum_index' => sprintf($txt['forum_index'], $context['forum_name'])));

		$config_vars = array(
			array('title', 'optimus_main_page'),
			array('text', 'optimus_forum_index', 40),
			array('large_text', 'optimus_description', '4" style="width:80%'),
			array('title', 'optimus_all_pages'),
			array('select', 'optimus_board_extend_title', $txt['optimus_board_extend_title_set']),
			array('select', 'optimus_topic_extend_title', $txt['optimus_topic_extend_title_set']),
			array('check', 'optimus_topic_description'),
			array('check', 'optimus_404_status', 'help' => 'optimus_404_status_help')
		);

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

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
		global $context, $txt, $scripturl, $modSettings, $settings;

		$context['page_title'] .= ' - ' . $txt['optimus_extra_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=extra;save';

		$config_vars = array(
			array('title', 'optimus_extra_title'),
			array('check',  'optimus_og_image', 50, 'help' => 'optimus_og_image_help'),
			array('text', 'optimus_fb_appid', 40, 'help' => 'optimus_fb_appid_help'),
			array('text', 'optimus_tw_cards', 40, 'preinput' => '@', 'help' => 'optimus_tw_cards_help'),
			array('check', 'optimus_json_ld', 'help' => 'optimus_json_ld_help')
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

		if (!isset($modSettings['optimus_counters_css']))
			updateSettings(array('optimus_counters_css' => '.counters {margin: 1em 0 -3.3em; text-align: center}'));
		if (!isset($modSettings['optimus_ignored_actions']))
			updateSettings(array('optimus_ignored_actions' => 'admin,bookmarks,credits,helpadmin,pm,printpage'));

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
		global $context, $txt, $scripturl;

		$context['sub_template'] = 'robots';
		$context['page_title']  .= ' - ' . $txt['optimus_robots_title'];
		$context['post_url']     = $scripturl . '?action=admin;area=optimus;sa=robots;save';

		$common_rules_path = $_SERVER['DOCUMENT_ROOT'] . "/robots.txt";

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
	 * Страница с настройками карты форума
	 *
	 * @return void
	 */
	public static function sitemapSettings()
	{
		global $context, $txt, $scripturl, $pmxcFunc, $modSettings, $sourcedir;

		$context['page_title'] .= ' - ' . $txt['optimus_sitemap_title'];
		$context['post_url']    = $scripturl . '?action=admin;area=optimus;sa=sitemap;save';

		$config_vars = array(
			array('title', 'optimus_sitemap_xml_link'),
			array('check', 'optimus_sitemap_enable'),
			array('check', 'optimus_sitemap_link'),
			array('check', 'optimus_sitemap_boards'),
			array('int',   'optimus_sitemap_topics')
		);

		// Обновляем запись в Диспетчере задач
		$pmxcFunc['db_query']('', '
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

	/**
	 * Страница пожертвований
	 *
	 * @return void
	 */
	public static function donateSettings()
	{
		global $context, $txt;

		$context['sub_template'] = 'donate';
		$context['page_title']  .= ' - ' . $txt['optimus_donate_title'];
	}

	/**
	 * Подготовка к созданию файла robots.txt
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

		$temp_map = file_exists($boarddir . '/' . $map);
		$map      = $temp_map ? $path_map : '';
		$url_path = parse_url($boardurl, PHP_URL_PATH);

		$folders = array('attachments','avatars','Packages','Smileys','Sources');
		$actions = array('msg','profile','help','search','mlist','sort','recent','register','groups','stats','unread','topicseen','showtopic','prev_next','imode','wap','all');

		$common_rules = [];
		$common_rules[] = "User-agent: *";

		// Special rules for Pretty URLs or SimpleSEF
		if ($sef) {
			foreach ($folders as $folder)
				$common_rules[] = "Disallow: " . $url_path . "/" . $folder . "/";

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

		$new_robots = implode("<br>", str_replace("|", "", $new_robots));
		$context['new_robots_content'] = parse_bbc('[code]' . $new_robots . '[/code]');
	}
}
