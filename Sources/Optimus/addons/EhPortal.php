<?php

namespace Bugo\Optimus\Addons;

/**
 * EhPortal.php
 *
 * @package Optimus
 */

if (! defined('SMF'))
	die('No direct access...');

/**
 * ExPortal pages for Sitemap
 */
class EhPortal
{
	public function __construct()
	{
		if (! function_exists('sportal_actions'))
			return;

		add_integration_function('integrate_optimus_robots', __CLASS__ . '::optimusRobots', false, __FILE__, true);
		add_integration_function('integrate_optimus_sitemap', __CLASS__ . '::optimusSitemap', false, __FILE__, true);
	}

	public function optimusRobots(array &$custom_rules, string $url_path)
	{
		$custom_rules[] = "Allow: " . $url_path . "/*page=*";
	}

	public function optimusSitemap(array &$links)
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

			call_integration_hook('integrate_optimus_create_sef_url', array(&$url));

			$links[] = array(
				'loc' => $url
			);
		}

		$smcFunc['db_free_result']($request);
	}
}
