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
 * @version 2.0 beta
 */

if (!defined('SMF'))
	die('Hacking attempt...');

define('OB_MOD', 'Optimus');
define('OB_VER', '2.0 beta');
define('OB_LINK', 'https://dragomano.ru/mods/optimus');
define('OB_AUTHOR', 'Bugo');

function loadOptimusHooks()
{
	add_integration_function('integrate_load_theme', 'addOptimusLoadTheme', false);
	add_integration_function('integrate_load_theme', 'addOptimusCounters', false);
	add_integration_function('integrate_admin_include', '$sourcedir/Admin-Optimus.php', false);
	add_integration_function('integrate_admin_areas', 'addOptimusAdminArea', false);
	add_integration_function('integrate_menu_buttons', 'addOptimusCopyright', false);
	add_integration_function('integrate_menu_buttons', 'addOptimusOperations', false);
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
	}
}

function addOptimusCopyright()
{
    global $context;

    if ($context['current_action'] == 'credits') {
        $context['credits_modifications'][] = '<a href="' . OB_LINK . '" target="_blank" title="' . OB_VER . '">' . OB_MOD . '</a> &copy; 2010&ndash;' . date('Y') . ', ' . OB_AUTHOR;
    }
}

// integrate_menu_buttons hook
function addOptimusOperations()
{
	global $modSettings, $context, $boardurl, $scripturl, $smcFunc, $boarddir, $forum_copyright, $txt;

	// Последний пункт в хлебных крошках не будем делать ссылкой
	if (!empty($modSettings['optimus_remove_last_bc_item'])) {
		$linktree = count($context['linktree']);
		unset($context['linktree'][$linktree - 1]['url']);
	}

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

	// Verification tags
	if (isset($context['canonical_url']) && !empty($modSettings['optimus_meta'])) {
		$meta = '';
		$test = @unserialize($modSettings['optimus_meta']);

		foreach ($test as $var) {
			$context['meta_tags'][] = array('name' => $var['name'], 'content' => $var['content']);
		}
	}

	// Forum description
	if (empty($context['current_action']) && empty($_REQUEST['board']) && empty($_REQUEST['topic']))
		if (!empty($modSettings['optimus_description']))
			$context['meta_description'] = $smcFunc['htmlspecialchars']($modSettings['optimus_description']);

	// Topic description
	if (!empty($context['topic_first_message'])) {
		if (!empty($modSettings['optimus_topic_description'])) {
			if (!empty($context['topic_description']))
				$context['meta_description'] = $context['topic_description'];
			else {
				getOptimusDescription();
				$context['meta_description'] = $context['optimus_description'];
			}
		}

		getOptimusOgImage();
	}

	getOptimusPageTemplates();
	getOptimusHttpStatus();

	// XML sitemap link
	if (!empty($modSettings['optimus_sitemap_link']) && file_exists($boarddir . '/sitemap.xml'))
		$forum_copyright .= ' | <a href="' . $boardurl . '/sitemap.xml" target="_blank">' . $txt['optimus_sitemap_xml_link'] . '</a>';
}

// Обрабатываем шаблоны заголовков страниц
function getOptimusPageTemplates()
{
	global $modSettings, $txt, $context, $board_info;

	if (!empty($modSettings['optimus_templates'])) {
		if (strpos($modSettings['optimus_templates'], 'board') && strpos($modSettings['optimus_templates'], 'topic')) {
			$templates = @unserialize($modSettings['optimus_templates']);

			foreach ($templates as $name => $data) {
				if ($name == 'board') {
					$board_name_tpl = $data['name'];
					$board_site_tpl = $data['site'];
				}

				if ($name == 'topic') {
					$topic_name_tpl = $data['name'];
					$topic_site_tpl = $data['site'];
				}
			}
		}
	}
	else {
		foreach ($txt['optimus_templates'] as $name => $data) {
			if ($name == 'board') {
				$board_name_tpl = $data[0];
				$board_site_tpl = $data[1];
			}

			if ($name == 'topic') {
				$topic_name_tpl = $data[0];
				$topic_site_tpl = $data[1];
			}
		}
	}

	// Boards
	if (!empty($board_info['total_topics'])) {
		$trans = array(
			"{board_name}" => strip_tags($context['name']),
			"{cat_name}"   => $board_info['cat']['name'],
			"{forum_name}" => $context['forum_name']
		);

		$context['page_title'] = strtr($board_name_tpl . $board_site_tpl, $trans);
	}

	// Topics
	if (!empty($context['topic_first_message'])) {
		$trans = array(
            "{topic_name}" => $context['topicinfo']['subject'],
			"{board_name}" => strip_tags($board_info['name']),
			"{cat_name}"   => $board_info['cat']['name'],
			"{forum_name}" => $context['forum_name']
		);

		$context['page_title'] = strtr($topic_name_tpl . $topic_site_tpl, $trans);
	}
}

