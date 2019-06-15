<?php

namespace Bugo\Optimus;

/**
 * Sitemap.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2019 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.1
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class Sitemap
{
	private $t     = "\t";
	private $n     = "\n";
	private $count = 50000;
	private $xmlns = 'http://www.sitemaps.org/schemas/sitemap/0.9';

	private $custom_links = array();

	public function __construct($xmlns = '', $urls = [])
	{
		if (!empty($xmlns))
			$this->xmlns = (string) $xmlns;

		if (!empty($urls) && is_array($urls))
			$this->custom_links = $urls;
	}

	/**
	 * Генерация карты форума
	 *
	 * @return bool
	 */
	public function create()
	{
		global $modSettings, $sourcedir, $boardurl, $smcFunc, $scripturl, $context, $boarddir;

		// Master option
		if (empty($modSettings['optimus_sitemap_enable']))
			return;

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

		// Конвертация адресов кириллических доменов
		$idn_convert = false;
		require_once($sourcedir . '/Optimus/libs/idna_convert.class.php');
		$idn = new \idna_convert(array('idn_version' => 2008));
		if (stripos($idn->encode($boardurl), 'xn--') !== false)
			$idn_convert = true;

		$url_list    = array();
		$base_links  = array();
		$topic_links = array();

		// Добавляем главную страницу
		if (empty($modSettings['optimus_sitemap_boards'])) {
			$base_links[] = $url_list[] = array(
				'loc'      => $boardurl . '/',
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
						if (!in_array($entry['id_board'], unserialize($modSettings['BoardNoIndex_select_boards']))) {
							$base_links[] = $url_list[] = array(
								'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
								'lastmod'    => self::getSitemapDate($last_edit),
								'changefreq' => self::getSitemapFrequency($last_edit),
								'priority'   => self::getSitemapPriority($last_edit)
							);
						}
					} else {
						$base_links[] = $url_list[] = array(
							'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
							'lastmod'    => self::getSitemapDate($last_edit),
							'changefreq' => self::getSitemapFrequency($last_edit),
							'priority'   => self::getSitemapPriority($last_edit)
						);
					}

					$last[] = empty($entry['modified_time']) ? (empty($entry['poster_time']) ? '' : $entry['poster_time']) : $entry['modified_time'];
				}

				$home_last_edit = max($last);
				$home = array(
					'loc'        => $boardurl . '/',
					'lastmod'    => self::getSitemapDate($home_last_edit),
					'changefreq' => self::getSitemapFrequency($home_last_edit),
					'priority'   => '1.0'
				);
				array_unshift($url_list, $home);
				array_unshift($base_links, $home);
			}
		}

		$base_entries = '';
		foreach ($base_links as $entry) {
			if ($idn_convert)
				$entry['loc'] = $idn->encode($entry['loc']);

			$base_entries .= $this->t . '<url>' . $this->n;
			$base_entries .= $this->t . $this->t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $this->n;

			if (!empty($entry['lastmod']))
				$base_entries .= $this->t . $this->t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $this->n;

			if (!empty($entry['changefreq']))
				$base_entries .= $this->t . $this->t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $this->n;

			if (!empty($entry['priority']))
				$base_entries .= $this->t . $this->t . '<priority>' . $entry['priority'] . '</priority>' . $this->n;

			$base_entries .= $this->t . '</url>' . $this->n;
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
			$files[] = $year;

			foreach ($data as $topic => $entry) {
				$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];
				$url_list_topic = $scripturl . '?topic=' . $entry['id_topic'] . '.0';
				$url_list_topic = !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $url_list_topic;
				$years[count($topics[$year])] = $year;

				// Поддержка мода BoardNoIndex
				if (!empty($modSettings['BoardNoIndex_enabled'])) {
					if (!in_array($entry['id_board'], unserialize($modSettings['BoardNoIndex_select_boards']))) {
						$topic_links[$year][] = $url_list[] = array(
							'loc'        => $url_list_topic,
							'lastmod'    => self::getSitemapDate($last_edit),
							'changefreq' => self::getSitemapFrequency($last_edit),
							'priority'   => self::getSitemapPriority($last_edit)
						);
					}
				} else {
					$topic_links[$year][] = $url_list[] = array(
						'loc'        => $url_list_topic,
						'lastmod'    => self::getSitemapDate($last_edit),
						'changefreq' => self::getSitemapFrequency($last_edit),
						'priority'   => self::getSitemapPriority($last_edit)
					);
				}
			}

			$topic_entries[$year] = '';
			foreach ($topic_links[$year] as $entry) {
				if ($idn_convert)
					$entry['loc'] = $idn->encode($entry['loc']);

				$topic_entries[$year] .= $this->t . '<url>' . $this->n;
				$topic_entries[$year] .= $this->t . $this->t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $this->n;

				if (!empty($entry['lastmod']))
					$topic_entries[$year] .= $this->t . $this->t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $this->n;

				if (!empty($entry['changefreq']))
					$topic_entries[$year] .= $this->t . $this->t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $this->n;

				if (!empty($entry['priority']))
					$topic_entries[$year] .= $this->t . $this->t . '<priority>' . $entry['priority'] . '</priority>' . $this->n;

				$topic_entries[$year] .= $this->t . '</url>' . $this->n;
			}
		}

		// Есть массив с дополнительными ссылками?
		if (!empty($this->custom_links)) {
			foreach ($this->custom_links as $custom_link) {
				$url_list[] = array(
					'loc'     => $custom_link['url'],
					'lastmod' => self::getSitemapDate($custom_link['date'])
				);
			}
		}

		// Обработаем все ссылки
		$one_file = '';
		foreach ($url_list as $entry) {
			if ($idn_convert)
				$entry['loc'] = $idn->encode($entry['loc']);

			$one_file .= $this->t . '<url>' . $this->n;
			$one_file .= $this->t . $this->t . '<loc>' . ($sef ? create_sefurl($entry['loc']) : $entry['loc']) . '</loc>' . $this->n;

			if (!empty($entry['lastmod']))
				$one_file .= $this->t . $this->t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $this->n;

			if (!empty($entry['changefreq']))
				$one_file .= $this->t . $this->t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $this->n;

			if (!empty($entry['priority']))
				$one_file .= $this->t . $this->t . '<priority>' . $entry['priority'] . '</priority>' . $this->n;

			$one_file .= $this->t . '</url>' . $this->n;
		}

		// Pretty URLs installed?
		$pretty = $sourcedir . '/PrettyUrls-Filters.php';
		if (file_exists($pretty) && !empty($modSettings['pretty_enable_filters'])) {
			if (!function_exists('pretty_rewrite_buffer'))
				require_once($pretty);

			$context['pretty']['search_patterns'][]  = '~(<loc>)([^#<]+)~';
			$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';
			$context['pretty']['search_patterns'][]  = '~(">)([^#<]+)~';
			$context['pretty']['replace_patterns'][] = '~(">)([^<]+)~';

			$base_entries  = pretty_rewrite_buffer($base_entries);

			foreach ($files as $year)
				$topic_entries[$year] = pretty_rewrite_buffer($topic_entries[$year]);

			$one_file = pretty_rewrite_buffer($one_file);
		}

		// Создаем карту сайта (если ссылок больше $this->count, то делаем файл индекса)
		$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . "\n";
		if (count($url_list) > $this->count) {
			$base_entries = $header . '<urlset xmlns="' . $this->xmlns . '">' . $this->n . $base_entries . '</urlset>';
			$sitemap      = $boarddir . '/sitemap_main.xml';
			self::createFile($sitemap, $base_entries);

			foreach ($files as $year) {
				$topic_entries[$year] = $header . '<urlset xmlns="' . $this->xmlns . '">' . $this->n . $topic_entries[$year] . '</urlset>';
				$sitemap    = $boarddir . '/sitemap_' . $year . '.xml';
				self::createFile($sitemap, $topic_entries[$year]);
			}

			// Создаем файл индекса Sitemap
			$maps = '';
			$maps .= $this->t . '<sitemap>' . $this->n;
			$maps .= $this->t . $this->t . '<loc>' . $boardurl . '/sitemap_main.xml</loc>' . $this->n;
			$maps .= $this->t . $this->t . '<lastmod>' . self::getSitemapDate() . '</lastmod>' . $this->n;
			$maps .= $this->t . '</sitemap>' . $this->n;

			foreach ($files as $year) {
				$maps .= $this->t . '<sitemap>' . $this->n;
				$maps .= $this->t . $this->t . '<loc>' . $boardurl . '/sitemap_' . $year . '.xml</loc>' . $this->n;
				$maps .= $this->t . $this->t . '<lastmod>' . self::getSitemapDate() . '</lastmod>' . $this->n;
				$maps .= $this->t . '</sitemap>' . $this->n;
			}

			$index_data = $header . '<sitemapindex xmlns="' . $this->xmlns . '">' . $this->n . $maps . '</sitemapindex>';
			$index_file = $boarddir . '/sitemap.xml';
			self::createFile($index_file, $index_data);
		} else {
			$one_file = $header . '<urlset xmlns="' . $this->xmlns . '">' . $this->n . $one_file . '</urlset>';
			$sitemap  = $boarddir . '/sitemap.xml';
			self::createFile($sitemap, $one_file);
		}

		return true;
	}

	/**
	 * Создаем файл карты
	 *
	 * @param string $path — путь к файлу
	 * @param string $data — содержимое
	 * @return bool
	 */
	private static function createFile($path, $data)
	{
		if (!$fp = fopen($path, 'w'))
			return false;

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		// Если размер файла превышает 10 МБ, создадим и упакованную gz-версию
		if (filesize($path) > (10 * 1024 * 1024)) {
			$data = implode('', file($path));
			$gzdata = gzencode($data, 9);
			$fp = fopen($path . '.gz', 'w');
			fwrite($fp, $gzdata);
			fclose($fp);
		}

		return true;
	}

	/**
	 * Обработка дат
	 *
	 * @param int $timestamp
	 * @return string
	 */
	private static function getSitemapDate($timestamp = 0)
	{
		$timestamp = empty($timestamp) ? time() : $timestamp;
		$gmt       = substr(date("O", $timestamp), 0, 3) . ':00';
		$result    = date('Y-m-d\TH:i:s', $timestamp) . $gmt;

		return $result;
	}

	/**
	 * Определяем периодичность обновлений
	 *
	 * @param int $time
	 * @return string
	 */
	private static function getSitemapFrequency($time)
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
	 * Определяем приоритет индексирования
	 *
	 * @param int $time
	 * @return float
	 */
	private static function getSitemapPriority($time)
	{
		$diff = floor((time() - $time) / 60 / 60 / 24);

		if ($diff <= 30)
			return '0.8';
		elseif ($diff <= 60)
			return '0.6';
		elseif ($diff <= 90)
			return '0.4';
		else
			return '0.2';
	}
}
