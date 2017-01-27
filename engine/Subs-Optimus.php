<?php

/**
 * Subs-Optimus.php
 *
 * @package Optimus
 * @link http://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo http://dragomano.ru/mods/optimus
 * @copyright 2010-2016 Bugo
 * @license http://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.0 beta
 */

if (!defined('SMF'))
	die('Hacking attempt...');

define('OB_VER', '2.0 beta');
define('OB_LINK', 'http://dragomano.ru/mods/optimus');
define('OB_AUTHOR', 'Bugo');

function loadOptimusHooks()
{
	add_integration_function('integrate_load_theme', 'addOptimusLoadTheme', false);
	add_integration_function('integrate_load_theme', 'addOptimusCounters', false);
	add_integration_function('integrate_admin_areas', 'addOptimusAdminArea', false);
	add_integration_function('integrate_menu_buttons', 'addOptimusCopyright', false);
	add_integration_function('integrate_menu_buttons', 'addOptimusOperations', false);
	add_integration_function('integrate_buffer', 'addOptimusBuffer', false);
	add_integration_function('integrate_create_topic', 'getOptimusSitemap', false);
}

// integrate_load_theme hook
function addOptimusLoadTheme()
{
	global $txt, $modSettings;

	loadLanguage('Optimus/');

	// Forum title
	$txt['forum_index'] = '%1$s';
	if (!empty($modSettings['optimus_forum_index']))
		$txt['forum_index'] = '%1$s - ' . $modSettings['optimus_forum_index'];
}

// Выводим счетчики
function addOptimusCounters()
{
	global $modSettings, $context;

	$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();

	if (!in_array($context['current_action'], $ignored_actions)) {
		// Invisible counters like Google
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
		if (!empty($modSettings['optimus_count_code']) && !empty($modSettings['optimus_count_code_css']))
			addInlineCss($modSettings['optimus_count_code_css']);
	}
}

function addOptimusCopyright()
{
    global $context;

    if ($context['current_action'] == 'credits') {
        $context['copyrights']['mods'][] = '<a href="' . OB_LINK . '" target="_blank" title="' . OB_VER . '">Optimus</a> &copy; 2010&ndash;' . date('Y') . ', ' . OB_AUTHOR;
    }
}

// integrate_menu_buttons hook
function addOptimusOperations()
{
	global $modSettings, $context, $scripturl, $smcFunc, $boarddir, $forum_copyright, $boardurl, $txt;

	if (!isset($modSettings['optimus_portal_compat']))
		$modSettings['optimus_portal_compat'] = 0;

	// Some hacks for portal mods
	if (!empty($modSettings['optimus_portal_compat'])) {
		// PortaMx
		if ($modSettings['optimus_portal_compat'] == 1) {
			if (isset($context['current_action']) && $context['current_action'] == 'community') {
				if (empty($modSettings['pmx_frontmode'])) {
					$context['canonical_url'] = $boardurl . '/';
				} else {
					$context['canonical_url'] = $scripturl . '?action=' . $context['current_action'];
				}
			} else {
				if (empty($_REQUEST['action']) && empty($_REQUEST['board']) && empty($_REQUEST['topic'])) {
					$context['canonical_url'] = $boardurl . '/';

					// Portal title
					if (!empty($modSettings['optimus_portal_index']))
						$context['page_title'] .= ' - ' . $modSettings['optimus_portal_index'];
				}
			}
		}

		// EhPortal
		if ($modSettings['optimus_portal_compat'] == 2 && isset($context['current_action'])) {
			if ($context['current_action'] == 'portal') {
				$context['canonical_url'] = $boardurl . '/';

				// Portal title
				if (!empty($modSettings['optimus_portal_index']))
					$context['page_title'] .= ' - ' . $modSettings['optimus_portal_index'];
			}

			if ($context['current_action'] == 'forum') {
				$context['canonical_url'] = $scripturl . '?action=' . $context['current_action'];
			}
		}
	}

	// Forum description
	if (empty($context['current_action']) && empty($_REQUEST['board']) && empty($_REQUEST['topic']))
		$context['meta_description'] = !empty($modSettings['optimus_description']) ? $smcFunc['htmlspecialchars']($modSettings['optimus_description']) : $context['meta_description'];

	getOptimusPageTemplates();

	getOptimusAevaMedia();

	getOptimusHttpStatus();

	// XML sitemap link
	if (!empty($modSettings['optimus_sitemap_link']) && file_exists($boarddir . '/sitemap.xml'))
		$forum_copyright .= ' | <a href="' . $boardurl . '/sitemap.xml" target="_blank">' . $txt['optimus_sitemap_xml_link'] . '</a>';
}

