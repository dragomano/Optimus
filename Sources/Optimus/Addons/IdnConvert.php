<?php declare(strict_types=1);

/**
 * IdnConvert.php
 *
 * @package IdnConvert (Optimus)
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
use Bugo\Optimus\Tasks\Sitemap;

if (! defined('SMF'))
	die('No direct access...');

final class IdnConvert extends AbstractAddon
{
	public const PACKAGE_ID = 'Optimus:IdnConvert';

	public static array $events = [
		self::ROBOTS_RULES,
		self::SITEMAP_LINKS,
	];

	public function __invoke(AddonEvent $event): void
	{
		global $boardurl;

		if (iri_to_url($boardurl) === $boardurl)
			return;

		match ($event->eventName()) {
			self::ROBOTS_RULES  => $this->changeRobots(),
			self::SITEMAP_LINKS => $this->changeSitemap($event->getTarget()),
		};
	}

	public function changeRobots(): void
	{
		global $boardurl, $scripturl;

		$boardurl  = iri_to_url($boardurl);
		$scripturl = $boardurl . '/index.php';
	}

	public function changeSitemap(object $sitemap): void
	{
		/* @var Sitemap $sitemap */
		foreach ($sitemap->links as &$url) {
			$url['loc'] = iri_to_url($url['loc']);
		}
	}
}
