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
 * @version 1.9.9
 */

if (!defined('SMF'))
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
		global $modSettings, $context, $mbname, $txt;

		loadLanguage('Optimus/');

		// Set Portal Compat Mode
		if (!isset($modSettings['optimus_portal_compat']))
			$modSettings['optimus_portal_compat'] = 0;

		// Меняем заголовок главной страницы в зависимости от режима совместимости и установленного портала
		if (!empty($modSettings['optimus_portal_compat']) && !empty($modSettings['optimus_portal_index'])) {
			if (!empty($modSettings['pmx_frontmode']) || (!empty($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 1)) {
				if (empty($context['current_board']) && empty($context['current_topic']) && empty($_REQUEST['action']))	{
					$context['forum_name'] = $mbname . ' - ' . $modSettings['optimus_portal_index'];
				}
			}
		}

		// Forum
		$txt['forum_index'] = '%1$s';
		if (!empty($modSettings['optimus_forum_index']))
			$txt['forum_index'] = '%1$s - ' . $modSettings['optimus_forum_index'];

		// Favicon
		if (!empty($modSettings['optimus_favicon_text'])) {
			$favicon = explode("\n", trim($modSettings['optimus_favicon_text']));
			foreach ($favicon as $fav_line) {
				$context['html_headers'] .= "\n\t" . $fav_line;
			}
		}

		self::addCounters();

		// Special fix for PortaMx
		if (!empty($modSettings['optimus_portal_compat']) && $modSettings['optimus_portal_compat'] == 1) {
			if (empty($_REQUEST['action']) && empty($_REQUEST['board']) && empty($_REQUEST['topic'])) {
				// Для режима "Без главной страницы, направлять сразу на форум"
				if (!empty($modSettings['optimus_meta']) && empty($modSettings['pmx_frontmode'])) {
					$test = unserialize($modSettings['optimus_meta']);

					foreach ($test as $n => $val) {
						if (!empty($val))
							$context['html_headers'] .= "\n\t" . '<meta name="' . $n . '" content="' . $val . '" />';
					}
				}
			}
		}
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
				foreach ($head as $part) {
					$context['html_headers'] .= "\n\t" . $part;
				}
			}

			// Other invisible counters
			if (!empty($modSettings['optimus_stat_code'])) {
				$stat = explode("\n", trim($modSettings['optimus_stat_code']));
				foreach ($stat as $part) {
					$context['insert_after_template'] .= "\n\t" . $part;
				}
			}

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
	private static function processPageTemplates()
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

			if (!empty($modSettings['optimus_topic_description'])) {
				if (!empty($context['topic_description']))
					$context['optimus_description'] = $context['topic_description'];
				else
					self::getDescription();
			}

			self::getOgImage();
		}

		// Boards
		if (!empty($board_info['total_topics'])) {
			$trans = array(
				"{board_name}" => strip_tags($context['name']),
				"{cat_name}"   => $board_info['cat']['name'],
				"{forum_name}" => $context['forum_name'],
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
	private static function processErrorCodes()
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
	public static function getDescription()
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

			$row['body'] = explode("<br />", $row['body'])[0];
			$row['body'] = explode("<hr />", $row['body'])[0];
			$row['body'] = strip_tags($row['body']);
			$row['body'] = str_replace($txt['quote'], '', $row['body']);

			if ($smcFunc['strlen']($row['body']) > 130)
				$row['body'] = $smcFunc['substr']($row['body'], 0, 127) . '...';

			$context['optimus_description'] = $row['body'];

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
	public static function getOgImage()
	{
		global $context, $smcFunc, $scripturl;

		if (!allowedTo('view_attachments') || !empty($context['optimus_og_image']))
			return;

		$context['optimus_og_image'] = '';
		if (($context['optimus_og_image'] = cache_get_data('og_image_' . $context['current_topic'], 360)) == null) {
			$request = $smcFunc['db_query']('', '
				SELECT IFNULL(id_attach, 0) AS id
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
	 * Добавляем различные скрипты в код страниц, меняем переменные, подключаем копирайт
	 *
	 * @return void
	 */
	public static function menuButtons()
	{
		global $modSettings, $context, $mbname, $boardurl, $scripturl, $smcFunc;

		// JSON-LD
		if (!empty($modSettings['optimus_json_ld']) && isset($context['canonical_url'])) {
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

		// Canonical url fix for portal mods
		if (!empty($modSettings['optimus_portal_compat'])) {
			if (empty($context['current_board']) && empty($context['current_topic']) && empty($_REQUEST['action'])) {
				$context['linktree'][0]['name'] = $mbname;
				$context['canonical_url'] = $boardurl . '/';
			}

			if (in_array($context['current_action'], array('forum', 'community'))) {
				if (!empty($modSettings['pmx_frontmode']) || !empty($modSettings['sp_portal_mode']))
					$context['canonical_url'] = $scripturl . '?action=' . $context['current_action'];
			}
		}

		// TinyPortal compat mode
		if (!empty($modSettings['optimus_portal_compat'])) {
			if ($modSettings['optimus_portal_compat'] == 3 && !empty($context['TPortal']['is_front']))
				$context['page_title'] = $mbname . ' - ' . $modSettings['optimus_portal_index'];
		}

		// Description
		if (empty($context['current_action']) && !empty($modSettings['optimus_description'])) {
			if (empty($_REQUEST['topic']) && empty($_REQUEST['board']))
				$context['optimus_description'] = $smcFunc['htmlspecialchars']($modSettings['optimus_description']);
		}

		self::processPageTemplates();
		self::processErrorCodes();

		// Copyright Info
		if ($context['current_action'] == 'credits')
			$context['copyrights']['mods'][] = '<a href="https://dragomano.ru/mods/optimus" target="_blank">Optimus</a> &copy; 2010&ndash;2018, Bugo';
	}

	/**
	 * Различные замены вывода в шаблонах
	 *
	 * @param array $buffer
	 * @return array
	 */
	public static function buffer($buffer)
	{
		global $context, $modSettings, $mbname, $scripturl, $boardurl, $forum_copyright, $boarddir, $txt;

		if (isset($_REQUEST['xml']) || $context['current_action'] == 'printpage')
			return $buffer;

		if (!empty($context['robot_no_index']) || empty($context['canonical_url']))
			return $buffer;

		$replacements = array();

		// Description
		if (!empty($context['optimus_description'])) {
			$desc_old = '<meta name="description" content="' . $context['page_title_html_safe'] . '" />';
			$desc_new = '<meta name="description" content="' . $context['optimus_description'] . '" />';
			$replacements[$desc_old] = $desc_new;
		}

		// Metatags
		if (!empty($modSettings['optimus_meta']) && $modSettings['optimus_portal_compat'] != 1) {
			$meta = '';
			$test = unserialize($modSettings['optimus_meta']);

			foreach ($test as $n => $val) {
				if (!empty($val))
					$meta .= "\n\t" . '<meta name="' . $n . '" content="' . $val . '" />';
			}

			$charset_meta = '<meta http-equiv="Content-Type" content="text/html; charset=' . $context['character_set'] . '" />';
			$check_meta = $charset_meta . $meta;
			$replacements[$charset_meta] = $check_meta;
		}

		// Open Graph
		if (!empty($modSettings['optimus_open_graph'])) {
			$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			$new_doctype = '<!DOCTYPE html>';
			$replacements[$doctype] = $new_doctype;

			$type = !empty($context['optimus_og_type']) ? key($context['optimus_og_type']) : 'website';
			$xmlns = 'html xmlns="http://www.w3.org/1999/xhtml"';
			$new_xmlns = 'html prefix="og: http://ogp.me/ns#' . ($type == 'article' ? ' article: http://ogp.me/ns/article#' : '') . (!empty($modSettings['optimus_fb_appid']) ? ' fb: http://ogp.me/ns/fb#' : '') . '" lang="' . $txt['lang_dictionary'] . '"';
			$replacements[$xmlns] = $new_xmlns;

			$xmlns1 = '<html lang';
			$new_xmlns1 = '<html prefix="og: http://ogp.me/ns#' . ($type == 'article' ? ' article: http://ogp.me/ns/article#' : '') . (!empty($modSettings['optimus_fb_appid']) ? ' fb: http://ogp.me/ns/fb#' : '') . '" lang';
			$replacements[$xmlns1] = $new_xmlns1;

			$xmlns2 = '<html>';
			$new_xmlns2 = '<html prefix="og: http://ogp.me/ns#' . ($type == 'article' ? ' article: http://ogp.me/ns/article#' : '') . (!empty($modSettings['optimus_fb_appid']) ? ' fb: http://ogp.me/ns/fb#' : '') . '" lang="' . $txt['lang_dictionary'] . '">';
			$replacements[$xmlns2] = $new_xmlns2;

			$open_graph = '<meta property="og:title" content="' . (!empty($context['subject']) ? $context['subject'] : $context['page_title_html_safe']) . '" />';

			$open_graph .= '
	<meta property="og:type" content="' . $type . '" />';

			if (!empty($context['optimus_og_type'])) {
				$og_type = $context['optimus_og_type'][$type];
				foreach ($og_type as $t_key => $t_value) {
					$open_graph .= '
	<meta property="' . $type . ':' . $t_key . '" content="' . $t_value . '" />';
				}
			}

			$open_graph .= '
	<meta property="og:url" content="' . $context['canonical_url'] . '" />';

			if (!empty($modSettings['optimus_og_image'])) {
				$img_link = !empty($context['optimus_og_image']) ? $context['optimus_og_image'] : $modSettings['optimus_og_image'];
				$open_graph .= '
	<meta property="og:image" content="' . $img_link . '" />';
			}

			$open_graph .= '
	<meta property="og:description" content="' . (!empty($context['optimus_description']) ? $context['optimus_description'] : $context['page_title_html_safe']) . '" />
	<meta property="og:site_name" content="' . $mbname . '" />';

			if (!empty($modSettings['optimus_fb_appid']))
				$open_graph .= '
	<meta property="fb:app_id" name="app_id" content="' . $modSettings['optimus_fb_appid'] . '" />';

			$head_op = '<title>' . $context['page_title_html_safe'] . '</title>';
			$op_head = $open_graph . "\n\t" . $head_op;
			$replacements[$head_op] = $op_head;
		}

		if (!empty($modSettings['optimus_tw_cards']) && isset($context['canonical_url'])) {
			$twitter = '<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@' . $modSettings['optimus_tw_cards'] . '" />';

			if (empty($modSettings['optimus_open_graph']))
				$twitter .= '
	<meta name="twitter:title" content="' . (!empty($context['subject']) ? $context['subject'] : $context['page_title_html_safe']) . '" />
	<meta name="twitter:description" content="' . (!empty($context['optimus_description']) ? $context['optimus_description'] : $context['page_title_html_safe']) . '" />';

			if (!empty($context['optimus_og_image']))
				$twitter .= '
	<meta name="twitter:image" content="' . $context['optimus_og_image'] . '" />';

			$head_tw = '<title>';
			$tw_head = $twitter . "\n\t" . $head_tw;
			$replacements[$head_tw] = $tw_head;
		}

		// Counters
		$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();
		if (!in_array($context['current_action'], $ignored_actions)) {
			if (!empty($modSettings['optimus_count_code']))
				$replacements[$forum_copyright] = $modSettings['optimus_count_code'] . '<br />' . $forum_copyright;
		}

		// XML sitemap link
		if (!empty($modSettings['optimus_sitemap_link'])) {
			clearstatcache();

			if (file_exists($boarddir . '/sitemap.xml')) {
				$text = '<li class="last"><a id="button_wap2"';
				$link = '<li><a href="' . $boardurl . '/sitemap.xml" target="_blank">' . $txt['optimus_sitemap_xml_link'] . '</a></li>';
				$replacements[$text] = $link . $text;
			}
		}

		return str_replace(array_keys($replacements), array_values($replacements), $buffer);
	}
}