/**
 * This called via integrate_buffer hook
 *
 * @param  array $buffer [принимаем текущее содержимое буфера страницы]
 * @return array $buffer [возвращаем новое содержимое буфера, с произведенными заменами]
 */
function addOptimusBuffer(&$buffer)
{
	global $context, $modSettings, $scripturl, $boardurl, $settings, $forum_copyright;

	if (isset($_REQUEST['xml']) || $context['current_action'] == 'printpage')
		return $buffer;

	$replacements = array();
	
	// Remove index.php from URLs
	if (!empty($modSettings['optimus_remove_indexphp']) && empty($modSettings['queryless_urls']) && empty($modSettings['simplesef_enable'])) {
		$index = $scripturl;
		$del_index = $boardurl . '/';
		$replacements[$index] = $del_index;
	}

	// Verification tags
	if (isset($context['canonical_url']) && !empty($modSettings['optimus_meta'])) {
		$meta = '';
		$test = @unserialize($modSettings['optimus_meta']);

		foreach ($test as $var) {
			$meta .= "\n\t" . '<meta name="' . $var['name'] . '" content="' . $var['content'] . '">';
		}

		$charset_meta = '<meta charset="' . $context['character_set'] . '">';
		$check_meta = $charset_meta . $meta;
		$replacements[$charset_meta] = $check_meta;
	}

	// Open Graph for media pages
	if ($context['current_action'] == 'media' && !empty($_REQUEST['sa']) && !empty($_REQUEST['in'])) {
		if ($_REQUEST['sa'] == 'item') {
			$item = (int) $_REQUEST['in'];

			if (function_exists('aeva_getItemData')) {
				$handler = new aeva_media_handler;
				$exif = @unserialize($context['item_data']['exif']);

				if ($context['item_data']['type'] == 'video') {
					$xmlns = 'html' . ($context['right_to_left'] ? ' dir="rtl"' : '');
					$new_xmlns = $xmlns . ' xmlns:og="http://ogp.me/ns#"';
					$replacements[$xmlns] = $new_xmlns;

					$duration = $exif['duration'];

					$context['page_title_html_safe'] = $context['item_data']['title'];
					$context['canonical_url'] = $scripturl . '?action=media;sa=item;in=' . $item;
					$settings['og_image'] = $scripturl . '?action=media;sa=media;in=' . $item . ';thumb';

					if (!empty($context['item_data']['description']))
						$context['meta_description'] = html_entity_decode($context['item_data']['description'], ENT_QUOTES, $context['character_set']);

					$context['ogp_meta'] = '
	<meta property="og:video" content="' . $boardurl . '/MGalleryItem.php?id=' . $item . '">
	<meta property="og:video:height" content="' . $context['item_data']['height'] . '">
	<meta property="og:video:width" content="' . $context['item_data']['width'] . '">
	<meta property="og:video:type" content="' . $handler->getMimeFromExt($context['item_data']['filename']) . '">
	<meta property="og:duration" content="' . $duration . '" />';
				}

				if (!empty($context['ogp_meta'])) {
					$head = '<title>' . $context['page_title_html_safe'] . '</title>';
					$new_head = $context['ogp_meta'] . "\n\t" . $head;
					$replacements[$head] = $new_head;
				}
			}
		}
	}

	// Counters
	$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();
	if (!in_array($context['current_action'], $ignored_actions)) {
		if (!empty($modSettings['optimus_count_code']))
			$replacements[$forum_copyright] = $modSettings['optimus_count_code'] . '<br>' . $forum_copyright;
	}

	return str_replace(array_keys($replacements), array_values($replacements), $buffer);
}

