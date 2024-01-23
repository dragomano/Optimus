<?php declare(strict_types=1);

/**
 * AbstractAddon.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Addons;

abstract class AbstractAddon implements AddonInterface
{
	public const PACKAGE_ID = '';

	public static array $events = [];

	protected function loadLanguages(string $baseDir): void
	{
		global $txt, $user_info;

		if (empty($txt))
			return;

		$languages = array_merge(['english'], [$user_info['language'] ?? null]);

		foreach ($languages as $lang) {
			$langFile = $baseDir . '/langs/' . $lang . '.php';

			if (is_file($langFile)) {
				require_once $langFile;
			}
		}
	}
}