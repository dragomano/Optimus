<?php declare(strict_types=1);

/**
 * TinyPortal.php
 *
 * @package TinyPortal (Optimus)
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
use Bugo\Optimus\Utils\Input;
use Bugo\Optimus\Utils\Str;

if (! defined('SMF'))
	die('No direct access...');

class TinyPortal extends AbstractAddon
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
		add_integration_function('integrate_tp_post_init', self::class . '::prepareArticleMeta#', false, __FILE__);
	}

	public function prepareArticleMeta(): void
	{
		global $context, $settings, $scripturl;

		if (! Input::isGet('page') || empty($context['TPortal']['article']))
			return;

		$article = $context['TPortal']['article'];

		$pattern = $article['rendertype'] == 'bbc' ? '/\[img.*]([^\]\[]+)\[\/img\]/U' : '/<img(.*)src(.*)=(.*)"(.*)"/U';
		$firstPostImage = preg_match($pattern, $article['body'], $value);
		$settings['og_image'] = $firstPostImage ? array_pop($value) : null;

		$context['meta_description'] = Str::teaser($article['intro'] ?: $article['body']);
		$context['optimus_og_type']['article']['published_time'] = date('Y-m-d\TH:i:s', (int) $article['date']);
		$context['optimus_og_type']['article']['section'] = $article['category_name'] ?? '';
		$context['canonical_url'] = $scripturl . '?page=' . ($article['shortname'] ?: $article['id']);
	}

	public function changeRobots(object $generator): void
	{
		/* @var Generator $generator */
		$generator->customRules[] = "Allow: " . $generator->urlPath . "/*page";
	}

	public function changeSitemap(object $sitemap): void
	{
		global $modSettings, $smcFunc, $scripturl;

		$startYear = (int) ($modSettings['optimus_start_year'] ?? 0);

		$request = $smcFunc['db_query']('', '
			SELECT a.id, a.date, a.shortname
			FROM {db_prefix}tp_articles AS a
				INNER JOIN {db_prefix}tp_variables AS v ON (a.category = v.id)
			WHERE a.approved = {int:approved}
				AND a.off = {int:off_status}
				AND {int:guests} IN (v.value3)' . ($startYear ? '
				AND YEAR(FROM_UNIXTIME(a.date)) >= {int:start_year}' : '') . '
			ORDER BY a.id DESC',
			[
				'approved'   => 1, // The article must be approved
				'off_status' => 0, // The article must be active
				'guests'     => -1, // The article category must be available to guests
				'start_year' => $startYear
			]
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$url = $scripturl . '?page=' . ($row['shortname'] ?: $row['id']);

			/* @var Sitemap $sitemap */
			$sitemap->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date']
			];
		}

		$smcFunc['db_free_result']($request);
	}
}
