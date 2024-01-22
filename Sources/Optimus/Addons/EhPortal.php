<?php

/**
 * EhPortal.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

if (! defined('SMF'))
	die('No direct access...');

/**
 * EhPortal pages for Sitemap
 */
class EhPortal extends AbstractAddon
{
	public function __construct()
	{
		parent::__construct();

		if (! function_exists('sportal_actions'))
			return;

		$this->dispatcher->subscribeTo('robots.rules', [$this, 'changeRobots']);
		$this->dispatcher->subscribeTo('sitemap.links', [$this, 'changeSitemap']);
	}

	public function changeRobots(object $object): void
	{
		$object->getTarget()->customRules[] = "Allow: " . $object->getTarget()->urlPath . "/*page=*";
	}

	public function changeSitemap(object $object): void
	{
		global $smcFunc, $scripturl;

		$request = $smcFunc['db_query']('', '
			SELECT namespace
			FROM {db_prefix}sp_pages
			WHERE status = {int:status}
				AND (permission_set IN ({array_int:permissions}) OR (permission_set = 0 AND {int:guests} IN (groups_allowed)))
			ORDER BY id_page DESC',
			array(
				'status'      => 1, // The page must be active
				'permissions' => array(1, 3), // The page must be available to guests
				'guests'      => -1
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$url = $scripturl . '?page=' . $row['namespace'];

			$object->getTarget()->links[] = array(
				'loc' => $url
			);
		}

		$smcFunc['db_free_result']($request);
	}
}
