<?php

namespace Bugo\Optimus;

/**
 * Settings.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2021 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7.3
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
			'label' => $txt['optimus_title'],
			'function' => function() {
				self::settingActions();
			},
			'icon' => 'maintain.gif',
			'subsections' => array(
				'basic'    => array($txt['optimus_base_title']),
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
	 * Ключевая функция, подключающая все остальные при их вызове
	 *
	 * @return void
	 */
	public static function settingActions()
	{
		global $context, $sourcedir, $txt, $smcFunc, $scripturl;

		$context['page_title'] = OP_NAME;

		// Подключаем файл шаблона вместе с таблицами стилей
		loadTemplate('Optimus', array('admin', 'optimus/optimus'));

		$subActions = array(
			'basic'    => 'basicSettings',
			'extra'    => 'extraSettings',
			'favicon'  => 'faviconSettings',
			'metatags' => 'metatagsSettings',
			'counters' => 'counterSettings',
			'robots'   => 'robotsSettings',
			'sitemap'  => 'sitemapSettings'
		);

		require_once($sourcedir . '/ManageSettings.php');
		loadGeneralSettingParameters($subActions, 'basic');

		db_extend();

		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['optimus_title'],
			'tabs' => array(
				'basic' => array(
					'description' => sprintf($txt['optimus_base_desc'], OP_VERSION, phpversion(), $smcFunc['db_title'], $smcFunc['db_get_version']()),
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
	public static function basicSettings()
	{
		global $context, $txt, $scripturl, $modSettings, $smcFunc;

		$context['sub_template'] = 'base';
		$context['page_title'] .= ' - ' . $txt['optimus_base_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=basic;save';

		$add_settings = array();
		if (!isset($modSettings['optimus_forum_index']))
			$add_settings['optimus_forum_index'] = $smcFunc['substr']($txt['forum_index'], 7);
		if (!isset($modSettings['optimus_no_first_number']))
			$add_settings['optimus_no_first_number'] = 1;
		if (!empty($add_settings))
			updateSettings($add_settings);

		$config_vars = array(
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

			if (isset($_POST['optimus_forum_index']))
				$_POST['optimus_forum_index'] = $smcFunc['htmlspecialchars']($_POST['optimus_forum_index']);

			if (isset($_POST['optimus_description']))
				$_POST['optimus_description'] = $smcFunc['htmlspecialchars']($_POST['optimus_description']);

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			updateSettings(array('optimus_templates' => serialize($templates)));
			redirectexit('action=admin;area=optimus;sa=basic');
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

		$add_settings = array();
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
		global $context, $txt, $scripturl, $boarddir, $modSettings, $sourcedir;

		$context['sub_template'] = 'robots';
		$context['page_title'] .= ' - ' . $txt['optimus_robots_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=robots;save';

		$root = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : $boarddir;
		if (empty($modSettings['optimus_root_path']))
			updateSettings(array('optimus_root_path' => $root));

		$config_vars = array(
			array('text', 'optimus_root_path')
		);

		$robots_path = (!empty($modSettings['optimus_root_path']) ? $modSettings['optimus_root_path'] : $root) . '/robots.txt';
		$context['robots_content'] = is_writable($robots_path) ? file_get_contents($robots_path) : '';

		require_once($sourcedir . "/Optimus/Robots.php");
		(new Robots())->generate();

		if (isset($_GET['save'])) {
			checkSession();

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			if (is_writable($root))
				file_put_contents($robots_path, filter_input(INPUT_POST, 'robots', FILTER_SANITIZE_STRING), LOCK_EX);

			redirectexit('action=admin;area=optimus;sa=robots');
		}

		prepareDBSettingContext($config_vars);
	}

	/**
	 * Страница с настройками карты форума
	 *
	 * @return void
	 */
	public static function sitemapSettings()
	{
		global $context, $txt, $scripturl, $modSettings, $smcFunc;

		$context['page_title'] .= ' - ' . $txt['optimus_sitemap_title'];
		$context['settings_title'] = $txt['optimus_sitemap_title'];
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=sitemap;save';

		if (!isset($modSettings['optimus_sitemap_topics_num_replies']))
			updateSettings(array('optimus_sitemap_topics_num_replies' => 5));
		if (!isset($modSettings['optimus_sitemap_items_display']))
			updateSettings(array('optimus_sitemap_items_display' => 10000));

		$config_vars = array(
			array('check', 'optimus_sitemap_enable'),
			array('check', 'optimus_sitemap_link'),
			array('check', 'optimus_remove_previous_xml_files'),
			array('select', 'optimus_main_page_frequency', $txt['optimus_main_page_frequency_set']),
			array('check', 'optimus_sitemap_boards', 'subtext' => $txt['optimus_sitemap_boards_subtext']),
			array('int', 'optimus_sitemap_topics_num_replies'),
			array('int', 'optimus_sitemap_items_display')
		);

		if (isset($_GET['save'])) {
			checkSession();

			if ($_POST['optimus_sitemap_topics_num_replies'] < 0)
				$_POST['optimus_sitemap_topics_num_replies'] = 0;

			if ($_POST['optimus_sitemap_items_display'] > 50000)
				$_POST['optimus_sitemap_items_display'] = 50000;
			elseif ($_POST['optimus_sitemap_items_display'] < 1)
				$_POST['optimus_sitemap_items_display'] = 1;

			// Обновляем запись в Диспетчере задач
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}scheduled_tasks
				SET disabled = {int:disabled}
				WHERE task = {string:task}',
				array(
					'disabled' => !empty($_POST['optimus_sitemap_enable']) ? 0 : 1,
					'task'     => 'optimus_sitemap'
				)
			);

			if (!empty($_POST['optimus_sitemap_enable'])) {
				scheduled_optimus_sitemap();

				CalculateNextTrigger('optimus_sitemap');
			}

			$save_vars = $config_vars;
			saveDBSettings($save_vars);

			redirectexit('action=admin;area=optimus;sa=sitemap');
		}

		prepareDBSettingContext($config_vars);
	}
}