// Обрабатываем шаблоны заголовков страниц
function getOptimusPageTemplates()
{
	global $modSettings, $txt, $context, $topicinfo, $board_info;

	if (!empty($modSettings['optimus_templates']) && strpos($modSettings['optimus_templates'], 'board') && strpos($modSettings['optimus_templates'], 'topic')) {
		$templates = @unserialize($modSettings['optimus_templates']);

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
	}
	else {
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
		if (!empty($context['page_info']['current_page']) && $context['page_info']['num_pages'] != 1) {
			$trans = array("{#}" => $context['page_info']['current_page']);
			$board_page_number = strtr($board_page_tpl, $trans);
			$topic_page_number = strtr($topic_page_tpl, $trans);
		}
	}

	// Topics
	if (!empty($context['topic_first_message'])) {
		$trans = array(
            "{topic_name}" => $context['topicinfo']['subject'],
			"{board_name}" => strip_tags($board_info['name']),
			"{cat_name}"   => $board_info['cat']['name'],
			"{forum_name}" => $context['forum_name'],
		);

		$topic_page_number = !empty($topic_page_number) ? $topic_page_number : (!empty($topic_site_tpl) ? ' - ' : '');

		$context['page_title'] = strtr($topic_name_tpl . $topic_page_number . $topic_site_tpl, $trans);
		$context['meta_description'] = !empty($modSettings['optimus_topic_description']) && !empty($context['topic_description']) ? $context['topic_description'] : '';

		getOptimusOgImage();
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
	}
}

// Достаем URL вложения из первого сообщения темы
function getOptimusOgImage()
{
	global $modSettings, $settings, $smcFunc, $context, $scripturl;

	if (empty($modSettings['optimus_og_image']))
		return;

	// Кэшируем запрос
	if (($settings['og_image'] = cache_get_data('og_image_' . $context['current_topic'], 3600)) == null) {
		$request = $smcFunc['db_query']('', '
			SELECT IFNULL(id_attach, 0) AS id
			FROM {db_prefix}attachments
			WHERE id_msg = {int:msg}
			GROUP BY id
			LIMIT 1',
			array(
				'msg' => $context['topic_first_message']
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))	{
			if ($row['id'] != 0) {
				$settings['og_image'] = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . ';attach=' . $row['id'] . ';image';
			}
		}

		$smcFunc['db_free_result']($request);

		cache_put_data('og_image_' . $context['current_topic'], $settings['og_image'], 3600);
	}
}

// Set canonical URLs and descriptions for AM pages
function getOptimusAevaMedia()
{
	global $context, $scripturl, $smcFunc;

	if ($context['current_action'] == 'media' && !empty($_REQUEST['sa']) && !empty($_REQUEST['in'])) {
		$item = (int) $_REQUEST['in'];

		if ($_REQUEST['sa'] == 'album')
			$context['canonical_url'] = $scripturl . '?action=media;sa=album;in=' . $item;

		if ($_REQUEST['sa'] == 'item')
			$context['canonical_url'] = $scripturl . '?action=media;sa=item;in=' . $item;

		if (!empty($context['item_data']['description']))
			$context['optimus_description'] = $smcFunc['htmlspecialchars'](un_htmlspecialchars($context['item_data']['description']));
		else
			$context['optimus_description'] = '';
	}
}

// Возвращаемые коды состояния, в зависимости от ситуации
function getOptimusHttpStatus()
{
	global $modSettings, $board_info, $context, $txt;

	if (empty($modSettings['optimus_404_status']) || empty($board_info['error']))
		return;

	// Страница не существует?
	if ($board_info['error'] == 'exist') {
		header('HTTP/1.1 404 Not Found');

		loadTemplate('Optimus');

		$context['sub_template'] = '404';
		$context['page_title'] = $txt['optimus_404_page_title'];
	}

	// Нет доступа?
	if ($board_info['error'] == 'access') {
		header('HTTP/1.1 403 Forbidden');

		loadTemplate('Optimus');

		$context['sub_template'] = '403';
		$context['page_title'] = $txt['optimus_403_page_title'];
	}
}

/**
 * Обработка дат
 *
 * @param  string $timestamp [принимаем отметку времени страницы]
 * @return string $result    [возвращаем отметку времени в новом формате, для карты сайта]
 */
function getOptimusSitemapDate($timestamp = '')
{
	$timestamp = empty($timestamp) ? time() : $timestamp;
	$gmt = substr(date("O", $timestamp), 0, 3) . ':00';
	$result = date('Y-m-d\TH:i:s', $timestamp) . $gmt;

	return $result;
}

