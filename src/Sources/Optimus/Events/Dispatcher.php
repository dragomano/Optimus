<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC3
 */

namespace Bugo\Optimus\Events;

use League\Event\EventDispatcher;

final class Dispatcher extends EventDispatcher
{
	public function dispatchEvent(string $name, mixed $target): object
	{
		return $this->dispatch(new AddonEvent($name, $target));
	}
}
