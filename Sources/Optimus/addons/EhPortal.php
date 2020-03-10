<?php

namespace Bugo\Optimus\Addons;

/**
 * EhPortal.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * ExPortal pages for Sitemap
 */
class EhPortal
{
	/**
	 * Let's check if the portal tables exist in the database
	 *
	 * @return boolean
	 */
	public static function isPortalTableExist()
	{
		global $smcFunc, $db_prefix;

		if (!function_exists('sportal_actions'))
			return false;

		db_extend();

		return !empty($smcFunc['db_list_tables'](false, $db_prefix . 'sp_pages'));
	}

	/**
	 * Make EhPortal rules for robots.txt
	 *
	 * @param string $common_rules
	 * @param string $url_path
	 * @return void
	 */
	public static function robots(&$common_rules, $url_path)
	{
		global $modSettings, $boardurl, $boarddir;

		if (empty(self::isPortalTableExist()))
			return;

		$simple_portal = isset($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 1;
		$common_rules[] = $simple_portal ? "Allow: " . $url_path . "/*forum$" : "";

		// SP Standalone mode
		$simple_portal_standalone = !empty($modSettings['sp_standalone_url']) ? substr($modSettings['sp_standalone_url'], strlen($boardurl)) : '';
		if (isset($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 3 && file_exists($boarddir . $simple_portal_standalone))
			$common_rules[] = "Allow: " . $url_path . $simple_portal_standalone;

		$common_rules[] = $simple_portal ? "Allow: " . $url_path . "/*page=page*" : "";
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
			SELECT namespace
			FROM {db_prefix}sp_pages
			WHERE status = {int:status}
				AND (permission_set IN ({array_int:permissions}) OR permission_set = 0 AND {int:guests} IN (groups_allowed))
			ORDER BY id_page',
			array(
				'status'      => 1, // The page must be active
				'permissions' => array(1, 3), // The page must be available to guests
				'guests'      => -1
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$links[] = array(
				'loc'     => $scripturl . '?page=' . $row['namespace'],
				'lastmod' => time()
			);

		$smcFunc['db_free_result']($request);
	}
}
