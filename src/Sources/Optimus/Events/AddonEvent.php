<?php declare(strict_types=1);

/**
 * AddonEvent.php
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

use League\Event\HasEventName;

if (! defined('SMF'))
	die('No direct access...');

final class AddonEvent implements HasEventName
{
	public function __construct(
		private string $name,
		private object $target
	) {}

	public function eventName(): string
	{
		return $this->name;
	}

	public function getTarget(): object
	{
		return $this->target;
	}
}
