<?php declare(strict_types=1);

/**
 * Copyright.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Utils;

use Bugo\Compat\Lang;

if (! defined('SMF'))
	die('No direct access...');

final class Copyright
{
	public static function getLink(): string
	{
		$link = Lang::$txt['lang_dictionary'] === 'ru'
			? 'https://dragomano.ru/mods/optimus'
			: 'https://custom.simplemachines.org/mods/index.php?mod=2659';

		return '<a href="' . $link . '" target="_blank" rel="noopener" title="' . OP_VERSION . '">' . OP_NAME . '</a>';
	}

	public static function getYears(): string
	{
		return ' &copy; 2010&ndash;' . date('Y') . ', Bugo';
	}
}
