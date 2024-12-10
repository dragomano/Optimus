<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC1
 */

namespace Bugo\Optimus\Services;

use Bugo\Compat\{Config, Db};

class SitemapDataService
{
	private array $openBoards = [];

	private array $ignoredBoards = [];

	private array $links = [];

	private array $topics = [];

	private array $images = [];

	public function __construct(private readonly int $startYear) {}

	public function getBoardLinks(): array
	{
		if (! empty(Config::$modSettings['recycle_board'])) {
			$this->ignoredBoards[] = (int) Config::$modSettings['recycle_board'];
		}

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
				'start_year'     => $this->startYear,
			]
		);

		$links = [];
		while ($row = Db::$db->fetch_assoc($result)) {
			$this->openBoards[] = (int) $row['id_board'];

			if (empty(Config::$modSettings['optimus_sitemap_boards']))
				continue;

			$boardUrl = Config::$scripturl . '/board,' . $row['id_board'] . '.0.html';

			if (empty(Config::$modSettings['queryless_urls'])) {
				$boardUrl = Config::$scripturl . '?board=' . $row['id_board'] . '.0';
			}

			$links[] = [
				'loc'     => $boardUrl,
				'lastmod' => $row['last_date'],
			];
		}

		Db::$db->free_result($result);

		return $links;
	}

	public function getTopicLinks(): array
	{
		if (empty($this->openBoards))
			return [];

		$tempCache = Db::$cache;
		Db::$cache = [];

		$start = 0;
		$limit = 1000;

		$totalRows = $this->getTotalRows();

		while ($start < $totalRows) {
			$this->processTopicBatch($start, $limit);
			$start += $limit;
		}

		$this->processTopicPages();

		Db::$cache = $tempCache;

		return array_values($this->links);
	}

	private function getTotalRows(): int
	{
		return (int) (empty(Config::$modSettings['optimus_sitemap_all_topic_pages'])
			? (Config::$modSettings['totalTopics'] ?? 0)
			: (Config::$modSettings['totalMessages'] ?? 0)
		);
	}

	private function processTopicBatch(int $start, int $limit): void
	{
		$numReplies = (int) (Config::$modSettings['optimus_sitemap_topics_num_replies'] ?? 0);

		$result = Db::$db->query('', '
			SELECT t.id_topic, t.id_board, t.num_replies, t.id_first_msg, t.id_last_msg,
				GREATEST(m.poster_time, m.modified_time) AS last_date, m.subject,
				a.id_attach, a.filename, a.fileext, a.attachment_type
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
				LEFT JOIN {db_prefix}attachments AS a ON (a.id_msg = t.id_first_msg
					AND a.attachment_type = {int:attach_type})
					AND a.width <> {int:attach_width}
					AND a.height <> {int:attach_height}
					AND a.approved = {int:attach_approved}
			WHERE t.id_board IN ({array_int:boards})' . ($numReplies ? '
				AND t.num_replies >= {int:num_replies}' : '') .	($this->startYear ? '
				AND YEAR(FROM_UNIXTIME(m.poster_time)) >= {int:start_year}' : '') . '
			ORDER BY t.id_topic DESC, last_date
			LIMIT {int:start}, {int:limit}',
			[
				'attach_type'     => 0,
				'attach_width'    => 0,
				'attach_height'   => 0,
				'attach_approved' => 1,
				'boards'          => $this->openBoards,
				'num_replies'     => $numReplies,
				'start_year'      => $this->startYear,
				'start'           => $start,
				'limit'           => $limit,
			]
		);

		while ($row = Db::$db->fetch_assoc($result)) {
			$topicUrl = $this->buildTopicUrl($row['id_topic']);

			if (empty(Config::$modSettings['optimus_sitemap_all_topic_pages'])) {
				$this->links[$row['id_topic']] = ['loc' => $topicUrl, 'lastmod' => $row['last_date']];
			} else {
				$this->topics[$row['id_topic']] = [
					'url'         => $topicUrl,
					'last_date'   => $row['last_date'],
					'num_replies' => $row['num_replies'],
					'subject'     => $row['subject'],
				];
			}

			if (
				! empty(Config::$modSettings['optimus_sitemap_add_found_images'])
				&& ! empty($row['id_attach'])
				&& $this->isImageFile($row['fileext'])
			) {
				$this->images[$row['id_topic']] = [
					'loc' => implode('', [
						Config::$scripturl . '?action=dlattach;topic=',
						$row['id_topic'] . '.0;attach=',
						$row['id_attach'] . ';image',
					])
				];
			}
		}

		Db::$db->free_result($result);
	}

	private function buildTopicUrl(string $topicId): string
	{
		return $this->buildTopicPageUrl((int) $topicId, 0, 0);
	}

	private function isImageFile(string $extension): bool
	{
		return in_array($extension, ['jpg', 'png', 'gif', 'webp', 'svg']);
	}

	private function processTopicPages(): void
	{
		if (empty($this->topics))
			return;

		$messagesPerPage = (int) (Config::$modSettings['defaultMaxMessages'] ?? 20);

		foreach ($this->topics as $topicId => $topic) {
			$numPages = ceil(($topic['num_replies'] + 1) / $messagesPerPage);

			for ($page = 0; $page < $numPages; $page++) {
				$pageUrl = $this->buildTopicPageUrl($topicId, $page, $messagesPerPage);

				$entry = ['loc' => $pageUrl, 'lastmod' => $topic['last_date']];

				if (isset($this->images[$topicId])) {
					$entry['image'] = ['image:loc' => $this->images[$topicId]['loc']];
				}

				$this->links[] = $entry;
			}
		}
	}

	private function buildTopicPageUrl(int $topicId, int $page, int $messagesPerPage): string
	{
		$start = $page * $messagesPerPage;
		$suffix = $start === 0 ? '.0' : '.' . $start;

		if (empty(Config::$modSettings['queryless_urls'])) {
			return Config::$scripturl . '?topic=' . $topicId . $suffix;
		}

		return Config::$scripturl . '/topic,' . $topicId . $suffix . '.html';
	}
}