/**
 * Создаем файл карты
 *
 * @param  string $path [путь к файлу]
 * @param  string $data [текст для вставки в файл]
 * @return bool         [true при успешном создании, false в противном случае]
 */
function createOptimusFile($path, $data)
{
	if (!$fp = @fopen($path, 'w'))
		return false;

	flock($fp, LOCK_EX);
	fwrite($fp, $data);
	flock($fp, LOCK_UN);
	fclose($fp);

	return true;
}

/**
 * Если количество урлов больше 50000, заканчиваем сказку
 *
 * @param  array $array [массив страниц для занесения в карту]
 */
function checkOptimusCountUrls($array)
{
	global $txt;

	if (count($array) > 50000)
		log_error($txt['optimus_sitemap_url_limit'] . $txt['optimus_sitemap_rec'], 'general');

	return;
}

/**
 * Если размер файла превышает 10 МБ, отправляем запись в Журнал ошибок
 *
 * @param  string $file [принимаем ссылку на файл]
 */
function checkOptimusFilesize($file)
{
	global $txt;

	clearstatcache();

	if (filesize($file) > 10485760)
		log_error(sprintf($txt['optimus_sitemap_size_limit'], @pathinfo($file, PATHINFO_BASENAME)) . $txt['optimus_sitemap_rec'], 'general');

	return;
}

/**
 * Определяем периодичность обновлений
 *
 * @param  string $time [принимаем время изменения страницы]
 * @return string       [возвращаем одно из предустановленных значений, для установки периодичности проверки]
 */
function getOptimusSitemapFrequency($time)
{
	$frequency = time() - $time;

	if ($frequency < (24*60*60))
		return 'hourly';
	elseif ($frequency < (24*60*60*7))
		return 'daily';
	elseif ($frequency < (24*60*60*7*(52/12)))
		return 'weekly';
	elseif ($frequency < (24*60*60*365))
		return 'monthly';

	return 'yearly';
}

/**
 * Генерация карты форума
 *
 * @return bool [true для заполнения отчета в журнале диспетчера задач]
 */
