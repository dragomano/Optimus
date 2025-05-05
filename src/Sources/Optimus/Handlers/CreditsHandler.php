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

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\IntegrationHook;
use Bugo\Compat\Utils;
use Bugo\Optimus\Utils\Copyright;

if (! defined('SMF'))
	die('No direct access...');

final class CreditsHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_credits', self::class . '::credits#', false, __FILE__
		);
	}

	public function credits(): void
	{
		Utils::$context['credits_modifications'][] = Copyright::getLink() . Copyright::getYears();
	}
}
