<?php

namespace Bugo\Optimus\Addons;

/**
 * PortaMx.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7.3
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Support for PortaMx
 */
class PortaMx
{
	/**
	 * Make PortaMx rules for robots.txt
	 *
	 * @param string $common_rules
	 * @param string $url_path
	 * @return void
	 */
	public static function robots(&$common_rules, $url_path)
	{
		global $modSettings;

		$portamx = !empty($modSettings['pmx_frontmode']) && function_exists('PortaMx');

		// "forum" == "community"?
		$portamx_forum_alias = !empty($modSettings['pmxsef_aliasactions']) && strpos($modSettings['pmxsef_aliasactions'], 'forum');
		$common_rules[] = $portamx && $portamx_forum_alias ? "Allow: " . $url_path . "/*forum$" : "";
		$common_rules[] = $portamx && !$portamx_forum_alias ? "Allow: " . $url_path . "/*community$" : "";
	}

	/**
	 * Make SEO links
	 *
	 * @param array $links
	 * @return void
	 */
	public static function sitemap(&$links)
	{
		global $sourcedir;

		if (file_exists($sourcedir . '/PortaMx/PortaMxSEF.php') && function_exists('create_sefurl')) {
			foreach ($links as $id => $entry)
				$links[$id]['loc'] = create_sefurl($entry['loc']);
		}
	}
}
