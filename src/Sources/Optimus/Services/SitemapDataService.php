<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2026 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0
 */

namespace Bugo\Optimus\Services;

use Bugo\Compat\{Config, Db};
use Bugo\Optimus\Enums\Entity;
use Generator;

class SitemapDataService
{
	private const BATCH_SIZE = 500;

	private array $openBoards = [];

	private array $ignoredBoards = [];

	private ?int $startDate = null;

	public function __construct(private readonly int $startYear) {}

	public function getBoardLinks(): array
	{
		if (! empty(Config::$modSettings['recycle_board'])) {
			$this->ignoredBoards[] = (int) Config::$modSettings['recycle_board'];
		}

		$result = Db::$db->query(/** @lang text */ '
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
				AND m.poster_time >= {int:start_date}' : '') . '
			ORDER BY b.id_board DESC',
			[
				'ignored_boards' => $this->ignoredBoards,
				'empty_string'   => '',
				'num_posts'      => 0,
				'start_date'     => $this->getStartDate(),
			]
		);

		$links = [];
		while ($row = Db::$db->fetch_assoc($result)) {
			$this->openBoards[] = (int) $row['id_board'];

			if (empty(Config::$modSettings['optimus_sitemap_boards']))
				continue;

			$boardUrl = Entity::BOARD->buildUrl($row['id_board'] . '.0');

			$links[] = [
				'loc'     => $boardUrl,
				'lastmod' => $row['last_date'],
			];
		}

		Db::$db->free_result($result);

		return $links;
	}

	/**
	 * Streams topic links one by one, without accumulating the entire list in memory
	 */
	public function getTopicLinks(): Generator
	{
		if (empty($this->openBoards)) {
			return;
		}

		$tempCache = Db::$cache;
		Db::$cache = [];

		try {
			$allPages = ! empty(Config::$modSettings['optimus_sitemap_all_topic_pages']);
			$lastId   = null;

			do {
				$batch = $this->processTopicBatch($lastId, self::BATCH_SIZE, $allPages);

				foreach ($batch as $link) {
					yield $link;
				}

				$lastId = $batch->getReturn();
			} while ($lastId !== null);
		} finally {
			Db::$cache = $tempCache;
		}
	}

	public function getStartDate(): int
	{
		return $this->startDate ??= $this->startYear
			? gmmktime(0, 0, 0, 1, 1, $this->startYear)
			: 0;
	}

	/**
	 * @return Generator Yields the links of a single batch; the last processed topic id is available via getReturn()
	 */
	private function processTopicBatch(?int $lastId, int $limit, bool $allPages): Generator
	{
		$numReplies = (int) (Config::$modSettings['optimus_sitemap_topics_num_replies'] ?? 0);

		$result = Db::$db->query('
			SELECT t.id_topic, t.id_board, t.num_replies, t.id_first_msg, t.id_last_msg,
				GREATEST(m.poster_time, m.modified_time) AS last_date, m.subject,
				a.id_attach, a.filename, a.fileext, a.attachment_type
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
				LEFT JOIN {db_prefix}attachments AS a ON (a.id_msg = t.id_first_msg
					AND a.attachment_type = {int:attach_type})
					AND a.width > {int:attach_width}
					AND a.height > {int:attach_height}
					AND a.approved = {int:attach_approved}
			WHERE t.id_board IN ({array_int:boards})' . ($numReplies ? '
				AND t.num_replies >= {int:num_replies}' : '') .	($this->startYear ? '
				AND m.poster_time >= {int:start_date}' : '') . ($lastId !== null ? '
				AND t.id_topic < {int:last_id}' : '') . '
			ORDER BY t.id_topic DESC
			LIMIT {int:limit}',
			[
				'attach_type'     => 0,
				'attach_width'    => 0,
				'attach_height'   => 0,
				'attach_approved' => 1,
				'boards'          => $this->openBoards,
				'num_replies'     => $numReplies,
				'start_date'      => $this->getStartDate(),
				'last_id'         => $lastId ?? 0,
				'limit'           => $limit,
			]
		);

		$newLastId = null;

		while ($row = Db::$db->fetch_assoc($result)) {
			$newLastId = (int) $row['id_topic'];

			foreach ($this->buildTopicLinks($row, $allPages) as $link) {
				yield $link;
			}
		}

		Db::$db->free_result($result);

		return $newLastId;
	}

	private function buildTopicLinks(array $row, bool $allPages): array
	{
		$image = $this->getImagePart($row);

		if (! $allPages) {
			return [[
				'loc'     => $this->buildTopicUrl($row['id_topic']),
				'lastmod' => $row['last_date'],
			] + $image];
		}

		$messagesPerPage = (int) (Config::$modSettings['defaultMaxMessages'] ?? 20);
		$numPages        = (int) ceil(((int) $row['num_replies'] + 1) / $messagesPerPage);

		$links = [];

		for ($page = 0; $page < $numPages; $page++) {
			$links[] = [
				'loc'     => $this->buildTopicPageUrl((int) $row['id_topic'], $page, $messagesPerPage),
				'lastmod' => $row['last_date'],
			] + $image;
		}

		return $links;
	}

	private function getImagePart(array $row): array
	{
		if (empty(Config::$modSettings['optimus_sitemap_add_found_images']) || empty($row['id_attach'])) {
			return [];
		}

		if (! $this->isImageFile($row['fileext'])) {
			return [];
		}

		return ['image' => ['image:loc' => implode('', [
			Config::$scripturl . '?action=dlattach;topic=',
			$row['id_topic'] . '.0;attach=',
			$row['id_attach'] . ';image',
		])]];
	}

	private function buildTopicUrl(string $topicId): string
	{
		return $this->buildTopicPageUrl((int) $topicId, 0, 0);
	}

	private function isImageFile(string $extension): bool
	{
		return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
	}

	private function buildTopicPageUrl(int $topicId, int $page, int $messagesPerPage): string
	{
		$start  = $page * $messagesPerPage;
		$suffix = $start === 0 ? '.0' : '.' . $start;

		return Entity::TOPIC->buildUrl($topicId . $suffix);
	}
}
