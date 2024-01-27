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

final class ErrorHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_fallback_action', self::class . '::fallbackAction#', false, __FILE__);
		add_integration_function('integrate_menu_buttons', self::class . '::handleStatusErrors#', false, __FILE__);
	}

	public function fallbackAction(): void
	{
		global $modSettings, $context;

		if (empty($modSettings['optimus_errors_for_wrong_actions']))
			return;

		loadTemplate('Errors');

		$context['sub_template'] = 'fatal_error';

		$this->changeErrorPage();
	}

	public function handleStatusErrors(): void
	{
		global $modSettings, $board_info;

		if (empty($modSettings['optimus_errors_for_wrong_boards_topics']) || empty($board_info['error']))
			return;

		if ($board_info['error'] === 'exist') {
			$this->changeErrorPage();
		}

		if ($board_info['error'] === 'access') {
			$this->changeErrorPage(403);
		}
	}

	private function changeErrorPage(int $code = 404): void
	{
		global $context, $txt, $scripturl;

		send_http_status($code);

		addInlineCss('#fatal_error { text-align: center }');

		$context['page_title'] = $txt["optimus_{$code}_page_title"];

		$context['error_code'] = '';
		$context['error_link'] = 'javascript:history.go(-1)';
		$context['error_title'] = $txt["optimus_{$code}_h2"];
		$context['error_message'] = $txt["optimus_{$code}_h3"];
		$context['error_message'] .= '<br>' . sprintf($txt['optimus_goto_main_page'], $scripturl);
	}
}