<?php

namespace Bugo\Optimus;

/**
 * Sitemap.php
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

/**
 * Map generation class
 */
class Sitemap
{
	/**
	 * The maximum number of items per page
	 *
	 * @var int
	 */
	public static $max_items = 50000;

	/**
	 * Create sitemap XML
	 *
	 * @return bool
	 */
	public static function createXml()
	{
		global $modSettings, $context, $scripturl, $boardurl, $boarddir;

		if (@ini_get('memory_limit') < 128)
			@ini_set('memory_limit', '128M');

		// Might take some time.
		@set_time_limit(1800);

		$modSettings['disableQueryCheck'] = true;

		$items     = [];
		$max_items = $modSettings['optimus_sitemap_items_display'] ?: self::$max_items;
		$sitemap_counter = 0;

		$links = self::getLinks();
		if (empty($links))
			return false;

		foreach ($links as $counter => $entry) {
			if (!empty($counter) && $counter % $max_items == 0)
				$sitemap_counter++;

			$items[$sitemap_counter][] = array(
				'loc'        => $entry['loc'],
				'lastmod'    => self::getDate($entry['lastmod']),
				'changefreq' => self::getFrequency($entry['lastmod']),
				'priority'   => self::getPriority($entry['lastmod'])
			);
		}

		unset($links);

		// The update frequency of the main page
		if (empty($modSettings['optimus_main_page_frequency']))
			$items[0][0]['changefreq'] = 'always';

		// The priority of the main page
		$items[0][0]['priority'] = '1.0';

		loadTemplate('Optimus');

		if ($sitemap_counter > 0) {
			$gz_maps = [];
			for ($number = 0; $number <= $sitemap_counter; $number++) {
				$context['sitemap']['items'] = $items[$number];

				ob_start();
				template_sitemap_xml();
				$content = ob_get_clean();

				Subs::runAddons('sitemapRewriteContent', array(&$content));

				$gz_maps[$number] = self::createFile($boarddir . '/sitemap_' . $number . '.xml', $content);
			}

			$context['sitemap']['items'] = [];
			for ($number = 0; $number <= $sitemap_counter; $number++)
				$context['sitemap']['items'][$number]['loc'] = $boardurl . '/sitemap_' . $number . '.xml' . (!empty($gz_maps[$number]) ? '.gz' : '');

			ob_start();
			template_sitemapindex_xml();
			$content = ob_get_clean();

			self::createFile($boarddir . '/sitemap.xml', $content);
		} else {
			$context['sitemap']['items'] = $items[0];

			ob_start();
			template_sitemap_xml();
			$content = ob_get_clean();

			Subs::runAddons('sitemapRewriteContent', array(&$content));

			self::createFile($boarddir . '/sitemap.xml', $content);
		}

		return true;
	}

	/**
	 * Find the most recent date in the array of links for the map
	 *
	 * @param array $links
	 * @return null|int
	 */
	public static function getLastDate($links)
	{
		if (empty($links))
			return null;

		$data = array_values(array_values($links));

		$dates = [];
		foreach ($data as $value)
			$dates[] = $value['lastmod'];

		return max($dates);
	}

	/**
	 * Get an array of forum links to create a Sitemap
	 *
	 * @return array
	 */
	public static function getLinks()
	{
		global $context, $modSettings, $boardurl;

		$context['optimus_ignored_boards'] = [];

		if (!empty($modSettings['recycle_board']))
			$context['optimus_ignored_boards'][] = (int) $modSettings['recycle_board'];

		Subs::runAddons('ignoredBoards');

		$boards = self::getBoardLinks();
		$topics = self::getTopicLinks();
		$links  = !empty($modSettings['optimus_sitemap_boards']) ? array_merge($boards, $topics) : $topics;

		// Adding the main page
		$home = array(
			'loc'     => $boardurl . '/',
			'lastmod' => !empty($modSettings['optimus_main_page_frequency']) ? self::getLastDate($links) : time()
		);

		array_unshift($links, $home);

		// Possibility for the mod authors to add their own links or process them
		Subs::runAddons('sitemap', array(&$links));

		return $links;
	}

