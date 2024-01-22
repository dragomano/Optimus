<?php declare(strict_types=1);

/**
 * AddonHandler.php
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

use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Events\AddonListener;
use Bugo\Optimus\Events\DispatcherFactory;
use FilesystemIterator;

if (! defined('SMF'))
	die('No direct access...');

final class AddonHandler
{
	private static array $loaded = [];

	public function __invoke(): void
	{
		$addons = $this->getAll();

		if (empty($addons))
			return;

		$dispatcher = (new DispatcherFactory())();

		foreach ($addons as $addon) {
			$this->loadLanguages($addon);

			if (isset(self::$loaded[$addon])) {
				continue;
			}

			$class = $this->getClassName($addon);

			$dispatcher->subscribeTo($addon, new AddonListener());
			$dispatcher->dispatch(new AddonEvent($addon, new $class));

			self::$loaded[$addon] = true;
		}
	}

	private function getAll(): ?array
	{
		if (! is_dir(dirname(__DIR__ ) . '/Addons'))
			return [];

		if (($addons = cache_get_data('optimus_addons', 3600)) === null) {
			foreach ((new FilesystemIterator(dirname(__DIR__ ) . '/Addons')) as $object) {
				$filename = $object->getBasename();
				if ($object->isFile()) {
					$addons[] = str_replace('.php', '', $filename);
				}

				if ($object->isDir() && is_file($object->getPathname() . '/' . $filename . '.php')) {
					$addons[] = $filename . '|' . $filename;
				}
			}

			$addons = array_diff($addons, ['AbstractAddon', 'index']);

			cache_put_data('optimus_addons', $addons, 3600);
		}

		return $addons;
	}

	private function getClassName(string $addon): string
	{
		return '\Bugo\Optimus\Addons\\' . str_replace('|', '\\', $addon);
	}

	private function loadLanguages(string $addon): void
	{
		global $user_info, $txt;

		if (empty($txt))
			return;

		$languages = array_merge(['english'], [$user_info['language'] ?? null]);
		$baseDir = dirname(__DIR__ ) . '/Addons/' . explode('|', $addon)[0] . '/langs/';

		foreach ($languages as $lang) {
			$langFile = $baseDir . $lang . '.php';

			if (is_file($langFile)) {
				require_once $langFile;
			}
		}
	}
}