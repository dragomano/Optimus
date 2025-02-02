<?php declare(strict_types=1);

/**
 * @package LightPortal (Optimus)
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
use Bugo\LightPortal\Enums\EntryType;
use Bugo\LightPortal\Enums\Permission;
use Bugo\LightPortal\Enums\Status;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;

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

	public function changeRobots(RobotsGenerator $robots): void
	{
		$rule = $robots->urlPath . '/*' . LP_PAGE_PARAM;

		if ($robots->useSef) {
			$rule = $robots->urlPath . '/pages/';
		}

		$robots->customRules['*'][$robots::RULE_ALLOW][] = $rule;
	}

	public function changeSitemap(SitemapGenerator $sitemap): void
	{
		$result = Db::$db->query('', '
			SELECT page_id, slug, GREATEST(created_at, updated_at) AS date
			FROM {db_prefix}lp_pages
			WHERE status = {int:status}
				AND entry_type = {string:entry_type}
				AND created_at <= {int:current_time}
				AND permissions IN ({array_int:permissions})' . ($sitemap->startYear ? '
				AND YEAR(FROM_UNIXTIME(created_at)) >= {int:start_year}' : '') . '
			ORDER BY page_id DESC',
			[
				'status'       => Status::ACTIVE->value,
				'entry_type'   => EntryType::DEFAULT->name(),
				'current_time' => time(),
				'permissions'  => [Permission::GUEST->value, Permission::ALL->value],
				'start_year'   => $sitemap->startYear,
			]
		);

		while ($row = Db::$db->fetch_assoc($result)) {
			$url = Config::$scripturl . '?' . (Config::$modSettings['lp_page_param'] ?? 'page') . '=' . $row['slug'];

			$sitemap->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date'],
			];
		}

		Db::$db->free_result($result);
	}
}
