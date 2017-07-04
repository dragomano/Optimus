<?php

/**
 * Subs-Optimus.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2017 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 1.9.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

// Подключаем используемые хуки
function optimus_hooks()
{
	add_integration_function('integrate_load_theme', 'optimus_home', false);
	add_integration_function('integrate_admin_include', '$sourcedir/Admin-Optimus.php', false);
	add_integration_function('integrate_admin_areas', 'optimus_admin_areas', false);
	add_integration_function('integrate_menu_buttons', 'optimus_operations', false);
	add_integration_function('integrate_create_topic', 'optimus_sitemap', false);
	add_integration_function('integrate_buffer', 'optimus_buffer', false);
}

// integrate_load_theme hook
function optimus_home()
{
	global $modSettings, $scripturl, $context, $boardurl, $mbname, $txt;

	loadLanguage('Optimus/');

	// Portal
	if (!isset($modSettings['optimus_portal_compat']))
		$modSettings['optimus_portal_compat'] = 0;

	if (!empty($modSettings['optimus_portal_compat'])) {
		if (!empty($modSettings['pmx_frontmode']) || (!empty($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 1)) {
			if (empty($context['current_board']) && empty($context['current_topic']) && empty($_REQUEST['action']) && !empty($modSettings['optimus_portal_index']))	{
				$context['forum_name'] = $mbname . ' - ' . $modSettings['optimus_portal_index'];
			}
		}
	}

	// Forum
	$txt['forum_index'] = '%1$s';
	if (!empty($modSettings['optimus_forum_index']))
		$txt['forum_index'] = '%1$s - ' . $modSettings['optimus_forum_index'];

	// Counters
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
		if (!empty($modSettings['optimus_count_code']) && !empty($modSettings['optimus_counters_css']))
			$context['html_headers'] .= "\n\t" . '<style type="text/css">' . $modSettings['optimus_counters_css'] . '</style>';
	}

	// Special fix for PortaMx
	if (!empty($modSettings['optimus_portal_compat']) && $modSettings['optimus_portal_compat'] == 1) {
		if (empty($_REQUEST['action']) && empty($_REQUEST['board']) && empty($_REQUEST['topic'])) {
			// Для режима "Без главной страницы, направлять сразу на форум"
			if (!empty($modSettings['optimus_meta']) && empty($modSettings['pmx_frontmode'])) {
				$meta = '';
				$test = @unserialize($modSettings['optimus_meta']);

				foreach ($test as $var) {
					$meta .= "\n\t" . '<meta name="' . $var['name'] . '" content="' . $var['content'] . '" />';
				}

				$context['html_headers'] .= $meta;
			}
		}
	}
}

// integrate_menu_buttons hook
function optimus_operations()
{
	global $modSettings, $context, $mbname, $boardurl, $scripturl, $smcFunc;

	// Последний пункт в хлебных крошках не будем делать ссылкой
	if (!empty($modSettings['optimus_remove_last_bc_item']) && !defined('WIRELESS')) {
		$linktree = count($context['linktree']);
		unset($context['linktree'][$linktree - 1]['url']);
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

	// Description
	if (empty($context['current_action']) && !empty($modSettings['optimus_description'])) {
		if (empty($_REQUEST['topic']) && empty($_REQUEST['board']))
			$context['optimus_description'] = $smcFunc['htmlspecialchars']($modSettings['optimus_description']);
	}

	get_optimus_page_templates();
	get_optimus_aeva_media();
	get_optimus_http_status();

	// Copyright Info
	if ($context['current_action'] == 'credits')
		$context['copyrights']['mods'][] = '<a href="//dragomano.ru/mods/optimus" target="_blank">Optimus</a> &copy; 2010&ndash;' . date('Y') . ', Bugo';
}

// Обрабатываем шаблоны заголовков страниц
function get_optimus_page_templates()
{
	global $modSettings, $txt, $context, $board_info, $smcFunc;

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
				get_optimus_description();
		}

		get_optimus_og_image();
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

// Создаем описание страницы из первого сообщения
function get_optimus_description()
{
	global $smcFunc, $context;

	if (empty($context['first_message']))
		return;

	$request = $smcFunc['db_query']('substring', '
		SELECT SUBSTRING(body, 1, 200) AS body, smileys_enabled, id_msg
		FROM {db_prefix}messages
		WHERE id_msg = {int:id_msg}
		LIMIT 1',
		array(
			'id_msg' => $context['first_message']
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))	{
		censorText($row['body']);

		$row['body'] = strip_tags(strtr(parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']), array('<br />' => '&#10;')));
		if ($smcFunc['strlen']($row['body']) > 160)
			$row['body'] = $smcFunc['substr']($row['body'], 0, 157) . '...';

		$context['optimus_description'] = $row['body'];
	}

	$smcFunc['db_free_result']($request);
}

// Достаем URL вложения из первого сообщения темы
function get_optimus_og_image()
{
	global $context, $smcFunc, $scripturl;

	// Кэшируем запрос
	if (($context['optimus_og_image'] = cache_get_data('og_image_' . $context['current_topic'], 3600)) == null) {
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

		$context['optimus_og_image'] = '';
		while ($row = $smcFunc['db_fetch_assoc']($request))	{
			if ($row['id'] != 0) {
				$context['optimus_og_image'] = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . ';attach=' . $row['id'] . ';image';
			}
		}

		$smcFunc['db_free_result']($request);

		cache_put_data('og_image_' . $context['current_topic'], $context['optimus_og_image'], 3600);
	}
}

// Set canonical URLs and descriptions for AM pages
function get_optimus_aeva_media()
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
function get_optimus_http_status()
{
	global $modSettings, $board_info, $context, $txt;

	if (empty($modSettings['optimus_404_status']) || empty($board_info['error']))
		return;

	// Страница не существует?
	if ($board_info['error'] == 'exist') {
		header('HTTP/1.1 404 Not Found');

		loadTemplate('Optimus');

		$context['sub_template'] = '404';
		$context['page_title']   = $txt['optimus_404_page_title'];
	}

	// Нет доступа?
	if ($board_info['error'] == 'access') {
		header('HTTP/1.1 403 Forbidden');

		loadTemplate('Optimus');

		$context['sub_template'] = '403';
		$context['page_title']   = $txt['optimus_403_page_title'];
	}
}

// integrate_buffer hook
function optimus_buffer($buffer)
{
	global $context, $modSettings, $mbname, $scripturl, $boardurl, $forum_copyright, $boarddir, $txt;

	if (isset($_REQUEST['xml']) || $context['current_action'] == 'printpage')
		return $buffer;

	$replacements = array();

	if (isset($context['canonical_url'])) {
		// Description
		if (!empty($context['optimus_description'])) {
			$desc_old = '<meta name="description" content="' . $context['page_title_html_safe'] . '" />';
			$desc_new = '<meta name="description" content="' . $context['optimus_description'] . '" />';
			$replacements[$desc_old] = $desc_new;
		}

		// Verification tags
		if (!empty($modSettings['optimus_meta']) && $modSettings['optimus_portal_compat'] != 1) {
			$meta = '';
			$test = @unserialize($modSettings['optimus_meta']);

			foreach ($test as $var) {
				$meta .= "\n\t" . '<meta name="' . $var['name'] . '" content="' . $var['content'] . '" />';
			}

			$charset_meta = '<meta http-equiv="Content-Type" content="text/html; charset=' . $context['character_set'] . '" />';
			$check_meta = $charset_meta . $meta;
			$replacements[$charset_meta] = $check_meta;
		}
	}

	// Correct prev/next links ~ http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
	if (!empty($modSettings['optimus_correct_prevnext'])) {
		if (!empty($_REQUEST['topic']) && isset($context['start']) && !empty($context['page_info']['num_pages'])) {
			$prev = '<link rel="prev" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.0;prev_next=prev" />' . "\n\t";
			$next = '<link rel="next" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.0;prev_next=next" />' . "\n\t";
			$new_prev = '<link rel="prev" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.' . ($context['start'] - $modSettings['defaultMaxMessages']) . '" />' . "\n\t";
			$new_next = '<link rel="next" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.' . ($context['start'] + $modSettings['defaultMaxMessages']) . '" />' . "\n\t";

			if ($context['page_info']['num_pages'] > 1)	{
				if ($context['page_info']['current_page'] == 1) {
					$replacements[$prev] = '';
					$replacements[$next] = $new_next;
				}

				if ($context['page_info']['current_page'] == $context['page_info']['num_pages']) {
					$replacements[$prev] = $new_prev;
					$replacements[$next] = '';
				}

				if ($context['page_info']['current_page'] < $context['page_info']['num_pages'] && $context['page_info']['current_page'] > 1) {
					$replacements[$prev] = $new_prev;
					$replacements[$next] = $new_next;
				}
			} else
				$replacements[$prev] = $replacements[$next] = '';
		}
	}

	// Open Graph for forum pages
	if (!empty($modSettings['optimus_open_graph']) && isset($context['canonical_url'])) {
		$xmlns = 'html xmlns="http://www.w3.org/1999/xhtml"';
		$new_xmlns = $xmlns . ' xmlns:og="http://ogp.me/ns#"';
		$replacements[$xmlns] = $new_xmlns;

		$open_graph = '<meta property="og:title" content="' . $context['page_title_html_safe'] . '" />';

		if (!empty($context['optimus_og_type'])) {
			$type = key($context['optimus_og_type']);
			$open_graph .= '
	<meta property="og:type" content="' . $type . '" />';

			foreach ($context['optimus_og_type'][$type] as $t_key => $t_value) {
				$open_graph .= '
	<meta property="' . $type . ':' . $t_key . '" content="' . $t_value . '" />';
			}
		} else
			$open_graph .= '
	<meta property="og:type" content="website" />';

		$open_graph .= '
	<meta property="og:url" content="' . $context['canonical_url'] . '" />';

		if (!empty($modSettings['optimus_og_image'])) {
			$open_graph .= '
	<meta property="og:image" content="' . (!empty($context['optimus_og_image']) ? $context['optimus_og_image'] : $modSettings['optimus_og_image']) . '" />';
		}

		$open_graph .= '
	<meta property="og:description" content="' . (!empty($context['optimus_description']) ? $context['optimus_description'] : $context['page_title_html_safe']) . '" />
	<meta property="og:site_name" content="' . $mbname . '" />';

		$head = '<title>' . $context['page_title_html_safe'] . '</title>';
		$new_head = $open_graph . "\n\t" . $head;
		$replacements[$head] = $new_head;
	}

	// Open Graph for media pages
	if ($context['current_action'] == 'media' && !empty($_REQUEST['sa']) && !empty($_REQUEST['in'])) {
		if ($_REQUEST['sa'] == 'item') {
			$item = (int) $_REQUEST['in'];

			if (function_exists('aeva_getItemData')) {
				$handler = new aeva_media_handler;
				$exif = @unserialize($context['item_data']['exif']);

				if ($context['item_data']['type'] == 'video') {
					$xmlns = 'html xmlns="http://www.w3.org/1999/xhtml"';
					$new_xmlns = $xmlns . ' xmlns:og="http://ogp.me/ns#"';
					$replacements[$xmlns] = $new_xmlns;

					$duration = $exif['duration'];

					$context['ogp_meta'] = '<meta property="og:title" content="' . $context['item_data']['title'] . '" />
	<meta property="og:url" content="' . $scripturl . '?action=media;sa=item;in=' . $item . '" />
	<meta property="og:image" content="' . $scripturl . '?action=media;sa=media;in=' . $item . ';thumb" />';

					if (!empty($context['item_data']['description']))
						$context['ogp_meta'] .= '
	<meta property="og:description" content="' . html_entity_decode($context['item_data']['description'], ENT_QUOTES, $context['character_set']) . '" />';

					$context['ogp_meta'] .= '
	<meta property="og:video" content="' . $boardurl . '/MGalleryItem.php?id=' . $item . '" />
	<meta property="og:video:height" content="' . $context['item_data']['height'] . '" />
	<meta property="og:video:width" content="' . $context['item_data']['width'] . '" />
	<meta property="og:video:type" content="' . $handler->getMimeFromExt($context['item_data']['filename']) . '" />
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

// Обработка дат
function get_optimus_sitemap_date($timestamp = '')
{
	$timestamp = empty($timestamp) ? time() : $timestamp;
	$gmt       = substr(date("O", $timestamp), 0, 3) . ':00';
	$result    = date('Y-m-d\TH:i:s', $timestamp) . $gmt;

	return $result;
}

// Создаем файл карты
function create_optimus_file($path, $data)
{
	if (!$fp = @fopen($path, 'w'))
		return false;

	flock($fp, LOCK_EX);
	fwrite($fp, $data);
	flock($fp, LOCK_UN);
	fclose($fp);

	return true;
}

// Если размер файла превышает 10 МБ (ограничение Яндекса), отправляем запись в Журнал ошибок
function check_optimus_filesize($file)
{
	global $txt;

	clearstatcache();

	if (filesize($file) > (10*1024*1024))
		log_error(sprintf($txt['optimus_sitemap_size_limit'], @pathinfo($file, PATHINFO_BASENAME)) . $txt['optimus_sitemap_rec'], 'general');

	return;
}

// Определяем приоритет индексирования
function get_optimus_sitemap_priority($time)
{
	$diff = floor((time() - $time)/60/60/24);
	
	if ($diff <= 30)
		return '0.8';
	elseif ($diff <= 60)
		return '0.6';
	elseif ($diff <= 90)
		return '0.4';
	else
		return '0.2';
}

// Определяем периодичность обновлений
function get_optimus_sitemap_frequency($time)
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

// Генерация карты форума
function optimus_sitemap()
{
	global $modSettings, $sourcedir, $smcFunc, $boardurl, $scripturl, $context, $boarddir;

	// Прежде всего проверяем, активировано ли создание карты форума (в настройках)
	if (empty($modSettings['optimus_sitemap_enable']))
		return;

	$t            = "\t";
	$n            = "\n";
	$sef          = false;
	$mobile_type  = 'wap2'; // wap, wap2, imode
	$mobile       = '';
	$header       = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n;
	$xmlns        = 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
	$tab          = $xmlns . $n . $t . $t;
	$xmlns_mobile = $tab . 'xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0"';
	$xmlns_image  = $tab . 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
	$xmlns_video  = $tab . 'xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"';

/*	http://makarou.com/sozdanie-xml-dokumenta-sredstvami-php5
	$xml = new DomDocument('1.0','utf-8');	
	$xml->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

	$urlset = $xml->appendChild($xml->createElement('urlset'));
	$url    = $urlset->appendChild($xml->createElement('url'));
	$loc    = $url->appendChild($xml->createElement('loc'));

	$loc->appendChild($xml->createTextNode($scripturl . '?action=bbs'));

	$xml->formatOutput = true;
	$xml->save('sitemap-test.xml');*/

	clearstatcache();

	// SimpleSEF enabled?
	$sef = !empty($modSettings['simplesef_enable']) && file_exists($sourcedir . '/SimpleSEF.php');
	if ($sef) {
		function create_sefurl($new_url)
		{
			global $sourcedir;

			require_once($sourcedir . '/SimpleSEF.php');
			$simple = new SimpleSEF;

			return $simple->create_sef_url($new_url);
		}
	}

	// PortaMx SEF enabled?
	if (file_exists($sourcedir . '/PortaMx/PortaMxSEF.php') && function_exists('create_sefurl'))
		$sef = true;

	$url   = array();
	$first = array();
	$sec   = array();

	// Главную страницу грех не добавить :)
	if (empty($modSettings['optimus_sitemap_boards'])) {
		$first[] = $url[] = array(
			'loc'      => $boardurl . '/',
			'wap'      => $scripturl . '/?' . $mobile_type,
			'priority' => '1.0'
		);
	}

	// Boards
	if (!empty($modSettings['optimus_sitemap_boards'])) {
		$request = $smcFunc['db_query']('', '
			SELECT b.id_board, m.poster_time, m.modified_time
			FROM {db_prefix}boards AS b
				LEFT JOIN {db_prefix}messages AS m ON (m.id_msg = b.id_last_msg)
			WHERE FIND_IN_SET(-1, b.member_groups) != 0' . (!empty($modSettings['recycle_board']) ? ' AND b.id_board <> {int:recycle_board}' : '') . (!empty($modSettings['optimus_sitemap_topics']) ? ' AND b.num_posts > {int:posts}' : '') . '
			ORDER BY b.id_board',
			array(
				'recycle_board' => !empty($modSettings['recycle_board']) ? (int) $modSettings['recycle_board'] : 0,
				'posts'         => !empty($modSettings['optimus_sitemap_topics']) ? (int) $modSettings['optimus_sitemap_topics'] : 0
			)
		);

		$boards = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$boards[] = $row;

		$smcFunc['db_free_result']($request);

		$last = array(0);

		// А вот насчет разделов можно и подумать...
		if (!empty($boards)) {
			foreach ($boards as $entry)	{
				$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];

				// Поддержка мода BoardNoIndex
				if (!empty($modSettings['BoardNoIndex_enabled'])) {
					if (!in_array($entry['id_board'], @unserialize($modSettings['BoardNoIndex_select_boards']))) {
						$first[] = $url[] = array(
							'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
							'wap'        => $scripturl . '?board=' . $entry['id_board'] . '.0;' . $mobile_type,
							'lastmod'    => get_optimus_sitemap_date($last_edit),
							'changefreq' => get_optimus_sitemap_frequency($last_edit),
							'priority'   => get_optimus_sitemap_priority($last_edit)
						);
					}
				} else {
					$first[] = $url[] = array(
						'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
						'wap'        => $scripturl . '?board=' . $entry['id_board'] . '.0;' . $mobile_type,
						'lastmod'    => get_optimus_sitemap_date($last_edit),
						'changefreq' => get_optimus_sitemap_frequency($last_edit),
						'priority'   => get_optimus_sitemap_priority($last_edit)
					);
				}

				$last[] = empty($entry['modified_time']) ? (empty($entry['poster_time']) ? '' : $entry['poster_time']) : $entry['modified_time'];
			}

			$home_last_edit = max($last);
			$home = array(
				'loc'        => $boardurl . '/',
				'wap'        => $scripturl . '/?' . $mobile_type,
				'lastmod'    => get_optimus_sitemap_date($home_last_edit),
				'changefreq' => get_optimus_sitemap_frequency($home_last_edit),
				'priority'   => '1.0'
			);			
			array_unshift($url, $home);
			array_unshift($first, $home);
		}
	}

	$main = '';		
	foreach ($first as $entry) {
		$main .= $t . '<url>' . $n;
		$main .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;

		if (!empty($entry['wap'])) {
			$mobile .= $t . '<url>' . $n;
			$mobile .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['wap']) : $entry['wap']) . '</loc>' . $n;

			if (!empty($entry['lastmod']))
				$mobile .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;
			
			$mobile .= $t . $t . '<mobile:mobile/>' . $n;
			$mobile .= $t . '</url>' . $n;
		}

		if (!empty($entry['lastmod']))
			$main .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;

		if (!empty($entry['changefreq']))
			$main .= $t . $t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $n;

		if (!empty($entry['priority']))
			$main .= $t . $t . '<priority>' . $entry['priority'] . '</priority>' . $n;
		
		$main .= $t . '</url>' . $n;
	}

	// Topics
	$request = $smcFunc['db_query']('', '
		SELECT date_format(FROM_UNIXTIME(m.poster_time), "%Y") AS date, t.id_topic, m.poster_time, m.modified_time
		FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
		WHERE FIND_IN_SET(-1, b.member_groups) != 0' . (!empty($modSettings['recycle_board']) ? ' AND b.id_board <> {int:recycle_board}' : '') . (!empty($modSettings['optimus_sitemap_topics']) ? ' AND t.num_replies > {int:replies}' : '') . ' AND t.approved = 1
		ORDER BY t.id_topic',
		array(
			'recycle_board' => !empty($modSettings['recycle_board']) ? (int) $modSettings['recycle_board'] : 0,
			'replies'       => !empty($modSettings['optimus_sitemap_topics']) ? (int) $modSettings['optimus_sitemap_topics'] : 0
		)
	);
	
	$topics = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$topics[$row['date']][$row['id_topic']] = $row;

	$smcFunc['db_free_result']($request);

	$years = $files = array();
	foreach ($topics as $year => $data) {
		foreach ($data as $topic => $entry) {
			$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];

			$years[count($topics[$year])] = $year;

			// Поддержка мода BoardNoIndex
			if (!empty($modSettings['BoardNoIndex_enabled'])) {
				if (!in_array($entry['id_board'], @unserialize($modSettings['BoardNoIndex_select_boards']))) {
					$sec[$year][] = $url[] = array(
						'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $scripturl . '?topic=' . $entry['id_topic'] . '.0',
						'wap'        => $scripturl . '?topic=' . $entry['id_topic'] . '.0;' . $mobile_type,
						'lastmod'    => get_optimus_sitemap_date($last_edit),
						'changefreq' => get_optimus_sitemap_frequency($last_edit),
						'priority'   => get_optimus_sitemap_priority($last_edit)
					);
				}
			} else {
				$sec[$year][] = $url[] = array(
					'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $scripturl . '?topic=' . $entry['id_topic'] . '.0',
					'wap'        => $scripturl . '?topic=' . $entry['id_topic'] . '.0;' . $mobile_type,
					'lastmod'    => get_optimus_sitemap_date($last_edit),
					'changefreq' => get_optimus_sitemap_frequency($last_edit),
					'priority'   => get_optimus_sitemap_priority($last_edit)
				);
			}
		}

		$files[] = $year;
		$out[$year] = '';		
		foreach ($sec[$year] as $entry) {
			$out[$year] .= $t . '<url>' . $n;
			$out[$year] .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;

			if (!empty($entry['wap'])) {
				$mobile .= $t . '<url>' . $n;
				$mobile .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['wap']) : $entry['wap']) . '</loc>' . $n;
			
				if (!empty($entry['lastmod']))
					$mobile .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;
				
				$mobile .= $t . $t . '<mobile:mobile/>' . $n;
				$mobile .= $t . '</url>' . $n;
			}

			if (!empty($entry['lastmod']))
				$out[$year] .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;

			if (!empty($entry['changefreq']))
				$out[$year] .= $t . $t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $n;

			if (!empty($entry['priority']))
				$out[$year] .= $t . $t . '<priority>' . $entry['priority'] . '</priority>' . $n;
			
			$out[$year] .= $t . '</url>' . $n;
		}
	}

	// Simple Classifieds
	if (!empty($modSettings['optimus_sitemap_classifieds'])) {
		$request = $smcFunc['db_query']('', '
			SELECT id, date, last_edit
			FROM {db_prefix}bbs_items
			WHERE status = {int:approved}
			ORDER BY date',
			array(
				'approved' => 1
			)
		);

		$bbs = array();
		$bbs[] = $url[] = array(
			'loc'      => $scripturl . '?action=bbs',
			'priority' => '1.0'
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$last_edit = empty($row['last_edit']) ? $row['date'] : $row['last_edit'];

			$bbs[] = $url[] = array(
				'loc'        => $scripturl . '?action=bbs;sa=item;id=' . $row['id'],
				'lastmod'    => get_optimus_sitemap_date($last_edit),
				'changefreq' => get_optimus_sitemap_frequency($last_edit),
				'priority'   => get_optimus_sitemap_priority($last_edit)
			);
		}

		$smcFunc['db_free_result']($request);

		$bbs_map = '';
		foreach ($bbs as $entry) {
			$bbs_map .= $t . '<url>' . $n;
			$bbs_map .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;

			if (!empty($entry['lastmod']))
				$bbs_map .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;

			if (!empty($entry['changefreq']))
				$bbs_map .= $t . $t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $n;

			if (!empty($entry['priority']))
				$bbs_map .= $t . $t . '<priority>' . $entry['priority'] . '</priority>' . $n;
			
			$bbs_map .= $t . '</url>' . $n;
		}
	}

	$media = array();

	// Aeva Media
	if (!empty($modSettings['optimus_sitemap_aeva']) && file_exists($sourcedir . '/Aeva-Subs.php'))	{
		$query_one = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}aeva_media'", array());
		$query_two = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}aeva_albums'", array());
		$result    = $smcFunc['db_num_rows']($query_one) != 0 && $smcFunc['db_num_rows']($query_two) != 0;

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
					'name'    => $entry['name']
				);
			}
		}
	}

	// SMF Gallery mod
	if (!empty($modSettings['optimus_sitemap_gallery']) && file_exists($sourcedir . '/Gallery2.php')) {
		$query_one = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}gallery_cat'", array());
		$query_two = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}gallery_pic'", array());
		$result    = $smcFunc['db_num_rows']($query_one) != 0 && $smcFunc['db_num_rows']($query_two) != 0;

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
					'caption' => $entry['title']
				);
			}
		}
	}

	// Адреса для карты изображений
	if (!empty($media) && (!empty($modSettings['optimus_sitemap_aeva']) || !empty($modSettings['optimus_sitemap_gallery']))) {
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
	}

	// Адреса для карты видеороликов
	if (!empty($media) && !empty($modSettings['optimus_sitemap_aeva'])) {
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
	}

	// Обработаем все ссылки
	$one_file = '';
	foreach ($url as $entry) {
		$one_file .= $t . '<url>' . $n;
		$one_file .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;

		if (!empty($entry['lastmod']))
			$one_file .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;

		if (!empty($entry['changefreq']))
			$one_file .= $t . $t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $n;

		if (!empty($entry['priority']))
			$one_file .= $t . $t . '<priority>' . $entry['priority'] . '</priority>' . $n;
		
		$one_file .= $t . '</url>' . $n;
	}

	// Pretty URLs installed?
	$pretty = $sourcedir . '/PrettyUrls-Filters.php';
	if (file_exists($pretty) && !empty($modSettings['pretty_enable_filters'])) {
		require_once($pretty);
		
		$context['pretty']['search_patterns'][]  = '~(<loc>)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';
		$context['pretty']['search_patterns'][]  = '~(<video:thumbnail_loc>)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(<video:thumbnail_loc>)([^<]+)~';
		$context['pretty']['search_patterns'][]  = '~(">)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(">)([^<]+)~';

		$main = pretty_rewrite_buffer($main);
		
		foreach ($files as $year)
			$out[$year] = pretty_rewrite_buffer($out[$year]);

		$one_file = pretty_rewrite_buffer($one_file);
		
		if (!empty($mobile))
			$mobile = pretty_rewrite_buffer($mobile);
		
		if (!empty($images))
			$images = pretty_rewrite_buffer($images);
		
		if (!empty($videos))
			$videos = pretty_rewrite_buffer($videos);
	}

	// Создаем карту сайта (если ссылок больше 10к, то сделаем файл индекса)
	if (count($url) > 10000) {
		$main = $header . '<urlset ' . $xmlns . '>' . $n . $main . '</urlset>';
		$sitemap = $boarddir . '/sitemap_main.xml';
		create_optimus_file($sitemap, $main);

		foreach ($files as $year) {
			$out[$year] = $header . '<urlset ' . $xmlns . '>' . $n . $out[$year] . '</urlset>';
			$sitemap = $boarddir . '/sitemap_' . $year . '.xml';
			create_optimus_file($sitemap, $out[$year]);
			check_optimus_filesize($sitemap);
		}

		// Отдельный файл для объявлений
		if (!empty($bbs_map)) {
			$bbs_map = $header . '<urlset ' . $xmlns . '>' . $n . $bbs_map . '</urlset>';
			$sitemap = $boarddir . '/sitemap_classifieds.xml';
			create_optimus_file($sitemap, $bbs_map);
		}

		// Создаем файл индекса Sitemap
		$maps = '';

		if (!empty($main)) {
			$maps .= $t . '<sitemap>' . $n;
			$maps .= $t . $t . '<loc>' . $boardurl . '/sitemap_main.xml</loc>' . $n;
			$maps .= $t . $t . '<lastmod>' . get_optimus_sitemap_date() . '</lastmod>' . $n;
			$maps .= $t . '</sitemap>' . $n;
		}

		foreach ($files as $year) {
			$maps .= $t . '<sitemap>' . $n;
			$maps .= $t . $t . '<loc>' . $boardurl . '/sitemap_' . $year . '.xml</loc>' . $n;
			$maps .= $t . $t . '<lastmod>' . get_optimus_sitemap_date() . '</lastmod>' . $n;
			$maps .= $t . '</sitemap>' . $n;
		}
		
		$index_data = $header . '<sitemapindex ' . $xmlns . '>' . $n . $maps . '</sitemapindex>';
		$index_file = $boarddir . '/sitemap.xml';	
		create_optimus_file($index_file, $index_data);
	} else {
		$one_file = $header . '<urlset ' . $xmlns . '>' . $n . $one_file . '</urlset>';
		$sitemap = $boarddir . '/sitemap.xml';
		create_optimus_file($sitemap, $one_file);
	}

	// Карта для мобилок
	if (!empty($mobile_type) && !empty($modSettings['optimus_sitemap_mobile'])) {
		$xml_data = $header . '<urlset ' . $xmlns_mobile . '>' . $n . $mobile . '</urlset>';
		create_optimus_file($boarddir . '/sitemap_mobile.xml', $xml_data);
		check_optimus_filesize($boarddir . '/sitemap_mobile.xml');
	}

	// Карта ссылок на изображения
	if (!empty($images) && (!empty($modSettings['optimus_sitemap_gallery']) || !empty($modSettings['optimus_sitemap_aeva']))) {
		$xml_data = $header . '<urlset ' . $xmlns_image . '>' . $n . $images . '</urlset>';
		create_optimus_file($boarddir . '/sitemap_images.xml', $xml_data);
	}

	// Карта ссылок на видеоролики
	if (!empty($videos) && !empty($modSettings['optimus_sitemap_aeva'])) {
		$xml_data = $header . '<urlset ' . $xmlns_video . '>' . $n . $videos . '</urlset>';
		create_optimus_file($boarddir . '/sitemap_videos.xml', $xml_data);
	}

	return true;
}
