<?php declare(strict_types=1);

/**
 * EhPortal.php
 *
 * @package EhPortal (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 03.02.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{Config, Utils};
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Robots\Generator;
use Bugo\Optimus\Tasks\Sitemap;

if (! defined('SMF'))
	die('No direct access...');

final class EhPortal extends AbstractAddon
{
	public const PACKAGE_ID = '[ChenZhen]:EhPortal';

	public static array $events = [
		self::ROBOTS_RULES,
		self::SITEMAP_LINKS,
	];

	public function __invoke(AddonEvent $event): void
	{
		match ($event->eventName()) {
			self::ROBOTS_RULES  => $this->changeRobots($event->getTarget()),
			self::SITEMAP_LINKS => $this->changeSitemap($event->getTarget()),
		};
	}

	public function changeRobots(object $generator): void
	{
		/* @var Generator $generator */
		$generator->customRules[] = "Allow: " . $generator->urlPath . "/*page=*";
	}

	public function changeSitemap(object $sitemap): void
	{
		$request = Utils::$smcFunc['db_query']('', '
			SELECT namespace
			FROM {db_prefix}sp_pages
			WHERE status = {int:status}
				AND (permission_set IN ({array_int:permissions})
				OR (permission_set = 0 AND {int:guests} IN (groups_allowed)))
			ORDER BY id_page DESC',
			[
				'status'      => 1, // The page must be active
				'permissions' => [1, 3], // The page must be available to guests
				'guests'      => -1
			]
		);

		while ($row = Utils::$smcFunc['db_fetch_assoc']($request)) {
			$url = Config::$scripturl . '?page=' . $row['namespace'];

			/* @var Sitemap $sitemap */
			$sitemap->links[] = [
				'loc' => $url
			];
		}

		Utils::$smcFunc['db_free_result']($request);
	}
}
