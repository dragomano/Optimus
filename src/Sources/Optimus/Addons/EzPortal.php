<?php declare(strict_types=1);

/**
 * @package EzPortal (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 02.01.25
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{Config, Db};
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;

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

	public function changeRobots(RobotsGenerator $robots): void
	{
		global $ezpSettings;

		$robots->customRules['*'][$robots::RULE_ALLOW][] = empty($ezpSettings['ezp_pages_seourls'])
			? $robots->urlPath . '/*ezportal;sa=page;p=*'
			: $robots->urlPath . '/pages/';
	}

	public function changeSitemap(SitemapGenerator $sitemap): void
	{
		global $ezpSettings;

		$result = Db::$db->query('', '
			SELECT id_page, date, title, permissions
			FROM {db_prefix}ezp_page
			WHERE {int:guests} IN (permissions)' . ($sitemap->startYear ? '
				AND YEAR(FROM_UNIXTIME(date)) >= {int:start_year}' : '') . '
			ORDER BY id_page DESC',
			[
				'guests'     => -1, // The page must be available to guests
				'start_year' => $sitemap->startYear,
			]
		);

		while ($row = Db::$db->fetch_assoc($result)) {
			$url = empty($ezpSettings['ezp_pages_seourls']) || ! function_exists('MakeSEOUrl')
				? Config::$scripturl . '?action=ezportal;sa=page;p=' . $row['id_page']
				: Config::$boardurl . '/pages/' . \MakeSEOUrl($row['title']) . '-' . $row['id_page'];

			$sitemap->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date'],
			];
		}

		Db::$db->free_result($result);
	}
}
