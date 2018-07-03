<?php

/**
 * Class-OptimusSitemap.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2018 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 0.1
 */

if (!defined('PMX'))
	die('Hacking attempt...');

class OptimusSitemap
{
	private $t     = "\t";
	private $n     = "\n";
	private $xmlns = 'https://www.sitemaps.org/schemas/sitemap/0.9';

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
		global $modSettings, $sourcedir, $boardurl, $pmxcFunc, $scripturl, $context, $boarddir;

		// Master option
		if (empty($modSettings['optimus_sitemap_enable']))
			return;

		clearstatcache();

		// SEF enabled?
		$sef = !empty($modSettings['sef_enabled']) && function_exists('pmxsef_fixurl');

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
			$request = $pmxcFunc['db_query']('', '
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
			while ($row = $pmxcFunc['db_fetch_assoc']($request))
				$boards[] = $row;

			$pmxcFunc['db_free_result']($request);

			$last = array(0);

			// А вот насчет разделов можно и подумать...
			if (!empty($boards)) {
				foreach ($boards as $entry)	{
					$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];

					$base_links[] = $url_list[] = array(
						'loc'        => !empty($modSettings['queryless_urls']) ? $scripturl . '/board,' . $entry['id_board'] . '.0.html' : $scripturl . '?board=' . $entry['id_board'] . '.0',
						'lastmod'    => self::getSitemapDate($last_edit),
						'changefreq' => self::getSitemapFrequency($last_edit),
						'priority'   => self::getSitemapPriority($last_edit)
					);

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
			$base_entries .= $this->t . '<url>' . $this->n;
			$base_entries .= $this->t . $this->t . '<loc>' . ($sef ? pmxsef_fixurl($entry['loc']) : $entry['loc']) . '</loc>' . $this->n;

			if (!empty($entry['lastmod']))
				$base_entries .= $this->t . $this->t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $this->n;

			if (!empty($entry['changefreq']))
				$base_entries .= $this->t . $this->t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $this->n;

			if (!empty($entry['priority']))
				$base_entries .= $this->t . $this->t . '<priority>' . $entry['priority'] . '</priority>' . $this->n;

			$base_entries .= $this->t . '</url>' . $this->n;
		}

		// Topics
		$request = $pmxcFunc['db_query']('', '
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
		while ($row = $pmxcFunc['db_fetch_assoc']($request))
			$topics[$row['date']][$row['id_topic']] = $row;

		$pmxcFunc['db_free_result']($request);

		$years = $files = array();
		foreach ($topics as $year => $data) {
			$files[] = $year;

			foreach ($data as $topic => $entry) {
				$last_edit = empty($entry['modified_time']) ? $entry['poster_time'] : $entry['modified_time'];
				$url_list_topic = $scripturl . '?topic=' . $entry['id_topic'] . '.0';
				$url_list_topic = !empty($modSettings['queryless_urls']) ? $scripturl . '/topic,' . $entry['id_topic'] . '.0.html' : $url_list_topic;
				$years[count($topics[$year])] = $year;

				$topic_links[$year][] = $url_list[] = array(
					'loc'        => $url_list_topic,
					'lastmod'    => self::getSitemapDate($last_edit),
					'changefreq' => self::getSitemapFrequency($last_edit),
					'priority'   => self::getSitemapPriority($last_edit)
				);
			}

			$topic_entries[$year] = '';
			foreach ($topic_links[$year] as $entry) {
				$topic_entries[$year] .= $this->t . '<url>' . $this->n;
				$topic_entries[$year] .= $this->t . $this->t . '<loc>' . ($sef ? pmxsef_fixurl($entry['loc']) : $entry['loc']) . '</loc>' . $this->n;

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
			$url_list = array_merge($url_list, $this->custom_links);
		}

		// Обработаем все ссылки
		$one_file = '';
		foreach ($url_list as $entry) {
			$one_file .= $this->t . '<url>' . $this->n;
			$one_file .= $this->t . $this->t . '<loc>' . ($sef ? pmxsef_fixurl($entry['loc']) : $entry['loc']) . '</loc>' . $this->n;

			if (!empty($entry['lastmod']))
				$one_file .= $this->t . $this->t . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . $this->n;

			if (!empty($entry['changefreq']))
				$one_file .= $this->t . $this->t . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . $this->n;

			if (!empty($entry['priority']))
				$one_file .= $this->t . $this->t . '<priority>' . $entry['priority'] . '</priority>' . $this->n;

			$one_file .= $this->t . '</url>' . $this->n;
		}

		// Создаем карту сайта (если ссылок больше 10к, то делаем файл индекса)
		$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . "\n";
		if (count($url_list) > 10000) {
			$base_entries = $header . '<urlset xmlns="' . $this->xmlns . '">' . $this->n . $base_entries . '</urlset>';
			$sitemap      = $boarddir . '/sitemap_main.xml';
			self::createFile($sitemap, $base_entries);

			foreach ($files as $year) {
				$topic_entries[$year] = $header . '<urlset xmlns="' . $this->xmlns . '">' . $this->n . $topic_entries[$year] . '</urlset>';
				$sitemap    = $boarddir . '/sitemap_' . $year . '.xml';
				self::createFile($sitemap, $topic_entries[$year]);
				self::checkFilesize($sitemap);
			}

			// Создаем файл индекса Sitemap
			$maps = '';
			$maps .= $this->t . '<sitemap>' . $this->n;
			$maps .= $this->t . $this->t . '<loc>' . $boardurl . '/sitemap_main.xml</loc>' . $this->n;
			$maps .= $this->t . $this->t . '<lastmod>' . self::getSitemapDate() . '</lastmod>' . $this->n;
			$maps .= $t . '</sitemap>' . $this->n;

			foreach ($files as $year) {
				$maps .= $t . '<sitemap>' . $this->n;
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

		return true;
	}

	/**
	 * Если размер файла превышает 10 МБ (ограничение Яндекса), отправляем запись в Журнал ошибок
	 *
	 * @param string $file
	 * @return bool
	 */
	private static function checkFilesize($file)
	{
		global $txt;

		clearstatcache();

		if (filesize($file) > (10 * 1024 * 1024))
			log_error(sprintf($txt['optimus_sitemap_size_limit'], pathinfo($file, PATHINFO_BASENAME)) . $txt['optimus_sitemap_rec'], 'general');

		return;
	}

	/**
	 * Обработка дат
	 *
	 * @param int $timestamp
	 * @return string
	 */
	private static function getSitemapDate($timestamp)
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

/**
 * Вызов генерации карты через Диспетчер задач
 *
 * @return void
 */
function scheduled_optimus_sitemap()
{
	// Additional links for Sitemap
	$urls = array(
		array(
			'loc'          => 'https://www.example.com',
			//'lastmod'    => time(),
			//'changefreq' => weekly,
			//'priority'   => 0.8
		)
	);

	//$sitemap = new OptimusSitemap(false, $urls);
	$sitemap = new OptimusSitemap();
	return $sitemap->create();
}
