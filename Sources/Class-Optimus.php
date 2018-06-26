<?php

/**
 * Class-Optimus.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2018 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 0.1 beta
 */

if (!defined('PMX'))
	die('Hacking attempt...');

class Optimus
{
	/**
	 * Подключаем используемые хуки
	 *
	 * @return void
	 */
	public static function hooks()
	{
		add_integration_function('integrate_load_theme', 'Optimus::loadTheme', false);
		add_integration_function('integrate_menu_buttons', 'Optimus::menuButtons', false);
		add_integration_function('integrate_theme_context', 'Optimus::themeContext', false);
		add_integration_function('integrate_buffer', 'Optimus::buffer', false);
		add_integration_function('integrate_admin_include', '$sourcedir/Class-OptimusSitemap.php', false);
		add_integration_function('integrate_admin_include', '$sourcedir/Class-OptimusAdmin.php', false);
		add_integration_function('integrate_admin_areas', 'OptimusAdmin::adminAreas', false);
	}

	/**
	 * Подключаем языковой файл, проводим различные операции с заголовками и пр.
	 *
	 * @return void
	 */
	public static function loadTheme()
	{
		global $modSettings, $context, $txt;

		loadLanguage('Optimus/');

		// Front page title
		if (!empty($modSettings['optimus_forum_index']))
			$txt['forum_index'] = $modSettings['optimus_forum_index'];

		// Favicon
		if (!empty($modSettings['optimus_favicon_text'])) {
			$favicon = explode("\n", trim($modSettings['optimus_favicon_text']));
			foreach ($favicon as $fav_line) {
				$context['html_headers'] .= "\n\t" . $fav_line;
			}
		}

		self::addCounters();
	}

	/**
	 * Добавляем коды счётчиков в тело страниц
	 *
	 * @return void
	 */
	private static function addCounters()
	{
		global $modSettings, $context;

		$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();

		if (!in_array($context['current_action'], $ignored_actions)) {
			// Invisible counters like Google Analytics
			if (!empty($modSettings['optimus_head_code'])) {
				$head = explode("\n", trim($modSettings['optimus_head_code']));
				foreach ($head as $part)
					$context['html_headers'] .= "\n\t" . $part;
			}

			// Other invisible counters
			if (!empty($modSettings['optimus_stat_code'])) {
				$stat = explode("\n", trim($modSettings['optimus_stat_code']));
				foreach ($stat as $part)
					$context['insert_after_template'] .= "\n\t" . $part;
			}

			// Styles for visible counters
			if (!empty($modSettings['optimus_count_code']) && !empty($modSettings['optimus_counters_css']))
				addInlineCss($modSettings['optimus_counters_css']);
		}
	}

	/**
	 * Дополняем заголовки разделов и тем
	 *
	 * @return void
	 */
	private static function processExtendTitles()
	{
		global $board_info, $modSettings, $context;

		if (PMX == 'SSI')
			return;

		// Boards
		if (!empty($board_info['total_topics']) && !empty($modSettings['optimus_board_extend_title'])) {
			if ($modSettings['optimus_board_extend_title'] == 1)
				$context['page_title'] = $context['forum_name'] . ' - ' . $context['page_title'];
			else
				$context['page_title'] = $context['page_title'] . ' - ' . $context['forum_name'];
		}

		// Topics
		if (!empty($context['first_message']) && !empty($modSettings['optimus_topic_extend_title'])) {
			if ($modSettings['optimus_topic_extend_title'] == 1)
				$context['page_title'] = $context['forum_name'] . ' - ' . $board_info['name'] . ' - ' . $context['page_title'];
			else
				$context['page_title'] = $context['page_title'] . ' - ' . $board_info['name'] . ' - ' . $context['forum_name'];
		}
	}

	/**
	 * Возвращаемые статусы страниц разделов и тем
	 *
	 * @return void
	 */
	private static function processErrorCodes()
	{
		global $modSettings, $board_info, $context, $txt;

		if (empty($modSettings['optimus_404_status']) || empty($board_info['error']))
			return;

		// Страница не существует? Does not page exist?
		if ($board_info['error'] == 'exist') {
			header('HTTP/1.1 404 Not Found');

			loadTemplate('Optimus');

			$context['sub_template'] = '404';
			$context['page_title']   = $txt['optimus_404_page_title'];
		}

		// Нет доступа? No access?
		if ($board_info['error'] == 'access') {
			header('HTTP/1.1 403 Forbidden');

			loadTemplate('Optimus');

			$context['sub_template'] = '403';
			$context['page_title']   = $txt['optimus_403_page_title'];
		}
	}

