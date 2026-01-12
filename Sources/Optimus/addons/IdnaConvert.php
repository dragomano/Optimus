<?php

namespace Bugo\Optimus\Addons;

/**
 * IdnaConvert.php
 *
 * @package Optimus
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Conversion of Cyrillic domain addresses
 */
class IdnaConvert
{
	private static function getEncodedBoardurl(): string
	{
		global $boardurl;

		$host = parse_url($boardurl, PHP_URL_HOST);
		$encoded_host = idn_to_ascii($host);

		return str_replace($host, $encoded_host, $boardurl);
	}

	private static function requiredIdnConvert(): bool
	{
		global $boardurl;

		return self::getEncodedBoardurl() !== $boardurl;
	}

	public static function robots(array &$common_rules)
	{
		global $boardurl;

		if (empty($common_rules))
			return;

		if (self::requiredIdnConvert()) {
			$encoded_boardurl = self::getEncodedBoardurl();
			foreach ($common_rules as $key => $rule) {
				if (strpos($rule, $boardurl)) {
					$common_rules[$key] = str_replace($boardurl, $encoded_boardurl, $rule);
				}
			}
		}
	}

	public static function sitemap(array &$links)
	{
		if (empty($links))
			return;

		if (self::requiredIdnConvert()) {
			foreach ($links as $id => $entry) {
				$parsed = parse_url($entry['loc']);
				if (isset($parsed['host'])) {
					$encoded_host = idn_to_ascii($parsed['host']);
					$links[$id]['loc'] = str_replace($parsed['host'], $encoded_host, $entry['loc']);
				}
			}
		}
	}
}
