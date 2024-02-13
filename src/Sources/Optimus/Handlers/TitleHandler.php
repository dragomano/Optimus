<?php declare(strict_types=1);

/**
 * TitleHandler.php
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

use Bugo\Compat\{Board, Config, IntegrationHook, Utils};

if (! defined('SMF'))
	die('No direct access...');

final class TitleHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_theme_context', self::class . '::handle#', false, __FILE__
		);
	}

	public function handle(): void
	{
		if (SMF === 'SSI')
			return;

		$this->handleBoardTitles();
		$this->handleTopicTitles();
	}

	private function handleBoardTitles(): void
	{
		if (empty(Board::$info['total_topics']) || empty(Config::$modSettings['optimus_board_extend_title']))
			return;

		Utils::$context['page_title_html_safe'] = Config::$modSettings['optimus_board_extend_title'] == 1
			? Utils::$context['forum_name'] . ' - ' . Utils::$context['page_title_html_safe']
			: Utils::$context['page_title_html_safe'] . ' - ' . Utils::$context['forum_name'];
	}

	private function handleTopicTitles(): void
	{
		if (empty(Utils::$context['first_message']) || empty(Config::$modSettings['optimus_topic_extend_title']))
			return;

		Utils::$context['page_title_html_safe'] = Config::$modSettings['optimus_topic_extend_title'] == 1
			? Utils::$context['forum_name'] . ' - ' . Board::$info['name'] . ' - ' . Utils::$context['page_title_html_safe']
			: Utils::$context['page_title_html_safe'] . ' - ' . Board::$info['name'] . ' - ' . Utils::$context['forum_name'];
	}
}
