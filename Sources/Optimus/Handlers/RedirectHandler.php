<?php declare(strict_types=1);

/**
 * RedirectHandler.php
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

final class RedirectHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_actions', self::class . '::handle#', false, __FILE__);
	}

	public function handle(): void
	{
		global $modSettings, $scripturl;

		$redirects = empty($modSettings['optimus_redirect']) ? [] : unserialize($modSettings['optimus_redirect']);

		if (empty($redirects))
			return;

		if (isset($_SERVER['QUERY_STRING']) && isset($redirects[$_SERVER['QUERY_STRING']])) {
			$url = $scripturl . '?';
			$to = $redirects[$_SERVER['QUERY_STRING']];

			if (str_starts_with($to, 'http'))
				$url = '';

			header('location: ' . $url . $to, true, 302);
		}
	}
}