<?php

namespace Bugo\Optimus\Addons\IdnaConvert;

/**
 * IdnaConvert.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Conversion of Cyrillic domain addresses
 */
class IdnaConvert
{
	/**
	 * Check if conversion is needed
	 *
	 * @return boolean
	 */
	private static function requiredIdnConvert()
	{
		global $sourcedir, $idn, $boardurl;

		require_once($sourcedir . '/Optimus/addons/IdnaConvert/idna_convert.class.php');

		$idn = new \idna_convert(array('idn_version' => 2008));

		if (stripos($idn->encode($boardurl), 'xn--') !== false)
			return true;

		return false;
	}

	/**
	 * Convert robots.txt links if Cyrillic domain is used
	 *
	 * @param string $common_rules
	 * @return void
	 */
	public static function robots(&$common_rules)
	{
		global $boardurl, $idn;

		if (self::requiredIdnConvert()) {
			foreach ($common_rules as $key => $rule) {
				if (strpos($rule, $boardurl))
					$common_rules[$key] = str_replace($boardurl, $idn->encode($boardurl), $rule);
			}
		}
	}

	/**
	 * Get converted links for Cyrillic domain
	 *
	 * @param array $links
	 * @return void
	 */
	public static function sitemap(&$links)
	{
		global $idn;

		if (self::requiredIdnConvert()) {
			foreach ($links as $id => $entry)
				$links[$id]['loc'] = $idn->encode($links[$id]['loc']);
		}
	}
}
