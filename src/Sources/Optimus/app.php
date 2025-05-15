<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC4
 */

use Bugo\Optimus\Prime;

if (! defined('SMF'))
	die('No direct access...');

defined('OP_NAME') || define('OP_NAME', 'Optimus for SMF');
defined('OP_VERSION') || define('OP_VERSION', '3.0 RC4');
defined('OP_ADDONS') || define('OP_ADDONS', __DIR__ . '/Addons');

require_once __DIR__ . '/Libs/autoload.php';

(new Prime())();
