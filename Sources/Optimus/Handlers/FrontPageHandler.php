<?php declare(strict_types=1);

/**
 * FrontPageHandler.php
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

use Bugo\Optimus\Utils\Input;

final class FrontPageHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_load_theme', __CLASS__ . '::changeTitle#', false, __FILE__);
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::addDescription#', false, __FILE__);
	}

	public function changeTitle(): void
	{
		global $modSettings, $txt;

		if (! empty($modSettings['optimus_forum_index']))
			$txt['forum_index'] = $modSettings['optimus_forum_index'];
	}

	public function addDescription(): void
	{
		global $context, $modSettings;

		if (empty($context['current_action'])
			&& empty(Input::server('query_string'))
			&& empty(Input::server('argv'))
			&& ! empty($modSettings['optimus_description'])
		) {
			$context['meta_description'] = Input::xss($modSettings['optimus_description']);
		}
	}
}