	/**
	 * Создаем описание страницы из первого сообщения
	 *
	 * @return void
	 */
	public static function getDescription()
	{
		global $context, $pmxcFunc, $settings, $txt, $board_info;

		if (empty($context['first_message']))
			return;

		$request = $pmxcFunc['db_query']('substring', '
			SELECT body, poster_time, modified_time
			FROM {db_prefix}messages
			WHERE id_msg = {int:id_msg}
			LIMIT 1',
			array(
				'id_msg' => $context['first_message']
			)
		);

		while ($row = $pmxcFunc['db_fetch_assoc']($request))	{
			censorText($row['body']);

			$row['body'] = parse_bbc($row['body'], false);

			// Ищем изображение в тексте сообщения
			$first_post_image = preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $row['body'], $value);
			$settings['og_image'] = $first_post_image ? array_pop($value) : null;

			$row['body'] = explode("<br />", $row['body'])[0];
			$row['body'] = explode("<hr />", $row['body'])[0];
			$row['body'] = strip_tags($row['body']);
			$row['body'] = str_replace($txt['quote'], '', $row['body']);

			if ($pmxcFunc['strlen']($row['body']) > 130)
				$row['body'] = $pmxcFunc['substr']($row['body'], 0, 127) . '...';

			$context['meta_description'] = $row['body'];

			$context['optimus_og_article'] = array(
				'published_time' => date('Y-m-d\TH:i:s', $row['poster_time']),
				'modified_time'  => !empty($row['modified_time']) ? date('Y-m-d\TH:i:s', $row['modified_time']) : null,
				'section'        => $board_info['name']
			);
		}

