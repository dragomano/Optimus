<?php declare(strict_types=1);

/**
 * LightPortal.php
 *
 * @package LightPortal (Optimus)
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

final class LightPortal extends AbstractAddon
{
	public const PACKAGE_ID = 'Bugo:LightPortal';

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
		$generator->customRules[] = "Allow: " . $generator->urlPath . "/*" . LP_PAGE_PARAM;
	}

	public function changeSitemap(object $sitemap): void
	{
		$startYear = (int) (Config::$modSettings['optimus_start_year'] ?? 0);

		$request = Utils::$smcFunc['db_query']('', '
			SELECT page_id, alias, GREATEST(created_at, updated_at) AS date
			FROM {db_prefix}lp_pages
			WHERE status = {int:status}
				AND created_at <= {int:current_time}
				AND permissions IN ({array_int:permissions})' . ($startYear ? '
				AND YEAR(FROM_UNIXTIME(created_at)) >= {int:start_year}' : '') . '
			ORDER BY page_id DESC',
			[
				'status'       => 1, // The page must be active
				'current_time' => time(),
				'permissions'  => [1, 3], // The page must be available to guests
				'start_year'   => $startYear
			]
		);

		while ($row = Utils::$smcFunc['db_fetch_assoc']($request)) {
			$url = Config::$scripturl . '?' . (Config::$modSettings['lp_page_param'] ?? 'page') . '=' . $row['alias'];

			/* @var Sitemap $sitemap */
			$sitemap->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date']
			];
		}

		Utils::$smcFunc['db_free_result']($request);
	}
}
