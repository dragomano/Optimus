<?php declare(strict_types=1);

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

class ExampleAddon extends AbstractAddon
{
	public const PACKAGE_ID = 'Optimus:ExampleAddon';

	public static array $events = [
		self::HOOK_EVENT,
	];

	public function __invoke(AddonEvent $event): void
	{
		// Test implementation
	}
}
