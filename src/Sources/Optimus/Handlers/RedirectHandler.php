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

use Bugo\Compat\{Config, IntegrationHook};
use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...');

final class RedirectHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add('integrate_actions', self::class . '::handle#', false, __FILE__);
	}

	public function handle(): void
	{
		$redirects = empty(Config::$modSettings['optimus_redirect'])
			? []
			: unserialize(Config::$modSettings['optimus_redirect']);

		if (empty($redirects) || empty($queryString = Input::server('query_string')))
			return;

		if (isset($redirects[$queryString])) {
			$url = Config::$scripturl . '?';
			$to = $redirects[$queryString];

			if (str_starts_with($to, 'http'))
				$url = '';

			header('location: ' . $url . $to, true, 302);
		}
	}
}
