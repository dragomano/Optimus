<?php declare(strict_types=1);

namespace Bugo\Optimus\Addons;

use Bugo\Compat\Utils;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Utils\Input;

/**
 * A neutral test addon. It is copied into src/Sources/Optimus/Addons
 * by tests/Pest.php for the duration of the test run, so the addon
 * handling can be checked everywhere, including CI, without the
 * premium addon sources.
 */
class TestAddon extends AbstractAddon implements HasSettingsInterface
{
	public const PACKAGE_ID = 'Optimus:TestAddon';

	public static array $events = [
		self::HOOK_EVENT,
	];

	public function __invoke(AddonEvent $event): void
	{
	}

	public function getSettings(): array
	{
		return [
			['check', 'optimus_test_addon_enabled'],
		];
	}

	public function saveSettings(): void
	{
		if (! Input::isPost('optimus_test_addon_enabled'))
			return;

		Utils::$context['optimus_test_addon_saved'] = Input::filter('optimus_test_addon_enabled');
	}
}