	/**
	 * Get an array of forum boards ([] = array('url' => link, 'date' => date))
	 *
	 * @return array
	 */
	public static function getBoardLinks()
	{
		global $modSettings, $smcFunc, $context, $scripturl;

		$request = $smcFunc['db_query']('', '
			SELECT b.id_board, GREATEST(m.poster_time, m.modified_time) AS last_date
			FROM {db_prefix}boards AS b
				LEFT JOIN {db_prefix}messages AS m ON (m.id_msg = b.id_last_msg)
			WHERE FIND_IN_SET(-1, b.member_groups) != 0' . (!empty($context['optimus_ignored_boards']) ? '
				AND b.id_board NOT IN ({array_int:ignored_boards})' : '') . '
				AND b.num_posts > {int:num_posts}
			ORDER BY b.id_board',
			array(
				'ignored_boards' => $context['optimus_ignored_boards'],
				'num_posts'      => 0
			)
		);

		$context['optimus_open_boards'] = $links = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['optimus_open_boards'][] = $row['id_board'];
			$links[] = array(
				'loc'     => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $row['id_board'] . '.0.html' : $scripturl . '?board=' . $row['id_board'] . '.0',
				'lastmod' => $row['last_date']
			);
		}

		$smcFunc['db_free_result']($request);

		return $links;
	}

	/**
	 * Get an array of forum topics ([] = array('url' => link, 'date' => date))
	 *
	 * @return array
	 */
	public static function getTopicLinks()
	{
		global $modSettings, $smcFunc, $context, $scripturl, $txt;

		$start = 0;
		$limit = 1000;

		$links = [];
		while ($start < $modSettings['totalTopics']) {
			$request = $smcFunc['db_query']('', '
				SELECT t.id_topic, m.id_msg, GREATEST(m.poster_time, m.modified_time) AS last_date, t.num_replies
				FROM {db_prefix}topics AS t
					INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
				WHERE t.id_board IN ({array_int:open_boards})
					AND t.num_replies > {int:num_replies}
					AND t.approved = {int:is_approved}
				ORDER BY t.id_topic, m.id_msg
				LIMIT {int:start}, {int:limit}',
				array(
					'open_boards' => $context['optimus_open_boards'],
					'num_replies' => !empty($modSettings['optimus_sitemap_topics_num_replies']) ? (int) $modSettings['optimus_sitemap_topics_num_replies'] : -1,
					'is_approved' => 1,
					'start'       => $start,
					'limit'       => $limit
				)
			);

			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$links[] = array(
					'loc'     => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $row['id_topic'] . '.0.html' : $scripturl . '?topic=' . $row['id_topic'] . '.0',
					'lastmod' => $row['last_date']
				);
			}

			$smcFunc['db_free_result']($request);

			$start = $start + $limit;
		}

		return $links;
	}

	/**
	 * Date processing
	 *
	 * @param int $timestamp
	 * @return string
	 */
	private static function getDate($time = 0)
	{
		$timestamp = $time ?: time();
		$gmt       = substr(date("O", $timestamp), 0, 3) . ':00';
		$result    = date('Y-m-d\TH:i:s', $timestamp) . $gmt;

		return $result;
	}

	/**
	 * Determine the frequency of updates
	 *
	 * @param int $time
	 * @return string
	 */
	private static function getFrequency($time)
	{
		$frequency = time() - $time;

		if ($frequency < (24 * 60 * 60))
			return 'hourly';
		elseif ($frequency < (24 * 60 * 60 * 7))
			return 'daily';
		elseif ($frequency < (24 * 60 * 60 * 7 * (52 / 12)))
			return 'weekly';
		elseif ($frequency < (24 * 60 * 60 * 365))
			return 'monthly';

		return 'yearly';
	}

	/**
	 * Determine the priority of indexing
	 *
	 * @param int $time
	 * @return string
	 */
	private static function getPriority($time)
	{
		$diff = floor((time() - $time) / 60 / 60 / 24);

		if ($diff <= 30)
			return '0.8';
		elseif ($diff <= 60)
			return '0.6';
		elseif ($diff <= 90)
			return '0.4';

		return '0.2';
	}

	/**
	 * Создаем файл карты
	 *
	 * @param string $path — путь к файлу
	 * @param string $data — содержимое
	 * @return bool if true then we have gz-version
	 */
	private static function createFile($path, $data)
	{
		if (!$fp = fopen($path, 'w'))
			return false;

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		// Если размер файла превышает 50 МБ, создадим упакованную gz-версию
		if (filesize($path) > (50 * 1024 * 1024)) {
			$data   = implode('', file($path));
			$gzdata = gzencode($data, 9);
			$fp     = fopen($path . '.gz', 'w');
			fwrite($fp, $gzdata);
			fclose($fp);

			return true;
		}

		return false;
	}
}
