<?php

namespace Bugo\Optimus\Addons;

/**
 * LightPortal.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.5
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * LightPortal pages for Sitemap
 */
class LightPortal
{
	/**
	 * Let's check if the portal tables exist in the database
	 *
	 * @return boolean
	 */
	public static function isPortalTableExist()
	{
		global $smcFunc, $db_prefix;

		db_extend();

		return !empty($smcFunc['db_list_tables'](false, $db_prefix . 'lp_pages')) && class_exists('\Bugo\LightPortal\Integration');
	}

	/**
	 * Make Light Portal rules for robots.txt
	 *
	 * @param string $common_rules
	 * @param string $url_path
	 * @return void
	 */
	public static function robots(&$common_rules, $url_path)
	{
		global $modSettings;

		if (empty(self::isPortalTableExist()))
			return;

		$common_rules[] = empty($modSettings['lp_standalone']) && empty($modSettings['lp_main_page_disable']) ? "Allow: " . $url_path . "/*action=forum$" : "";
		$common_rules[] = "Allow: " . $url_path . "/*page";
	}

	/**
	 * Get an array of portal pages ([] = array('url' => link, 'date' => date))
	 *
	 * @param array $links
	 * @return void
	 */
	public static function sitemap(&$links)
	{
		global $smcFunc, $scripturl;

		if (empty(self::isPortalTableExist()))
			return;

		$request = $smcFunc['db_query']('', '
			SELECT alias, GREATEST(created_at, updated_at) AS date
			FROM {db_prefix}lp_pages
			WHERE status = {int:status}
				AND permissions IN ({array_int:permissions})
				AND alias != {string:main_page}
			ORDER BY id_page',
			array(
				'status'      => 1, // The page must be active
				'permissions' => array(1, 3), // The page must be available to guests
				'main_page'   => '/'
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$links[] = array(
				'url'  => $scripturl . '?page=' . $row['alias'],
				'date' => $row['date']
			);

		$smcFunc['db_free_result']($request);
	}
}
