<?php

namespace Bugo\Optimus\Addons;

/**
 * PrettyUrls.php
 *
 * @package Optimus
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
	 * Replace sitemap links on displaying
	 *
	 * @return void
	 */
	public static function sitemapRewriteContent(&$content)
	{
		global $modSettings, $sourcedir, $context;

		if (empty($modSettings['optimus_sitemap_enable']) || empty($modSettings['pretty_enable_filters']))
			return;

		if (file_exists($pretty = $sourcedir . '/PrettyUrls-Filters.php')) {
			if (!function_exists('pretty_rewrite_buffer'))
				require_once($pretty);

			$context['pretty']['search_patterns'][]  = '~(<loc>)([^#<]+)~';
			$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';

			$content = pretty_rewrite_buffer($content);
		}
	}
}
