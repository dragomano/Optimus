<?php declare(strict_types=1);

/**
 * FaviconHandler.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Handlers;

final class FaviconHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_menu_buttons', self::class . '::handle#', false, __FILE__);
	}

	public function handle(): void
	{
		global $modSettings, $context;

		if (empty($modSettings['optimus_favicon_text']))
			return;

		$favicon = explode(PHP_EOL, trim($modSettings['optimus_favicon_text']));

		foreach ($favicon as $line) {
			$context['html_headers'] .= "\n\t" . $line;
		}
	}
}