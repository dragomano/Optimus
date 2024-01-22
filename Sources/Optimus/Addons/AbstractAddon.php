<?php declare(strict_types=1);

/**
 * AbstractAddon.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\DispatcherFactory;
use League\Event\EventDispatcher;

abstract class AbstractAddon
{
	protected EventDispatcher $dispatcher;

	public function __construct()
	{
		$this->dispatcher = (new DispatcherFactory())();
	}
}