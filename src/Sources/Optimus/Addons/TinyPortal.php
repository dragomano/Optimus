<?php declare(strict_types=1);

/**
 * @package TinyPortal (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 01.12.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{Config, Db, IntegrationHook};
use Bugo\Compat\{Theme, Utils};
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;
use Bugo\Optimus\Utils\{Input, Str};

if (! defined('SMF'))
	die('No direct access...');

final class TinyPortal extends AbstractAddon
{
	public const PACKAGE_ID = 'bloc:tinyportal';

	public static array $events = [
		self::HOOK_EVENT,
		self::ROBOTS_RULES,
		self::SITEMAP_LINKS,
	];

	public function __invoke(AddonEvent $event): void
	{
		match ($event->eventName()) {
			self::HOOK_EVENT    => $this->postInit(),
			self::ROBOTS_RULES  => $this->changeRobots($event->getTarget()),
			self::SITEMAP_LINKS => $this->changeSitemap($event->getTarget()),
		};
	}

	public function postInit(): void
	{
		IntegrationHook::add(
			'integrate_tp_post_init', self::class . '::prepareArticleMeta#', false,	__FILE__
		);
	}

	public function prepareArticleMeta(): void
	{
		if (! Input::isGet('page') || empty(Utils::$context['TPortal']['article']))
			return;

		$article = Utils::$context['TPortal']['article'];

		$pattern = $article['rendertype'] == 'bbc' ? '/\[img.*]([^\]\[]+)\[\/img\]/U' : '/<img(.*)src(.*)=(.*)"(.*)"/U';
		$firstPostImage = preg_match($pattern, $article['body'], $value);
		Theme::$current->settings['og_image'] = $firstPostImage ? array_pop($value) : null;

		Utils::$context['meta_description'] = Str::teaser($article['intro'] ?: $article['body']);
		Utils::$context['optimus_og_type']['article']['published_time'] = date('Y-m-d\TH:i:s', (int) $article['date']);
		Utils::$context['optimus_og_type']['article']['section'] = $article['category_name'] ?? '';
		Utils::$context['canonical_url'] = Config::$scripturl . '?page=' . ($article['shortname'] ?: $article['id']);
	}

	public function changeRobots(RobotsGenerator $robots): void
	{
		$robots->customRules[] = "Allow: " . $robots->urlPath . "/*page";
	}

	public function changeSitemap(SitemapGenerator $sitemap): void
	{
		$result = Db::$db->query('', '
			SELECT a.id, a.date, a.shortname
			FROM {db_prefix}tp_articles AS a
				INNER JOIN {db_prefix}tp_variables AS v ON (a.category = v.id)
			WHERE a.approved = {int:approved}
				AND a.off = {int:off_status}
				AND {int:guests} IN (v.value3)' . ($sitemap->startYear ? '
				AND YEAR(FROM_UNIXTIME(a.date)) >= {int:start_year}' : '') . '
			ORDER BY a.id DESC',
			[
				'approved'   => 1, // The article must be approved
				'off_status' => 0, // The article must be active
				'guests'     => -1, // The article category must be available to guests
				'start_year' => $sitemap->startYear,
			]
		);

		while ($row = Db::$db->fetch_assoc($result)) {
			$url = Config::$scripturl . '?page=' . ($row['shortname'] ?: $row['id']);

			$sitemap->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date'],
			];
		}

		Db::$db->free_result($result);
	}
}