function getOptimusSitemap()
{
	global $modSettings, $sourcedir, $boardurl, $smcFunc, $scripturl, $context, $boarddir;

	// Прежде всего проверяем, активировано ли создание карты форума (в настройках)
	if (empty($modSettings['optimus_sitemap_enable']))
		return;

	$t = "\t";
	$n = "\n";
	$sef = false;
	$mobile_type = 'wap2'; // wap, wap2, imode
	$xmlns = 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
	$tab = $xmlns . $n . $t . $t;
	$xmlns_mobile = $tab . 'xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0"';
	$xmlns_image  = $tab . 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
	$xmlns_video  = $tab . 'xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"';

	clearstatcache();

	// SimpleSEF enabled?
	$sef = !empty($modSettings['simplesef_enable']) && file_exists($sourcedir . '/SimpleSEF.php');
	if ($sef) {
		function create_sefurl($url)
		{
			global $sourcedir;

			require_once($sourcedir . '/SimpleSEF.php');
			$simple = new SimpleSEF;

			return $simple->create_sef_url($url);
		}
	}

	// PortaMx SEF enabled?
	if (file_exists($sourcedir . '/PortaMx/PortaMxSEF.php') && function_exists('create_sefurl'))
		$sef = true;

	// Объявляем массивы, с которыми будем работать
	$fm = $media = $boards = $topics = array();

	// Boards
	$request = $smcFunc['db_query']('', '
		SELECT b.id_board, m.poster_time, m.modified_time
		FROM {db_prefix}boards AS b
			LEFT JOIN {db_prefix}messages AS m ON (m.id_msg = b.id_last_msg)
		WHERE FIND_IN_SET(-1, b.member_groups) != 0' . (!empty($modSettings['recycle_board']) ? ' AND b.id_board <> {int:recycle_board}' : '') . (!empty($modSettings['optimus_sitemap_topic_size']) ? ' AND b.num_posts > {int:posts}' : '') . '
		ORDER BY b.id_board',
		array(
			'recycle_board' => !empty($modSettings['recycle_board']) ? (int) $modSettings['recycle_board'] : 0,
			'posts'         => !empty($modSettings['optimus_sitemap_topic_size']) ? (int) $modSettings['optimus_sitemap_topic_size'] : 0,
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
		$boards[] = $row;

	$smcFunc['db_free_result']($request);

	$last = array(0);
	foreach ($boards as $entry) {
		$last[] = empty($entry['modified_time']) ? (empty($entry['poster_time']) ? '' : $entry['poster_time']) : $entry['modified_time'];
	}
	$last_edit = max($last);

	// Здесь быстренько заполняем информацию о главной странице
	$fm[] = array(
		'loc'        => $boardurl . '/',
		'wap'        => $scripturl . '/?' . $mobile_type,
		'lastmod'    => getOptimusSitemapDate($last_edit),
		'changefreq' => getOptimusSitemapFrequency($last_edit),
		'priority'   => 1,
	);

	// А здесь — для разделов
	foreach ($boards as $entry)	{
		$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];

		// Поддержка мода BoardNoIndex
		if (!empty($modSettings['BoardNoIndex_enabled'])) {
			if (!in_array($entry['id_board'], @unserialize($modSettings['BoardNoIndex_select_boards']))) {
				$fm[] = array(
					'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
					'wap'        => $scripturl . '?board=' . $entry['id_board'] . '.0;' . $mobile_type,
					'lastmod'    => getOptimusSitemapDate($last_edit),
					'changefreq' => getOptimusSitemapFrequency($last_edit),
					'priority'   => 0.8,
				);
			}
		}
		else {
			$fm[] = array(
				'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
				'wap'        => $scripturl . '?board=' . $entry['id_board'] . '.0;' . $mobile_type,
				'lastmod'    => getOptimusSitemapDate($last_edit),
				'changefreq' => getOptimusSitemapFrequency($last_edit),
				'priority'   => 0.8,
			);
		}
	}

	// Topics
	$request = $smcFunc['db_query']('', '
		SELECT t.id_topic, t.id_board, t.id_last_msg, m.poster_time, m.modified_time
		FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
		WHERE FIND_IN_SET(-1, b.member_groups) != 0' . (!empty($modSettings['recycle_board']) ? ' AND b.id_board <> {int:recycle_board}' : '') . (!empty($modSettings['optimus_sitemap_topic_size']) ? ' AND t.num_replies > {int:replies}' : '') . '
		ORDER BY t.id_topic',
		array(
			'recycle_board' => !empty($modSettings['recycle_board']) ? (int) $modSettings['recycle_board'] : 0,
			'replies'       => !empty($modSettings['optimus_sitemap_topic_size']) ? (int) $modSettings['optimus_sitemap_topic_size'] : 0,
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
		$topics[] = $row;

	$smcFunc['db_free_result']($request);

	foreach ($topics as $entry)	{
		$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];

		// Поддержка мода BoardNoIndex
		if (!empty($modSettings['BoardNoIndex_enabled'])) {
			if (!in_array($entry['id_board'], @unserialize($modSettings['BoardNoIndex_select_boards']))) {
				$fm[] = array(
					'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $scripturl . '?topic=' . $entry['id_topic'] . '.0',
					'wap'        => $scripturl . '?topic=' . $entry['id_topic'] . '.0;' . $mobile_type,
					'lastmod'    => getOptimusSitemapDate($last_edit),
					'changefreq' => getOptimusSitemapFrequency($last_edit),
					'priority'   => 0.6,
				);
			}
		}
		else
		{
			$fm[] = array(
				'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $scripturl . '?topic=' . $entry['id_topic'] . '.0',
				'wap'        => $scripturl . '?topic=' . $entry['id_topic'] . '.0;' . $mobile_type,
				'lastmod'    => getOptimusSitemapDate($last_edit),
				'changefreq' => getOptimusSitemapFrequency($last_edit),
				'priority'   => 0.6,
			);
		}
	}

	// Aeva Media
	if (file_exists($sourcedir . '/Aeva-Subs.php'))	{
		$query_one = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}aeva_media'", array());
		$query_two = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}aeva_albums'", array());
		$result = $smcFunc['db_num_rows']($query_one) != 0 && $smcFunc['db_num_rows']($query_two) != 0;

		if ($result) {
			$items = array();

			$request = $smcFunc['db_query']('', '
				SELECT am.id_media, am.title, am.description, am.type, am.album_id, am.rating, am.views, aa.name
				FROM {db_prefix}aeva_media AS am
					INNER JOIN {db_prefix}aeva_albums AS aa ON (aa.id_album = am.album_id)
					INNER JOIN {db_prefix}permissions AS ps ON (ps.id_group = -1)
				WHERE FIND_IN_SET(-1, aa.access) != 0
					AND ps.permission LIKE "aeva_access"
				ORDER BY am.id_media',
				array()
			);

			while ($row = $smcFunc['db_fetch_assoc']($request))
				$items[] = $row;

			$smcFunc['db_free_result']($request);

			// AM Items
			foreach ($items as $entry) {
				$media[] = array(
					'loc'     => $scripturl . '?action=media;sa=item;in=' . $entry['id_media'],
					'album'   => $scripturl . '?action=media;sa=album;in=' . $entry['album_id'],
					'image'   => $entry['type'] == 'image' ? $boardurl . '/MGalleryItem.php?id=' . $entry['id_media'] : '',
					'video'   => $entry['type'] == 'video' ? $boardurl . '/MGalleryItem.php?id=' . $entry['id_media'] : '',
					'caption' => $entry['title'],
					'thumb'   => $scripturl . '?action=media;sa=media;in=' . $entry['id_media'] . ';thumb',
					'desc'    => !empty($entry['description']) ? $entry['description'] : '',
					'rating'  => !empty($entry['rating']) ? $entry['rating'] : 0,
					'count'   => !empty($entry['views']) ? $entry['views'] : 0,
					'name'    => $entry['name'],
				);
			}
		}
	}

	// SMF Gallery mod
	if (file_exists($sourcedir . '/Gallery2.php')) {
		$query_one = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}gallery_cat'", array());
		$query_two = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}gallery_pic'", array());
		$result = $smcFunc['db_num_rows']($query_one) != 0 && $smcFunc['db_num_rows']($query_two) != 0;

		if ($result) {
			$items = array();

			$request = $smcFunc['db_query']('', '
				SELECT gp.id_picture, gp.title, gp.filename
				FROM {db_prefix}gallery_pic AS gp
					INNER JOIN {db_prefix}permissions AS ps ON (ps.id_group = -1)
				WHERE ps.permission LIKE "smfgallery_view"
				ORDER BY gp.id_picture',
				array()
			);

			while ($row = $smcFunc['db_fetch_assoc']($request))
				$items[] = $row;

			$smcFunc['db_free_result']($request);

			// Gallery Items
			foreach ($items as $entry) {
				$media[] = array(
					'loc'     => $scripturl . '?action=gallery;sa=view;pic=' . $entry['id_picture'],
					'image'   => $boardurl . '/gallery/' . $entry['filename'],
					'caption' => $entry['title'],
				);
			}
		}
	}

	$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n . '<?xml-stylesheet type="text/xsl" href="' . $boardurl . '/Themes/default/css/sitemap.xsl"?>' . $n . '<urlset ' . $xmlns . '>' . $n;
	$footer = '</urlset>';
	$out = '';

	checkOptimusCountUrls($fm);

	foreach ($fm as $entry)	{
		$out .= $t . '<url>' . $n;
		$out .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;
		$out .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;
		$out .= $t . $t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $n;
		$out .= $t . $t . '<priority>' . $entry['priority'] . '</priority>' . $n;
		$out .= $t . '</url>' . $n;
	}

	// Это для мобилок, задел на будущее
	if (!empty($mobile_type)) {
		$mobile = '';
		foreach ($fm as $entry) {
			if (!empty($entry['wap'])) {
				$mobile .= $t . '<url>' . $n;
				$mobile .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['wap']) : $entry['wap']) . '</loc>' . $n;
				$mobile .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;
				$mobile .= $t . $t . '<mobile:mobile/>' . $n;
				$mobile .= $t . '</url>' . $n;
			}
		}
	}

	// Карта изображений в Галерее
	$images = '';
	foreach ($media as $entry) {
		if (!empty($entry['image'])) {
			$images .= $t . '<url>' . $n;
			$images .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;
			$images .= $t . $t . '<image:image>' . $n;
			$images .= $t . $t . $t . '<image:loc>' . $entry['image'] . '</image:loc>' . $n;
			$images .= $t . $t . $t . '<image:caption>' . $entry['caption'] . '</image:caption>' . $n;
			$images .= $t . $t . '</image:image>' . $n;
			$images .= $t . '</url>' . $n;
		}
	}

	// Карта видеороликов в Галерее
	$videos = '';
	foreach ($media as $entry) {
		if (!empty($entry['video'])) {
			$videos .= $t . '<url>' . $n;
			$videos .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;
			$videos .= $t . $t . '<video:video>' . $n;
			$videos .= $t . $t . $t . '<video:thumbnail_loc>' . ($sef ? create_sefurl($entry['thumb']) : $entry['thumb']) . '</video:thumbnail_loc>' . $n;
			$videos .= $t . $t . $t . '<video:title>' . $entry['caption'] . '</video:title>' . $n;
			$videos .= $t . $t . $t . '<video:description>' . $entry['desc'] . '</video:description>' . $n;
			$videos .= $t . $t . $t . '<video:content_loc>' . $entry['video'] . '</video:content_loc>' . $n;
			$videos .= $t . $t . $t . '<video:rating>' . $entry['rating'] . '</video:rating>' . $n;
			$videos .= $t . $t . $t . '<video:view_count>' . $entry['count'] . '</video:view_count>' . $n;
			$videos .= $t . $t . $t . '<video:gallery_loc title="' . $entry['name'] . '">' . ($sef ? create_sefurl($entry['album']) : $entry['album']) . '</video:gallery_loc>' . $n;
			$videos .= $t . $t . '</video:video>' . $n;
			$videos .= $t . '</url>' . $n;
		}
	}

	// Pretty URLs installed?
	$pretty = $sourcedir . '/PrettyUrls-Filters.php';
	if (file_exists($pretty) && !empty($modSettings['pretty_enable_filters'])) {
		require_once($pretty);
		$context['pretty']['search_patterns'][] = '~(<loc>)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';
		$context['pretty']['search_patterns'][] = '~(<video:thumbnail_loc>)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(<video:thumbnail_loc>)([^<]+)~';
		$context['pretty']['search_patterns'][] = '~(">)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(">)([^<]+)~';
		$out = pretty_rewrite_buffer($out);
		if (!empty($mobile))
			$mobile = pretty_rewrite_buffer($mobile);
		if (!empty($images))
			$images = pretty_rewrite_buffer($images);
		if (!empty($videos))
			$videos = pretty_rewrite_buffer($videos);
	}

	$out = $header . $out . $footer;

	// Создаем обычную карту сайта
	$sitemap = $boarddir . '/sitemap.xml';
	createOptimusFile($sitemap, $out);
	checkOptimusFilesize($sitemap);

	// Карта для мобилок
	if (!empty($mobile_type)) {
		$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n . '<?xml-stylesheet type="text/xsl" href="' . $boardurl . '/Themes/default/css/sitemap.xsl"?>' . $n . '<urlset ' . $xmlns_mobile . '>' . $n;
		$xml_data = $header . $mobile . $footer;
		$sitemap = $boarddir . '/sitemap_mobile.xml';

		createOptimusFile($sitemap, $xml_data);
		checkOptimusFilesize($sitemap);
	}

	// Карта ссылок на изображения в Галерее
	if (!empty($images)) {
		$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n . '<?xml-stylesheet type="text/xsl" href="' . $boardurl . '/Themes/default/css/sitemap.xsl"?>' . $n . '<urlset ' . $xmlns_image . '>' . $n;
		$xml_data = $header . $images . $footer;
		$sitemap = $boarddir . '/sitemap_images.xml';

		createOptimusFile($sitemap, $xml_data);
		checkOptimusFilesize($sitemap);
	}

	// Карта ссылок на видеоролики в Галерее
	if (!empty($videos)) {
		$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n . '<?xml-stylesheet type="text/xsl" href="' . $boardurl . '/Themes/default/css/sitemap.xsl"?>' . $n . '<urlset ' . $xmlns_video . '>' . $n;
		$xml_data = $header . $videos . $footer;
		$sitemap = $boarddir . '/sitemap_videos.xml';

		createOptimusFile($sitemap, $xml_data);
		checkOptimusFilesize($sitemap);
	}

	// Return for the log...
	return true;
}

?>