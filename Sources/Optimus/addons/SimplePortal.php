<?php

namespace Bugo\Optimus\Addons;

/**
 * SimplePortal.php
 *
 * @package Optimus
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * SimplePortal pages for Sitemap
 */
class SimplePortal
{
	/**
	 * Let's check if the portal installed
	 *
	 * @return boolean
	 */
	public static function isInstalled()
	{
		return function_exists('sportal_actions');
	}

	/**
	 * Add title and canonical url
	 *
	 * @return void
	 */
	public static function meta()
	{
		global $modSettings, $context, $mbname, $scripturl;

		if (empty(self::isInstalled()))
			return;

		if (!empty($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 1) {
			if (empty($context['current_board']) && empty($context['current_topic']) && empty($_REQUEST['action']))
				$context['forum_name'] = $mbname . ' - ' . $modSettings['optimus_portal_index'];
		}

		if ($context['current_action'] == 'forum' && !empty($modSettings['sp_portal_mode']))
			$context['canonical_url'] = $scripturl . '?action=' . $context['current_action'];
	}

	/**
	 * Make rules for robots.txt
	 *
	 * @param string $common_rules
	 * @param string $url_path
	 * @return void
	 */
	public static function robots(&$common_rules, $url_path)
	{
		global $modSettings, $boardurl, $boarddir;

		if (empty(self::isInstalled()))
			return;

		$simple_portal = !empty($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 1;
		$common_rules[] = $simple_portal ? "Allow: " . $url_path . "/*forum$" : "";

		// Standalone mode
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

		if (empty(self::isInstalled()))
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
