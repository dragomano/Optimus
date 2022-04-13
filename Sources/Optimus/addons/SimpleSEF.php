<?php

namespace Bugo\Optimus\Addons;

/**
 * SimpleSEF.php
 *
 * @package Optimus
 */

if (! defined('SMF'))
	die('No direct access...');

/**
 * Support for SimpleSEF
 */
class SimpleSEF
{
	public function __construct()
	{
		if (is_off('simplesef_enable'))
			return;

		add_integration_function('integrate_menu_buttons', __CLASS__ . '::makeCompatWithOptimus', false, __FILE__, true);
		add_integration_function('integrate_optimus_robots', __CLASS__ . '::optimusRobots', false, __FILE__, true);
		add_integration_function('integrate_optimus_create_sef_url', __CLASS__ . '::optimusCreateSefUrl', false, __FILE__, true);
	}

	public function makeCompatWithOptimus()
	{
		if (is_on('simplesef_enable') && is_on('optimus_remove_index_php'))
			updateSettings(['optimus_remove_index_php' => 0]);
	}

	public function optimusRobots(array &$custom_rules, string $url_path, bool &$use_sef)
	{
		$use_sef = is_on('simplesef_enable') && is_file(dirname(__DIR__, 2) . '/SimpleSEF.php');
	}

	public function optimusCreateSefUrl(string &$url)
	{
		if (! class_exists('\SimpleSEF'))
			return;

		$method = method_exists('\SimpleSEF', 'getSefUrl') ? 'getSefUrl' : 'create_sef_url';

		$url = (new \SimpleSEF)->$method($url);
	}
}
