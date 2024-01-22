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
		add_integration_function('integrate_menu_buttons', self::class . '::handle#', false, __FILE__);
	}

	public function handle(): void
	{
		global $modSettings, $board_info, $context, $txt, $scripturl;

		if (empty($modSettings['optimus_correct_http_status']) || empty($board_info['error']))
			return;

		// Does not page exist?
		if ($board_info['error'] === 'exist') {
			send_http_status(404);

			$context['page_title']    = $txt['optimus_404_page_title'];
			$context['error_title']   = $txt['optimus_404_h2'];
			$context['error_message'] = $txt['optimus_404_h3'] . '<br>' . sprintf($txt['optimus_goto_main_page'], $scripturl);
		}

		// No access?
		if ($board_info['error'] === 'access') {
			send_http_status(403);

			$context['page_title']    = $txt['optimus_403_page_title'];
			$context['error_title']   = $txt['optimus_403_h2'];
			$context['error_message'] = $txt['optimus_403_h3'] . '<br>' . sprintf($txt['optimus_goto_main_page'], $scripturl);
		}

		if ($board_info['error'] === 'exist' || $board_info['error'] === 'access') {
			addInlineJavaScript('
		let error_block = document.getElementById("fatal_error");
		error_block.classList.add("centertext");
		error_block.nextElementSibling.querySelector("a.button").setAttribute("href", "javascript:history.go(-1)");', true);
		}
	}
}