<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2026 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.1
 */

namespace Bugo\Optimus\Tasks;

use Bugo\Compat\{Cache\CacheApi, Config, Db};
use Bugo\Compat\Tasks\BackgroundTask;

if (! defined('SMF'))
	die('No direct access...'); // @codeCoverageIgnore

class PruneSearchTerms extends BackgroundTask
{
	private const WEEK = 7 * 24 * 60 * 60;

	public function execute(): bool
	{
		if (empty(Config::$modSettings['optimus_log_search'])) {
			return false;
		}

		$this->deleteOneTimePhrases();

		$limit = (int) (Config::$modSettings['optimus_search_terms_limit'] ?? 0);

		if ($limit > 0) {
			$this->deleteOverflow($limit);
		}

		CacheApi::put('optimus_search_terms', null);

		$this->scheduleNextRun();

		return true;
	}

	private function deleteOneTimePhrases(): void
	{
		Db::$db->query('
			DELETE FROM {db_prefix}optimus_search_terms
			WHERE hit <= 1'
		);
	}

	private function deleteOverflow(int $limit): void
	{
		Db::$db->query('
			DELETE FROM {db_prefix}optimus_search_terms
			WHERE id_term NOT IN (
				SELECT id_term
				FROM {db_prefix}optimus_search_terms
				ORDER BY hit DESC, id_term DESC
				LIMIT {int:limit}
			)',
			[
				'limit' => $limit,
			]
		);
	}

	private function scheduleNextRun(): void
	{
		Db::$db->query('
			DELETE FROM {db_prefix}background_tasks
			WHERE task_class = {string:task_class}',
			[
				'task_class' => '\\' . self::class,
			]
		);

		Db::$db->insert('insert',
			'{db_prefix}background_tasks',
			[
				'task_file'    => 'string-255',
				'task_class'   => 'string-255',
				'task_data'    => 'string',
				'claimed_time' => 'int',
			],
			[
				'$sourcedir/Optimus/Tasks/PruneSearchTerms.php',
				'\\' . self::class,
				'',
				time() + self::WEEK,
			],
			['id_task']
		);
	}
}
