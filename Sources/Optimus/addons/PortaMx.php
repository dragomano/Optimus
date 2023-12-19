<?php

namespace Bugo\Optimus\Addons;

/**
 * PortaMx.php
 *
 * @package Optimus
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Support for PortaMx
 */
class PortaMx
{
	public static function meta()
	{
		global $context, $modSettings, $scripturl;

		if (!function_exists('PortaMx'))
			return;

		if (in_array($context['current_action'], array('forum', 'community')) && !empty($modSettings['pmx_frontmode']))
			$context['canonical_url'] = $scripturl . '?action=' . $context['current_action'];
	}

	public static function robots(array &$common_rules, string $url_path)
	{
		global $modSettings;

		$portamx = !empty($modSettings['pmx_frontmode']) && function_exists('PortaMx');

		// "forum" == "community"?
		$portamx_forum_alias = !empty($modSettings['pmxsef_aliasactions']) && strpos($modSettings['pmxsef_aliasactions'], 'forum');
		$common_rules[] = $portamx && $portamx_forum_alias ? "Allow: " . $url_path . "/*forum$" : "";
		$common_rules[] = $portamx && !$portamx_forum_alias ? "Allow: " . $url_path . "/*community$" : "";
	}

	public static function createSefUrl(string &$url)
	{
		if (!function_exists('PortaMx') || !function_exists('create_sefurl'))
			return;

		$url = create_sefurl($url);
	}
}
