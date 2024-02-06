<?php declare(strict_types=1);

/**
 * EzPortal.php
 *
 * @package EzPortal (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 06.02.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{Config, Database as Db};
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Robots\Generator;
use Bugo\Optimus\Tasks\Sitemap;
use function MakeSEOUrl;

if (! defined('SMF'))
	die('No direct access...');

final class EzPortal extends AbstractAddon
{
	public const PACKAGE_ID = 'vbgamer45:ezportal';

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
		global $ezpSettings;

		/* @var Generator $generator */
		if (! empty($ezpSettings['ezp_pages_seourls']))
			$generator->customRules[] = "Allow: " . $generator->urlPath . "/pages/";
		else
			$generator->customRules[] = "Allow: " . $generator->urlPath . "/*ezportal;sa=page;p=*";
	}

	public function changeSitemap(object $sitemap): void
	{
		global $ezpSettings;

		$request = Db::$db->query('', '
			SELECT id_page, date, title, permissions
			FROM {db_prefix}ezp_page
			WHERE {int:guests} IN (permissions)
			ORDER BY id_page DESC',
			[
				'guests' => -1 // The page must be available to guests
			]
		);

		while ($row = Db::$db->fetch_assoc($request)) {
			if (! empty($ezpSettings['ezp_pages_seourls']) && function_exists('MakeSEOUrl')) {
				$url = Config::$boardurl . '/pages/' . MakeSEOUrl($row['title']) . '-' . $row['id_page'];
			} else {
				$url = Config::$scripturl . '?action=ezportal;sa=page;p=' . $row['id_page'];
			}

			/* @var Sitemap $sitemap */
			$sitemap->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date']
			];
		}

		Db::$db->free_result($request);
	}
}
