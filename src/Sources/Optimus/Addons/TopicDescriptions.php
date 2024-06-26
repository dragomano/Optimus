<?php declare(strict_types=1);

/**
 * @package TopicDescriptions (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 03.02.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\Utils;
use Bugo\Optimus\Events\AddonEvent;

if (! defined('SMF'))
	die('No direct access...');

final class TopicDescriptions extends AbstractAddon
{
	public const PACKAGE_ID = 'runic:TopicDescriptions';

	public static array $events = [
		self::HOOK_EVENT,
	];

	public function __invoke(AddonEvent $event): void
	{
		if ($event->eventName() !== self::HOOK_EVENT || empty(Utils::$context['topic_description']))
			return;

		Utils::$context['meta_description'] = Utils::$context['topic_description'];
	}
}
