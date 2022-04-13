<?php

namespace Bugo\Optimus\Addons;

/**
 * IdnConvert.php
 *
 * @package Optimus
 */

if (! defined('SMF'))
	die('No direct access...');

/**
 * Conversion of Cyrillic urls
 */
class IdnConvert
{
	public function __construct()
	{
		global $boardurl;

		if (iri_to_url($boardurl) === $boardurl)
			return;

		add_integration_function('integrate_optimus_robots', __CLASS__ . '::optimusRobots', false, __FILE__, true);
		add_integration_function('integrate_optimus_sitemap', __CLASS__ . '::optimusSitemap', false, __FILE__, true);
	}

	public function optimusRobots()
	{
		global $boardurl, $scripturl;

		$boardurl  = iri_to_url($boardurl);
		$scripturl = $boardurl . '/index.php';
	}

	public function optimusSitemap(array &$links)
	{
		foreach ($links as $id => $entry)
			$links[$id]['loc'] = iri_to_url($entry['loc']);
	}
}
