<?php declare(strict_types=1);

/**
 * DispatcherFactory.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Events;

use League\Event\EventDispatcher;

if (! defined('SMF'))
	die('No direct access...');

final class DispatcherFactory
{
	private static EventDispatcher $dispatcher;

	public function __invoke(): EventDispatcher
	{
		if (! isset(self::$dispatcher)) {
			self::$dispatcher = new EventDispatcher();
		}

		return self::$dispatcher;
	}
}
