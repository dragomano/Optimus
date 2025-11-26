<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\Actions\Admin\ACP;
use Bugo\Compat\{Config, Db, ErrorHandler};
use Bugo\Compat\{IntegrationHook, Lang};
use Bugo\Compat\{Theme, User, Utils};
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Tasks\Sitemap;
use Bugo\Optimus\Utils\Input;
use Bugo\Optimus\Utils\Str;

if (! defined('SMF'))
	die('No direct access...');

final class SettingHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_modify_basic_settings', self::class . '::modifyBasicSettings#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_admin_areas', self::class . '::adminAreas#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_admin_search', self::class . '::adminSearch#', false, __FILE__
		);
	}

	/**
	 * Remove meta_keywords setting and move it to the Optimus settings
	 *
	 * Удаляем настройку meta_keywords и помещаем на страницу настроек Optimus
	 */
	public function modifyBasicSettings(array &$config_vars): void
	{
		foreach ($config_vars as $key => $dump) {
			if (isset($dump[1]) && $dump[1] === 'meta_keywords') {
				unset($config_vars[$key]);
			}
		}
	}

	public function adminAreas(array &$admin_areas): void
	{
		Theme::addInlineCss('
		.main_icons.optimus::before {
			background:url(' . Theme::$current->settings['default_images_url'] . '/optimus.png) no-repeat 0 0 !important;
		}
		.large_admin_menu_icon.optimus::before {
			background:url(' . Theme::$current->settings['default_images_url'] . '/optimus_large.png) no-repeat 0 0;
		}
		.fa-optimus::before {
			content: "\f717";
		}');

		if (Input::request('area') === 'optimus') {
			Theme::loadCSSFile('optimus/optimus.css');
		}

		$admin_areas['config']['areas']['optimus'] = [
			'label' => Lang::getTxt('optimus_title', file: 'Optimus/Optimus'),
			'function' => $this->actions(...),
			'icon' => 'optimus',
			'subsections' => [
				'basic'    => [Lang::getTxt('optimus_basic_title')],
				'extra'    => [Lang::getTxt('optimus_extra_title')],
				'favicon'  => [Lang::getTxt('optimus_favicon_title')],
				'metatags' => [Lang::getTxt('optimus_meta_title')],
				'redirect' => [Lang::getTxt('optimus_redirect_title')],
				'counters' => [Lang::getTxt('optimus_counters')],
				'robots'   => [Lang::getTxt('optimus_robots_title')],
				'htaccess' => [Lang::getTxt('optimus_htaccess_title')],
				'sitemap'  => [Lang::getTxt('optimus_sitemap_title')],
			]
		];

		if (str_starts_with(SMF_VERSION, '3.0')) {
			$admin_areas['config']['areas']['optimus']['label'] = 'optimus_title';
		}
	}

	public function adminSearch(array $language_files, array $include_files, array &$settings_search): void
	{
		$settings_search[] = [$this->basicTabSettings(...), 'area=optimus;sa=basic'];
		$settings_search[] = [$this->extraTabSettings(...), 'area=optimus;sa=extra'];
		$settings_search[] = [$this->faviconTabSettings(...), 'area=optimus;sa=favicon'];
		$settings_search[] = [$this->sitemapTabSettings(...), 'area=optimus;sa=sitemap'];
	}

	public function actions(): void
	{
		User::$me->isAllowedTo('admin_forum');

		Utils::$context['page_title'] = OP_NAME;

		Theme::loadTemplate('Optimus');

		$subActions = [
			'basic'    => 'basicTabSettings',
			'extra'    => 'extraTabSettings',
			'favicon'  => 'faviconTabSettings',
			'metatags' => 'metatagsTabSettings',
			'redirect' => 'redirectTabSettings',
			'counters' => 'counterTabSettings',
			'robots'   => 'robotsTabSettings',
			'htaccess' => 'htaccessTabSettings',
			'sitemap'  => 'sitemapTabSettings',
		];

		Utils::$context[Utils::$context['admin_menu_name']]['tab_data'] = [
			'title' => Lang::getTxt('optimus_title'),
			'tabs' => [
				'basic' => [
					'description' => sprintf(
						Lang::getTxt('optimus_basic_desc'),
						OP_VERSION,
						PHP_VERSION,
						Utils::$smcFunc['db_title'],
						Db::$db->get_version(),
					)
				],
				'extra' => [
					'description' => Lang::getTxt('optimus_extra_desc')
				],
				'favicon' => [
					'description' => Lang::getTxt('optimus_favicon_desc')
				],
				'metatags' => [
					'description' => Lang::getTxt('optimus_meta_desc')
				],
				'redirect' => [
					'description' => Lang::getTxt('optimus_redirect_desc')
				],
				'counters' => [
					'description' => Lang::getTxt('optimus_counters_desc')
				],
				'robots' => [
					'description' => Lang::getTxt('optimus_robots_desc')
				],
				'htaccess' => [
					'description' => Lang::getTxt('optimus_htaccess_desc')
				],
				'sitemap' => [
					'description' => sprintf(Lang::getTxt('optimus_sitemap_desc'), OP_NAME)
				]
			]
		];

		$this->addBlockWithTips();

		$this->callActionFromAreas($subActions);
	}

	/**
	 * @return void|array
	 */
	public function basicTabSettings(bool $return_config = false)
	{
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_basic_title');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=basic;save';

		$this->addDefaultSettings(
			['optimus_forum_index' => sprintf(Lang::getTxt('forum_index'), Utils::$context['forum_name'])]
		);

		$config_vars = [
			['title', 'optimus_main_page'],
			[
				'text',
				'optimus_forum_index',
				80,
				'value' => Utils::htmlspecialcharsDecode((string) (Config::$modSettings['optimus_forum_index'] ?? ''))
			],
			[
				'large_text',
				'optimus_description',
				'value' => Utils::htmlspecialcharsDecode((string) (Config::$modSettings['optimus_description'] ?? '')),
				'subtext' => Lang::getTxt('optimus_description_subtext')
			],
			[
				'large_text',
				'meta_keywords',
				'label' => Lang::getTxt('meta_keywords', file: 'Search'),
				'subtext' => Lang::getTxt('meta_keywords_note', file: 'ManageSettings')
			],
			['title', 'optimus_all_pages'],
			['select', 'optimus_board_extend_title', Lang::getTxt('optimus_board_extend_title_set')],
			['select', 'optimus_topic_extend_title', Lang::getTxt('optimus_topic_extend_title_set')],
			'',
			['title', 'optimus_extra_settings'],
			['check', 'optimus_errors_for_wrong_actions'],
			['check', 'optimus_errors_for_wrong_boards_topics'],
			['check', 'optimus_log_search'],
		];

		// You can add your own options
		IntegrationHook::call('integrate_optimus_basic_settings', [&$config_vars]);

		if ($return_config) {
			return $config_vars;
		}

		if (Input::isGet('save')) {
			User::$me->checkSession();

			if (Input::isPost('optimus_forum_index')) {
				Input::post(['optimus_forum_index' => Input::filter('optimus_forum_index')]);
			}

			if (Input::isPost('optimus_description')) {
				Input::post(['optimus_description' => Input::filter('optimus_description')]);
			}

			IntegrationHook::call('integrate_save_optimus_basic_settings');

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			Utils::redirectexit('action=admin;area=optimus;sa=basic');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	/**
	 * @return void|array
	 */
	public function extraTabSettings(bool $return_config = false)
	{
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_extra_title');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=extra;save';

		Lang::setTxt('optimus_extra_info', sprintf(Lang::getTxt('optimus_extra_info'), Config::$scripturl));

		$config_vars = [
			['title', 'optimus_extra_title'],
			['desc', 'optimus_extra_info'],
			[
				'check',
				'optimus_og_image',
				'help' => 'optimus_og_image_help',
				'subtext' => sprintf(Lang::getTxt('optimus_og_image_subtext'), implode('', [
					Config::$scripturl . '?action=admin;area=theme;sa=list;th=',
					Theme::$current->settings['theme_id']  . '#options_og_image',
				]))
			],
			[
				'check',
				'optimus_allow_change_board_og_image',
				'subtext' => Lang::getTxt('optimus_allow_change_board_og_image_subtext')
			],
			['text', 'optimus_fb_appid', 40, 'help' => 'optimus_fb_appid_help'],
			['text', 'optimus_tw_cards', 40, 'preinput' => '@', 'help' => 'optimus_tw_cards_help'],
		];

		// You can add your own options
		IntegrationHook::call('integrate_optimus_extra_settings', [&$config_vars]);

		if ($return_config) {
			return $config_vars;
		}

		if (Input::isGet('save')) {
			User::$me->checkSession();

			if (Input::isPost('optimus_fb_appid')) {
				Input::post(['optimus_fb_appid' => Input::filter('optimus_fb_appid')]);
			}

			if (Input::isPost('optimus_tw_cards')) {
				Input::post([
					'optimus_tw_cards' => str_replace(
						'@', '', Input::filter('optimus_tw_cards'))
				]);
			}

			IntegrationHook::call('integrate_save_optimus_extra_settings');

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			Utils::redirectexit('action=admin;area=optimus;sa=extra');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	/**
	 * @return void|array
	 */
	public function faviconTabSettings(bool $return_config = false)
	{
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_favicon_title');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=favicon;save';

		$config_vars = [
			['large_text', 'optimus_favicon_text'],
		];

		if ($return_config) {
			return $config_vars;
		}

		Utils::$context['sub_template'] = 'favicon';

		if (Input::isGet('save')) {
			User::$me->checkSession();

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			Utils::redirectexit('action=admin;area=optimus;sa=favicon');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	public function metatagsTabSettings(): void
	{
		Utils::$context['sub_template'] = 'metatags';
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_meta_title');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=metatags;save';

		Utils::$context['optimus_metatags_rules'] = empty(Config::$modSettings['optimus_meta'])
			? [] : unserialize(Config::$modSettings['optimus_meta']);

		$config_vars = [];

		if (Input::isGet('save')) {
			User::$me->checkSession();

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			$meta = [];
			if (Input::isPost('custom_tag_name') && Input::isPost('custom_tag_value')) {
				$custom_tag_name = Input::post('custom_tag_name');
				$custom_tag_value = Input::post('custom_tag_value');
				$custom_tag_name = array_filter($custom_tag_name);

				foreach ($custom_tag_name as $key => $value) {
					$meta[$value] = $custom_tag_value[$key];
				}
			}

			Config::updateModSettings(['optimus_meta' => serialize($meta)]);
			Utils::redirectexit('action=admin;area=optimus;sa=metatags');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	public function redirectTabSettings(): void
	{
		Utils::$context['sub_template'] = 'redirect';
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_redirect_title');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=redirect;save';

		Utils::$context['optimus_redirect_rules'] = empty(Config::$modSettings['optimus_redirect'])
			? [] : unserialize(Config::$modSettings['optimus_redirect']);

		$config_vars = [];

		if (Input::isGet('save')) {
			User::$me->checkSession();

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			$redirect = [];
			if (Input::isPost('custom_redirect_from') && Input::isPost('custom_redirect_to')) {
				$custom_redirect_from = Input::post('custom_redirect_from');
				$custom_redirect_to = Input::post('custom_redirect_to');
				$custom_redirect_from = array_filter($custom_redirect_from);

				foreach ($custom_redirect_from as $to => $from) {
					$redirect[$from] = $custom_redirect_to[$to];
				}
			}

			Config::updateModSettings(['optimus_redirect' => serialize($redirect)]);
			Utils::redirectexit('action=admin;area=optimus;sa=redirect');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	public function counterTabSettings(): void
	{
		Utils::$context['sub_template'] = 'counters';
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_counters');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=counters;save';

		$this->addDefaultSettings([
			'optimus_counters_css'    => '.counters {text-align: center}',
			'optimus_ignored_actions' => 'admin,bookmarks,credits,helpadmin,pm,printpage',
		]);

		$config_vars = [
			['large_text', 'optimus_head_code'],
			['large_text', 'optimus_stat_code'],
			['large_text', 'optimus_count_code'],
			['large_text', 'optimus_counters_css'],
			['text', 'optimus_ignored_actions'],
		];

		if (Input::isGet('save')) {
			User::$me->checkSession();

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			Utils::redirectexit('action=admin;area=optimus;sa=counters');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	public function robotsTabSettings(): void
	{
		Utils::$context['sub_template'] = 'robots';
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_robots_title');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=robots;save';

		$config_vars = [];

		$path = (Input::server('document_root') ?: Config::$boarddir) . '/robots.txt';

		Utils::$context['robots_content'] = Utils::makeWritable($path) ? @file_get_contents($path) : '';

		(new RobotsGenerator())->generate();

		if (Input::isGet('save')) {
			User::$me->checkSession();

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			file_put_contents($path, Input::filter('optimus_robots'), LOCK_EX);

			Utils::redirectexit('action=admin;area=optimus;sa=robots');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	public function htaccessTabSettings(): void
	{
		Utils::$context['sub_template'] = 'htaccess';
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_htaccess_title');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=htaccess;save';

		$config_vars = [];

		$path = (Input::server('document_root') ?: Config::$boarddir) . '/.htaccess';

		Utils::$context['htaccess_content'] = Utils::makeWritable($path) ? @file_get_contents($path) : '';

		if (Input::isGet('save')) {
			User::$me->checkSession();

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			if (is_file($path)) {
				copy($path, $path . '.backup');
			}

			file_put_contents($path, trim(Input::post('optimus_htaccess')), LOCK_EX);

			Utils::redirectexit('action=admin;area=optimus;sa=htaccess');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	/**
	 * @return void|array
	 */
	public function sitemapTabSettings(bool $return_config = false)
	{
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_sitemap_title');
		Utils::$context['settings_title'] = Lang::getTxt('optimus_sitemap_title');
		Utils::$context['post_url'] = Config::$scripturl . '?action=admin;area=optimus;sa=sitemap;save';

		if (! Utils::makeWritable(Config::$boarddir)) {
			ErrorHandler::fatalLang('optimus_root_is_not_writable');
		}

		$this->addDefaultSettings([
			'optimus_sitemap_topics_num_replies' => 5,
			'optimus_sitemap_items_display'      => 10000,
			'optimus_start_year'                 => 1994,
			'optimus_update_frequency'           => 1,
		]);

		$title = Lang::getTxt('admin_maintenance', file: 'ManageMaintenance') . ' - ' . Lang::getTxt('maintain_recount');
		$link = Str::html('a', $title)->class('bbc_link')
			->href(sprintf('%s?action=admin;area=maintain;sa=routine', Config::$scripturl));

		Utils::$context['settings_insert_above'] = Str::html('div')->class('roundframe')
			->setHtml(sprintf(Lang::getTxt('optimus_sitemap_info'), $link));

		$config_vars = [
			['check', 'optimus_sitemap_enable', 'subtext' => Lang::getTxt('optimus_sitemap_enable_subtext')],
			['check', 'optimus_sitemap_link'],
			['check', 'optimus_remove_previous_xml_files'],
			'',
			['select', 'optimus_main_page_frequency', Lang::getTxt('optimus_main_page_frequency_set')],
			['check', 'optimus_sitemap_boards', 'subtext' => Lang::getTxt('optimus_sitemap_boards_subtext')],
			[
				'check',
				'optimus_sitemap_all_topic_pages',
				'subtext' => Lang::getTxt('optimus_sitemap_all_topic_pages_subtext')
			],
			['int', 'optimus_sitemap_topics_num_replies', 'min' => 0],
			['check', 'optimus_sitemap_add_found_images'],
			'',
			['int', 'optimus_sitemap_items_display', 'min' => 1, 'max' => 50000],
			['int', 'optimus_start_year', 'min' => 1994, 'max' => date('Y')],
			['select', 'optimus_update_frequency', Lang::getTxt('optimus_update_frequency_set')],
		];

		// You can add your own options
		IntegrationHook::call('integrate_optimus_sitemap_settings', [&$config_vars]);

		if ($return_config) {
			return $config_vars;
		}

		if (Input::isGet('save')) {
			User::$me->checkSession();

			// Recreate a sitemap after save settings
			Db::$db->query('
				DELETE FROM {db_prefix}background_tasks
				WHERE task_class = {string:task_class}',
				[
					'task_class' => '\\' . Sitemap::class,
				]
			);

			if (Input::isPost('optimus_sitemap_enable')) {
				Db::$db->insert('insert',
					'{db_prefix}background_tasks',
					['task_file' => 'string-255', 'task_class' => 'string-255', 'task_data' => 'string'],
					['$sourcedir/Optimus/Tasks/Sitemap.php', '\\' . Sitemap::class, ''],
					['id_task'],
				);
			}

			IntegrationHook::call('integrate_save_optimus_sitemap_settings');

			$save_vars = $config_vars;
			ACP::saveDBSettings($save_vars);

			Utils::redirectexit('action=admin;area=optimus;sa=sitemap');
		}

		ACP::prepareDBSettingContext($config_vars);
	}

	private function addBlockWithTips(): void
	{
		if (empty(Input::isRequest('area')) || empty(Utils::$context['template_layers']))
			return;

		if (str_contains(Input::request('area'), 'optimus')) {
			Theme::loadTemplate('Optimus');

			Utils::$context['template_layers'][] = 'tips';
		}
	}

	private function callActionFromAreas(array $subActions): void
	{
		Utils::$context['sub_template'] = 'show_settings';

		$sa = Input::request('sa', 'basic');
		Input::request(['sa' => isset($subActions[$sa]) ? $sa : key($subActions)]);

		$this->{$subActions[Input::request('sa')]}();
	}

	private function addDefaultSettings($settings): void
	{
		if (empty($settings))
			return;

		$vars = array_filter($settings, fn($key) => ! isset(Config::$modSettings[$key]), ARRAY_FILTER_USE_KEY);

		Config::updateModSettings($vars);
	}
}
