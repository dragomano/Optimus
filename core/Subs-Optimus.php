<?php
/**************************************************************
* Optimus Brave © 2010-2013, Bugo
***************************************************************
* Subs-Optimus.php
***************************************************************
* License http://opensource.org/licenses/artistic-license-2.0
* Support and updates for this software can be found at
* http://dragomano.ru/page/optimus-brave and
* http://custom.simplemachines.org/mods/index.php?mod=2659
**************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');
	
define('OB', '1.8.7');

function optimus_home()
{
	global $modSettings, $context, $mbname, $txt;

	loadLanguage('Optimus');

	if (!isset($modSettings['optimus_portal_compat']))
		$modSettings['optimus_portal_compat'] = 0;

	if (!empty($modSettings['optimus_portal_compat']))
		if (empty($context['current_board']) && empty($context['current_topic']) && empty($_REQUEST['action']) && !empty($modSettings['optimus_portal_index']))
		{
			$context['forum_name'] = $mbname . ' ' . $modSettings['optimus_portal_index'];
			if ($modSettings['optimus_portal_compat'] == 2) {
				$context['forum_name'] = $mbname;
				$txt['home'] = $modSettings['optimus_portal_index'];
			}
		}

	// Forum
	$txt['forum_index'] = '%1$s';
	if (!empty($modSettings['optimus_forum_index']))
		$txt['forum_index'] = '%1$s - ' . $modSettings['optimus_forum_index'];
		
	$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();

	if (!in_array($context['current_action'], $ignored_actions))
	{
		// Invisible counters like Google
		if (!empty($modSettings['optimus_head_code']))
		{
			$head = explode("\n", trim($modSettings['optimus_head_code']));
			foreach ($head as $part) $context['html_headers'] .= "\n\t" . $part;
		}
		
		// Other invisible counters
		if (!empty($modSettings['optimus_stat_code']))
		{
			$stat = explode("\n", trim($modSettings['optimus_stat_code']));
			foreach ($stat as $part) $context['insert_after_template'] .= "\n\t" . $part;
		}

		// Styles for visible counters
		if (!empty($modSettings['optimus_count_code']) && !empty($modSettings['optimus_count_code_css']))
			$context['html_headers'] .= "\n\t" . '<style type="text/css">' . $modSettings['optimus_count_code_css'] . '</style>';
	}
	
	// Special fix for PortaMx
	if ($modSettings['optimus_portal_compat'] == 4)
	{
		if (empty($_REQUEST['action']) && empty($_REQUEST['board']) && empty($_REQUEST['topic']))
		{
			if (!empty($modSettings['optimus_meta']) && empty($modSettings['pmx_frontmode']))
			{
				$meta = '';
				$test = @unserialize($modSettings['optimus_meta']);
				foreach ($test as $var)
					$meta .= "\n\t" . '<meta name="' . $var['name'] . '" content="' . $var['content'] . '" />';
				$context['html_headers'] .= $meta;
			}
		}
	}
}

function optimus_operations()
{
	global $context, $mbname, $boardurl, $modSettings, $smcFunc, $txt, $scripturl, $topicinfo, $board_info;
	
	if (empty($context['current_board']) && empty($context['current_topic']) && empty($_REQUEST['action'])) {
		$context['linktree'][0]['name'] = $mbname;
		$context['canonical_url'] = $boardurl . '/';
	}

	if (!empty($modSettings['optimus_portal_compat']) && in_array($context['current_action'], array('forum', 'community')))
		$context['canonical_url'] = $scripturl . '?action=' . $context['current_action'];

	// Description
	if (empty($context['current_action']))
		$context['optimus_description'] = !empty($modSettings['optimus_description']) ? $smcFunc['htmlspecialchars']($modSettings['optimus_description']) : '';

	// Обрабатываем шаблоны заголовков страниц
	if (!empty($modSettings['optimus_templates']) && strpos($modSettings['optimus_templates'], 'board') && strpos($modSettings['optimus_templates'], 'topic'))
	{
		$templates = @unserialize($modSettings['optimus_templates']);
		foreach ($templates as $name => $data)
		{
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
	else
	{
		foreach ($txt['optimus_templates'] as $name => $data)
		{
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
	if ($context['current_action'] != 'wiki')
	{
		if (!empty($context['page_info']['current_page']) && $context['page_info']['num_pages'] != 1)
		{
			$trans = array("{#}" => $context['page_info']['current_page']);
			$board_page_number = strtr($board_page_tpl, $trans);
			$topic_page_number = strtr($topic_page_tpl, $trans);
		}
	}

	// Topics
	if (!empty($context['topic_first_message']))
	{
		$trans = array(
			"{topic_name}" => $topicinfo['subject'],
			"{board_name}" => strip_tags($board_info['name']),
			"{cat_name}" => $board_info['cat']['name'],
			"{forum_name}" => $context['forum_name']
		);
		$topic_page_number = !empty($topic_page_number) ? $topic_page_number : (!empty($topic_site_tpl) ? ' - ' : '');
		$context['page_title'] = strtr($topic_name_tpl . $topic_page_number . $topic_site_tpl, $trans);
		$context['optimus_description'] = !empty($modSettings['optimus_topic_description']) ? optimus_meta_teaser() : '';
	}

	// Boards
	if (!empty($board_info['total_topics']))
	{
		$trans = array(
			"{board_name}" => strip_tags($context['name']),
			"{cat_name}" => $board_info['cat']['name'],
			"{forum_name}" => $context['forum_name']
		);
		$board_page_number = !empty($board_page_number) ? $board_page_number : (!empty($board_site_tpl) ? ' - ' : '');
		$context['page_title'] = strtr($board_name_tpl . $board_page_number . $board_site_tpl, $trans);
		if (!empty($modSettings['optimus_board_description']))
			$context['optimus_description'] = optimus_meta_chars(!empty($context['description']) ? $context['description'] : $context['name']);
	}

	// Set canonical URLs and descriptions for AM pages
	if ($context['current_action'] == 'media' && !empty($_REQUEST['sa']) && !empty($_REQUEST['in']))
	{
		$item = (int) $_REQUEST['in'];
		
		if ($_REQUEST['sa'] == 'album')
			$context['canonical_url'] = $scripturl . '?action=media;sa=album;in=' . $item;
		if ($_REQUEST['sa'] == 'item')
			$context['canonical_url'] = $scripturl . '?action=media;sa=item;in=' . $item;
		
		$context['optimus_description'] = !empty($context['item_data']['description']) ? html_entity_decode($context['item_data']['description'], ENT_QUOTES, $context['character_set']) : '';
	}
	
	// Возвращаемые коды состояния, в зависимости от ситуации
	if (!empty($modSettings['optimus_404_status']))
	{
		if (!empty($board_info['error']))
		{
			if ($board_info['error'] == 'exist') // Страница не существует?
			{
				header('HTTP/1.1 404 Not Found');
				loadTemplate('Optimus');
				$context['sub_template'] = '404';
				$context['page_title'] = $txt['optimus_404_page_title'];
			}
			
			if ($board_info['error'] == 'access') // Нет доступа?
			{
				header('HTTP/1.1 403 Forbidden');
				loadTemplate('Optimus');
				$context['sub_template'] = '403';
				$context['page_title'] = $txt['optimus_403_page_title'];
			}
		}
	}
	//if (empty($_REQUEST['action']) || !empty($_REQUEST['board']) || !empty($_REQUEST['topic']))

	// Copyright Info
	if ($context['current_action'] == 'credits')
		$context['copyrights']['mods'][] = '<a href="http://dragomano.ru/page/optimus-brave" target="_blank" title="' . OB . '">Optimus Brave</a> &copy; 2010&ndash;' . date('Y') . ', Bugo';
}

// Убираем двойные кавычки из описания, а также любые теги
function optimus_meta_chars($text)
{
	$result = $text;

	if (strpos($text, '"') !== false)
		$result = str_replace('"', '', strip_tags(un_htmlspecialchars($text)));

	return $result;
}

// Выборка фразы из первого сообщения каждой темы
function optimus_meta_teaser()
{
	global $context, $txt, $smcFunc;
	
	// Если в теме есть опрос, то выводим вопрос в качестве описания
	if ($context['is_poll'])
	{
		$teaser = $txt['poll'] . ': ' . $context['poll']['question'] . ' (' . $context['page_info']['current_page'] . ')';
	}
	else
	{
		$request = $smcFunc['db_query']('', '
			SELECT SUBSTRING(body, 1, 200)
			FROM {db_prefix}messages
			WHERE id_msg = {int:id_msg}
			LIMIT 1',
			array(
				'id_msg' => $context['first_message']
			)
		);

		list ($teaser) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
	}
	
	$teaser = optimus_meta_chars(str_replace('<br />', ' ', parse_bbc($teaser, false)));
	
	// Если длина первого сообщения меньше 150 символов, оно целиком попадает в description
	if ($smcFunc['strlen']($teaser) < 150) return strip_tags($teaser);

	// Иначе отсекаем первые 150 символов
	$teaser = $smcFunc['substr']($teaser, 0, 150);
	
	$teaser = str_replace("&nbsp;", "", $teaser);
	
	// Затем убираем всё до первого отступа
	$tmp1 = $smcFunc['substr']($teaser, 0, $smcFunc['strpos']($teaser, '  '));
	
	// Или до первой точки
	$tmp2 = $smcFunc['substr']($teaser, 0, $smcFunc['strpos']($teaser, '.'));
	
	// Или же до первого восклицательного знака
	$tmp3 = $smcFunc['substr']($teaser, 0, $smcFunc['strpos']($teaser, '!'));
	
	// Если ни одно из трёх условий выше не подходит, то просто выводим первые 150 символов, с удалением всех пробелов справа
	$result = !empty($tmp1) ? $tmp1 : (!empty($tmp2) ? $tmp2 : (!empty($tmp3) ? $tmp3 : rtrim($teaser)));

	return strip_tags($result);
}

// Здесь у нас различные замены в буфере
function optimus_buffer(&$buffer)
{
	global $modSettings, $context, $txt, $scripturl, $boardurl, $sourcedir, $forum_copyright, $boarddir;
	
	if (isset($_REQUEST['xml']) || $context['current_action'] == 'printpage') return $buffer;
	
	$replacements = array();

	if (empty($_REQUEST['action']) || !empty($_REQUEST['board']) || !empty($_REQUEST['topic']) || $context['current_action'] == 'media')
	{
		// Description
		if (!empty($context['optimus_description']))
		{
			$desc_old = '<meta name="description" content="' . $context['page_title_html_safe'] . '" />';
			$desc_new = '<meta name="description" content="' . $context['optimus_description'] . '" />';
			$replacements[$desc_old] = $desc_new;
		}
		
		// Verification tags
		if (!empty($modSettings['optimus_meta']) && $modSettings['optimus_portal_compat'] != 4)
		{
			$meta = '';
			$test = @unserialize($modSettings['optimus_meta']);
			foreach ($test as $var)
				$meta .= "\n\t" . '<meta name="' . $var['name'] . '" content="' . $var['content'] . '" />';
			$charset_meta = '<meta http-equiv="Content-Type" content="text/html; charset=' . $context['character_set'] . '" />';
			$check_meta = $charset_meta . $meta;
			$replacements[$charset_meta] = $check_meta;
		}
	}
	
	// Prev/next links ~ http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
	if (!empty($_REQUEST['topic']) && isset($context['start']) && !empty($context['page_info']['num_pages']))
	{
		$prev = '<link rel="prev" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.0;prev_next=prev" />' . "\n\t";
		$next = '<link rel="next" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.0;prev_next=next" />' . "\n\t";
		$new_prev = '<link rel="prev" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.' . ($context['start'] - $modSettings['defaultMaxMessages']) . '" />' . "\n\t";
		$new_next = '<link rel="next" href="' . $scripturl . '?topic=' . $context['current_topic'] . '.' . ($context['start'] + $modSettings['defaultMaxMessages']) . '" />' . "\n\t";
		
		if ($context['page_info']['num_pages'] > 1)
		{
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
		}
		else
			$replacements[$prev] = $replacements[$next] = '';
	}
	
	// The Open Graph Protocol for media pages
	if ($context['current_action'] == 'media' && !empty($_REQUEST['sa']) && !empty($_REQUEST['in']))
	{
		if ($_REQUEST['sa'] == 'item')
		{
			$item = (int) $_REQUEST['in'];
			
			if (function_exists('aeva_getItemData'))
			{
				$handler = new aeva_media_handler;
				$exif = @unserialize($context['item_data']['exif']);
				
				if ($context['item_data']['type'] == 'video')
				{
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
			
				if (!empty($context['ogp_meta']))
				{
					$head = '<title>' . $context['page_title_html_safe'] . '</title>';
					$new_head = $context['ogp_meta'] . "\n\t" . $head;
					$replacements[$head] = $new_head;
				}
			}
		}
	}
	
	// Counters
	$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();
	if (!in_array($context['current_action'], $ignored_actions))
		if (!empty($modSettings['optimus_count_code']))
			$replacements[$forum_copyright] = $modSettings['optimus_count_code'] . '<br />' . $forum_copyright;
		
	// XML sitemap link
	if (!empty($modSettings['optimus_sitemap_link']) && file_exists($boarddir . '/sitemap.xml'))
	{
		$text = '<li class="last"><a id="button_wap2"';
		$link = '<li><a href="' . $boardurl . '/sitemap.xml" target="_blank">' . $txt['optimus_sitemap_xml_link'] . '</a></li>';
		$replacements[$text] = $link . $text;
	}
	
	return str_replace(array_keys($replacements), array_values($replacements), $buffer);
}

// Обработка дат
function optimus_sitemap_date($timestamp = '')
{
	$timestamp = empty($timestamp) ? time() : $timestamp;
	$gmt = substr(date("O", $timestamp), 0, 3) . ':00';
	$result = date('Y-m-d\TH:i:s', $timestamp) . $gmt;
	
	return $result;
}

// Создаем файл карты
function optimus_file_create($path, $data)
{
	if (!$fp = @fopen($path, 'w')) return false;

	flock($fp, LOCK_EX);
	fwrite($fp, $data);
	flock($fp, LOCK_UN);
	fclose($fp);
	
	return true;
}

// Если количество урлов больше 50000, заканчиваем сказку
function check_count_urls($array)
{
	global $txt;
	
	if (count($array) > 50000)
		log_error($txt['optimus_sitemap_url_limit'] . $txt['optimus_sitemap_rec'], 'general');
	
	return;
}

// Если размер файла превышает 10 МБ, отправляем запись в Журнал ошибок
function check_filesize($file)
{
	global $txt;
	
	clearstatcache();

	if (filesize($file) > 10485760)
		log_error(sprintf($txt['optimus_sitemap_size_limit'], @pathinfo($file, PATHINFO_BASENAME)) . $txt['optimus_sitemap_rec'], 'general');
	
	return;
}

// Определяем периодичность обновлений
function optimus_sitemap_frequency($time)
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
	$xmlns_image = $tab . 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
	$xmlns_video = $tab . 'xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"';
	
	// SimpleSEF enabled?
	$sef = !empty($modSettings['simplesef_enable']) && file_exists($sourcedir . '/SimpleSEF.php');
	if ($sef)
	{
		function create_sefurl($url)
		{
			global $sourcedir;
			
			require_once($sourcedir . '/SimpleSEF.php');
			$simple = new SimpleSEF;
			
			return $simple->create_sef_url($url);
		}
	}
	
	// PortaMx SEF enabled?
	if (file_exists($sourcedir . '/PortaMx/PortaMxSEF.php') && function_exists('create_sefurl')) $sef = true;
	
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
			'posts' => !empty($modSettings['optimus_sitemap_topic_size']) ? (int) $modSettings['optimus_sitemap_topic_size'] : 0
		)
	);
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$boards[] = $row;
	
	$smcFunc['db_free_result']($request);
	
	$last = array(0);
	foreach ($boards as $entry)	$last[] = empty($entry['modified_time']) ? (empty($entry['poster_time']) ? '' : $entry['poster_time']) : $entry['modified_time'];
	$last_edit = max($last);
	
	// Здесь быстренько заполняем информацию о главной странице
	$fm[] = array(
		'loc' => $boardurl . '/',
		'wap' => $scripturl . '/?' . $mobile_type,
		'lastmod' => optimus_sitemap_date($last_edit),
		'changefreq' => optimus_sitemap_frequency($last_edit),
		'priority' => 1
	);
	
	// А здесь — для разделов
	foreach ($boards as $entry)
	{
		$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];
		
		// Поддержка мода BoardNoIndex
		if (!empty($modSettings['BoardNoIndex_enabled']))
		{
			if (!in_array($entry['id_board'], @unserialize($modSettings['BoardNoIndex_select_boards'])))
				$fm[] = array(
					'loc' => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
					'wap' => $scripturl . '?board=' . $entry['id_board'] . '.0;' . $mobile_type,
					'lastmod' => optimus_sitemap_date($last_edit),
					'changefreq' => optimus_sitemap_frequency($last_edit),
					'priority' => 0.8
				);
		}
		else
		{
			$fm[] = array(
				'loc' => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
				'wap' => $scripturl . '?board=' . $entry['id_board'] . '.0;' . $mobile_type,
				'lastmod' => optimus_sitemap_date($last_edit),
				'changefreq' => optimus_sitemap_frequency($last_edit),
				'priority' => 0.8
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
			'replies' => !empty($modSettings['optimus_sitemap_topic_size']) ? (int) $modSettings['optimus_sitemap_topic_size'] : 0
		)
	);
	
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$topics[] = $row;
	
	$smcFunc['db_free_result']($request);
	
	foreach ($topics as $entry)
	{
		$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];
		
		// Поддержка мода BoardNoIndex
		if (!empty($modSettings['BoardNoIndex_enabled']))
		{
			if (!in_array($entry['id_board'], @unserialize($modSettings['BoardNoIndex_select_boards'])))
				$fm[] = array(
					'loc' => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $scripturl . '?topic=' . $entry['id_topic'] . '.0',
					'wap' => $scripturl . '?topic=' . $entry['id_topic'] . '.0;' . $mobile_type,
					'lastmod' => optimus_sitemap_date($last_edit),
					'changefreq' => optimus_sitemap_frequency($last_edit),
					'priority' => 0.6
				);
		}
		else
		{
			$fm[] = array(
				'loc' => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $scripturl . '?topic=' . $entry['id_topic'] . '.0',
				'wap' => $scripturl . '?topic=' . $entry['id_topic'] . '.0;' . $mobile_type,
				'lastmod' => optimus_sitemap_date($last_edit),
				'changefreq' => optimus_sitemap_frequency($last_edit),
				'priority' => 0.6
			);
		}
	}
	
	// Aeva Media
	if (file_exists($sourcedir . '/Aeva-Subs.php'))
	{
		$query_one = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}aeva_media'", array());
		$query_two = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}aeva_albums'", array());
		$result = $smcFunc['db_num_rows']($query_one) != 0 && $smcFunc['db_num_rows']($query_two) != 0;

		if ($result)
		{
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
			foreach ($items as $entry)
			{
				$media[] = array(
					'loc' => $scripturl . '?action=media;sa=item;in=' . $entry['id_media'],
					'album' => $scripturl . '?action=media;sa=album;in=' . $entry['album_id'],
					'image' => $entry['type'] == 'image' ? $boardurl . '/MGalleryItem.php?id=' . $entry['id_media'] : '',
					'video' => $entry['type'] == 'video' ? $boardurl . '/MGalleryItem.php?id=' . $entry['id_media'] : '',
					'caption' => $entry['title'],
					'thumb' => $scripturl . '?action=media;sa=media;in=' . $entry['id_media'] . ';thumb',
					'desc' => !empty($entry['description']) ? $entry['description'] : '',
					'rating' => !empty($entry['rating']) ? $entry['rating'] : 0,
					'count' => !empty($entry['views']) ? $entry['views'] : 0,
					'name' => $entry['name']
				);
			}
		}
	}
	
	// SMF Gallery mod
	if (file_exists($sourcedir . '/Gallery2.php'))
	{
		$query_one = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}gallery_cat'", array());
		$query_two = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}gallery_pic'", array());
		$result = $smcFunc['db_num_rows']($query_one) != 0 && $smcFunc['db_num_rows']($query_two) != 0;

		if ($result)
		{
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
			foreach ($items as $entry)
			{
				$media[] = array(
					'loc' => $scripturl . '?action=gallery;sa=view;pic=' . $entry['id_picture'],
					'image' => $boardurl . '/gallery/' . $entry['filename'],
					'caption' => $entry['title']
				);
			}
		}
	}
	
	$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n . '<?xml-stylesheet type="text/xsl" href="' . $boardurl . '/Themes/default/css/sitemap.xsl"?>' . $n . '<urlset ' . $xmlns . '>' . $n;
	$footer = '</urlset>';
	$out = '';
	
	check_count_urls($fm);
	
	foreach ($fm as $entry)
	{
		$out .= $t . '<url>' . $n;
		$out .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;
		$out .= $t . $t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $n;
		$out .= $t . $t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $n;
		$out .= $t . $t . '<priority>' . $entry['priority'] . '</priority>' . $n;
		$out .= $t . '</url>' . $n;
	}
 
	// Это для мобилок, задел на будущее
	if (!empty($mobile_type))
	{
		$mobile = '';
		foreach ($fm as $entry)
		{
			if (!empty($entry['wap']))
			{
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
	foreach ($media as $entry)
	{
		if (!empty($entry['image']))
		{
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
	foreach ($media as $entry)
	{
		if (!empty($entry['video']))
		{
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
	if (file_exists($pretty) && !empty($modSettings['pretty_enable_filters']))
	{
		require_once($pretty);
		$context['pretty']['search_patterns'][] = '~(<loc>)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';
		$context['pretty']['search_patterns'][] = '~(<video:thumbnail_loc>)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(<video:thumbnail_loc>)([^<]+)~';
		$context['pretty']['search_patterns'][] = '~(">)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(">)([^<]+)~';
		$out = pretty_rewrite_buffer($out);
		if (!empty($mobile)) $mobile = pretty_rewrite_buffer($mobile);
		if (!empty($images)) $images = pretty_rewrite_buffer($images);
		if (!empty($videos)) $videos = pretty_rewrite_buffer($videos);
	}
	
	$out = $header . $out . $footer;

	// Создаем обычную карту сайта
	$sitemap = $boarddir . '/sitemap.xml';
	optimus_file_create($sitemap, $out);
	check_filesize($sitemap);
	
	if (!empty($mobile) || !empty($images) || !empty($videos))
	{
		// Карта для мобилок
		if (!empty($mobile_type))
		{
			$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n . '<?xml-stylesheet type="text/xsl" href="' . $boardurl . '/Themes/default/css/sitemap.xsl"?>' . $n . '<urlset ' . $xmlns_mobile . '>' . $n;
			$xml_data = $header . $mobile . $footer;
			$sitemap = $boarddir . '/sitemap_mobile.xml';
		}
		
		// Карта ссылок на изображения в Галерее
		if (!empty($images))
		{
			$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n . '<?xml-stylesheet type="text/xsl" href="' . $boardurl . '/Themes/default/css/sitemap.xsl"?>' . $n . '<urlset ' . $xmlns_image . '>' . $n;
			$xml_data = $header . $images . $footer;
			$sitemap = $boarddir . '/sitemap_images.xml';
		}
		
		// Карта ссылок на видеоролики в Галерее
		if (!empty($videos))
		{
			$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n . '<?xml-stylesheet type="text/xsl" href="' . $boardurl . '/Themes/default/css/sitemap.xsl"?>' . $n . '<urlset ' . $xmlns_video . '>' . $n;
			$xml_data = $header . $videos . $footer;
			$sitemap = $boarddir . '/sitemap_videos.xml';
		}

		optimus_file_create($sitemap, $xml_data);
		check_filesize($sitemap);
	}
	
	// Return for the log...
	return true;
}

?>