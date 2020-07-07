<?php

namespace Bugo\Optimus\Addons;

/**
 * PrettyUrls.php
 *
 * @package SMF Optimus
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Support for PrettyUrls
 */
class PrettyUrls
{
	/**
	 * Make SEF url from string
	 *
	 * @return void
	 */
	public static function sitemapRewriteContent(&$content)
	{
		global $sourcedir, $modSettings, $context;

		$pretty = $sourcedir . '/PrettyUrls-Filters.php';
		if (file_exists($pretty) && !empty($modSettings['pretty_enable_filters'])) {
			if (!function_exists('pretty_rewrite_buffer'))
				require_once($pretty);

			$context['pretty']['search_patterns'][]  = '~(<loc>)([^#<]+)~';
			$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';

			$content = pretty_rewrite_buffer($content);
		}
	}
}
