<?php declare(strict_types=1);

/**
 * SimpleSEF.php
 *
 * @package SimpleSEF (Optimus)
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
use Bugo\Optimus\Robots\Generator;
use Bugo\Optimus\Tasks\Sitemap;

if (! defined('SMF'))
	die('No direct access...');

class SimpleSEF extends AbstractAddon
{
	public const PACKAGE_ID = 'slammeddime:simplesef';

	public static array $events = [
		self::ROBOTS_RULES,
		self::CREATE_SEF_URLS,
	];

	public function __invoke(AddonEvent $event): void
	{
		global $modSettings;

		if (empty($modSettings['simplesef_enable']))
			return;

		if (! empty($modSettings['optimus_remove_index_php']))
			updateSettings(['optimus_remove_index_php' => 0]);

		match ($event->eventName()) {
			self::ROBOTS_RULES  => $this->changeRobots($event->getTarget()),
			self::CREATE_SEF_URLS => $this->createSefLinks($event->getTarget()),
		};
	}

	public function changeRobots(object $generator): void
	{
		global $modSettings;

		/* @var Generator $generator */
		$generator->useSef = ! empty($modSettings['simplesef_enable'])
			&& is_file(dirname(__DIR__, 2) . '/SimpleSEF.php');
	}

	public function createSefLinks(object $sitemap): void
	{
		$sef = new \SimpleSEF();
		$method = method_exists('\SimpleSEF', 'getSefUrl') ? 'getSefUrl' : 'create_sef_url';

		/* @var Sitemap $sitemap */
		foreach ($sitemap->links as &$url) {
			$url['loc'] = $sef->$method($url['loc']);
		}
	}
}
