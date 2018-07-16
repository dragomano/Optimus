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
 * @version 0.1 alpha
 */

if (!defined('WEDGE'))
	die('Hacking attempt...');

class OptimusAdmin
{
	/**
	 * Прописываем менюшку с настройками мода в админке
	 *
	 * @param array $admin_areas
	 * @return void
	 */
	public static function adminAreas()
	{
		global $admin_areas, $txt, $context;

		$admin_areas['plugins']['areas']['optimus'] = array(
			'label'    => $txt['optimus_main'],
			'function' => function(){self::settingActions();},
			'icon' => $context['plugins_url']['Bugo:Optimus'] . '/images/optimus_small.png',
			'bigicon' => $context['plugins_url']['Bugo:Optimus'] . '/images/optimus_large.png',
			'permission' => array('admin_forum'),
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

	/**
	 * Легкий доступ к настройкам мода через быстрый поиск в админке
	 *
	 * @param array $language_files
	 * @param array $include_files
	 * @param array $settings_search
	 * @return void
	 */
	public static function adminSearch(&$settings_search)
	{
		$settings_search['plugins'][] = array('OptimusAdmin::baseSettings', 'area=optimus;sa=base');
		$settings_search['plugins'][] = array('OptimusAdmin::extraSettings', 'area=optimus;sa=extra');
		$settings_search['plugins'][] = array('OptimusAdmin::faviconSettings', 'area=optimus;sa=favicon');
		$settings_search['plugins'][] = array('OptimusAdmin::counterSettings', 'area=optimus;sa=counters');
		$settings_search['plugins'][] = array('OptimusAdmin::sitemapSettings', 'area=optimus;sa=sitemap');
	}

	/**
	 * Ключевая функция, подключающая все остальные при их вызове
	 *
	 * @return void
	 */
	public static function settingActions()
	{
		global $context, $txt, $sourcedir;

		$context['page_title'] = $txt['optimus_main'];

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

		$_REQUEST['sa'] = isset($_REQUEST['sa'], $subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'base';

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['optimus_title'],
			'tabs' => array(
				'base' => array(
					'description' => $txt['optimus_base_desc']
				),
				'extra' => array(
					'description' => $txt['optimus_extra_desc']
				),
				'favicon' => array(
					'description' => $txt['optimus_favicon_desc']
				),
				'metatags' => array(
					'description' => $txt['optimus_meta_desc']
				),
				'counters' => array(
					'description' => $txt['optimus_counters_desc']
				),
				'robots' => array(
					'description' => $txt['optimus_robots_desc']
				),
				'sitemap' => array(
					'description' => sprintf($txt['optimus_sitemap_desc'], SCRIPT . '?action=admin;area=scheduledtasks;' . $context['session_var'] . '=' . $context['session_id'])
				),
				'donate' => array(
					'description' => $txt['optimus_donate_desc']
				)
			)
		);

		$subActions[$_REQUEST['sa']]();
	}

	/**
	 * Основные настройки мода
	 *
	 * @return void
	 */
	public static function baseSettings($return_config = false)
	{
		global $context, $txt, $settings;

		$context['page_title'] .= ' - ' . $txt['optimus_base_title'];
		$context['post_url'] = SCRIPT . '?action=admin;area=optimus;sa=base;save';

		if (empty($settings['optimus_forum_index']))
			updateSettings(array('optimus_forum_index' => $context['forum_name'] . ' - ' . $txt['home']));

		$config_vars = array(
			array('title', 'optimus_main_page'),
			array('text', 'optimus_forum_index', 40),
			array('large_text', 'optimus_description', '4" style="width:80%'),
			array('title', 'optimus_all_pages'),
			array('select', 'optimus_board_extend_title', $txt['optimus_board_extend_title_set']),
			array('select', 'optimus_topic_extend_title', $txt['optimus_topic_extend_title_set'])
		);

		if ($return_config)
			return $config_vars;

		loadSource('ManageServer');

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			redirectexit('action=admin;area=optimus;sa=base');
		}

		wetem::load('show_settings');
		prepareDBSettingContext($config_vars);
	}

	/**
	 * Страница с настройками микроразметки
	 *
	 * @return void
	 */
	public static function extraSettings($return_config = false)
	{
		global $context, $txt;

		$context['page_title'] .= ' - ' . $txt['optimus_extra_title'];
		$context['settings_title'] = $txt['optimus_extra_title'];
		$context['post_url'] = SCRIPT . '?action=admin;area=optimus;sa=extra;save';

		$config_vars = array(
			array('text', 'optimus_default_og_image', 40),
			array('check', 'optimus_topic_body_og_image'),
			array('check', 'optimus_topic_attach_og_image'),
			array('text', 'optimus_fb_appid', 40, 'help' => 'optimus_fb_appid_help'),
			array('text', 'optimus_tw_cards', 40, 'preinput' => '@', 'help' => 'optimus_tw_cards_help'),
			array('check', 'optimus_json_ld', 'help' => 'optimus_json_ld_help')
		);

		if ($return_config)
			return $config_vars;

		loadSource('ManageServer');

		if (isset($_GET['save'])) {
			$_POST['optimus_tw_cards'] = str_replace('@', '', $_POST['optimus_tw_cards']);

			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			redirectexit('action=admin;area=optimus;sa=extra');
		}

		wetem::load('show_settings');
		prepareDBSettingContext($config_vars);
	}

	/**
	 * Управление фавиконкой форума
	 *
	 * @return void
	 */
	public static function faviconSettings($return_config = false)
	{
		global $context, $txt;

		$context['page_title'] .= ' - ' . $txt['optimus_favicon_title'];
		$context['settings_title'] = $txt['optimus_favicon_title'];
		$context['post_url'] = SCRIPT . '?action=admin;area=optimus;sa=favicon;save';

		$config_vars = array(
			array('text', 'optimus_favicon_api_key'),
			array('large_text', 'optimus_favicon_text')
		);

		if ($return_config)
			return $config_vars;

		loadSource('ManageServer');
		loadPluginTemplate('Bugo:Optimus', 'Optimus');
		wetem::load('favicon');

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
		global $context, $txt;

		$context['page_title'] .= ' - ' . $txt['optimus_meta_title'];
		$context['post_url'] = SCRIPT . '?action=admin;area=optimus;sa=metatags;save';

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

		loadSource('ManageServer');
		loadPluginTemplate('Bugo:Optimus', 'Optimus');
		wetem::load('metatags');

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
	public static function counterSettings($return_config = false)
	{
		global $context, $txt;

		$context['page_title'] .= ' - ' . $txt['optimus_counters'];
		$context['post_url'] = SCRIPT . '?action=admin;area=optimus;sa=counters;save';

		$config_vars = array(
			array('large_text', 'optimus_head_code'),
			array('large_text', 'optimus_stat_code'),
			array('large_text', 'optimus_count_code'),
			array('large_text', 'optimus_counters_css'),
			array('text', 'optimus_ignored_actions')
		);

		if ($return_config)
			return $config_vars;

		loadSource('ManageServer');
		loadPluginTemplate('Bugo:Optimus', 'Optimus');
		wetem::load('counters');

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
		global $context, $txt;

		$context['page_title']  .= ' - ' . $txt['optimus_robots_title'];
		$context['post_url'] = SCRIPT . '?action=admin;area=optimus;sa=robots;save';

		$robots_file = ROOT_DIR . "/robots.txt";

		clearstatcache();

		$context['robots_txt_exists'] = file_exists($robots_file);
		$context['robots_content']    = $context['robots_txt_exists'] ? file_get_contents($robots_file) : '';

		self::robotsCreate();

		loadPluginTemplate('Bugo:Optimus', 'Optimus');
		wetem::load('robots');

		if (isset($_GET['save'])) {
			checkSession();

			if (isset($_POST['robots'])) {
				$common_rules = stripslashes($_POST['robots']);
				file_put_contents($robots_file, $common_rules);
			}

			redirectexit('action=admin;area=optimus;sa=robots');
		}
	}

	/**
	 * Страница с настройками карты форума
	 *
	 * @return void
	 */
	public static function sitemapSettings($return_config = false)
	{
		global $context, $txt, $settings;

		$context['page_title'] .= ' - ' . $txt['optimus_sitemap_title'];
		$context['settings_title'] = $txt['optimus_sitemap_title'];
		$context['post_url'] = SCRIPT . '?action=admin;area=optimus;sa=sitemap;save';

		$config_vars = array(
			array('check', 'optimus_sitemap_enable'),
			array('check', 'optimus_sitemap_link'),
			array('check', 'optimus_sitemap_boards'),
			array('int', 'optimus_sitemap_topics')
		);

		if ($return_config)
			return $config_vars;

		loadSource('ManageServer');
		wetem::load('show_settings');

		// Обновляем запись в Диспетчере задач
		wesql::query('
			UPDATE {db_prefix}scheduled_tasks
			SET disabled = {int:disabled}
			WHERE task = {string:task}',
			array(
				'disabled' => !empty($settings['optimus_sitemap_enable']) ? 0 : 1,
				'task'     => 'optimus_sitemap'
			)
		);

		if (!empty($settings['optimus_sitemap_enable'])) {
			loadSource('ScheduledTasks');
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

		loadPluginTemplate('Bugo:Optimus', 'Optimus');
		wetem::load('donate');
		$context['page_title']  .= ' - ' . $txt['optimus_donate_title'];
	}

	/**
	 * Генерация правил для robots.txt
	 *
	 * @return void
	 */
	private static function robotsCreate()
	{
		global $settings, $boardurl, $context;

		// SEF enabled?
		$sef = !empty($settings['pretty_enable_filters']);

		$map      = 'sitemap.xml';
		$path_map = $boardurl . '/' . $map;

		clearstatcache();

		$temp_map    = file_exists(ROOT_DIR . '/' . $map);
		$temp_map_gz = file_exists(ROOT_DIR . '/' . $map . '.gz');
		$map         = $temp_map ? $path_map : '';
		$map_gz      = $temp_map_gz ? $path_map . '.gz': '';
		$url_path    = parse_url($boardurl, PHP_URL_PATH);

		$common_rules = [];
		$common_rules[] = "User-agent: *";

		if ($sef) {
			$common_rules[] = "Disallow: " . $url_path . '/' . (!empty($settings['pretty_filters']['actions']) && !empty($settings['pretty_prefix_action']) ? $settings['pretty_prefix_action'] : '');
		} else
			$common_rules[] = "Disallow: " . $url_path . "/?action";

		$common_rules[] = $sef ? "" : "Disallow: " . $url_path . "/?topic=*.msg\nDisallow: " . $url_path . "/?topic=*.new";

		$common_rules[] = "Disallow: " . $url_path . "/*PHPSESSID";
		$common_rules[] = $sef ? "" : "Disallow: " . $url_path . "/*;";

		// Front page
		$common_rules[] = "Allow: " . $url_path . "/$";

		// Content
		$common_rules[] = ($sef ? "" : "Allow: " . $url_path . "/?board=*\nAllow: " . $url_path . "/?topic=*");

		// RSS feed
		$common_rules[] = !empty($settings['xmlnews_enable']) ? "Allow: " . $url_path . ($sef ? "/do/feed/" : "/?action=feed") : "";

		// We have nothing to hide ;)
		$common_rules[] = "Allow: /*.css$\nAllow: /*.js$\nAllow: /*.png$\nAllow: /*.jpg$\nAllow: /*.gif$";

		// Sitemap XML
		$common_rules[] = !empty($map) || !empty($map_gz) ? "|" : "";
		$common_rules[] = !empty($map) ? "Sitemap: " . $map : "";
		$common_rules[] = !empty($map_gz) ? "Sitemap: " . $map_gz : "";

		$new_robots = array();

		foreach ($common_rules as $line) {
			if (!empty($line))
				$new_robots[] = $line;
		}

		$new_robots = implode("<br>", str_replace("|", "", $new_robots));
		$context['new_robots_content'] = parse_bbc('[code]' . $new_robots . '[/code]');
	}
}
