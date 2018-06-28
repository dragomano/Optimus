<?php

/**
 * Optimus.class.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2018 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 0.1
 */

if (!defined('ELK'))
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
		add_integration_function('integrate_credits', 'Optimus::credits', false);
		add_integration_function('integrate_buffer', 'Optimus::buffer', false);
		add_integration_function('integrate_admin_areas', 'Optimus::adminAreas', false);
	}

	/**
	 * Подключаем языковой файл, проводим различные операции с заголовками и пр.
	 *
	 * @return void
	 */
	public static function loadTheme()
	{
		global $modSettings, $context, $txt;

		loadLanguage('Optimus');

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
				$context['html_headers'] .= "\n\t" . '<style>' . $modSettings['optimus_counters_css'] . '</style>';
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

		if (ELK == 'SSI')
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

			loadTemplate('Errors');

			$context['error_code']    = '';
			$context['sub_template']  = 'fatal_error';
			$context['page_title']    = $txt['optimus_404_page_title'];
			$context['error_title']   = $txt['optimus_404_h2'];
			$context['error_message'] = $txt['optimus_404_h3'];
		}

		// Нет доступа? No access?
		if ($board_info['error'] == 'access') {
			header('HTTP/1.1 403 Forbidden');

			loadTemplate('Errors');

			$context['error_code']    = '';
			$context['sub_template']  = 'fatal_error';
			$context['page_title']    = $txt['optimus_403_page_title'];
			$context['error_title']   = $txt['optimus_403_h2'];
			$context['error_message'] = $txt['optimus_403_h3'];
		}
	}

	/**
	 * Создаем описание страницы из первого сообщения
	 *
	 * @return void
	 */
	public static function getDescription()
	{
		global $context, $modSettings, $txt, $board_info;

		if (empty($context['first_message']))
			return;

		$db = database();

		$request = $db->query('substring', '
			SELECT body, poster_time, modified_time
			FROM {db_prefix}messages
			WHERE id_msg = {int:id_msg}
			LIMIT 1',
			array(
				'id_msg' => $context['first_message']
			)
		);

		while ($row = $db->fetch_assoc($request))	{
			censorText($row['body']);

			$row['body'] = parse_bbc($row['body'], false);

			// Ищем изображение в тексте сообщения
			if (!empty($modSettings['optimus_og_image'])) {
				$first_post_image = preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $row['body'], $value);
				$context['optimus_og_image'] = $first_post_image ? array_pop($value) : null;
			}

			$row['body'] = explode("<br />", $row['body'])[0];
			$row['body'] = explode("<hr />", $row['body'])[0];
			$row['body'] = strip_tags($row['body']);
			$row['body'] = str_replace($txt['quote'], '', $row['body']);

			if (Util::strlen($row['body']) > 130)
				$row['body'] = Util::substr($row['body'], 0, 127) . '...';

			$context['optimus_description'] = $row['body'];

			$context['optimus_og_type']['article'] = array(
				'published_time' => date('Y-m-d\TH:i:s', $row['poster_time']),
				'modified_time'  => !empty($row['modified_time']) ? date('Y-m-d\TH:i:s', $row['modified_time']) : null,
				'section'        => $board_info['name']
			);
		}

		$db->free_result($request);
	}

	/**
	 * Достаем URL вложения из первого сообщения темы
	 *
	 * @return void
	 */
	public static function getOgImage()
	{
		global $modSettings, $context, $scripturl;

		if (!allowedTo('view_attachments') || empty($modSettings['optimus_og_image']))
			return;

		$temp_image = $context['optimus_og_image'];

		$db = database();

		if (($context['optimus_og_image'] = cache_get_data('og_image_' . $context['current_topic'])) == null) {
			$request = $db->query('', '
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

			list ($image_id) = $db->fetch_row($request);
			$db->free_result($request);

			if (!empty($image_id))
				$context['optimus_og_image'] = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . ';attach=' . $image_id . ';image';
			else
				$context['optimus_og_image'] = $temp_image;

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
		global $modSettings, $context, $boarddir, $forum_copyright, $boardurl, $txt;

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
				$context['optimus_description'] = Util::htmlspecialchars($modSettings['optimus_description']);
		}

		self::processExtendTitles();
		self::processErrorCodes();

		// description & og:image
		if (!empty($context['first_message'])) {
			if (!empty($modSettings['optimus_topic_description'])) {
				if (!empty($context['topic_description']))
					$context['optimus_description'] = $context['topic_description'];
				else
					self::getDescription();
			}

			self::getOgImage();
		}

		// XML sitemap link
		if (!empty($modSettings['optimus_sitemap_link']) && file_exists($boarddir . '/sitemap.xml'))
			$forum_copyright .= ' | <a href="' . $boardurl . '/sitemap.xml" target="_blank">' . $txt['optimus_sitemap_xml_link'] . '</a>';
	}

	/**
	 * Выводим копирайты (куда без них)
	 *
	 * @param array $credits
	 * @return void
	 */
	public static function credits(&$credits)
	{
		$credits['credits_addons'][] = '<a href="https://dragomano.ru/mods/optimus" target="_blank">Optimus</a> &copy; 2010&ndash;2018, Bugo';
	}

	/**
	 * Различные замены вывода в коде шаблонов
	 *
	 * @param array $buffer
	 * @return array
	 */
	public static function buffer($buffer)
	{
		global $context, $modSettings, $mbname, $forum_copyright, $boarddir, $boardurl, $txt;

		if (isset($_REQUEST['xml']) || !empty($context['robot_no_index']))
			return $buffer;

		$replacements = array();

		// Description
		if (!empty($context['optimus_description'])) {
			$desc_old = '<meta name="description" content="' . $context['page_title_html_safe'] . '" />';
			$desc_new = '<meta name="description" content="' . $context['optimus_description'] . '" />';
			$replacements[$desc_old] = $desc_new;
		}

		// Metatags
		if (!empty($modSettings['optimus_meta'])) {
			$meta = '';
			$test = unserialize($modSettings['optimus_meta']);

			foreach ($test as $n => $val) {
				if (!empty($val))
					$meta .= "\n\t" . '<meta name="' . $n . '" content="' . $val . '" />';
			}

			$charset_meta = '<meta name="mobile-web-app-capable" content="yes" />';
			$check_meta = $charset_meta . $meta;
			$replacements[$charset_meta] = $check_meta;
		}

		// Open Graph
		$type = !empty($context['optimus_og_type']) ? key($context['optimus_og_type']) : 'website';

		$xmlns = '<html>';
		$new_xmlns = '<html prefix="og: http://ogp.me/ns#' . ($type == 'article' ? ' article: http://ogp.me/ns/article#' : '') . (!empty($modSettings['optimus_fb_appid']) ? ' fb: http://ogp.me/ns/fb#' : '') . '">';
		$replacements[$xmlns] = $new_xmlns;

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

		if (!empty($context['canonical_url']))
			$open_graph .= '
	<meta property="og:url" content="' . $context['canonical_url'] . '" />';

		if (!empty($context['optimus_og_image']))
			$open_graph .= '
	<meta property="og:image" content="' . $context['optimus_og_image'] . '" />';

		$open_graph .= '
	<meta property="og:description" content="' . (!empty($context['optimus_description']) ? $context['optimus_description'] : $context['page_title_html_safe']) . '" />
	<meta property="og:site_name" content="' . $mbname . '" />';

		if (!empty($modSettings['optimus_fb_appid']))
			$open_graph .= '
	<meta property="fb:app_id" name="app_id" content="' . $modSettings['optimus_fb_appid'] . '" />';

		$head_op = '<meta name="viewport"';
		$op_head = $open_graph . "\n\t" . $head_op;
		$replacements[$head_op] = $op_head;

		if (!empty($modSettings['optimus_tw_cards'])) {
			$twitter = '<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@' . $modSettings['optimus_tw_cards'] . '" />';

			if (!empty($context['optimus_og_image']))
				$twitter .= '
	<meta name="twitter:image" content="' . $context['optimus_og_image'] . '" />';

			$head_tw = '<meta name="description"';
			$tw_head = $twitter . "\n\t" . $head_tw;
			$replacements[$head_tw] = $tw_head;
		}

		// Counters
		$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();
		if (!in_array($context['current_action'], $ignored_actions) && !empty($modSettings['optimus_count_code']))
			$count_code = $modSettings['optimus_count_code'] . '<br>';

		// XML sitemap link
		if (!empty($modSettings['optimus_sitemap_link']) && file_exists($boarddir . '/sitemap.xml'))
			$xml_link = '<li class="smalltext">| <a href="' . $boardurl . '/sitemap.xml" target="_blank">' . $txt['optimus_sitemap_xml_link'] . '</a></li>';

		$replacements[$forum_copyright] = (!empty($count_code) ? $count_code : '') . $forum_copyright . (!empty($xml_link) ? $xml_link : '');

		return str_replace(array_keys($replacements), array_values($replacements), $buffer);
	}

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
			'label'       => $txt['optimus_title'],
			'file'        => 'ManageOptimus.controller.php',
			'controller'  => 'ManageOptimus_Controller',
			'function'    => 'action_index',
			'icon'        => 'maintain.png',
			'permission'  => array('admin_forum'),
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
}
