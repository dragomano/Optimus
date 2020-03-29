<?php

namespace Bugo\Optimus\Addons;

/**
 * PrettyUrls.php
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
 * Support for PrettyUrls
 */
class PrettyUrls
{
	/**
	 * Make preparing of sitemap content before creating
	 *
	 * @return void
	 */
	public static function sitemap()
	{
		global $sourcedir, $modSettings, $context;

		$pretty = $sourcedir . '/PrettyUrls-Filters.php';
		if (file_exists($pretty) && !empty($modSettings['pretty_enable_filters'])) {
			if (!function_exists('pretty_rewrite_buffer'))
				require_once($pretty);

			$context['pretty']['search_patterns'][]  = '~(<loc>)([^#<]+)~';
			$context['pretty']['replace_patterns'][] = '~(<loc>)([^<]+)~';
		}
	}
}
