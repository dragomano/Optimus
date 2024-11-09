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

use Bugo\Compat\{Config, IntegrationHook, Utils};

if (! defined('SMF'))
	die('No direct access...');

final class FaviconHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::handle#', false, __FILE__
		);
	}

	public function handle(): void
	{
		if (empty(Config::$modSettings['optimus_favicon_text']))
			return;

		$favicon = explode(PHP_EOL, trim(Config::$modSettings['optimus_favicon_text']));

		foreach ($favicon as $line) {
			Utils::$context['html_headers'] .= "\n\t" . $line;
		}
	}
}
