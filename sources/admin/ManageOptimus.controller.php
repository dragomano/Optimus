<?php

/**
 * ManageOptimus.controller.php
 *
 * @package Optimus
 * @link https://addons.elkarte.net/feature/Optimus.html
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 0.5
 */

class ManageOptimus_Controller extends Action_Controller
{
	/**
	 * Ключевая функция, подключающая все остальные при их вызове
	 *
	 * @return void
	 */
	public function action_index()
	{
		global $context, $txt, $scripturl;

		$context['page_title'] = $txt['optimus_main'];

		// Подключаем файл шаблона вместе с таблицей стилей
		loadTemplate('Optimus');
		loadCSSFile('optimus.css');

		$subActions = array(
			'base'     => array($this, 'baseSettings'),
			'extra'    => array($this, 'extraSettings'),
			'favicon'  => array($this, 'faviconSettings'),
			'metatags' => array($this, 'metatagsSettings'),
			'counters' => array($this, 'counterSettings'),
			'robots'   => array($this, 'robotsSettings'),
			'sitemap'  => array($this, 'sitemapSettings')
		);

		// Запускаем контроллер
		$action = new Action();

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
					'description' => sprintf($txt['optimus_sitemap_desc'], $scripturl . '?action=admin;area=scheduledtasks;' . $context['session_var'] . '=' . $context['session_id'])
				)
			)
		);

		// Устанавливаем вкладку по умолчанию
		$subAction = $action->initialize($subActions, 'base');

		// Переключаемся на другие вкладки
		$context[$context['admin_menu_name']]['current_subsection'] = $subAction;
		$context['sub_action'] = $subAction;
		$action->dispatch($subAction);
	}

	/**
	 * Основные настройки мода
	 *
	 * @return void
	 */
	public function baseSettings()
	{
		global $context, $txt, $scripturl, $modSettings;

		$context['page_title'] .= ' - ' . $txt['optimus_base_title'];
		$context['sub_template'] = 'show_settings';
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
			array('check', 'optimus_404_status')
		);

		$settingsForm = new Settings_Form(Settings_Form::DB_ADAPTER);
		$settingsForm->setConfigVars($config_vars);

		if (isset($this->_req->query->save)) {
			checkSession();

			$settingsForm->setConfigValues((array) $this->_req->post);
			$settingsForm->save();

			redirectexit('action=admin;area=optimus;sa=base');
		}

		$settingsForm->prepare();
	}

	/**
	 * Страница с настройками микроразметки
	 *
	 * @return void
	 */
	public function extraSettings()
	{
		global $context, $txt, $scripturl, $modSettings, $settings;

		$context['page_title'] .= ' - ' . $txt['optimus_extra_title'];
		$context['sub_template'] = 'show_settings';
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=extra;save';

		$config_vars = array(
			array('title', 'optimus_extra_title'),
			array('text', 'optimus_fb_appid', 40),
			array('text', 'optimus_tw_cards', 40, 'preinput' => '@'),
			array('check', 'optimus_json_ld')
		);

		$settingsForm = new Settings_Form(Settings_Form::DB_ADAPTER);
		$settingsForm->setConfigVars($config_vars);

		if (isset($this->_req->query->save)) {
			$this->_req->post['optimus_tw_cards'] = str_replace('@', '', $this->_req->post['optimus_tw_cards']);

			checkSession();

			$settingsForm->setConfigValues((array) $this->_req->post);
			$settingsForm->save();

			redirectexit('action=admin;area=optimus;sa=extra');
		}

		$settingsForm->prepare();
	}

	/**
	 * Управление фавиконкой форума
	 *
	 * @return void
	 */
	public function faviconSettings()
	{
		global $context, $txt, $scripturl;

		$context['page_title'] .= ' - ' . $txt['optimus_favicon_title'];
		$context['sub_template'] = 'favicon';
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=favicon;save';

		$config_vars = array(
			array('text', 'optimus_favicon_api_key'),
			array('large_text', 'optimus_favicon_text')
		);

		$settingsForm = new Settings_Form(Settings_Form::DB_ADAPTER);
		$settingsForm->setConfigVars($config_vars);

		if (isset($this->_req->query->save)) {
			checkSession();

			$settingsForm->setConfigValues((array) $this->_req->post);
			$settingsForm->save();

			redirectexit('action=admin;area=optimus;sa=favicon');
		}

		$settingsForm->prepare();
	}

	/**
	 * Управление мета-тегами
	 *
	 * @return void
	 */
	public function metatagsSettings()
	{
		global $context, $txt, $scripturl;

		$context['page_title'] .= ' - ' . $txt['optimus_meta_title'];
		$context['sub_template'] = 'metatags';
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=metatags;save';

		$config_vars = array();

		$settingsForm = new Settings_Form(Settings_Form::DB_ADAPTER);
		$settingsForm->setConfigVars($config_vars);

		if (isset($this->_req->query->save)) {
			$meta = array();
			if (isset($this->_req->post['custom_tag_name'])) {
				foreach ($this->_req->post['custom_tag_name'] as $key => $value) {
					if (empty($value))
						unset($this->_req->post['custom_tag_name'][$key], $this->_req->post['custom_tag_value'][$key]);
					else
						$meta[$this->_req->post['custom_tag_name'][$key]] = $this->_req->post['custom_tag_value'][$key];
				}
			}

			checkSession();

			$settingsForm->setConfigValues((array) $this->_req->post);
			$settingsForm->save();

			updateSettings(array('optimus_meta' => serialize($meta)));
			redirectexit('action=admin;area=optimus;sa=metatags');
		}

		$settingsForm->prepare();
	}

	/**
	 * Управление счетчиками
	 *
	 * @return void
	 */
	public function counterSettings()
	{
		global $context, $txt, $scripturl, $modSettings;

		$context['page_title'] .= ' - ' . $txt['optimus_counters'];
		$context['sub_template'] = 'counters';
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=counters;save';

		if (!isset($modSettings['optimus_counters_css']))
			updateSettings(array('optimus_counters_css' => '.copyright a>img {opacity: 0.3} .copyright a:hover>img {opacity: 1.0}'));
		if (!isset($modSettings['optimus_ignored_actions']))
			updateSettings(array('optimus_ignored_actions' => 'admin,bookmarks,credits,helpadmin,pm,printpage'));

		$config_vars = array(
			array('large_text', 'optimus_head_code'),
			array('large_text', 'optimus_stat_code'),
			array('large_text', 'optimus_count_code'),
			array('large_text', 'optimus_counters_css'),
			array('text', 'optimus_ignored_actions')
		);

		$settingsForm = new Settings_Form(Settings_Form::DB_ADAPTER);
		$settingsForm->setConfigVars($config_vars);

		if (isset($this->_req->query->save)) {
			checkSession();

			$settingsForm->setConfigValues((array) $this->_req->post);
			$settingsForm->save();

			redirectexit('action=admin;area=optimus;sa=counters');
		}

		$settingsForm->prepare();
	}

	/**
	 * Страница для изменения robots.txt
	 *
	 * @return void
	 */
	public function robotsSettings()
	{
		global $context, $txt, $scripturl;

		$context['page_title'] .= ' - ' . $txt['optimus_robots_title'];
		$context['sub_template'] = 'robots';
		$context['post_url'] = $scripturl . '?action=admin;area=optimus;sa=robots;save';

		$common_rules_path = (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '') . '/robots.txt';

		clearstatcache();

		$context['robots_txt_exists'] = file_exists($common_rules_path);
		$context['robots_content']    = $context['robots_txt_exists'] ? file_get_contents($common_rules_path) : '';

		self::robotsCreate();

		if (isset($this->_req->query->save)) {
			checkSession();

			$common_rules = stripslashes($this->_req->post['robots']);
			file_put_contents($common_rules_path, $common_rules);

			redirectexit('action=admin;area=optimus;sa=robots');
		}
	}

	/**
	 * Страница с настройками карты форума
	 *
	 * @return void
	 */
	public function sitemapSettings()
	{
		global $context, $txt, $scripturl, $modSettings;

		$context['page_title'] .= ' - ' . $txt['optimus_sitemap_title'];
		$context['sub_template'] = 'show_settings';
		$context['post_url']    = $scripturl . '?action=admin;area=optimus;sa=sitemap;save';

		$config_vars = array(
			array('title', 'optimus_sitemap_xml_link'),
			array('check', 'optimus_sitemap_enable'),
			array('check', 'optimus_sitemap_link'),
			array('check', 'optimus_sitemap_boards'),
			array('int',   'optimus_sitemap_topics')
		);

		$db = database();

		// Обновляем запись в Диспетчере задач
		$db->query('', '
			UPDATE {db_prefix}scheduled_tasks
			SET disabled = {int:disabled}
			WHERE task = {string:task}',
			array(
				'disabled' => !empty($modSettings['optimus_sitemap_enable']) ? 0 : 1,
				'task'     => 'optimus_sitemap'
			)
		);

		if (!empty($modSettings['optimus_sitemap_enable'])) {
			require_once(SUBSDIR . '/ScheduledTasks.subs.php');
			CalculateNextTrigger('optimus_sitemap');
		}

		$settingsForm = new Settings_Form(Settings_Form::DB_ADAPTER);
		$settingsForm->setConfigVars($config_vars);

		if (isset($this->_req->query->save)) {
			checkSession();

			$settingsForm->setConfigValues((array) $this->_req->post);
			$settingsForm->save();

			redirectexit('action=admin;area=optimus;sa=sitemap');
		}

		$settingsForm->prepare();
	}

	/**
	 * Подготовка к созданию файла robots.txt
	 *
	 * @return void
	 */
	private static function robotsCreate()
	{
		global $boardurl, $boarddir, $modSettings, $context;

		clearstatcache();

		$map         = 'sitemap.xml';
		$path_map    = $boardurl . '/' . $map;
		$temp_map    = file_exists($boarddir . '/' . $map);
		$temp_map_gz = file_exists($boarddir . '/' . $map . '.gz');
		$map         = $temp_map ? $path_map : '';
		$map_gz      = $temp_map_gz ? $path_map . '.gz': '';
		$url_path    = parse_url($boardurl, PHP_URL_PATH);

		$folders = array('addons','attachments','avatars','cache','packages','smileys','sources');

		$common_rules = [];
		$common_rules[] = "User-agent: *";
		$common_rules[] = "Disallow: " . $url_path . "/*action";

		if (!empty($modSettings['queryless_urls']))
			$common_rules[] = "";
		else
			$common_rules[] = "Disallow: " . $url_path . "/*topic=*.msg\nDisallow: " . $url_path . "/*topic=*.new";

		$common_rules[] = "Disallow: " . $url_path . "/*PHPSESSID";
		$common_rules[] = "Disallow: " . $url_path . "/*;";

		// Front page
		$common_rules[] = "Allow: " . $url_path . "/$";

		// Content
		if (!empty($modSettings['queryless_urls']))
			$common_rules[] = "Allow: " . $url_path . "/*board*.html$\nAllow: " . $url_path . "/*topic*.html$";
		else
			$common_rules[] = "Allow: " . $url_path . "/*board\nAllow: " . $url_path . "/*topic";

		// RSS
		$common_rules[] = !empty($modSettings['xmlnews_enable']) ? "Allow: " . $url_path . "/*.xml" : "";

		// Sitemap
		$common_rules[] = !empty($map) || file_exists(SUBSDIR . '/Sitemap.php') ? "Allow: " . $url_path . "/*sitemap" : "";

		// We have nothing to hide ;)
		$common_rules[] = "Allow: /*.css$\nAllow: /*.js$\nAllow: /*.png$\nAllow: /*.jpg$\nAllow: /*.gif$";

		// Sitemap XML
		if (!empty($map)) {
			$common_rules[] = "|";
			$common_rules[] = "Sitemap: " . $map;
			$common_rules[] = !empty($map_gz) ? "Sitemap: " . $map_gz : "";
		}

		$new_robots = array();

		foreach ($common_rules as $line) {
			if (!empty($line))
				$new_robots[] = $line;
		}

		$new_robots = implode('<br>', str_replace('|', '', $new_robots));
		$context['new_robots_content'] = parse_bbc('[code]' . $new_robots . '[/code]');
	}
}
