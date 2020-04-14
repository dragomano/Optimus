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
 * @version 2.7.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Optimus settings
 */
class Settings
{
	/**
	 * Remove meta_keywords setting (it moved to Optimus settings)
	 *
	 * @param array $config_vars
	 * @return void
	 */
	public static function modifyBasicSettings(&$config_vars)
	{
		foreach ($config_vars as $key => $dump) {
			if (isset($dump[1]) && $dump[1] == 'meta_keywords') {
				unset($config_vars[$key]);
				break;
			}
		}
	}

	/**
	 * The menu with the settings of the mod in the admin area
	 *
	 * @param array $admin_areas
	 * @return void
	 */
	public static function adminAreas(&$admin_areas)
	{
		global $settings, $txt;

		addInlineCss('
		.main_icons.optimus::before {
			background:url(' . $settings['default_images_url'] . '/optimus.png) no-repeat 0 0 !important;
		}
		.large_admin_menu_icon.optimus::before {
			background:url(' . $settings['default_images_url'] . '/optimus_large.png) no-repeat 0 0;
		}
		.fa-optimus::before {
			content: "\f717";
		}');

		if (isset($_REQUEST['area']) && $_REQUEST['area'] == 'optimus')
			loadCSSFile('optimus/optimus.css');

		$admin_areas['config']['areas']['optimus'] = array(
			'label' => $txt['optimus_title'],
			'function' => function () {
				self::settingActions();
			},
			'icon' => 'optimus',
			'subsections' => array(
				'base'     => array($txt['optimus_base_title']),
				'extra'    => array($txt['optimus_extra_title']),
				'favicon'  => array($txt['optimus_favicon_title']),
				'metatags' => array($txt['optimus_meta_title']),
				'counters' => array($txt['optimus_counters']),
				'robots'   => array($txt['optimus_robots_title']),
				'sitemap'  => array($txt['optimus_sitemap_title'])
			)
		);
	}

	/**
	 * Extend the quick search in the admin panel
	 *
	 * @param array $language_files
	 * @param array $include_files
	 * @param array $settings_search
	 * @return void
	 */
	public static function adminSearch(&$language_files, &$include_files, &$settings_search)
	{
		$settings_search[] = array(__CLASS__ . '::baseTabSettings', 'area=optimus;sa=base');
		$settings_search[] = array(__CLASS__ . '::extraTabSettings', 'area=optimus;sa=extra');
		$settings_search[] = array(__CLASS__ . '::faviconTabSettings', 'area=optimus;sa=favicon');
		$settings_search[] = array(__CLASS__ . '::countersTabSettings', 'area=optimus;sa=counters');
		$settings_search[] = array(__CLASS__ . '::sitemapTabSettings', 'area=optimus;sa=sitemap');
	}

	/**
	 * The main function that connects all others when they are called
	 *
	 * @return void
	 */
	public static function settingActions()
	{
		global $context, $txt, $sourcedir;

		$context['page_title'] = $txt['optimus_main'];

		loadTemplate('Optimus');
		loadLanguage('ManageSettings');

		$subActions = array(
			'base'     => 'baseTabSettings',
			'extra'    => 'extraTabSettings',
			'favicon'  => 'faviconTabSettings',
			'metatags' => 'metatagsTabSettings',
			'counters' => 'countersTabSettings',
			'robots'   => 'robotsTabSettings',
			'sitemap'  => 'sitemapTabSettings'
		);

		require_once($sourcedir . '/ManageSettings.php');
		loadGeneralSettingParameters($subActions, 'base');

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
					'description' => $txt['optimus_sitemap_desc']
				)
			)
		);

		call_helper(__CLASS__ . '::' . $subActions[$_REQUEST['sa']]);
	}

	/**
	 * Main mod settings
	 *
	 * @param bool $return_config
	 *
	 * @return array|void
	 */
	public static function baseTabSettings($return_config = false)
	{
		global $context, $txt, $scripturl, $modSettings;

		$context['page_title'] .= ' - ' . $txt['optimus_base_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=base;save';

		if (empty($modSettings['optimus_forum_index']))
			updateSettings(array('optimus_forum_index' => sprintf($txt['forum_index'], $context['forum_name'])));

		$config_vars = array(
			array('title', 'optimus_main_page'),
			array('text', 'optimus_forum_index', 40),
			array('large_text', 'optimus_description', '4" style="width:80%', 'subtext' => $txt['optimus_description_subtext']),
			array('large_text', 'meta_keywords', '4" style="width:80%', 'subtext' => $txt['meta_keywords_note']),
			array('title', 'optimus_all_pages'),
			array('select', 'optimus_board_extend_title', $txt['optimus_board_extend_title_set']),
			array('select', 'optimus_topic_extend_title', $txt['optimus_topic_extend_title_set']),
			array('check', 'optimus_topic_description'),
			array('check', 'optimus_allow_change_topic_desc', 'subtext' => $txt['optimus_allow_change_topic_desc_subtext']),
			array('check', 'optimus_allow_change_topic_keywords', 'subtext' => $txt['optimus_allow_change_topic_keywords_subtext']),
			array('check', 'optimus_show_keywords_block'),
			array('check', 'optimus_allow_change_board_og_image', 'subtext' => $txt['optimus_allow_change_board_og_image_subtext'], 'disabled' => !defined('JQUERY_VERSION')),
			array('check', 'optimus_correct_http_status')
		);

		if (defined('JQUERY_VERSION')) {
			$config_vars[] = array('title', 'optimus_extra_settings');
			$config_vars[] = array('check', 'optimus_use_only_cookies', 'help' => 'optimus_use_only_cookies_help');
			$config_vars[] = array('check', 'optimus_remove_index_php', 'disabled' => !empty($modSettings['simplesef_enable']));
			$config_vars[] = array('check', 'optimus_extend_h1');
		}

		if ($return_config)
			return $config_vars;

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			redirectexit('action=admin;area=optimus;sa=base');
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * The markup settings
	 *
	 * @param bool $return_config
	 *
	 * @return array|void
	 */
	public static function extraTabSettings($return_config = false)
	{
		global $context, $txt, $scripturl, $settings;

		$context['page_title'] .= ' - ' . $txt['optimus_extra_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=extra;save';

		$og_image_option_link = $scripturl . '?action=admin;area=theme;sa=list;th=' . $settings['theme_id']  . '#options_og_image';

		$config_vars = array(
			array('title', 'optimus_extra_title'),
			array('check', 'optimus_og_image', 'help' => 'optimus_og_image_help', 'subtext' => sprintf($txt['optimus_og_image_subtext'], $og_image_option_link)),
			array('text', 'optimus_fb_appid', 40, 'help' => 'optimus_fb_appid_help'),
			array('text', 'optimus_tw_cards', 40, 'preinput' => '@', 'help' => 'optimus_tw_cards_help'),
		);

		if ($return_config)
			return $config_vars;

		if (isset($_GET['save'])) {
			$_POST['optimus_tw_cards'] = str_replace('@', '', filter_input(INPUT_POST, 'optimus_tw_cards', FILTER_SANITIZE_STRING));

			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			redirectexit('action=admin;area=optimus;sa=extra');
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * Favicon settings
	 *
	 * @param bool $return_config
	 *
	 * @return array|void
	 */
	public static function faviconTabSettings($return_config = false)
	{
		global $context, $txt, $scripturl;

		$context['page_title'] .= ' - ' . $txt['optimus_favicon_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=favicon;save';

		$config_vars = array(
			array('text', 'optimus_favicon_api_key'),
			array('large_text', 'optimus_favicon_text')
		);

		if ($return_config)
			return $config_vars;

		$context['sub_template'] = 'favicon';

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			redirectexit('action=admin;area=optimus;sa=favicon');
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * Metatags settings
	 *
	 * @return void
	 */
	public static function metatagsTabSettings()
	{
		global $context, $txt, $scripturl;

		$context['sub_template'] = 'metatags';
		$context['page_title'] .= ' - ' . $txt['optimus_meta_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=metatags;save';

		$config_vars = [];

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			$meta = [];
			if (isset($_POST['custom_tag_name']) && isset($_POST['custom_tag_value'])) {
				$custom_tag = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
				foreach ($custom_tag['custom_tag_name'] as $key => $value) {
					if (!empty($value))
						$meta[$value] = $custom_tag['custom_tag_value'][$key];
				}
			}

			updateSettings(array('optimus_meta' => serialize($meta)));
			redirectexit('action=admin;area=optimus;sa=metatags');
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * Counters settings
	 *
	 * @param bool $return_config
	 *
	 * @return array|void
	 */
	public static function countersTabSettings($return_config = false)
	{
		global $context, $txt, $scripturl, $modSettings;

		$context['page_title'] .= ' - ' . $txt['optimus_counters'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=counters;save';

		if (!isset($modSettings['optimus_counters_css']))
			updateSettings(array('optimus_counters_css' => '.counters {margin: 1.6em 0 -4.8em; text-align: center}'));
		if (!isset($modSettings['optimus_ignored_actions']))
			updateSettings(array('optimus_ignored_actions' => 'admin,bookmarks,credits,helpadmin,pm,printpage'));

		$config_vars = array(
			array('large_text', 'optimus_head_code'),
			array('large_text', 'optimus_stat_code'),
			array('large_text', 'optimus_count_code'),
			array('large_text', 'optimus_counters_css'),
			array('text', 'optimus_ignored_actions')
		);

		if ($return_config)
			return $config_vars;

		$context['sub_template'] = 'counters';

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			redirectexit('action=admin;area=optimus;sa=counters');
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * The robots.txt settings
	 *
	 * @return void
	 */
	public static function robotsTabSettings()
	{
		global $context, $txt, $scripturl, $modSettings, $sourcedir;

		$context['sub_template'] = 'robots';
		$context['page_title'] .= ' - ' . $txt['optimus_robots_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=robots;save';

		$config_vars = array(
			array('text', 'optimus_root_path')
		);

		$robots_path = ($_SERVER['DOCUMENT_ROOT'] ?? $modSettings['optimus_root_path'] ?? '') . "/robots.txt";
		$context['robots_content'] = file_get_contents($robots_path);
		require_once($sourcedir . "/Optimus/Robots.php");

		$robots = new Robots();
		$robots->generate();

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			file_put_contents($robots_path, filter_input(INPUT_POST, 'robots', FILTER_SANITIZE_STRING), LOCK_EX);

			redirectexit('action=admin;area=optimus;sa=robots');
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * The sitemap settings
	 *
	 * @param bool $return_config
	 *
	 * @return array|void
	 */
	public static function sitemapTabSettings($return_config = false)
	{
		global $context, $txt, $scripturl, $modSettings;

		$context['page_title'] .= ' - ' . $txt['optimus_sitemap_title'];
		$context['settings_title'] = $txt['optimus_sitemap_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=sitemap;save';

		if (!isset($modSettings['optimus_sitemap_items_display']))
			updateSettings(array('optimus_sitemap_items_display' => 10000));

		$config_vars = array(
			array('check', 'optimus_sitemap_enable'),
			array('check', 'optimus_sitemap_link'),
			array('select', 'optimus_main_page_frequency', $txt['optimus_main_page_frequency_set']),
			array('check', 'optimus_sitemap_boards', 'subtext' => $txt['optimus_sitemap_boards_subtext']),
			array('int', 'optimus_sitemap_topics_num_replies', 'min' => 0),
			array('int', 'optimus_sitemap_items_display', 'max' => 50000, 'min' => 1),
			array('check', 'optimus_sitemap_all_topic_pages', 'subtext' => $txt['optimus_sitemap_all_topic_pages_subtext'])
		);

		if ($return_config)
			return $config_vars;

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			redirectexit('action=admin;area=optimus;sa=sitemap');
		}

		prepareDBSettingContext($config_vars);
	}
}
