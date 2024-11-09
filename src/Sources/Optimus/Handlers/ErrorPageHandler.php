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
use Bugo\Compat\{Board, Lang, Theme, Utils};

if (! defined('SMF'))
	die('No direct access...');

final class ErrorPageHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_load_theme', self::class . '::handleWrongActions#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::handleWrongBoardsTopics#', false, __FILE__
		);
	}

	public function handleWrongActions(): void
	{
		if (empty(Config::$modSettings['optimus_errors_for_wrong_actions']))
			return;

		Theme::$current->settings['catch_action'] = [
			'template' => 'Errors',
			'function' => self::class . '::changeErrorPage#',
			'sub_template' => 'fatal_error',
		];
	}

	public function handleWrongBoardsTopics(): void
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

	public function changeErrorPage(int $code = 404): void
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
