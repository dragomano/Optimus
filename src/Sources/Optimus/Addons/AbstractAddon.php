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

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{Lang, User};
use League\Event\ListenerPriority;

abstract class AbstractAddon implements AddonInterface
{
	public const PACKAGE_ID = '';

	public const PRIORITY = ListenerPriority::NORMAL;

	public static array $events = [];

	protected function loadLanguages(string $baseDir): void
	{
		if (empty(Lang::$txt))
			return;

		$userLang = property_exists(Lang::class, 'LANG_TO_LOCALE')
			? array_flip(Lang::LANG_TO_LOCALE)[User::$info['language']] ?? 'english'
			: User::$info['language'];

		$languages = array_merge(['english'], [$userLang ?? null]);

		foreach ($languages as $lang) {
			$langFile = $baseDir . '/langs/' . $lang . '.php';

			if (is_file($langFile)) {
				$addonStrings = (array) require_once $langFile;

				foreach ($addonStrings as $key => $value) {
					Lang::$txt[$key] = $value;
				}
			}
		}
	}
}
