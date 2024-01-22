<?php /** @noinspection PhpIgnoredClassAliasDeclaration */
/** @noinspection PhpMultipleClassDeclarationsInspection */

declare(strict_types=1);

/**
 * app.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

use Laminas\Loader\StandardAutoloader;
use Bugo\Optimus\Integration;

if (! defined('SMF'))
	die('No direct access...');

defined('OP_NAME') || define('OP_NAME', 'Optimus for SMF');
defined('OP_VERSION') || define('OP_VERSION', '3.0 Beta');
defined('OP_ADDONS') || define('OP_ADDONS', __DIR__ . '/Addons');

require_once __DIR__ . '/Libs/autoload.php';

$loader = new StandardAutoloader();
$loader->registerNamespace('Bugo\Optimus', __DIR__);
$loader->register();

if (str_starts_with(SMF_VERSION, '3.0')) {
	class_alias('SMF\\Tasks\\BackgroundTask', 'SMF_BackgroundTask');
}

(new Integration())();
