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

use Bugo\Optimus\Events\DispatcherFactory;
use League\Event\ListenerRegistry;
use League\Event\ListenerSubscriber;

if (! defined('SMF'))
	die('No direct access...');

final class AddonHandler implements ListenerSubscriber
{
	private static bool $hasSubscribed = false;

	public function __invoke(): void
	{
		if (self::$hasSubscribed)
			return;

		(new DispatcherFactory())()->subscribeListenersFrom($this);
	}

	public function subscribeListeners(ListenerRegistry $acceptor): void
	{
		global $smcFunc;

		$result = $smcFunc['db_query']('', /** @lang text */ '
			SELECT package_id
			FROM {db_prefix}log_packages
			WHERE install_state = 1',
			[]
		);

		$mods = [
			'Optimus:IdnConvert',
			'Optimus:ExampleAddon',
		];

		while ($row = $smcFunc['db_fetch_assoc']($result))
			$mods[] = $row['package_id'];

		$smcFunc['db_free_result']($result);

		if (empty($mods))
			return;

		$files = array_merge(
			glob(OP_ADDONS . '/*.php'),
			glob(OP_ADDONS . '/*/*.php')
		);

		$addons = array_filter(array_map(fn($file) => $this->mapNamespace($file), $files), 'strlen');

		foreach ($addons as $listener) {
			if (in_array($listener::PACKAGE_ID, $mods)) {
				/* @var array $events */
				for ($i = 0; $i < count($listener::$events); $i++) {
					$acceptor->subscribeTo($listener::$events[$i], new $listener);
				}
			}
		}

		self::$hasSubscribed = true;
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