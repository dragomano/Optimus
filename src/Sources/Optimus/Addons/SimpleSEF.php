<?php declare(strict_types=1);

/**
 * @package SimpleSEF (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 26.05.25
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\Config;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;
use League\Event\ListenerPriority;

if (! defined('SMF'))
	die('No direct access...');

final class SimpleSEF extends AbstractAddon
{
	public const PACKAGE_ID = 'slammeddime:simplesef';

	public const PRIORITY = ListenerPriority::HIGH;

	public static array $events = [
		self::ROBOTS_RULES,
		self::CREATE_SEF_URLS,
	];

	public function __invoke(AddonEvent $event): void
	{
		if (empty(Config::$modSettings['simplesef_enable']))
			return;

		if (! empty(Config::$modSettings['optimus_remove_index_php'])) {
			Config::updateModSettings(['optimus_remove_index_php' => 0]);
		}

		match ($event->eventName()) {
			self::ROBOTS_RULES    => $this->changeRobots($event->getTarget()),
			self::CREATE_SEF_URLS => $this->createSefUrls($event->getTarget()),
		};
	}

	public function changeRobots(RobotsGenerator $generator): void
	{
		$generator->useSef = ! empty(Config::$modSettings['simplesef_enable'])
			&& is_file(dirname(__DIR__, 2) . '/SimpleSEF.php');
	}

	public function createSefUrls(SitemapGenerator $generator): void
	{
		$engine = new \SimpleSEF();
		$method = method_exists('\SimpleSEF', 'getSefUrl') ? 'getSefUrl' : 'create_sef_url';

		foreach ($generator->links as &$url) {
			$url['loc'] = $engine->$method($url['loc']);
		}
	}
}
