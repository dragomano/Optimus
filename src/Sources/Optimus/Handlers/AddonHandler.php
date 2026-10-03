<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2026 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\Cache\CacheApi;
use Bugo\Compat\{Config, Db, IntegrationHook, Lang};
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\DownloadableAddons;
use Bugo\Optimus\Addons\HasSettingsInterface;
use Bugo\Optimus\Events\DispatcherFactory;
use League\Event\ListenerRegistry;
use League\Event\ListenerSubscriber;
use ReflectionClass;

if (! defined('SMF'))
	die('No direct access...'); // @codeCoverageIgnore

final class AddonHandler implements ListenerSubscriber
{
	private static bool $hasSubscribed = false;

	private const TTL = 24 * 60 * 60;

	public function __invoke(): void
	{
		if (self::$hasSubscribed)
			return;

		(new DispatcherFactory())()->subscribeListenersFrom($this);
	}

	public function subscribeListeners(ListenerRegistry $acceptor): void
	{
		$mods   = $this->getInstalledMods();
		$addons = $this->getAllAddons();
		$off    = $this->getDisabledAddons();

		foreach ($addons as $listener) {
			if (in_array($listener::PACKAGE_ID, $off)) {
				continue;
			}

			if (in_array($listener::PACKAGE_ID, $mods) || str_starts_with($listener::PACKAGE_ID, 'Optimus:')) {
				$addonInstance = new $listener;

				/* @var array $events */
				for ($i = 0; $i < count($listener::$events); $i++) {
					$acceptor->subscribeTo($listener::$events[$i], $addonInstance, $listener::PRIORITY);
				}
			}
		}

		self::$hasSubscribed = true;
	}

	/**
	 * Get data of all detected addons for the admin page.
	 */
	public function getAddonData(): array
	{
		$mods = $this->getInstalledMods();
		$off  = $this->getDisabledAddons();
		$data = [];

		foreach ($this->getAllAddons() as $class) {
			$packageId = $class::PACKAGE_ID;
			$name      = (new ReflectionClass($class))->getShortName();

			$data[] = [
				'class'           => $class,
				'package_id'      => $packageId,
				'name'            => $name,
				'description'     => Lang::getTxt('optimus_addon_' . strtolower($name) . '_desc'),
				'is_builtin'      => str_starts_with($packageId, 'Optimus:'),
				'is_active'       => str_starts_with($packageId, 'Optimus:') || in_array($packageId, $mods),
				'is_disabled'     => in_array($packageId, $off),
				'has_settings'    => is_subclass_of($class, HasSettingsInterface::class),
				'is_downloadable' => false,
			];
		}

		$data = $this->addDownloadable($data);

		$rank = static fn(array $row): int => match (true) {
			$row['is_downloadable'] => 3,
			! $row['is_active']     => 2,
			$row['is_disabled']     => 1,
			default                 => 0,
		};

		usort($data, static fn(array $a, array $b): int => $rank($a) <=> $rank($b) ?: strcmp($a['name'], $b['name']));

		return $data;
	}

	/**
	 * Add registry entries for downloadable addons that are
	 * physically absent on the forum.
	 */
	private function addDownloadable(array $data): array
	{
		$present = array_column($data, 'name');

		foreach (DownloadableAddons::all() as $addon) {
			if (in_array($addon['name'], $present)) {
				continue;
			}

			$data[] = [
				'class'           => '',
				'package_id'      => $addon['package_id'],
				'name'            => $addon['name'],
				'description'     => $addon['description'],
				'is_builtin'      => false,
				'is_active'       => false,
				'is_disabled'     => false,
				'has_settings'    => false,
				'is_downloadable' => true,
				'url'             => $addon['url'],
			];
		}

		return $data;
	}

	/**
	 * Enable or disable the addon with the given package id.
	 */
	public function toggle(string $packageId): bool
	{
		$known = array_map(
			static fn(string $class): string => $class::PACKAGE_ID,
			$this->getAllAddons()
		);

		if (! in_array($packageId, $known)) {
			return false;
		}

		$disabled = $this->getDisabledAddons();

		if (in_array($packageId, $disabled)) {
			$disabled = array_values(array_diff($disabled, [$packageId]));
		} else {
			$disabled[] = $packageId;
		}

		Config::updateModSettings(['optimus_disabled_addons' => implode(',', $disabled)]);

		return true;
	}

	public function getDisabledAddons(): array
	{
		$disabled = (string) (Config::$modSettings['optimus_disabled_addons'] ?? '');

		return array_values(array_filter(array_map(trim(...), explode(',', $disabled))));
	}

	private function getAllAddons(): array
	{
		$files = array_merge(
			glob(OP_ADDONS . '/*.php'),
			glob(OP_ADDONS . '/*/*.php'),
		);

		$addons = array_filter(array_map($this->mapNamespace(...), $files), strlen(...));

		// External integrations
		IntegrationHook::call('integrate_optimus_addons', [&$addons]);

		return array_filter(
			$addons,
			static fn($class): bool => is_string($class) && class_exists($class) && is_subclass_of($class, AddonInterface::class),
		);
	}

	private function getInstalledMods(): array
	{
		if (($mods = CacheApi::get('optimus_installed_mods', self::TTL)) === null) {
			$result = Db::$db->query(/** @lang text */ '
				SELECT package_id
				FROM {db_prefix}log_packages
				WHERE install_state <> 0',
			);

			$mods = [];
			while ($row = Db::$db->fetch_assoc($result)) {
				$mods[] = $row['package_id'];
			}

			Db::$db->free_result($result);

			CacheApi::put('optimus_installed_mods', $mods, self::TTL);
		}

		return $mods;
	}

	private function mapNamespace(string $fileName): string
	{
		$fileName = str_replace(OP_ADDONS, '', $fileName);

		if (
			str_ends_with($fileName, 'Interface.php') ||
			str_ends_with($fileName, 'AbstractAddon.php') ||
			str_ends_with($fileName, 'index.php')
		) {
			return '';
		}

		return '\Bugo\Optimus\Addons' . str_replace(['.php', '/'], ['', '\\'], $fileName);
	}
}
