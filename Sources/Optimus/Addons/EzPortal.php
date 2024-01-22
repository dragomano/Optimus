<?php

/**
 * EzPortal.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

use function MakeSEOUrl;

if (! defined('SMF'))
	die('No direct access...');

/**
 * EzPortal pages for Sitemap
 */
class EzPortal extends AbstractAddon
{
	public function __construct()
	{
		parent::__construct();

		if (! function_exists('EzPortalMain'))
			return;

		$this->dispatcher->subscribeTo('robots.rules', [$this, 'changeRobots']);
		$this->dispatcher->subscribeTo('sitemap.links', [$this, 'changeSitemap']);
	}

	public function changeRobots(object $object): void
	{
		global $ezpSettings;

		if (! empty($ezpSettings['ezp_pages_seourls']))
			$object->getTarget()->customRules[] = "Allow: " . $object->getTarget()->urlPath . "/pages/";
		else
			$object->getTarget()->customRules[] = "Allow: " . $object->getTarget()->urlPath . "/*ezportal;sa=page;p=*";
	}

	public function changeSitemap(object $object): void
	{
		global $smcFunc, $ezpSettings, $boardurl, $scripturl;

		$request = $smcFunc['db_query']('', '
			SELECT id_page, date, title, permissions
			FROM {db_prefix}ezp_page
			WHERE {int:guests} IN (permissions)
			ORDER BY id_page DESC',
			[
				'guests' => -1 // The page must be available to guests
			]
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			if (! empty($ezpSettings['ezp_pages_seourls']) && function_exists('MakeSEOUrl')) {
				$url = $boardurl . '/pages/' . MakeSEOUrl($row['title']) . '-' . $row['id_page'];
			} else {
				$url = $scripturl . '?action=ezportal;sa=page;p=' . $row['id_page'];
			}

			$object->getTarget()->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date']
			];
		}

		$smcFunc['db_free_result']($request);
	}
}
