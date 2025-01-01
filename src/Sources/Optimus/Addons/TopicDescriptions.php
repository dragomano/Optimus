<?php declare(strict_types=1);

/**
 * @package TopicDescriptions (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 08.11.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{IntegrationHook, Utils};
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
		if ($event->eventName() !== self::HOOK_EVENT)
			return;

		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::useTopicDescription#', false, __FILE__
		);
	}

	public function useTopicDescription(): void
	{
		if (empty(Utils::$context['topicinfo']['description']))
			return;

		Utils::$context['meta_description'] = Utils::$context['topicinfo']['description'];
	}
}
