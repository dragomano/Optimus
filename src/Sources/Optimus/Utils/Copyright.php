<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC3
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

		return Str::html('a', OP_NAME)
			->href($link)
			->setAttribute('target', '_blank')
			->setAttribute('rel', 'noopener')
			->setAttribute('title', OP_VERSION)
			->toHtml();
	}

	public static function getYears(): string
	{
		return ' &copy; 2010&ndash;' . date('Y') . ', Bugo';
	}
}
