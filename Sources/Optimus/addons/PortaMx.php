<?php

namespace Bugo\Optimus\Addons;

/**
 * PortaMx.php
 *
 * @package Optimus
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Support for PortaMx
 */
class PortaMx
{
	/**
	 * Various fixes
	 *
	 * @return void
	 */
	public static function meta()
	{
		global $modSettings, $context, $mbname, $scripturl;

		if (!function_exists('PortaMx'))
			return;

		if (in_array($context['current_action'], array('forum', 'community')) && !empty($modSettings['pmx_frontmode']))
			$context['canonical_url'] = $scripturl . '?action=' . $context['current_action'];

		if (!empty($modSettings['optimus_sitemap_enable'])) {
			global $PortaMxSEF;
			$PortaMxSEF['ignoreactions'][] = 'sitemap';
		}
	}

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
}
