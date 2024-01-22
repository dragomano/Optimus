<?php

/**
 * IdnConvert.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

if (! defined('SMF'))
	die('No direct access...');

/**
 * Conversion of Cyrillic urls
 */
class IdnConvert extends AbstractAddon
{
	public function __construct()
	{
		global $boardurl;

		parent::__construct();

		if (iri_to_url($boardurl) === $boardurl)
			return;

		$this->dispatcher->subscribeTo('robots.rules', [$this, 'changeRobots']);
		$this->dispatcher->subscribeTo('sitemap.links', [$this, 'changeSitemap']);
	}

	public function changeRobots(): void
	{
		global $boardurl, $scripturl;

		$boardurl  = iri_to_url($boardurl);
		$scripturl = $boardurl . '/index.php';
	}

	public function changeSitemap(object $object): void
	{
		foreach ($object->getTarget()->links as &$url) {
			$url['loc'] = iri_to_url($url['loc']);
		}
	}
}
