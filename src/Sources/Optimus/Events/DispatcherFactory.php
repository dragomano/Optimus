<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC4
 */

namespace Bugo\Optimus\Events;

if (! defined('SMF'))
	die('No direct access...');

final class DispatcherFactory
{
	private static ?Dispatcher $dispatcher = null;

	public function __invoke(): Dispatcher
	{
		if (self::$dispatcher === null) {
			self::$dispatcher = new Dispatcher();
		}

		return self::$dispatcher;
	}
}
