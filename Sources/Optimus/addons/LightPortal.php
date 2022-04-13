<?php

namespace Bugo\Optimus\Addons;

/**
 * LightPortal.php
 *
 * @package Optimus
 */

if (! defined('SMF'))
	die('No direct access...');

/**
 * Support of LightPortal mod
 */
class LightPortal
{
	public function __construct()
	{
		add_integration_function('integrate_optimus_robots', __CLASS__ . '::optimusRobots', false, __FILE__, true);
		add_integration_function('integrate_optimus_sitemap', __CLASS__ . '::optimusSitemap', false, __FILE__, true);
	}

	public function optimusRobots(array &$custom_rules, string $url_path)
	{
		if (! defined('LP_PAGE_PARAM'))
			return;

		$custom_rules[] = "Allow: " . $url_path . "/*" . LP_PAGE_PARAM;
	}

	public function optimusSitemap(array &$links)
	{
		global $smcFunc, $scripturl, $modSettings;

		if (! class_exists('\Bugo\LightPortal\Integration'))
			return;

		$start_year = (int) op_config('optimus_start_year', 0);

		$request = $smcFunc['db_query']('', '
			SELECT page_id, alias, GREATEST(created_at, updated_at) AS date
			FROM {db_prefix}lp_pages
			WHERE status = {int:status}
				AND created_at <= {int:current_time}
				AND permissions IN ({array_int:permissions})' . ($start_year ? '
				AND YEAR(FROM_UNIXTIME(created_at)) >= {int:start_year}' : '') . '
			ORDER BY page_id DESC',
			array(
				'status'       => 1, // The page must be active
				'current_time' => time(),
				'permissions'  => array(1, 3), // The page must be available to guests
				'start_year'   => $start_year
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$url = $scripturl . '?' . ($modSettings['lp_page_param'] ?? 'page') . '=' . $row['alias'];

			call_integration_hook('integrate_optimus_create_sef_url', array(&$url));

			$links[] = array(
				'loc'     => $url,
				'lastmod' => $row['date']
			);
		}

		$smcFunc['db_free_result']($request);
	}
}
