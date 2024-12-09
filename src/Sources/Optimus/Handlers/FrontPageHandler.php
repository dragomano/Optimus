<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC1
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\{Config, IntegrationHook};
use Bugo\Compat\{Lang, Utils};
use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...');

final class FrontPageHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_load_theme', self::class . '::changeTitle#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::addDescription#', false, __FILE__
		);
	}

	public function changeTitle(): void
	{
		if (empty(Config::$modSettings['optimus_forum_index']))
			return;

		Lang::$txt['forum_index'] = Config::$modSettings['optimus_forum_index'];
	}

	public function addDescription(): void
	{
		if (empty(Config::$modSettings['optimus_description']))
			return;

		if (empty(Utils::$context['current_action'])
			&& empty(Input::server('query_string'))
			&& empty(Input::server('path_info'))
			&& empty(Input::server('argv'))
		) {
			Utils::$context['meta_description'] = Input::xss(Config::$modSettings['optimus_description']);
		}
	}
}
