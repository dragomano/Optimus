<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC2
 */

namespace Bugo\Optimus;

use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Events\DispatcherFactory;
use Bugo\Optimus\Handlers\HandlerLoader;

if (! defined('SMF'))
	die('No direct access...');

final class Prime
{
	public function __construct()
	{
		new HandlerLoader();
	}

	public function __invoke(): void
	{
		(new DispatcherFactory())()->dispatchEvent(AddonInterface::HOOK_EVENT, $this);
	}
}
