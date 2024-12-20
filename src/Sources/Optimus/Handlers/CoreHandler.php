<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC1
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\IntegrationHook;
use Bugo\Compat\Lang;
use Bugo\Compat\Utils;
use Bugo\Optimus\Utils\Copyright;

if (! defined('SMF'))
	die('No direct access...');

final class CoreHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_load_theme', self::class . '::loadTheme#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_credits', self::class . '::credits#', false, __FILE__
		);
	}

	public function loadTheme(): void
	{
		Lang::load('Optimus/Optimus');
	}

	public function credits(): void
	{
		Utils::$context['credits_modifications'][] = Copyright::getLink() . Copyright::getYears();
	}
}
