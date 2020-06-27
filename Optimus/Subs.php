<?php

namespace Bugo\Optimus;

/**
 * Subs.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.6.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class Subs
{
	/**
	 * Меняем заголовок форума
	 *
	 * @return void
	 */
	public static function changeForumTitle()
	{
		global $txt, $modSettings;

		// Forum
		$txt['forum_index'] = '%1$s';
		if (!empty($modSettings['optimus_forum_index']))
			$txt['forum_index'] = '%1$s - ' . $modSettings['optimus_forum_index'];
	}

	/**
	 * Добавляем иконку сайта
	 *
	 * @return void
	 */
	public static function addFavicon()
	{
		global $modSettings, $context;

		if (!empty($modSettings['optimus_favicon_text'])) {
			$favicon = explode("\n", trim($modSettings['optimus_favicon_text']));
			foreach ($favicon as $fav_line)
				$context['html_headers'] .= "\n\t" . $fav_line;
		}
	}

	/**
	 * Добавляем коды счётчиков в тело страниц
	 *
	 * @return void
	 */
	public static function addCounters()
	{
		global $modSettings, $context, $forum_copyright;

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

			// Visible counters
			if (!empty($modSettings['optimus_count_code']))
				$forum_copyright = $modSettings['optimus_count_code'] . '<br />' . $forum_copyright;

			// Styles for visible counters
			if (!empty($modSettings['optimus_count_code']) && !empty($modSettings['optimus_counters_css']))
				$context['html_headers'] .= "\n\t" . '<style type="text/css">' . $modSettings['optimus_counters_css'] . '</style>';
		}
	}

	/**
	 * Обрабатываем шаблоны заголовков страниц
	 *
	 * @return void
	 */
	public static function processPageTemplates()
	{
		global $modSettings, $txt, $context, $board_info, $smcFunc;

		if (SMF == 'SSI' || empty($modSettings['optimus_templates']))
			return;

		if (strpos($modSettings['optimus_templates'], 'board') && strpos($modSettings['optimus_templates'], 'topic')) {
			$templates = unserialize($modSettings['optimus_templates']);

			foreach ($templates as $name => $data) {
				if ($name == 'board') {
					$board_name_tpl = $data['name'];
					$board_page_tpl = $data['page'];
					$board_site_tpl = $data['site'];
				}

				if ($name == 'topic') {
					$topic_name_tpl = $data['name'];
					$topic_page_tpl = $data['page'];
					$topic_site_tpl = $data['site'];
				}
			}
		} else {
			foreach ($txt['optimus_templates'] as $name => $data) {
				if ($name == 'board') {
					$board_name_tpl = $data[0];
					$board_page_tpl = $data[1];
					$board_site_tpl = $data[2];
				}

				if ($name == 'topic') {
					$topic_name_tpl = $data[0];
					$topic_page_tpl = $data[1];
					$topic_site_tpl = $data[2];
				}
			}
		}

		// Номер текущей страницы в заголовке (при условии, что страниц несколько)
		$board_page_number = $topic_page_number = '';
		if ($context['current_action'] != 'wiki') {
			if (!empty($context['page_info']['current_page']) && $context['page_info']['num_pages'] != 1 && (($context['page_info']['current_page'] == 1 && empty($modSettings['optimus_no_first_number'])) || $context['page_info']['current_page'] != 1)) {
				$trans = array("{#}" => $context['page_info']['current_page']);
				$board_page_number = strtr($board_page_tpl, $trans);
				$topic_page_number = strtr($topic_page_tpl, $trans);
			}
		}

		// Topics
		if (!empty($context['topic_first_message'])) {
			$trans = array(
				"{topic_name}" => $context['subject'],
				"{board_name}" => strip_tags($board_info['name']),
				"{cat_name}"   => $board_info['cat']['name'],
				"{forum_name}" => $context['forum_name']
			);

			$topic_page_number = !empty($topic_page_number) ? $topic_page_number : (!empty($topic_site_tpl) ? ' - ' : '');

			$context['page_title'] = strtr($topic_name_tpl . $topic_page_number . $topic_site_tpl, $trans);

			if (!empty($modSettings['optimus_topic_description']))
				self::getDescription();

			self::getOgImage();
		}

		// Boards
		if (!empty($board_info['total_topics'])) {
			$trans = array(
				"{board_name}" => strip_tags($context['name']),
				"{cat_name}"   => $board_info['cat']['name'],
				"{forum_name}" => $context['forum_name']
			);

			$board_page_number = !empty($board_page_number) ? $board_page_number : (!empty($board_site_tpl) ? ' - ' : '');
			$context['page_title'] = strtr($board_name_tpl . $board_page_number . $board_site_tpl, $trans);

			if (!empty($modSettings['optimus_board_description'])) {
				$context['optimus_description'] = !empty($context['description']) ? $context['description'] : $context['name'];
				$context['optimus_description'] = $smcFunc['htmlspecialchars']($context['optimus_description']);
			}
		}
	}

	/**
	 * Возвращаемые статусы страниц разделов и тем
	 *
	 * @return void
	 */
	public static function processErrorCodes()
	{
		global $modSettings, $board_info, $context, $txt;

		if (empty($modSettings['optimus_404_status']) || empty($board_info['error']))
			return;

		// Страница не существует? Does not exist?
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
	private static function getDescription()
	{
		global $context, $smcFunc, $txt, $board_info;

		if (empty($context['first_message']))
			return;

		$request = $smcFunc['db_query']('substring', '
			SELECT body, poster_time, modified_time
			FROM {db_prefix}messages
			WHERE id_msg = {int:id_msg}
			LIMIT 1',
			array(
				'id_msg' => $context['first_message']
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))	{
			censorText($row['body']);

			$row['body'] = parse_bbc($row['body'], false);

			// Ищем изображение в тексте сообщения
			$first_post_image = preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $row['body'], $value);
			$context['optimus_og_image'] = $first_post_image ? array_pop($value) : null;

			$row['body'] = self::getTeaser($row['body']);
			$row['body'] = str_replace($txt['quote'], '', $row['body']);

			$context['optimus_description'] = explode('&nbsp;', $row['body'])[0];

			if ($smcFunc['strlen']($context['optimus_description']) > 130)
				$context['optimus_description'] = $smcFunc['substr']($context['optimus_description'], 0, 127) . '...';

			$context['optimus_description'] = !empty($context['topic_description']) ? $context['topic_description'] : $context['optimus_description'];

			$context['optimus_og_type']['article']['published_time'] = date('Y-m-d\TH:i:s', $row['poster_time']);

			if (!empty($row['modified_time']))
				$context['optimus_og_type']['article']['modified_time'] = date('Y-m-d\TH:i:s', $row['modified_time']);

			$context['optimus_og_type']['article']['section'] = $board_info['name'];
		}

		$smcFunc['db_free_result']($request);
	}

	/**
	 * Достаем URL вложения из первого сообщения темы
	 *
	 * @return void
	 */
	private static function getOgImage()
	{
		global $context, $smcFunc, $scripturl;

		if (!allowedTo('view_attachments') || !empty($context['optimus_og_image']))
			return;

		if (($context['optimus_og_image'] = cache_get_data('og_image_' . $context['current_topic'], 360)) === null) {
			$request = $smcFunc['db_query']('', '
				SELECT COALESCE(id_attach, 0) AS id
				FROM {db_prefix}attachments
				WHERE id_msg = {int:msg}
					AND width > 0
					AND height > 0
				LIMIT 1',
				array(
					'msg' => $context['topic_first_message']
				)
			);

			list ($image_id) = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);

			if (!empty($image_id))
				$context['optimus_og_image'] = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . ';attach=' . $image_id . ';image';

			cache_put_data('og_image_' . $context['current_topic'], $context['optimus_og_image'], 360);
		}
	}

	/**
	 * Получаем выдержку текста для создания описания страницы
	 *
	 * @param string $text — текст для обработки
	 * @param integer $num_sentences — количество предложений, которые нужно взять из текста
	 * @return string
	 */
	public static function getTeaser($text, $num_sentences = 2)
	{
		$body = preg_replace('/\s+/', ' ', strip_tags($text));
		$sentences = preg_split('/(\.|\?|\!)(\s)/', $body);

		if (count($sentences) <= $num_sentences)
			return $body;

		$stopAt = 0;
		foreach ($sentences as $i => $sentence) {
			$stopAt += strlen($sentence);
			if ($i >= $num_sentences - 1)
				break;
		}

		$stopAt += ($num_sentences * 2);

		return trim(substr($body, 0, $stopAt));
	}

	/**
	 * Добавление канонического адреса при использовании некоторых модов порталов
	 *
	 * @return void
	 */
	public static function addCanonicalFix()
	{
		global $modSettings, $context, $mbname, $boardurl;

		if (!empty($modSettings['optimus_portal_compat'])) {
			if (empty($context['current_board']) && empty($context['current_topic']) && empty($_REQUEST['action'])) {
				$context['linktree'][0]['name'] = $mbname;
				$context['canonical_url'] = $boardurl . '/';
			}
		}
	}

	/**
	 * Добавляем описание для главной страницы
	 *
	 * @return void
	 */
	public static function addMainPageDescription()
	{
		global $context, $modSettings, $smcFunc;

		if (empty($context['current_action']) && !empty($modSettings['optimus_description'])) {
			if (empty($_REQUEST['topic']) && empty($_REQUEST['board']))
				$context['optimus_description'] = $smcFunc['htmlspecialchars']($modSettings['optimus_description']);
		}
	}

	/**
	 * Добавляем ссылку на карту форума в подвале
	 *
	 * @return void
	 */
	public static function addSitemap()
	{
		global $modSettings, $txt, $forum_copyright, $boardurl;

		if (empty($modSettings['optimus_sitemap_enable']) || !isset($txt['optimus_sitemap_title']))
			return;

		if (!empty($modSettings['optimus_sitemap_link']))
			$forum_copyright .= ' | <a href="' . $boardurl . '/sitemap.xml">' . $txt['optimus_sitemap_title'] . '</a>';
	}

	/**
	 * Получаем все вложенные директории по указанному пути
	 *
	 * @param string $path
	 * @param array $ret
	 * @return array
	 */
	public static function getNestedDirs($path, $nested_dirs = [])
	{
		$dirs = glob(rtrim($path, "/") . "/*", GLOB_ONLYDIR) or array();

		foreach ($dirs as $path) {
			$nested_dirs[] = $path;
			$nested_dirs = self::getNestedDirs($path, $nested_dirs);
		}

		return $nested_dirs;
	}

	/**
	 * Подключаем аддоны
	 *
	 * @param string $type
	 * @param array $vars
	 * @return void
	 */
	public static function runAddons($type = 'meta', $vars = [])
	{
		global $sourcedir;

		$addon_dir = $sourcedir . '/Optimus/addons';

		if (($optimus_addons = cache_get_data('optimus_addons', 3600)) === null) {
			foreach (glob($addon_dir . '/*.php') as $filename) {
				$filename = basename($filename);
				if ($filename !== 'index.php')
					$optimus_addons[] = str_replace('.php', '', $filename);
			}

			$dirs = self::getNestedDirs($addon_dir);
			foreach ($dirs as $dir)
				$optimus_addons[] = basename($dir) . '|' . basename($dir);

			cache_put_data('optimus_addons', $optimus_addons, 3600);
		}

		if (empty($optimus_addons))
			return;

		foreach ($optimus_addons as $addon) {
			$class = __NAMESPACE__ . '\Addons\\' . str_replace('|', '\\', $addon);
			require_once($addon_dir . '/' . str_replace('|', '/', $addon) . '.php');

			if (method_exists($class, $type))
				call_user_func_array(array($class, $type), $vars);
		}
	}

	/**
	 * Добавляем информацию об авторских правах
	 *
	 * @return void
	 */
	public static function addCredits()
	{
		global $context;

		if ($context['current_action'] == 'credits')
			$context['copyrights']['mods'][] = '<a href="https://dragomano.ru/mods/optimus" target="_blank">Optimus</a> &copy; 2010&ndash;2020, Bugo';
	}
}
