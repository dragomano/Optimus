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
 * @version 23.01.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

if (! defined('SMF'))
	die('No direct access...');

class ExampleAddon extends AbstractAddon
{
	public const PACKAGE_ID = 'Optimus:ExampleAddon';

	public static array $events = [
		AddonInterface::HOOK_EVENT,
	];

	public function __invoke(AddonEvent $event): void
	{
		if ($event->eventName() === AddonInterface::HOOK_EVENT) {
			add_integration_function(
				'integrate_theme_context',
				self::class . '::hideLockedTopicsForSpiders#',
				false,
				__FILE__
			);
		}
	}

	public function hideLockedTopicsForSpiders(): void
	{
		global $context;

		if (empty($context['topicinfo']))
			return;

		if ($context['topicinfo']['locked'] || $context['topicinfo']['num_replies'] < 2) {
			$context['meta_tags'][] = ['name' => 'robots', 'content' => 'noindex,nofollow'];
		}
	}
}
