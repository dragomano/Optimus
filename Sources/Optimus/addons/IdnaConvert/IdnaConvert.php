<?php

namespace Bugo\Optimus\Addons\IdnaConvert;

/**
 * IdnaConvert.php
 *
 * @package Optimus
 *
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

		if (empty($common_rules))
			return;

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

		if (empty($links))
			return;

		if (self::requiredIdnConvert()) {
			foreach ($links as $id => $entry)
				$links[$id]['loc'] = $idn->encode($links[$id]['loc']);
		}
	}
}
