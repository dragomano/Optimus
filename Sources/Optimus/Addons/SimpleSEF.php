<?php declare(strict_types=1);

/**
 * SimpleSEF.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

if (! defined('SMF'))
	die('No direct access...');

/**
 * Support for SimpleSEF
 */
class SimpleSEF extends AbstractAddon
{
	public function __construct()
	{
		global $modSettings;

		parent::__construct();

		if (empty($modSettings['simplesef_enable']))
			return;

		if (! empty($modSettings['optimus_remove_index_php']))
			updateSettings(['optimus_remove_index_php' => 0]);

		$this->dispatcher->subscribeTo('robots.rules', [$this, 'changeRobots']);
		$this->dispatcher->subscribeTo('sitemap.sef_links', [$this, 'createSefLinks']);
	}

	public function changeRobots(object $object): void
	{
		global $modSettings;

		$object->getTarget()->useSef = ! empty($modSettings['simplesef_enable'])
			&& is_file(dirname(__DIR__, 2) . '/SimpleSEF.php');
	}

	public function createSefLinks(object $object): void
	{
		if (! class_exists('\SimpleSEF'))
			return;

		$sef = new \SimpleSEF();
		$method = method_exists('\SimpleSEF', 'getSefUrl') ? 'getSefUrl' : 'create_sef_url';

		foreach ($object->getTarget()->links as &$url) {
			$url['loc'] = $sef->$method($url['loc']);
		}
	}
}
