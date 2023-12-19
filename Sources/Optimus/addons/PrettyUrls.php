<?php

namespace Bugo\Optimus\Addons;

/**
 * PrettyUrls.php
 *
 * @package Optimus
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Support for PrettyUrls
 */
class PrettyUrls
{
	public static function sitemapRewriteContent(string &$content)
	{
		global $sourcedir, $modSettings, $context;

		$pretty = $sourcedir . '/PrettyUrls-Filters.php';
		if (!file_exists($pretty) || empty($modSettings['pretty_enable_filters']))
			return;

		if (!function_exists('pretty_rewrite_buffer'))
			require_once($pretty);

		if (!isset($context['session_var']))
			$context['session_var'] = substr(md5(mt_rand() . session_id() . mt_rand()), 0, rand(7, 12));

		$context['pretty']['search_patterns'][]  = '~(<loc>)([^#<]+)~';
		$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';

		$content = pretty_rewrite_buffer($content);
	}
}
