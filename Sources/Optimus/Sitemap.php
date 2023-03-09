<?php

declare(strict_types=1);

namespace Bugo\Optimus;

/**
 * Sitemap.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.10
 */

if (! defined('SMF'))
	die('No direct access...');

final class Sitemap
{
	public const MAX_ITEMS = 50000;

	public static function createXml()
	{
		global $modSettings, $context, $boardurl, $boarddir;

		ignore_user_abort(true);
		@set_time_limit(600);

		$modSettings['disableQueryCheck'] = true;
		$modSettings['pretty_bufferusecache'] = false;

		$max_items = op_config('optimus_sitemap_items_display', self::MAX_ITEMS);

		$sitemap_counter = 0;

		$items = [];

		$getLinks = fn() => yield from self::getLinks();

		foreach ($getLinks() as $counter => $entry) {
			if (! empty($counter) && $counter % $max_items == 0)
				$sitemap_counter++;

			$entry['lastmod'] = (int) $entry['lastmod'];

			$items[$sitemap_counter][] = array(
				'loc'        => $entry['loc'],
				'lastmod'    => empty($entry['lastmod']) ? null : self::getDateIso8601($entry['lastmod']),
				'changefreq' => empty($entry['lastmod']) ? null : self::getFrequency($entry['lastmod']),
				'priority'   => empty($entry['lastmod']) ? null : self::getPriority($entry['lastmod']),
				//'image'      => $entry['image'] ?? null
			);
		}

		if (empty($items))
			return;

		// The update frequency of the main page
		if (is_off('optimus_main_page_frequency'))
			$items[0][0]['changefreq'] = 'always';

		// The priority of the main page
		$items[0][0]['priority'] = '1.0';

		$context['sitemap'] = [];

		loadTemplate('Optimus');

		if ($sitemap_counter > 0) {
			$gz_maps = [];
			for ($number = 0; $number <= $sitemap_counter; $number++) {
				$context['sitemap'] = $items[$number];

				ob_start();
				template_sitemap_xml();
				$content = ob_get_clean();

				call_integration_hook('integrate_optimus_sitemap_rewrite_content', array(&$content));

				$gz_maps[$number] = self::createFile($boarddir . '/sitemap_' . $number . '.xml', $content);
			}

			$context['sitemap'] = [];
			for ($number = 0; $number <= $sitemap_counter; $number++)
				$context['sitemap'][$number]['loc'] = $boardurl . '/sitemap_' . $number . '.xml' . (! empty($gz_maps[$number]) ? '.gz' : '');

			ob_start();
			template_sitemapindex_xml();
			$content = ob_get_clean();
		} else {
			$context['sitemap'] = $items[0];

			ob_start();
			template_sitemap_xml();
			$content = ob_get_clean();

			call_integration_hook('integrate_optimus_sitemap_rewrite_content', array(&$content));
		}

		self::createFile($boarddir . '/sitemap.xml', $content);

		ignore_user_abort(false);
	}

	public static function getLastDate(array $links): int
	{
		if (empty($links))
			return time();

		$data = array_values(array_values($links));

		$dates = [];
		foreach ($data as $value)
			$dates[] = (int) $value['lastmod'];

		return max($dates);
	}

	public static function getLinks(): array
	{
		global $context, $modSettings, $boardurl;

		if (! isset($context['optimus_ignored_boards']))
			$context['optimus_ignored_boards'] = [];

		if (is_on('recycle_board'))
			$context['optimus_ignored_boards'][] = (int) $modSettings['recycle_board'];

		$links = array_merge(self::getBoardLinks(), self::getTopicLinks());

		// Mod authors can add or process their own links
		call_integration_hook('integrate_optimus_sitemap', array(&$links));

		// Adding the main page
		$home = array(
			'loc'     => $boardurl . '/',
			'lastmod' => is_on('optimus_main_page_frequency') ? self::getLastDate($links) : time()
		);

		array_unshift($links, $home);

		return $links;
	}

