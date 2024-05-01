<?php declare(strict_types=1);

/**
 * ErrorHandler.php
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
use Bugo\Compat\{Board, Lang, Theme, Utils};

if (! defined('SMF'))
	die('No direct access...');

final class ErrorHandler
{
	public function __invoke(): void
	{
		if (! empty(Config::$modSettings['optimus_errors_for_wrong_actions'])) {
			IntegrationHook::add(
				'integrate_fallback_action', self::class . '::fallbackAction#', false, __FILE__
			);
		}

		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::handleStatusErrors#', false, __FILE__
		);
	}

	public function fallbackAction(): void
	{
		Theme::loadTemplate('Errors');

		Utils::$context['sub_template'] = 'fatal_error';

		$this->changeErrorPage();
	}

	public function handleStatusErrors(): void
	{
		if (empty(Config::$modSettings['optimus_errors_for_wrong_boards_topics']) || empty(Board::$info['error']))
			return;

		if (Board::$info['error'] === 'exist') {
			$this->changeErrorPage();
		}

		if (Board::$info['error'] === 'access') {
			$this->changeErrorPage(403);
		}
	}

	private function changeErrorPage(int $code = 404): void
	{
		Utils::sendHttpStatus($code);

		Theme::addInlineCss('#fatal_error { text-align: center }');

		Utils::$context['page_title'] = Lang::$txt["optimus_{$code}_page_title"];

		Utils::$context['error_code'] = '';
		Utils::$context['error_link'] = 'javascript:history.go(-1)';
		Utils::$context['error_title'] = Lang::$txt["optimus_{$code}_h2"];
		Utils::$context['error_message'] = Lang::$txt["optimus_{$code}_h3"];
		Utils::$context['error_message'] .= '<br>' . sprintf(Lang::$txt['optimus_goto_main_page'], Config::$scripturl);
	}
}