// Создаем описание страницы из первого сообщения
function getOptimusDescription()
{
	global $context, $smcFunc;

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
function getOptimusOgImage()
{
	global $modSettings, $settings, $context, $smcFunc, $scripturl;

	if (empty($modSettings['optimus_og_image']))
		return;

	// Кэшируем запрос
	if (($settings['og_image'] = cache_get_data('og_image_' . $context['current_topic'], 3600)) == null) {
		$request = $smcFunc['db_query']('', '
			SELECT COALESCE(id_attach, 0) AS id
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
 * Если размер файла превышает 10 МБ, отправляем запись в Журнал ошибок
 *
 * @param  string $file [принимаем ссылку на файл]
 */
function checkOptimusFilesize($file)
{
	global $txt;

	clearstatcache();

	if (filesize($file) > (10*1024*1024))
		log_error(sprintf($txt['optimus_sitemap_size_limit'], @pathinfo($file, PATHINFO_BASENAME)) . $txt['optimus_sitemap_rec'], 'general');

	return;
}

/**
 * Определяем приоритет индексирования (не влияет на позиции страниц в поисковой выдаче!)
 *
 * @param  string $time [принимаем время изменения страницы]
 * @return string       [возвращаем одно из предустановленных значений, для установки приоритета сканирования]
 */
function getOptimusSitemapPriority($time)
{
	$diff = floor((time() - $time)/60/60/24);
	
	if ($diff <= 30)
		return '0.8';
	else if ($diff <= 60)
		return '0.6';
	else if ($diff <= 90)
		return '0.4';
	else
		return '0.2';
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
	global $modSettings, $sourcedir, $boardurl, $smcFunc, $scripturl, $context, $boarddir, $db_prefix;

	// Прежде всего проверяем, активировано ли создание карты форума (в настройках)
	if (empty($modSettings['optimus_sitemap_enable']))
		return;

	$t            = "\t";
	$n            = "\n";
	$sef          = false;
	$header       = '<' . '?xml version="1.0" encoding="UTF-8"?>' . $n;
	$xmlns        = 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
	$tab          = $xmlns . $n . $t . $t;
	$xmlns_image  = $tab . 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';

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

	$url   = array();
	$first = array();
	$sec   = array();

	// Главную страницу грех не добавить :)
	if (empty($modSettings['optimus_sitemap_boards'])) {
		$first[] = $url[] = array(
			'loc'      => $boardurl . '/',
			'priority' => '1.0',
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
				'posts'         => !empty($modSettings['optimus_sitemap_topics']) ? (int) $modSettings['optimus_sitemap_topics'] : 0,
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
							'lastmod'    => getOptimusSitemapDate($last_edit),
							'changefreq' => getOptimusSitemapFrequency($last_edit),
							'priority'   => getOptimusSitemapPriority($last_edit)
						);
					}
				}
				else {
					$first[] = $url[] = array(
						'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
						'lastmod'    => getOptimusSitemapDate($last_edit),
						'changefreq' => getOptimusSitemapFrequency($last_edit),
						'priority'   => getOptimusSitemapPriority($last_edit)
					);
				}

				$last[] = empty($entry['modified_time']) ? (empty($entry['poster_time']) ? '' : $entry['poster_time']) : $entry['modified_time'];
			}

			$home_last_edit = max($last);
			$home = array(
				'loc'        => $boardurl . '/',
				'lastmod'    => getOptimusSitemapDate($home_last_edit),
				'changefreq' => getOptimusSitemapFrequency($home_last_edit),
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
		SELECT m.poster_time AS date, t.id_topic, m.poster_time, m.modified_time
		FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
		WHERE FIND_IN_SET(-1, b.member_groups) != 0' . (!empty($modSettings['recycle_board']) ? ' AND b.id_board <> {int:recycle_board}' : '') . (!empty($modSettings['optimus_sitemap_topics']) ? ' AND t.num_replies > {int:replies}' : '') . ' AND t.approved = 1
		ORDER BY t.id_topic',
		array(
			'recycle_board' => !empty($modSettings['recycle_board']) ? (int) $modSettings['recycle_board'] : 0,
			'replies'       => !empty($modSettings['optimus_sitemap_topics']) ? (int) $modSettings['optimus_sitemap_topics'] : 0,
		)
	);
	
	$topics = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$row['date'] = date('Y', $row['date']);
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
						'lastmod'    => getOptimusSitemapDate($last_edit),
						'changefreq' => getOptimusSitemapFrequency($last_edit),
						'priority'   => getOptimusSitemapPriority($last_edit)
					);
				}
			}
			else {
				$sec[$year][] = $url[] = array(
					'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $scripturl . '?topic=' . $entry['id_topic'] . '.0',
					'lastmod'    => getOptimusSitemapDate($last_edit),
					'changefreq' => getOptimusSitemapFrequency($last_edit),
					'priority'   => getOptimusSitemapPriority($last_edit)
				);
			}
		}

		$files[] = $year;
		$out[$year] = '';		
		foreach ($sec[$year] as $entry) {
			$out[$year] .= $t . '<url>' . $n;
			$out[$year] .= $t . $t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $n;

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
				'lastmod'    => getOptimusSitemapDate($last_edit),
				'changefreq' => getOptimusSitemapFrequency($last_edit),
				'priority'   => getOptimusSitemapPriority($last_edit)
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

	// SMF Gallery mod
	if (!empty($modSettings['optimus_sitemap_gallery']) && file_exists($sourcedir . '/Gallery2.php')) {
		$query_one = $smcFunc['db_query']('', '
			SELECT 1
			FROM   information_schema.tables 
			WHERE  table_name = {string:table}',
			array(
				'table' => $db_prefix.'gallery_cat',
			)
		);
		$query_two = $smcFunc['db_query']('', '
			SELECT 1
			FROM   information_schema.tables 
			WHERE  table_name = {db_prefix}gallery_pic',
			array(
				'table' => $db_prefix.'gallery_pic',
			)
		);
		$result    = $smcFunc['db_num_rows']($query_one) != 0 && $smcFunc['db_num_rows']($query_two) != 0;

		if ($result) {
			$items = array();

			$request = $smcFunc['db_query']('', '
				SELECT gp.id_picture, gp.title, gp.filename
				FROM {db_prefix}gallery_pic AS gp
					INNER JOIN {db_prefix}permissions AS ps ON (ps.id_group = -1)
				WHERE ps.permission = {string:smfgallery_view}
				ORDER BY gp.id_picture',
				array(
				'smfgallery_view' => 'smfgallery_view',
				)
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
	if (!empty($media) && !empty($modSettings['optimus_sitemap_gallery'])) {
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
		
		if (!empty($images))
			$images = pretty_rewrite_buffer($images);
	}

	// Создаем карту сайта (если ссылок больше 10к, то сделаем файл индекса)
	if (count($url) > 10000) {
		$main = $header . '<urlset ' . $xmlns . '>' . $n . $main . '</urlset>';
		$sitemap = $boarddir . '/sitemap_main.xml';
		createOptimusFile($sitemap, $main);

		foreach ($files as $year) {
			$out[$year] = $header . '<urlset ' . $xmlns . '>' . $n . $out[$year] . '</urlset>';
			$sitemap = $boarddir . '/sitemap_' . $year . '.xml';
			createOptimusFile($sitemap, $out[$year]);
			checkOptimusFilesize($sitemap);
		}

		// Отдельный файл для объявлений
		if (!empty($bbs_map)) {
			$bbs_map = $header . '<urlset ' . $xmlns . '>' . $n . $bbs_map . '</urlset>';
			$sitemap = $boarddir . '/sitemap_classifieds.xml';
			createOptimusFile($sitemap, $bbs_map);
		}

		// Создаем файл индекса Sitemap
		$maps = '';

		if (!empty($main)) {
			$maps .= $t . '<sitemap>' . $n;
			$maps .= $t . $t . '<loc>' . $boardurl . '/sitemap_main.xml</loc>' . $n;
			$maps .= $t . $t . '<lastmod>' . getOptimusSitemapDate() . '</lastmod>' . $n;
			$maps .= $t . '</sitemap>' . $n;
		}

		foreach ($files as $year) {
			$maps .= $t . '<sitemap>' . $n;
			$maps .= $t . $t . '<loc>' . $boardurl . '/sitemap_' . $year . '.xml</loc>' . $n;
			$maps .= $t . $t . '<lastmod>' . getOptimusSitemapDate() . '</lastmod>' . $n;
			$maps .= $t . '</sitemap>' . $n;
		}
		
		$index_data = $header . '<sitemapindex ' . $xmlns . '>' . $n . $maps . '</sitemapindex>';
		$index_file = $boarddir . '/sitemap.xml';	
		createOptimusFile($index_file, $index_data);
	}
	else {
		$one_file = $header . '<urlset ' . $xmlns . '>' . $n . $one_file . '</urlset>';
		$sitemap = $boarddir . '/sitemap.xml';
		createOptimusFile($sitemap, $one_file);
	}

	// Карта ссылок на изображения
	if (!empty($images) && (!empty($modSettings['optimus_sitemap_gallery']) || !empty($modSettings['optimus_sitemap_aeva']))) {
		$xml_data = $header . '<urlset ' . $xmlns_image . '>' . $n . $images . '</urlset>';
		createOptimusFile($boarddir . '/sitemap_images.xml', $xml_data);
	}

	return true;
}