	private static function getBoardLinks(): array
	{
		global $smcFunc, $context, $scripturl;

		$start_year = (int) op_config('optimus_start_year', 0);

		$request = $smcFunc['db_query']('', '
			SELECT b.id_board, GREATEST(m.poster_time, m.modified_time) AS last_date
			FROM {db_prefix}boards AS b
				LEFT JOIN {db_prefix}messages AS m ON (b.id_last_msg = m.id_msg)
			WHERE EXISTS (
					SELECT DISTINCT bpv.id_board
					FROM {db_prefix}board_permissions_view bpv
					WHERE bpv.id_group = -1
						AND bpv.deny = 0
						AND bpv.id_board = b.id_board
				)' . (! empty($context['optimus_ignored_boards']) ? '
				AND b.id_board NOT IN ({array_int:ignored_boards})' : '') . '
				AND b.redirect = {string:empty_string}
				AND b.num_posts > {int:num_posts}' . ($start_year ? '
				AND YEAR(FROM_UNIXTIME(m.poster_time)) >= {int:start_year}' : '') . '
			ORDER BY b.id_board DESC',
			array(
				'ignored_boards' => $context['optimus_ignored_boards'],
				'empty_string'   => '',
				'num_posts'      => 0,
				'start_year'     => $start_year
			)
		);

		$context['optimus_open_boards'] = $links = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['optimus_open_boards'][] = $row['id_board'];

			if (is_on('optimus_sitemap_boards')) {
				$board_url = $scripturl . '?board=' . $row['id_board'] . '.0';

				if (is_on('queryless_urls'))
					$board_url = $scripturl . '/board,' . $row['id_board'] . '.0.html';

				call_integration_hook('integrate_optimus_create_sef_url', array(&$board_url));

				$links[] = array(
					'loc'     => $board_url,
					'lastmod' => $row['last_date']
				);
			}
		}

		$smcFunc['db_free_result']($request);

