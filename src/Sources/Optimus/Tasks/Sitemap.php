<?php declare(strict_types=1);

/**
 * Sitemap.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Tasks;

use Bugo\Compat\{Config, Database as Db, IntegrationHook};
use Bugo\Compat\{Sapi, Theme, Utils};
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Events\DispatcherFactory;
use League\Event\EventDispatcher;
use SMF_BackgroundTask;

if (! defined('SMF'))
	die('No direct access...');

final class Sitemap extends SMF_BackgroundTask
{
	public const MAX_ITEMS = 50000;

	public int $startYear = 0;

	public array $links = [];

	public string $content = '';

	private array $openBoards = [];

	private array $ignoredBoards = [];

	private EventDispatcher $dispatcher;

	public function __construct($details)
	{
		parent::__construct($details);

		$this->dispatcher = (new DispatcherFactory())();

		$this->startYear = (int) (Config::$modSettings['optimus_start_year'] ?? 0);
	}

	public function execute(): bool
	{
		@ini_set('opcache.enable', '0');

		Theme::loadEssential();

		if (! empty(Config::$modSettings['optimus_remove_previous_xml_files'])) {
			array_map("unlink", glob(Config::$boarddir . "/sitemap*.xml*"));
		}

		$this->createXml();

		$frequency = 1;
		if (! empty(Config::$modSettings['optimus_update_frequency'])) {
			$frequency = match (Config::$modSettings['optimus_update_frequency']) {
				1 => 3,
				2 => 7,
				3 => 14,
				default => 30,
			};
		}

		Db::$db->insert('insert',
			'{db_prefix}background_tasks',
			[
				'task_file' => 'string-255',
				'task_class' => 'string-255',
				'task_data' => 'string',
				'claimed_time' => 'int'
			],
			[
				'$sourcedir/Optimus/Tasks/Sitemap.php',
				'\\' . self::class,
				'',
				time() + ($frequency * 24 * 60 * 60)
			],
			['id_task']
		);

		return true;
	}

	public function createXml(): void
	{
		ignore_user_abort(true);

		Sapi::setTimeLimit();

		Config::$modSettings['disableQueryCheck'] = true;
		Config::$modSettings['pretty_bufferusecache'] = false;

		$maxItems = Config::$modSettings['optimus_sitemap_items_display'] ?? self::MAX_ITEMS;

		$sitemapCounter = 0;

		$items = [];

		$getLinks = fn() => yield from $this->getLinks();

		foreach ($getLinks() as $counter => $entry) {
			if (! empty($counter) && $counter % $maxItems == 0)
				$sitemapCounter++;

			$entry['lastmod'] = (int) ($entry['lastmod'] ?? 0);

			$items[$sitemapCounter][] = [
				'loc'        => $entry['loc'],
				'lastmod'    => empty($entry['lastmod']) ? null : $this->getDateIso8601($entry['lastmod']),
				'changefreq' => empty($entry['lastmod']) ? null : $this->getFrequency($entry['lastmod']),
				'priority'   => empty($entry['lastmod']) ? null : $this->getPriority($entry['lastmod']),
				'image'      => empty(Config::$modSettings['optimus_sitemap_add_found_images'])
					? null
					: $entry['image'] ?? null
			];
		}

		if (empty($items))
			return;

		// The update frequency of the main page
		if (empty(Config::$modSettings['optimus_main_page_frequency']))
			$items[0][0]['changefreq'] = 'always';

		// The priority of the main page
		$items[0][0]['priority'] = '1.0';

		Utils::$context['sitemap'] = [];

		Theme::loadTemplate('Optimus');

		if ($sitemapCounter > 0) {
			$gzMaps = [];
			for ($number = 0; $number <= $sitemapCounter; $number++) {
				Utils::$context['sitemap'] = $items[$number];

				ob_start();
				template_sitemap_xml();
				$this->content = ob_get_clean();

				// Some mods should rewrite full content (PrettyURLs, etc.)
				$this->dispatcher->dispatch(new AddonEvent(AddonInterface::SITEMAP_CONTENT, $this));

				$gzMaps[$number] = $this->createFile(
					Config::$boarddir . '/sitemap_' . $number . '.xml', $this->content
				);
			}

			Utils::$context['sitemap'] = [];
			for ($number = 0; $number <= $sitemapCounter; $number++) {
				$gz = empty($gzMaps[$number]) ? '' : '.gz';
				Utils::$context['sitemap'][$number]['loc'] = Config::$boardurl . '/sitemap_' . $number . '.xml' . $gz;
			}

			ob_start();
			template_sitemapindex_xml();
			$this->content = ob_get_clean();
		} else {
			Utils::$context['sitemap'] = $items[0];

			ob_start();
			template_sitemap_xml();
			$this->content = ob_get_clean();

			// Some mods should rewrite full content (PrettyURLs, etc.)
			$this->dispatcher->dispatch(new AddonEvent(AddonInterface::SITEMAP_CONTENT, $this));
		}

		$this->createFile(Config::$boarddir . '/sitemap.xml', $this->content);

		ignore_user_abort(false);
	}

	public function getLastDate(array $links): int
	{
		if (empty($links))
			return time();

		$data = array_values(array_values($links));

		$dates = [];
		foreach ($data as $value) {
			$dates[] = (int) $value['lastmod'];
		}

		return max($dates);
	}

	public function getLinks(): array
	{
		$this->links = array_merge($this->getBoardLinks(), $this->getTopicLinks());

		// Modders can add custom links
		$this->dispatcher->dispatch(new AddonEvent(AddonInterface::SITEMAP_LINKS, $this));

		// External integrations
		IntegrationHook::call('integrate_optimus_sitemap_links', [&$this->links]);

		// Adding the main page
		$home = [
			'loc'     => Config::$boardurl . '/',
			'lastmod' => empty(Config::$modSettings['optimus_main_page_frequency'])
				? time()
				: $this->getLastDate($this->links)
		];

		// Modders can process links with SEF handler
		$this->dispatcher->dispatch(new AddonEvent(AddonInterface::CREATE_SEF_URLS, $this));

		array_unshift($this->links, $home);

		return $this->links;
	}

	private function getBoardLinks(): array
	{
		if (! empty(Config::$modSettings['recycle_board']))
			$this->ignoredBoards[] = (int) Config::$modSettings['recycle_board'];

		$result = Db::$db->query('', /** @lang text */ '
			SELECT b.id_board, GREATEST(m.poster_time, m.modified_time) AS last_date
			FROM {db_prefix}boards AS b
				LEFT JOIN {db_prefix}messages AS m ON (b.id_last_msg = m.id_msg)
			WHERE EXISTS (
					SELECT DISTINCT bpv.id_board
					FROM {db_prefix}board_permissions_view bpv
					WHERE bpv.id_group = -1
						AND bpv.deny = 0
						AND bpv.id_board = b.id_board
				)' . (empty($this->ignoredBoards) ? '' : '
				AND b.id_board NOT IN ({array_int:ignored_boards})') . '
				AND b.redirect = {string:empty_string}
				AND b.num_posts > {int:num_posts}' . ($this->startYear ? '
				AND YEAR(FROM_UNIXTIME(m.poster_time)) >= {int:start_year}' : '') . '
			ORDER BY b.id_board DESC',
			[
				'ignored_boards' => $this->ignoredBoards,
				'empty_string'   => '',
				'num_posts'      => 0,
				'start_year'     => $this->startYear
			]
		);

		$links = [];
		while ($row = Db::$db->fetch_assoc($result)) {
			$this->openBoards[] = $row['id_board'];

			if (! empty(Config::$modSettings['optimus_sitemap_boards'])) {
				$boardUrl = Config::$scripturl . '?board=' . $row['id_board'] . '.0';

				if (! empty(Config::$modSettings['queryless_urls']))
					$boardUrl = Config::$scripturl . '/board,' . $row['id_board'] . '.0.html';

				$links[] = [
					'loc'     => $boardUrl,
					'lastmod' => $row['last_date']
				];
			}
		}

		Db::$db->free_result($result);

		return $links;
	}

	private function getTopicLinks(): array
	{
		if (empty($this->openBoards))
			return [];

		$start = 0;
		$limit = 1000;

		// Don't allow the cache to get too full
		$tempCache = Db::$cache;
		Db::$cache = [];

		$this->startYear  = (int) (Config::$modSettings['optimus_start_year'] ?? 0);
		$numReplies = (int) (Config::$modSettings['optimus_sitemap_topics_num_replies'] ?? 0);
		$totalRows  = (int) (empty(Config::$modSettings['optimus_sitemap_all_topic_pages'])
			? (Config::$modSettings['totalTopics'] ?? 0)
			: (Config::$modSettings['totalMessages'] ?? 0));

		$links  = [];
		$topics = [];
		$images = [];

		$messagesPerPage = (int) (Config::$modSettings['defaultMaxMessages'] ?? 0);

		while ($start < $totalRows) {
			@set_time_limit(600);
			if (function_exists('apache_reset_timeout'))
				@apache_reset_timeout();

			if (! empty(Config::$modSettings['optimus_sitemap_all_topic_pages'])) {
				$result = Db::$db->query('', '
					SELECT t.id_topic, t.num_replies,
						m.id_msg, GREATEST(m.poster_time, m.modified_time) AS last_date' . (
							empty(Config::$modSettings['optimus_sitemap_add_found_images']) ? '' : ',
						a.id_attach, a.filename') . '
					FROM {db_prefix}messages AS m
						INNER JOIN {db_prefix}topics AS t ON (t.id_topic = m.id_topic)' . (
							empty(Config::$modSettings['optimus_sitemap_add_found_images']) ? '' : '
						LEFT JOIN {db_prefix}attachments AS a ON (a.id_msg = t.id_first_msg
							AND a.attachment_type = 0
							AND a.width <> 0
							AND a.height <> 0
							AND a.approved = 1
						)') . '
					WHERE t.id_board IN ({array_int:open_boards})
						AND t.num_replies >= {int:num_replies}
						AND t.approved = {int:is_approved}' . ($this->startYear ? '
						AND YEAR(FROM_UNIXTIME(GREATEST(m.poster_time, m.modified_time))) >= {int:start_year}' : '') . '
					ORDER BY t.id_topic DESC, last_date
					LIMIT {int:start}, {int:limit}',
					[
						'open_boards' => $this->openBoards,
						'num_replies' => $numReplies,
						'is_approved' => 1,
						'start_year'  => $this->startYear,
						'start'       => $start,
						'limit'       => $limit
					]
				);

				while ($row = Db::$db->fetch_assoc($result)) {
					$totalPages = ceil($row['num_replies'] / $messagesPerPage);
					$pageStart = 0;

					if (! empty($row['id_attach']) && ! isset($images[$row['id_topic']])) {
						$images[$row['id_topic']] = [
							'loc'   => Config::$scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach='
								. $row['id_attach'] . ';image',
							'title' => $row['filename']
						];
					}

					if (empty($totalPages)) {
						$topics[$row['id_topic']][$pageStart][$row['id_msg']] = $row['last_date'];
					} else {
						for ($i = 0; $i <= $totalPages; $i++) {
							$topics[$row['id_topic']][$pageStart][$row['id_msg']] = $row['last_date'];

							if (count($topics[$row['id_topic']][$pageStart]) <= $messagesPerPage)
								break;

							$topics[$row['id_topic']][$pageStart] = array_slice(
								$topics[$row['id_topic']][$pageStart], 0, $messagesPerPage, true
							);

							$pageStart += $messagesPerPage;
						}
					}
				}
			} else {
				$result = Db::$db->query('', '
					SELECT t.id_topic, GREATEST(m.poster_time, m.modified_time) AS last_date' . (
						empty(Config::$modSettings['optimus_sitemap_add_found_images']) ? '' : ',
						a.id_attach, a.filename') . '
					FROM {db_prefix}topics AS t
						INNER JOIN {db_prefix}messages AS m ON (t.id_last_msg = m.id_msg)' . (
							empty(Config::$modSettings['optimus_sitemap_add_found_images']) ? '' : '
						LEFT JOIN {db_prefix}attachments AS a ON (a.id_msg = t.id_first_msg
							AND a.attachment_type = 0
							AND a.width <> 0
							AND a.height <> 0
							AND a.approved = 1
						)') . '
					WHERE t.id_board IN ({array_int:open_boards})
						AND t.num_replies >= {int:num_replies}
						AND t.approved = {int:is_approved}' . ($this->startYear ? '
						AND YEAR(FROM_UNIXTIME(GREATEST(m.poster_time, m.modified_time))) >= {int:start_year}' : '') . '
					ORDER BY t.id_topic DESC, last_date DESC
					LIMIT {int:start}, {int:limit}',
					[
						'open_boards' => $this->openBoards,
						'num_replies' => $numReplies,
						'is_approved' => 1,
						'start_year'  => $this->startYear,
						'start'       => $start,
						'limit'       => $limit
					]
				);

				while ($row = Db::$db->fetch_assoc($result)) {
					$topicUrl = Config::$scripturl . '?topic=' . $row['id_topic'] . '.0';

					if (! empty(Config::$modSettings['queryless_urls']))
						$topicUrl = Config::$scripturl . '/topic,' . $row['id_topic'] . '.0.html';

					if (! empty($row['id_attach']) && ! isset($images[$row['id_topic']])) {
						$images[$row['id_topic']] = [
							'loc'   => Config::$scripturl . '?action=dlattach;topic=' . $row['id_topic'] . '.0;attach='
								. $row['id_attach'] . ';image',
							'title' => $row['filename']
						];
					}

					$links[$row['id_topic']] = [
						'loc'     => $topicUrl,
						'lastmod' => $row['last_date'],
						'image'   => $images[$row['id_topic']] ?? []
					];
				}
			}

			Db::$db->free_result($result);

			$start += $limit;
		}

		foreach ($topics as $topic_id => $topic_data) {
			foreach ($topic_data as $pageStart => $dates) {
				$topicUrl = empty(Config::$modSettings['queryless_urls'])
					? Config::$scripturl . '?topic=' . $topic_id . '.' . $pageStart
					: Config::$scripturl . '/topic,' . $topic_id . '.' . $pageStart . '.html';

				$links[] = [
					'loc'     => $topicUrl,
					'lastmod' => max($dates),
					'image'   => $images[$topic_id] ?? []
				];
			}
		}

		// Restore the cache
		Db::$cache = $tempCache;

		return array_values($links);
	}

	private function getDateIso8601(int $timestamp): string
	{
		if (empty($timestamp))
			return '';

		$gmt = substr(date("O", $timestamp), 0, 3) . ':00';

		return date('Y-m-d\TH:i:s', $timestamp) . $gmt;
	}

	private function getFrequency(int $timestamp): string
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

	private function getPriority(int $timestamp): string
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

	private function createFile(string $path, string $data): bool
	{
		fclose(fopen($path, "a+b"));

		if (! $fp = fopen($path, "w+b"))
			return false;

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		fflush($fp);
		flock($fp, LOCK_UN);
		fclose($fp);

		// If filesize > 50 MB, then create sitemap.xml.gz version
		if (function_exists('gzencode') && filesize($path) > (50 * 1024 * 1024)) {
			fclose(fopen($path . '.gz', "a+b"));

			if (! $fpgz = fopen($path . '.gz', 'w+b'))
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
