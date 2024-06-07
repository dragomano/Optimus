<?php declare(strict_types=1);

/**
 * @package SimpleSEF (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 07.06.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\Config;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Robots\Generator;
use Bugo\Optimus\Tasks\Sitemap;

if (! defined('SMF'))
	die('No direct access...');

final class SimpleSEF extends AbstractAddon
{
	public const PACKAGE_ID = 'slammeddime:simplesef';

	public static array $events = [
		self::ROBOTS_RULES,
		self::CREATE_SEF_URLS,
	];

	public function __invoke(AddonEvent $event): void
	{
		if (empty(Config::$modSettings['simplesef_enable']))
			return;

		if (! empty(Config::$modSettings['optimus_remove_index_php']))
			Config::updateModSettings(['optimus_remove_index_php' => 0]);

		match ($event->eventName()) {
			self::ROBOTS_RULES    => $this->changeRobots($event->getTarget()),
			self::CREATE_SEF_URLS => $this->createSefLinks($event->getTarget()),
		};
	}

	public function changeRobots(Generator $generator): void
	{
		$generator->useSef = ! empty(Config::$modSettings['simplesef_enable'])
			&& is_file(dirname(__DIR__, 2) . '/SimpleSEF.php');
	}

	public function createSefLinks(Sitemap $sitemap): void
	{
		$engine = new \SimpleSEF();
		$method = method_exists('\SimpleSEF', 'getSefUrl') ? 'getSefUrl' : 'create_sef_url';

		foreach ($sitemap->links as &$url) {
			$url['loc'] = $engine->$method($url['loc']);
		}
	}
}