		return $links;
	}

	private static function getTopicLinks(): array
	{
		global $db_temp_cache, $db_cache, $smcFunc, $context, $scripturl;

		$start = 0;
		$limit = 1000;

		// Don't allow the cache to get too full
		$db_temp_cache = $db_cache;
		$db_cache = [];

		$start_year   = (int) op_config('optimus_start_year', 0);
		$num_replies  = (int) op_config('optimus_sitemap_topics_num_replies', 0);
		$total_rows   = (int) (is_on('optimus_sitemap_all_topic_pages') ? op_config('totalMessages', 0) : op_config('totalTopics', 0));

		$links  = [];
		$topics = [];
		$images = [];

		$messages_per_page = (int) op_config('defaultMaxMessages', 0);

		while ($start < $total_rows) {
			@set_time_limit(600);
			if (function_exists('apache_reset_timeout'))
				@apache_reset_timeout();

			if (is_on('optimus_sitemap_all_topic_pages')) {
				$request = $smcFunc['db_query']('', '
					SELECT t.id_topic, t.num_replies, m.id_msg, GREATEST(m.poster_time, m.modified_time) AS last_date, a.id_attach, a.filename
					FROM {db_prefix}messages AS m
						INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)
						LEFT JOIN {db_prefix}attachments AS a ON (a.id_msg = t.id_first_msg AND a.attachment_type = 0 AND a.width <> 0 AND a.height <> 0 AND a.approved = 1)
					WHERE t.id_board IN ({array_int:open_boards})
						AND t.num_replies >= {int:num_replies}
						AND t.approved = {int:is_approved}' . ($start_year ? '
						AND YEAR(FROM_UNIXTIME(GREATEST(m.poster_time, m.modified_time))) >= {int:start_year}' : '') . '
					ORDER BY t.id_topic DESC, last_date
					LIMIT {int:start}, {int:limit}',
					array(
						'open_boards' => $context['optimus_open_boards'],
						'num_replies' => $num_replies,
						'is_approved' => 1,
						'start_year'  => $start_year,
						'start'       => $start,
						'limit'       => $limit
					)
				);

				while ($row = $smcFunc['db_fetch_assoc']($request)) {
					$total_pages = ceil($row['num_replies'] / $messages_per_page);
					$page_start = 0;

					if (! empty($row['id_attach']) && ! isset($images[$row['id_topic']])) {
						$images[$row['id_topic']] = [
							'loc'   => $scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach=' . $row['id_attach'] . ';image',
							'title' => $row['filename']
						];
					}

					if (empty($total_pages)) {
						$topics[$row['id_topic']][$page_start][$row['id_msg']] = $row['last_date'];
					} else {
						for ($i = 0; $i <= $total_pages; $i++) {
							$topics[$row['id_topic']][$page_start][$row['id_msg']] = $row['last_date'];

							if (count($topics[$row['id_topic']][$page_start]) <= $messages_per_page)
								break;

							$topics[$row['id_topic']][$page_start] = array_slice($topics[$row['id_topic']][$page_start], 0, $messages_per_page, true);
							$page_start += $messages_per_page;
						}
					}
				}
			} else {
				$request = $smcFunc['db_query']('', '
					SELECT t.id_topic, GREATEST(m.poster_time, m.modified_time) AS last_date, a.id_attach, a.filename
					FROM {db_prefix}topics AS t
						INNER JOIN {db_prefix}messages AS m ON (t.id_last_msg = m.id_msg)
						LEFT JOIN {db_prefix}attachments AS a ON (a.id_msg = t.id_first_msg AND a.attachment_type = 0 AND a.width <> 0 AND a.height <> 0 AND a.approved = 1)
					WHERE t.id_board IN ({array_int:open_boards})
						AND t.num_replies >= {int:num_replies}
						AND t.approved = {int:is_approved}' . ($start_year ? '
						AND YEAR(FROM_UNIXTIME(GREATEST(m.poster_time, m.modified_time))) >= {int:start_year}' : '') . '
					ORDER BY t.id_topic DESC, last_date DESC
					LIMIT {int:start}, {int:limit}',
					array(
						'open_boards' => $context['optimus_open_boards'],
						'num_replies' => $num_replies,
						'is_approved' => 1,
						'start_year'  => $start_year,
						'start'       => $start,
						'limit'       => $limit
					)
				);

				while ($row = $smcFunc['db_fetch_assoc']($request)) {
					$topic_url = $scripturl . '?topic=' . $row['id_topic'] . '.0';

					if (is_on('queryless_urls'))
						$topic_url = $scripturl . '/topic,' . $row['id_topic'] . '.0.html';

					call_integration_hook('integrate_optimus_create_sef_url', array(&$topic_url));

					if (! empty($row['id_attach']) && ! isset($images[$row['id_topic']])) {
						$images[$row['id_topic']] = [
							'loc'   => $scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach=' . $row['id_attach'] . ';image',
							'title' => $row['filename']
						];
					}

					$links[$row['id_topic']] = array(
						'loc'     => $topic_url,
						'lastmod' => $row['last_date'],
						'image'   => $images[$row['id_topic']] ?? []
					);
				}
			}

			$smcFunc['db_free_result']($request);

			$start += $limit;
		}

		foreach ($topics as $topic_id => $topic_data) {
			foreach ($topic_data as $page_start => $dates) {
				$topic_url = is_on('queryless_urls') ? $scripturl . '/topic,' . $topic_id . '.' . $page_start . '.html' : $scripturl . '?topic=' . $topic_id . '.' . $page_start;

				call_integration_hook('integrate_optimus_create_sef_url', array(&$topic_url));

				$links[] = array(
					'loc'     => $topic_url,
					'lastmod' => max($dates),
					'image'   => $images[$topic_id] ?? []
				);
			}
		}

		// Restore the cache
		$db_cache = $db_temp_cache;

		return array_values($links);
	}

	private static function getDateIso8601(int $timestamp): string
	{
		if (empty($timestamp))
			return '';

		$gmt = substr(date("O", $timestamp), 0, 3) . ':00';

		return date('Y-m-d\TH:i:s', $timestamp) . $gmt;
	}

	private static function getFrequency(int $timestamp): string
	{
		$frequency = time() - $timestamp;

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

	private static function getPriority(int $timestamp): string
	{
		$diff = floor((time() - $timestamp) / 60 / 60 / 24);

		if ($diff <= 30)
			return '0.8';
		elseif ($diff <= 60)
			return '0.6';
		elseif ($diff <= 90)
			return '0.4';

		return '0.2';
	}

	private static function createFile(string $path, string $data): bool
	{
		fclose(fopen($path, "a+b"));

		if (! $fp = fopen($path, "r+b"))
			return false;

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		fflush($fp);
		flock($fp, LOCK_UN);
		fclose($fp);

		// If filesize > 50MB, then create gz version
		if (filesize($path) > (50 * 1024 * 1024)) {
			fclose(fopen($path . '.gz', "a+b"));

			if (! $fpgz = fopen($path . '.gz', 'r+b'))
				return false;

			flock($fpgz, LOCK_EX);
			$data = implode('', file($path));
			$gzdata = gzencode($data, 9);
			fwrite($fpgz, $gzdata);
			fflush($fpgz);
			flock($fpgz, LOCK_UN);
			fclose($fpgz);

			return true;
		}

		return false;
	}
}
