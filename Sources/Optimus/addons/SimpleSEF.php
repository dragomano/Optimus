<?php

namespace Bugo\Optimus\Addons;

/**
 * SimpleSEF.php
 *
 * @package Optimus
 *
 */

if (! defined('SMF'))
	die('Hacking attempt...');

/**
 * Support for SimpleSEF
 */
class SimpleSEF
{
	/**
	 * Make SEF url from string
	 *
	 * @param string $url
	 * @return void
	 */
	public static function createSefUrl(&$url)
	{
		global $modSettings;

		if (empty($modSettings['simplesef_enable']) || ! class_exists('\SimpleSEF'))
			return;

		$url = (new \SimpleSEF)->create_sef_url($url);
	}
}
