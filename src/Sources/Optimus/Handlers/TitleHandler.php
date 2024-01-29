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

if (! defined('SMF'))
	die('No direct access...');

final class TitleHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_theme_context', self::class . '::handle#', false, __FILE__);
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
		global $board_info, $modSettings, $context;

		if (empty($board_info['total_topics']) || empty($modSettings['optimus_board_extend_title']))
			return;

		$context['page_title_html_safe'] = $modSettings['optimus_board_extend_title'] == 1
			? $context['forum_name'] . ' - ' . $context['page_title_html_safe']
			: $context['page_title_html_safe'] . ' - ' . $context['forum_name'];
	}

	private function handleTopicTitles(): void
	{
		global $context, $modSettings, $board_info;

		if (empty($context['first_message']) || empty($modSettings['optimus_topic_extend_title']))
			return;

		$context['page_title_html_safe'] = $modSettings['optimus_topic_extend_title'] == 1
			? $context['forum_name'] . ' - ' . $board_info['name'] . ' - ' . $context['page_title_html_safe']
			: $context['page_title_html_safe'] . ' - ' . $board_info['name'] . ' - ' . $context['forum_name'];
	}
}