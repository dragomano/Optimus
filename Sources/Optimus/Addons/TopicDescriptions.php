<?php declare(strict_types=1);

/**
 * TopicDescriptions.php
 *
 * @package TopicDescriptions (Optimus)
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

class TopicDescriptions extends AbstractAddon
{
	public const PACKAGE_ID = 'runic:TopicDescriptions';

	public static array $events = [
		AddonInterface::HOOK_EVENT,
	];

	public function __invoke(AddonEvent $event): void
	{
		global $context;

		if ($event->eventName() !== AddonInterface::HOOK_EVENT)
			return;

		if (empty($context['topic_description']))
			return;

		$context['meta_description'] = $context['topic_description'];
	}
}
