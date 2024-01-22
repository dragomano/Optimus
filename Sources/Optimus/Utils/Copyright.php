<?php declare(strict_types=1);

/**
 * Input.php
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

if (! defined('SMF'))
	die('No direct access...');

final class Copyright
{
	public static function getLink(): string
	{
		global $user_info;

		$link = $user_info['language'] === 'russian' ? 'https://dragomano.ru/mods/optimus' : 'https://custom.simplemachines.org/mods/index.php?mod=2659';

		return '<a href="' . $link . '" target="_blank" rel="noopener" title="' . OP_VERSION . '">' . OP_NAME . '</a>';
	}
}
