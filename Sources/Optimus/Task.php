<?php

declare(strict_types=1);

namespace Bugo\Optimus;

/**
 * Task.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.10
 */

class Task extends \SMF_BackgroundTask
{
	/**
	 * @return bool
	 */
	public function execute(): bool
	{
		global $sourcedir, $boarddir, $smcFunc;

		@ini_set('opcache.enable', '0');

		require_once($sourcedir . '/ScheduledTasks.php');
		require_once($sourcedir . '/Optimus/Subs.php');
		require_once($sourcedir . '/Optimus/Sitemap.php');

		loadEssentialThemeData();

		// Remove existing xml files if it is needed | Удаляем ранее созданные карты, если нужно
		if (is_on('optimus_remove_previous_xml_files'))
			array_map("unlink", glob($boarddir . "/sitemap*.xml*"));

		Sitemap::createXml();

		$frequency = 1;
		if (is_on('optimus_update_frequency')) {
			switch (op_config('optimus_update_frequency')) {
				case 1:
					$frequency = 3;
				break;
				case 2:
					$frequency = 7;
				break;
				case 3:
					$frequency = 14;
				break;
				default:
					$frequency = 30;
			}
		}

		$smcFunc['db_insert']('insert',
			'{db_prefix}background_tasks',
			array('task_file' => 'string-255', 'task_class' => 'string-255', 'task_data' => 'string', 'claimed_time' => 'int'),
			array('$sourcedir/Optimus/Task.php', '\Bugo\Optimus\Task', '', time() + ($frequency * 24 * 60 * 60)),
			array('id_task')
		);

		return true;
	}
}
