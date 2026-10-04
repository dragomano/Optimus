<?php declare(strict_types=1);

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

/**
 * A neutral test addon. It is copied into src/Sources/Optimus/Addons
 * by tests/Pest.php for the duration of the test run, so the addon
 * handling can be checked everywhere, including CI, without the
 * premium addon sources.
 */
class DemoAddon extends AbstractAddon implements HasSettingsInterface
{
	public const PACKAGE_ID = 'Optimus:DemoAddon';

	public static array $events = [
		self::HOOK_EVENT,
	];

	public function __invoke(AddonEvent $event): void
	{
	}

	public function getSettings(): array
	{
		return [
			['text', 'optimus_demo_addon_name'],
		];
	}
}
