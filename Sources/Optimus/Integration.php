<?php

declare(strict_types=1);

namespace Bugo\Optimus;

/**
 * Integration.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.12
 */

if (! defined('SMF'))
	die('No direct access...');

final class Integration
{
	public function __construct()
	{
		$this->hooks();

		(new Settings)->hooks();
		(new Boards)->hooks();
		(new Topics)->hooks();

		Subs::runAddons();
	}

	private function hooks()
	{
		add_integration_function('integrate_actions', __CLASS__ . '::actions', false, __FILE__, true);
		add_integration_function('integrate_pre_log_stats', __CLASS__ . '::preLogStats', false, __FILE__, true);
		add_integration_function('integrate_load_theme', __CLASS__ . '::loadTheme', false, __FILE__, true);
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::menuButtons', false, __FILE__, true);
		add_integration_function('integrate_theme_context', __CLASS__ . '::themeContext', false, __FILE__, true);
		add_integration_function('integrate_load_permissions', __CLASS__ . '::loadPermissions', false, __FILE__, true);
		add_integration_function('integrate_search_params', __CLASS__ . '::searchParams', false, __FILE__, true);
		add_integration_function('integrate_credits', __CLASS__ . '::credits', false, __FILE__, true);
	}

	public function actions(array &$actions)
	{
		$actions['sitemap_xsl'] = array(false, array($this, 'xsl'));
	}

	public function preLogStats(array &$no_stat_actions)
	{
		$no_stat_actions['sitemap_xsl'] = true;
	}

	public function loadTheme()
	{
		loadLanguage('Optimus/Optimus');

		$this->changeFrontPageTitle();
		$this->addCounters();
	}

	public function menuButtons()
	{
		$this->addFavicon();
		$this->addFrontPageDescription();
		$this->prepareErrorCodes();
		$this->addSitemapLink();
		$this->prepareSearchTerms();
	}

	public function themeContext()
	{
		$this->extendTitles();
		$this->prepareMetaTags();
	}

	public function loadPermissions(array &$permissionGroups, array &$permissionList)
	{
		if (is_on('optimus_log_search'))
			$permissionList['membergroup']['optimus_view_search_terms'] = array(false, 'general', 'view_basic_info');
	}

