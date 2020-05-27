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
	public static function meta()
	{
		global $context, $sourcedir, $modSettings;

		if ($context['current_action'] != 'sitemap')
			return;

		$pretty = $sourcedir . '/PrettyUrls-Filters.php';
		if (file_exists($pretty) && !empty($modSettings['pretty_enable_filters'])) {
			if (!function_exists('pretty_rewrite_buffer'))
				require_once($pretty);

			$context['pretty']['search_patterns'][]  = '~(<loc>)([^#<]+)~';
			$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';
		}
	}
}
