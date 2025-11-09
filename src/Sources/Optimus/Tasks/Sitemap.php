<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Tasks;

use Bugo\Compat\{Config, Db};
use Bugo\Compat\Tasks\BackgroundTask;
use Bugo\Optimus\Events\DispatcherFactory;
use Bugo\Optimus\Services\FileSystem;
use Bugo\Optimus\Services\SitemapDataService;
use Bugo\Optimus\Services\SitemapGenerator;
use Bugo\Optimus\Services\XmlGenerator;

if (! defined('SMF'))
	die('No direct access...');

class Sitemap extends BackgroundTask
{
	public function execute(): bool
	{
		if (empty(Config::$modSettings['optimus_sitemap_enable']))
			return false;

		$startYear = (int) (Config::$modSettings['optimus_start_year'] ?? 0);

		$generator = new SitemapGenerator(
			new SitemapDataService($startYear),
			new FileSystem(Config::$boarddir),
			new XmlGenerator(Config::$scripturl),
			(new DispatcherFactory())(),
			$startYear
		);

		$result = $generator->generate();

		if ($result) {
			$this->scheduleNextRun();
		}

		return $result;
	}

	private function scheduleNextRun(): void
	{
		$interval = $this->getTaskUpdateIntervalInDays() * 24 * 60 * 60;

		Db::$db->insert('insert',
			'{db_prefix}background_tasks',
			[
				'task_file'    => 'string-255',
				'task_class'   => 'string-255',
				'task_data'    => 'string',
				'claimed_time' => 'int',
			],
			[
				'$sourcedir/Optimus/Tasks/Sitemap.php',
				'\\' . self::class,
				'',
				time() + $interval,
			],
			['id_task']
		);
	}

	private function getTaskUpdateIntervalInDays(): int
	{
		if (empty(Config::$modSettings['optimus_update_frequency']))
			return 1;

		return match (Config::$modSettings['optimus_update_frequency']) {
			1 => 3,
			2 => 7,
			3 => 14,
			default => 30,
		};
	}
}
