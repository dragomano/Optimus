<?php

/**
 * LightPortal.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

if (! defined('SMF'))
	die('No direct access...');

/**
 * Support of LightPortal mod
 */
class LightPortal extends AbstractAddon
{
	public const PACKAGE_ID = 'Bugo:LightPortal';
	public static $events = [
		'robots.rules',
		'sitemap.links',
	];

	public function __construct()
	{
		parent::__construct();

		$this->dispatcher->subscribeTo('robots.rules', [$this, 'changeRobots']);
		$this->dispatcher->subscribeTo('sitemap.links', [$this, 'changeSitemap']);
	}

	public function __invoke(AddonEvent $event)
	{
		return match($event->eventName()) {
			'robots.rules' => $this->changeRobots($event->getTarget()),
			'sitemap.links' => $this->changeSitemap($event->getTarget())
		};
	}

	public function changeRobots(object $object): void
	{
		if (! defined('LP_PAGE_PARAM'))
			return;

		$object->getTarget()->customRules[] = "Allow: " . $object->getTarget()->urlPath . "/*" . LP_PAGE_PARAM;
	}

	public function changeSitemap(object $object): void
	{
		global $modSettings, $smcFunc, $scripturl;

		if (! class_exists('\Bugo\LightPortal\Integration'))
			return;

		$startYear = (int) ($modSettings['optimus_start_year'] ?? 0);

		$request = $smcFunc['db_query']('', '
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

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$url = $scripturl . '?' . ($modSettings['lp_page_param'] ?? 'page') . '=' . $row['alias'];

			$object->getTarget()->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date']
			];
		}

		$smcFunc['db_free_result']($request);
	}
}
