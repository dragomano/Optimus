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

if (! defined('SMF'))
	die('No direct access...');

final class AddonHandler
{
	private static array $loaded = [];

	private const TTL = 30 * 24 * 60 * 60;

	public function __invoke(): void
	{
		$addons = $this->getAll();

		if (empty($addons))
			return;

		$dispatcher = (new DispatcherFactory())();

		foreach ($addons as $addon) {
			$this->loadLanguages($addon);

			if (isset(self::$loaded[$addon]))
				continue;

			$dispatcher->subscribeTo($addon, new AddonListener());
			$dispatcher->dispatch(new AddonEvent($addon, new $addon));

			self::$loaded[$addon] = true;
		}
	}

	private function getAll(): ?array
	{
		global $modSettings;

		if (empty($modSettings['cache_enable']))
			return $this->getList();

		if (empty($modSettings['optimus_addons_hash'])) {
			updateSettings(['optimus_addons_hash' => $this->hashDirectory(OP_ADDONS)]);
		}

		if (
			$modSettings['optimus_addons_hash'] !== $this->hashDirectory(OP_ADDONS)
			|| (cache_get_data('optimus_addons', self::TTL)) === null
		) {
			$addons = $this->getList();

			cache_put_data('optimus_addons', $addons, self::TTL);

			updateSettings(['optimus_addons_hash' => $this->hashDirectory(OP_ADDONS)]);

			return $addons;
		}

		return cache_get_data('optimus_addons', self::TTL);
	}

	private function getList(): array
	{
		$files = array_merge(
			glob(OP_ADDONS . '/*.php'),
			glob(OP_ADDONS . '/*/*.php')
		);

		return array_filter(array_map('self::mapNamespace', $files), 'strlen');
	}

	private function mapNamespace(string $fileName): string
	{
		$fileName = str_replace(OP_ADDONS, '', $fileName);

		if (str_ends_with($fileName, 'AbstractAddon.php') || str_ends_with($fileName, 'index.php'))
			return '';

		return '\Bugo\Optimus\Addons' . str_replace(['.php', '/'], ['', '\\'], $fileName);
	}

	private function hashDirectory(string $directory): string|false
	{
		if (! is_dir($directory))
			return false;

		$files = [];
		$dir = dir($directory);

		while (false !== ($file = $dir->read())) {
			if ($file != '.' && $file != '..') {
				if (is_dir($directory . '/' . $file)) {
					$files[] = $this->hashDirectory($directory . '/' . $file);
				} else {
					$files[] = md5_file($directory . '/' . $file);
				}
			}
		}

		$dir->close();

		return md5(implode('', $files));
	}

	private function loadLanguages(string $addon): void
	{
		global $txt, $user_info;

		if (empty($txt))
			return;

		$baseDir = OP_ADDONS . DIRECTORY_SEPARATOR . basename($addon);

		if (! is_dir($baseDir))
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