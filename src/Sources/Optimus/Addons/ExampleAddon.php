<?php declare(strict_types=1);

/**
 * ExampleAddon.php
 *
 * @package ExampleAddon (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 28.02.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{IntegrationHook, Utils};
use Bugo\Optimus\Events\AddonEvent;

if (! defined('SMF'))
	die('No direct access...');

final class ExampleAddon extends AbstractAddon
{
	public const PACKAGE_ID = 'Optimus:ExampleAddon';

	public static array $events = [
		self::HOOK_EVENT,
	];

	public function __invoke(AddonEvent $event): void
	{
		if ($event->eventName() !== self::HOOK_EVENT)
			return;

		IntegrationHook::add(
			'integrate_theme_context', self::class . '::hideSomeTopicsFromSpiders#', false, __FILE__
		);
	}

	public function hideSomeTopicsFromSpiders(): void
	{
		if (empty(Utils::$context['topicinfo']))
			return;

		if (Utils::$context['topicinfo']['locked'] || Utils::$context['topicinfo']['num_replies'] < 2) {
			Utils::$context['meta_tags'][] = ['name' => 'robots', 'content' => 'noindex,nofollow'];
		}
	}
}