		$pmxcFunc['db_free_result']($request);
	}

	/**
	 * Достаем URL вложения из первого сообщения темы
	 *
	 * @return void
	 */
	public static function getOgImage()
	{
		global $settings, $pmxCacheFunc, $context, $pmxcFunc, $scripturl;

		if (!allowedTo('view_attachments'))
			return;

		$temp_image = $settings['og_image'];

		if (($settings['og_image'] = $pmxCacheFunc['get']('og_image_' . $context['current_topic'])) == null) {
			$request = $pmxcFunc['db_query']('', '
				SELECT IFNULL(id_attach, 0) AS id
				FROM {db_prefix}attachments
				WHERE id_msg = {int:msg}
					AND width > 0
					AND height > 0
				LIMIT 1',
				array(
					'msg' => $context['first_message']
				)
			);

			list ($image_id) = $pmxcFunc['db_fetch_row']($request);
			$pmxcFunc['db_free_result']($request);

			if (!empty($image_id))
				$settings['og_image'] = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . ';attach=' . $image_id . ';image';
			else
				$settings['og_image'] = $temp_image;

			$pmxCacheFunc['put']('og_image_' . $context['current_topic'], $settings['og_image'], 360);
		}
	}

	/**
	 * Добавляем различные скрипты в код страниц, меняем переменные, подключаем копирайт
	 *
	 * @return void
	 */
	public static function menuButtons()
	{
		global $modSettings, $context, $pmxcFunc, $boarddir, $forum_copyright, $boardurl, $txt;

		// JSON-LD
		if (!empty($modSettings['optimus_json_ld']) && empty($context['robot_no_index'])) {
			$context['insert_after_template'] .= '
		<script type="application/ld+json">
		{
			"@context": "http://schema.org",
			"@type": "BreadcrumbList",
			"itemListElement": [';

			$i = 1;
			foreach ($context['linktree'] as $id => $data)
				$list_item[$id] = '{
				"@type": "ListItem",
				"position": ' . $i++ . ',
				"item": {
					"@id": "' . (isset($data['url']) ? $data['url'] : '') . '",
					"name": "' . $data['name'] . '"
				}
			}';

			if (!empty($list_item))
				$context['insert_after_template'] .= implode($list_item, ',');

			$context['insert_after_template'] .= ']
		}
		</script>';
		}

		// Front page description
		if (empty($context['current_action']) || in_array($context['current_action'], array('forum', 'community'))) {
			if (empty($_REQUEST['topic']) && empty($_REQUEST['board']) && !empty($modSettings['optimus_description']))
				$context['meta_description'] = $pmxcFunc['htmlspecialchars']($modSettings['optimus_description']);
		}

		self::processExtendTitles();
		self::processErrorCodes();

		// description & og:image
		if (!empty($context['first_message'])) {
			if (!empty($modSettings['optimus_topic_description'])) {
				if (!empty($context['topic_description']))
					$context['meta_description'] = $context['topic_description'];
				else
					self::getDescription();
			}

			self::getOgImage();
		}

		// XML sitemap link
		if (!empty($modSettings['optimus_sitemap_link']) && file_exists($boarddir . '/sitemap.xml'))
			$forum_copyright .= ' | <a href="' . $boardurl . '/sitemap.xml" target="_blank">' . $txt['optimus_sitemap_xml_link'] . '</a>';

		// Copyright Info
		if ($context['current_action'] == 'credits')
			$context['credits_modifications'][] = '<a href="https://dragomano.ru/mods/optimus" target="_blank">Optimus</a> &copy; 2010&ndash;2018, Bugo';
	}

	/**
	 * Добавляем дополнительные мета-теги в HEAD
	 *
	 * @return void
	 */
	public static function themeContext()
	{
		global $context, $modSettings, $settings;

		// og:image
		if (!empty($settings['og_image']))
			$context['meta_tags'][] = array('property' => 'og:image', 'content' => $settings['og_image']);

		// Article type for topics
		if (!empty($context['optimus_og_article'])) {
			$context['meta_tags'][] = array('property' => 'og:type', 'content' => 'article');

			foreach ($context['optimus_og_article'] as $property => $content)
				$context['meta_tags'][] = array('property' => 'article:' . $property, 'content' => $content);
		}

		// Twitter cards
		if (!empty($modSettings['optimus_tw_cards']) && isset($context['canonical_url'])) {
			$context['meta_tags'][] = array('property' => 'twitter:card', 'content' => 'summary');
			$context['meta_tags'][] = array('property' => 'twitter:site', 'content' => '@' . $modSettings['optimus_tw_cards']);

			if (!empty($settings['og_image']))
				$context['meta_tags'][] = array('property' => 'twitter:image', 'content' => $settings['og_image']);
		}

		// Facebook
		if (!empty($modSettings['optimus_fb_appid']))
			$context['meta_tags'][] = array('property' => 'fb:app_id', 'content' => $modSettings['optimus_fb_appid']);

		// Metatags
		if (!empty($modSettings['optimus_meta'])) {
			$tags = unserialize($modSettings['optimus_meta']);

			foreach ($tags as $name => $value) {
				if (!empty($value))
					$context['meta_tags'][] = array('name' => $name, 'content' => $value);
			}
		}
	}

	/**
	 * Различные замены вывода в коде шаблонов
	 *
	 * @param array $buffer
	 * @return array
	 */
	public static function buffer($buffer)
	{
		global $context, $modSettings;

		if (isset($_REQUEST['xml']) || !empty($context['robot_no_index']))
			return $buffer;

		$replacements = array();

		// Open Graph
		$xmlns = '<html lang';
		$new_xmlns = '<html prefix="og: http://ogp.me/ns#' . (!empty($context['optimus_og_article']) ? ' article: http://ogp.me/ns/article#' : '') . (!empty($modSettings['optimus_fb_appid']) ? ' fb: http://ogp.me/ns/fb#' : '') . '" lang';
		$replacements[$xmlns] = $new_xmlns;

		// Counters
		$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();
		$footer_area = '<div id="footer">';
		if (!empty($modSettings['optimus_count_code']) && !in_array($context['current_action'], $ignored_actions))
			$replacements[$footer_area] = '<div class="counters">' . $modSettings['optimus_count_code'] . '</div>' . $footer_area;

		return str_replace(array_keys($replacements), array_values($replacements), $buffer);
	}
}
