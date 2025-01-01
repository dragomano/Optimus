<?php declare(strict_types=1);

/**
 * @package EhPortal (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 01.12.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{Config, Db};
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;

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

	public function changeRobots(RobotsGenerator $robots): void
	{
		$robots->customRules[] = "Allow: " . $robots->urlPath . "/*page=*";
	}

	public function changeSitemap(SitemapGenerator $sitemap): void
	{
		$result = Db::$db->query('', '
			SELECT namespace
			FROM {db_prefix}sp_pages
			WHERE status = {int:status}
				AND (permission_set IN ({array_int:permissions})
				OR (permission_set = 0 AND {int:guests} IN (groups_allowed)))
			ORDER BY id_page DESC',
			[
				'status'      => 1, // The page must be active
				'permissions' => [1, 3], // The page must be available to guests
				'guests'      => -1,
			]
		);

		while ($row = Db::$db->fetch_assoc($result)) {
			$url = Config::$scripturl . '?page=' . $row['namespace'];

			$sitemap->links[] = [
				'loc' => $url,
			];
		}

		Db::$db->free_result($result);
	}
}
