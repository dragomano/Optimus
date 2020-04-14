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
 * @version 2.7.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Map generation class
 */
class Sitemap
{
	/**
	 * Show main action of Sitemap area
	 *
	 * @return void
	 */
	public static function main()
	{
		loadTemplate('Optimus');

		if (isset($_REQUEST['xml']))
			return self::getXml();

		redirectexit('action=sitemap;xml');
	}

	/**
	 * Show sitemap XML
	 *
	 * @return void
	 */
	public static function getXml()
	{
		global $modSettings, $context, $scripturl;

		$links     = self::getLinks();
		$items     = [];
		$max_items = $modSettings['optimus_sitemap_items_display'] ?: 50000;

		$sitemap_counter = 0;
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

		// The update frequency of the main page
		if (empty($modSettings['optimus_main_page_frequency']))
			$items[0][0]['changefreq'] = 'always';

		// The priority of the main page
		$items[0][0]['priority'] = '1.0';

		$context['sitemap']['items'] = [];

		// If number of links is more than self::$count, we show the sitemapindex file
		if ($sitemap_counter > 0) {
			if (isset($_GET['start'])) {
				$index = (int) $_GET['start'];

				if (!array_key_exists($index, $items))
					redirectexit('action=sitemap;xml');

				$context['sitemap']['items'] = array_merge($context['sitemap']['items'], $items[$index]);
				$context['sub_template'] = 'sitemap_xml';

				return;
			}

			for ($number = 0; $number <= $sitemap_counter; $number++)
				$context['sitemap']['items'][$number]['loc'] = $scripturl . '?action=sitemap;xml;start=' . $number;

			$context['sub_template'] = 'sitemapindex_xml';
		} else {
			if (isset($_GET['start']))
				redirectexit('action=sitemap;xml');

			$context['sitemap']['items'] = $items[0];
			$context['sub_template'] = 'sitemap_xml';
		}
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

		$dates = array();
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
		global $boardurl, $modSettings;

		$cache_ttl = 24 * 60 * 60;

		if (($links = cache_get_data('optimus_sitemap', $cache_ttl)) == null) {
			$boards = self::getBoardLinks();
			$topics = self::getTopicLinks();
			$links  = !empty($boards) || !empty($topics) ? array_merge($boards, $topics) : [];

			// Adding the main page
			$home = array(
				'loc'     => $boardurl . '/',
				'lastmod' => !empty($modSettings['optimus_main_page_frequency']) ? self::getLastDate($links) : time()
			);

			array_unshift($links, $home);

			// Possibility for the mod authors to add their own links or process them
			Subs::runAddons('sitemap', array(&$links));

			cache_put_data('optimus_sitemap', $links, $cache_ttl);
		}

		return $links;
	}

	/**
	 * Get an array of forum boards ([] = array('url' => link, 'date' => date))
	 *
	 * @return array
	 */
	public static function getBoardLinks()
	{
		global $modSettings, $smcFunc, $scripturl;

		if (empty($modSettings['optimus_sitemap_boards']))
			return [];

		$request = $smcFunc['db_query']('', '
			SELECT b.id_board, GREATEST(m.poster_time, m.modified_time) AS last_date
			FROM {db_prefix}boards AS b
				LEFT JOIN {db_prefix}messages AS m ON (m.id_msg = b.id_last_msg)
			WHERE EXISTS (
					SELECT DISTINCT bpv.id_board
					FROM {db_prefix}board_permissions_view bpv
					WHERE (bpv.id_group = -1 AND bpv.deny = 0)
						AND bpv.id_board = b.id_board
				)' . (!empty($modSettings['recycle_board']) ? '
				AND b.id_board <> {int:recycle_board}' : '') . '
				AND b.num_posts > {int:posts}
			ORDER BY b.id_board',
			array(
				'recycle_board' => !empty($modSettings['recycle_board']) ? (int) $modSettings['recycle_board'] : 0,
				'posts'         => 0
			)
		);

		$boards = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$boards[] = $row;

		$smcFunc['db_free_result']($request);

		$links = array();

		if (!empty($boards)) {
			foreach ($boards as $entry)	{
				$links[] = array(
					'loc'     => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
					'lastmod' => $entry['last_date']
				);
			}
		}

		return $links;
	}

	/**
	 * Get an array of forum topics ([] = array('url' => link, 'date' => date))
	 *
	 * @return array
	 */
	public static function getTopicLinks()
	{
		global $smcFunc, $modSettings, $scripturl;

		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, m.id_msg, GREATEST(m.poster_time, m.modified_time) AS last_date, t.num_replies' . (!empty($modSettings['optimus_sitemap_all_topic_pages']) ? '
			FROM {db_prefix}messages AS m
				INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)' : '
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)') . '
				INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			WHERE EXISTS (
					SELECT DISTINCT bpv.id_board
					FROM {db_prefix}board_permissions_view bpv
					WHERE (bpv.id_group = -1 AND bpv.deny = 0)
						AND bpv.id_board = b.id_board
				)' . (!empty($modSettings['recycle_board']) ? '
				AND b.id_board <> {int:recycle_board}' : '') . '
				AND t.num_replies > {int:num_replies}
				AND t.approved = {int:is_approved}
			ORDER BY t.id_topic, m.id_msg',
			array(
				'recycle_board' => !empty($modSettings['recycle_board']) ? (int) $modSettings['recycle_board'] : 0,
				'num_replies'   => !empty($modSettings['optimus_sitemap_topics_num_replies']) ? (int) $modSettings['optimus_sitemap_topics_num_replies'] : -1,
				'is_approved'   => 1
			)
		);

		$topics = array();
		$max_messages = (int) $modSettings['defaultMaxMessages'];

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			if (!empty($modSettings['optimus_sitemap_all_topic_pages'])) {
				$total_pages = ceil($row['num_replies'] / $max_messages);
				$start = 0;

				if (empty($total_pages)) {
					$topics[$row['id_topic']][$start][$row['id_msg']] = $row['last_date'];
				} else {
					for ($i = 0; $i <= $total_pages; $i++) {
						$topics[$row['id_topic']][$start][$row['id_msg']] = $row['last_date'];

						if (count($topics[$row['id_topic']][$start]) <= $max_messages)
							break;

						$topics[$row['id_topic']][$start] = array_slice($topics[$row['id_topic']][$start], 0, $max_messages, true);
						$start += $max_messages;
					}
				}
			} else {
				$topics[$row['id_topic']] = $row['last_date'];
			}
		}

		$smcFunc['db_free_result']($request);

		$links = array();
		foreach ($topics as $id_topic => $data) {
			if (!empty($modSettings['optimus_sitemap_all_topic_pages'])) {
				foreach ($data as $start => $dump) {
					$links[] = array(
						'loc'     => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $id_topic . '.' . $start . '.html' : $scripturl . '?topic=' . $id_topic . '.' . $start,
						'lastmod' => max($dump)
					);
				}
			} else {
				$links[] = array(
					'loc'     => !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $id_topic . '.0.html' : $scripturl . '?topic=' . $id_topic . '.0',
					'lastmod' => $data
				);
			}
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
}
