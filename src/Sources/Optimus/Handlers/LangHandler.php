<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\IntegrationHook;
use Bugo\Compat\Lang;

if (! defined('SMF'))
	die('No direct access...');

final class LangHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_load_theme', self::class . '::loadTheme#', false, __FILE__
		);
	}

	public function loadTheme(): void
	{
		Lang::load('Optimus/Optimus');
	}
}