	public function searchParams()
	{
		global $smcFunc;

		if (! op_request('search') || is_off('optimus_log_search'))
			return;

		$search_string = un_htmlspecialchars(op_request('search'));

		if (empty($search_string))
			return;

		$request = $smcFunc['db_query']('', '
			SELECT id_term
			FROM {db_prefix}optimus_search_terms
			WHERE phrase = {string:phrase}
			LIMIT 1',
			array(
				'phrase' => $search_string
			)
		);

		[$id] = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		if (empty($id)) {
			$smcFunc['db_insert']('insert',
				'{db_prefix}optimus_search_terms',
				array(
					'phrase' => 'string-255',
					'hit'    => 'int'
				),
				array($search_string, 1),
				array('id_term')
			);
		} else {
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}optimus_search_terms
				SET hit = hit + 1
				WHERE id_term = {int:id_term}',
				array(
					'id_term' => $id
				)
			);
		}
	}

	public function credits()
	{
		global $context;

		$context['credits_modifications'][] = op_link() . ' &copy; 2010&ndash;2023, Bugo';
	}

	public function xsl()
	{
		global $modSettings, $settings, $txt;

		ob_end_clean();

		if (! empty($modSettings['enableCompressedOutput']))
			@ob_start('ob_gzhandler');
		else
			ob_start();

		header('content-type: text/xsl; charset=UTF-8');

		$content = file_get_contents($settings['default_theme_dir'] . '/css/optimus/sitemap.xsl');

		$content = strtr($content, array(
			'{link}'          => $settings['theme_url'] . '/css/index.css',
			'{sitemap}'       => $txt['optimus_sitemap_title'],
			'{mobile}'        => $txt['optimus_mobile'],
			'{images}'        => $txt['optimus_images'],
			'{news}'          => $txt['optimus_news'],
			'{video}'         => $txt['optimus_video'],
			'{index}'         => $txt['optimus_index'],
			'{total_files}'   => $txt['optimus_total_files'],
			'{total_urls}'    => $txt['optimus_total_urls'],
			'{url}'           => $txt['url'],
			'{last_modified}' => $txt['optimus_last_modified'],
			'{frequency}'     => $txt['optimus_frequency'],
			'{priority}'      => $txt['optimus_priority'],
			'{direct_link}'   => $txt['optimus_direct_link'],
			'{caption}'       => $txt['optimus_caption'],
			'{thumbnail}'     => $txt['optimus_thumbnail'],
			'{optimus}'       => OP_NAME
		));

		echo $content;

		obExit(false);
	}

	private function changeFrontPageTitle()
	{
		global $txt;

		if (is_on('optimus_forum_index'))
			$txt['forum_index'] = op_config('optimus_forum_index');
	}

	private function addCounters()
	{
		global $context;

		if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') !== false)
			return;

		if (op_is_request('xml') || in_array($context['current_action'], explode(',', op_config('optimus_ignored_actions', ''))))
			return;

		if (is_on('optimus_head_code')) {
			$head = explode(PHP_EOL, trim(op_config('optimus_head_code')));

			foreach ($head as $part)
				$context['html_headers'] .= "\n\t" . $part;
		}

		if (is_on('optimus_stat_code')) {
			$stat = explode(PHP_EOL, trim(op_config('optimus_stat_code')));

			foreach ($stat as $part)
				$context['insert_after_template'] .= "\n\t" . $part;
		}

		if (is_on('optimus_count_code')) {
			loadTemplate('Optimus');
			$context['template_layers'][] = 'footer_counters';

			if (is_on('optimus_counters_css'))
				addInlineCss(op_config('optimus_counters_css'));
		}
	}

	private function addFavicon()
	{
		global $context;

		if (is_on('optimus_favicon_text')) {
			$favicon = explode(PHP_EOL, trim(op_config('optimus_favicon_text')));

			foreach ($favicon as $fav_line)
				$context['html_headers'] .= "\n\t" . $fav_line;
		}
	}

	private function addFrontPageDescription()
	{
		global $context;

		if (empty($context['current_action']) && empty(op_server('query_string')) && empty(op_server('argv')) && is_on('optimus_description')) {
			$context['meta_description'] = op_xss(op_config('optimus_description'));
		}
	}

	private function prepareErrorCodes()
	{
		global $board_info, $context, $txt, $scripturl;

		if (is_off('optimus_correct_http_status') || empty($board_info['error']))
			return;

		// Does not page exist?
		if ($board_info['error'] === 'exist') {
			send_http_status(404);

			$context['page_title']    = $txt['optimus_404_page_title'];
			$context['error_title']   = $txt['optimus_404_h2'];
			$context['error_message'] = $txt['optimus_404_h3'] . '<br>' . sprintf($txt['optimus_goto_main_page'], $scripturl);
		}

		// No access?
		if ($board_info['error'] === 'access') {
			send_http_status(403);

			$context['page_title']    = $txt['optimus_403_page_title'];
			$context['error_title']   = $txt['optimus_403_h2'];
			$context['error_message'] = $txt['optimus_403_h3'] . '<br>' . sprintf($txt['optimus_goto_main_page'], $scripturl);
		}

		if ($board_info['error'] === 'exist' || $board_info['error'] === 'access') {
			addInlineJavaScript('
		let error_block = document.getElementById("fatal_error");
		error_block.classList.add("centertext");
		error_block.nextElementSibling.querySelector("a.button").setAttribute("href", "javascript:history.go(-1)");', true);
		}
	}

	private function addSitemapLink()
	{
		global $txt, $boarddir, $forum_copyright, $boardurl, $context;

		if (is_on('optimus_sitemap_enable') && is_on('optimus_sitemap_link') && isset($txt['optimus_sitemap_title']) && is_file($boarddir . '/sitemap.xml')) {
			$forum_copyright .= ' | <a href="' . $boardurl . '/sitemap.xml">' . $txt['optimus_sitemap_title'] . '</a>';

			$context['html_headers'] .= "\n\t" . '<link rel="sitemap" type="application/xml" title="Sitemap" href="' . $boardurl . '/sitemap.xml">';
		}
	}

	private function prepareSearchTerms()
	{
		global $context, $smcFunc;

		if (($context['current_action'] !== 'search' && $context['current_action'] !== 'search2') || is_off('optimus_log_search'))
			return;

		if (($context['search_terms'] = cache_get_data('optimus_search_terms', 3600)) === null) {
			$request = $smcFunc['db_query']('', '
				SELECT phrase, hit
				FROM {db_prefix}optimus_search_terms
				ORDER BY hit DESC
				LIMIT 30'
			);

			$scale = 1;
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				if ($scale < $row['hit'])
					$scale = $row['hit'];

				$context['search_terms'][] = array(
					'text'  => $row['phrase'],
					'scale' => round(($row['hit'] * 100) / $scale),
					'hit'   => $row['hit']
				);
			}

			$smcFunc['db_free_result']($request);

			cache_put_data('optimus_search_terms', $context['search_terms'], 3600);
		}

		$this->showTopSearchTerms();
	}

	private function extendTitles()
	{
		global $board_info, $context;

		if (SMF === 'SSI')
			return;

		// Board titles
		if (! empty($board_info['total_topics']) && is_on('optimus_board_extend_title')) {
			op_config('optimus_board_extend_title') == 1
				? $context['page_title_html_safe'] = $context['forum_name'] . ' - ' . $context['page_title_html_safe']
				: $context['page_title_html_safe'] = $context['page_title_html_safe'] . ' - ' . $context['forum_name'];
		}

		// Topic titles
		if (! empty($context['first_message']) && is_on('optimus_topic_extend_title')) {
			op_config('optimus_topic_extend_title') == 1
				? $context['page_title_html_safe'] = $context['forum_name'] . ' - ' . $board_info['name'] . ' - ' . $context['page_title_html_safe']
				: $context['page_title_html_safe'] = $context['page_title_html_safe'] . ' - ' . $board_info['name'] . ' - ' . $context['forum_name'];
		}
	}

	private function prepareMetaTags()
	{
		global $context, $settings;

		if (is_on('optimus_forum_index'))
			$context['page_title_html_safe'] = un_htmlspecialchars($context['page_title_html_safe']);

		if (! empty($context['robot_no_index']))
			return;

		$meta = [
			'og:site_name',
			'og:title',
			'og:url',
			'og:image',
			'og:description'
		];

		foreach ($context['meta_tags'] as $key => $value) {
			foreach ($value as $k => $v) {
				if ($k === 'property' && in_array($v, $meta))
					$context['meta_tags'][$key] = array_merge(array('prefix' => 'og: http://ogp.me/ns#'), $value);

				if ($v == 'og:image') {
					$og_image_key = $key;

					if (! empty($context['optimus_og_image'])) {
						$image_data[0]      = $context['optimus_og_image']['width'];
						$image_data[1]      = $context['optimus_og_image']['height'];
						$image_data['mime'] = $context['optimus_og_image']['mime'];
					}
				}
			}
		}

		if (! empty($image_data)) {
			$context['meta_tags'] = array_merge(
				array_slice($context['meta_tags'], 0, $og_image_key + 1, true),
				array(
					array('prefix' => 'og: http://ogp.me/ns#', 'property' => 'og:image:type', 'content' => $image_data['mime'])
				),
				array(
					array('prefix' => 'og: http://ogp.me/ns#', 'property' => 'og:image:width', 'content' => $image_data[0])
				),
				array(
					array('prefix' => 'og: http://ogp.me/ns#', 'property' => 'og:image:height', 'content' => $image_data[1])
				),
				array_slice($context['meta_tags'], $og_image_key + 1, null, true)
			);
		}

		// Various types
		if (! empty($context['optimus_og_type'])) {
			$type = key($context['optimus_og_type']);
			$context['meta_tags'][] = array('prefix' => 'og: http://ogp.me/ns#', 'property' => 'og:type', 'content' => $type);
			$optimus_custom_types = array_filter($context['optimus_og_type'][$type]);

			foreach ($optimus_custom_types as $property => $content) {
				if (is_array($content)) {
					foreach ($content as $value) {
						$context['meta_tags'][] = array('prefix' => $type . ': http://ogp.me/ns/' . $type . '#', 'property' => $type . ':' . $property, 'content' => $value);
					}
				} else {
					$context['meta_tags'][] = array('prefix' => $type . ': http://ogp.me/ns/' . $type . '#', 'property' => $type . ':' . $property, 'content' => $content);
				}
			}
		}

		if ($context['current_action'] == 'profile' && op_is_request('u')) {
			$context['meta_tags'][] = array('prefix' => 'og: http://ogp.me/ns#', 'property' => 'og:type', 'content' => 'profile');
		}

		// Twitter cards
		if (is_on('optimus_tw_cards') && isset($context['canonical_url'])) {
			$context['meta_tags'][] = array('property' => 'twitter:card', 'content' => 'summary');
			$context['meta_tags'][] = array('property' => 'twitter:site', 'content' => '@' . op_config('optimus_tw_cards'));

			if (! empty($settings['og_image']))
				$context['meta_tags'][] = array('property' => 'twitter:image', 'content' => $settings['og_image']);
		}

		// Facebook
		if (is_on('optimus_fb_appid'))
			$context['meta_tags'][] = array('prefix' => 'fb: http://ogp.me/ns/fb#', 'property' => 'fb:app_id', 'content' => op_config('optimus_fb_appid'));

		// Metatags
		if (is_on('optimus_meta')) {
			$tags = array_filter(unserialize(op_config('optimus_meta')));

			foreach ($tags as $name => $value) {
				$context['meta_tags'][] = array('name' => $name, 'content' => $value);
			}
		}
	}

	private function showTopSearchTerms()
	{
		global $context;

		if (empty($context['search_terms']) || ! $this->canViewSearchTerms())
			return;

		loadTemplate('Optimus');

		$context['template_layers'][] = 'search_terms';
	}

	private function canViewSearchTerms(): bool
	{
		return is_on('optimus_log_search') && allowedTo('optimus_view_search_terms');
	}
}
