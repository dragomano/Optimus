<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Routes;

use Bugo\Compat\Routable;

class Keywords implements Routable
{
	public static function buildRoute(array $params): array
	{
		$route[] = $params['action'];

		unset($params['action']);

		if (! isset($params['id'])) {
			$params['id'] = 'all';
		}

		if (isset($params['id'])) {
			if ($params['id'] > 0) {
				$route[] = $params['id'];
			}

			unset($params['id']);
		}

		if (isset($params['start'])) {
			if ($params['start'] > 0) {
				$route[] = $params['start'];
			}

			unset($params['start']);
		}

		return ['route' => $route, 'params' => $params];
	}

	public static function parseRoute(array $route, array $params = []): array
	{
		$params['action'] = array_shift($route);

		if (! empty($route)) {
			$params['id'] = array_shift($route);
		}

		if (! empty($route)) {
			$params['start'] = array_shift($route);
		}

		return $params;
	}
}
