<?php

/**
 * app.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7.5
 */

use Bugo\Optimus\Integration;
use Bugo\Optimus\Sitemap;

if (!defined('SMF'))
	die('Hacking attempt...');

defined('OP_NAME') || define('OP_NAME', 'Optimus for SMF');
defined('OP_VERSION') || define('OP_VERSION', '2.7.5');

/**
 * @param $classname
 * @return false|void
 */
function optimus_autoloader($classname)
{
	if (strpos($classname, 'Bugo\Optimus') === false)
		return false;

	$classname = str_replace('\\', '/', str_replace('Bugo\Optimus\\', '', $classname));
	$classname = str_replace('Addons/', 'addons/', $classname);
	$file_path = __DIR__ . '/' . $classname . '.php';

	if (!file_exists($file_path))
		return false;

	require_once ($file_path);
}

spl_autoload_register('optimus_autoloader');

$integration = new Integration;
$integration->hooks();

function scheduled_optimus_sitemap(): bool
{
	global $sourcedir, $modSettings, $boarddir;

	@ini_set('opcache.enable', false);

	require_once($sourcedir . '/ScheduledTasks.php');

	loadEssentialThemeData();

	if (!empty($modSettings['optimus_remove_previous_xml_files']))
		array_map("unlink", glob($boarddir . "/sitemap*.xml*"));

	return Sitemap::createXml();
}
