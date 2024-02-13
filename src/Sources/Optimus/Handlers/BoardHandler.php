<?php declare(strict_types=1);

/**
 * BoardHandler.php
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

use Bugo\Compat\{Board, Config, IntegrationHook};
use Bugo\Compat\{Lang, Theme, Utils};
use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...');

final class BoardHandler
{
	public function __invoke(): void
	{
		if (empty(Config::$modSettings['optimus_allow_change_board_og_image']))
			return;

		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::menuButtons#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_load_board', self::class . '::loadBoard#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_board_info', self::class . '::boardInfo#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_pre_boardtree', self::class . '::preBoardtree#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_boardtree_board', self::class . '::boardtreeBoard#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_edit_board', self::class . '::editBoard#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_modify_board', self::class . '::modifyBoard#', false, __FILE__
		);
	}

	/**
	 * Looking for an image of the current board
	 */
	public function menuButtons(): void
	{
		if (! empty(Board::$info['og_image']))
			Theme::$current->settings['og_image'] = Board::$info['og_image'];
	}

	/**
	 * Select optimus_og_image column from boards table
	 *
	 * Выбираем колонку optimus_og_image из таблицы boards
	 */
	public function loadBoard(array &$custom_column_selects): void
	{
		$custom_column_selects[] = 'b.optimus_og_image';
	}

	/**
	 * Extend $board_info with og_image key
	 *
	 * Дополняем массив $board_info ключом og_image
	 */
	public function boardInfo(array &$board_info, array $row): void
	{
		$board_info['og_image'] = $row['optimus_og_image'];
	}

	/**
	 * Add optimus_og_image to the common query
	 *
	 * Добавляем запрос поля optimus_og_image при просмотре раздела
	 */
	public function preBoardtree(array &$boardColumns): void
	{
		$boardColumns[] = 'b.optimus_og_image';
	}

	/**
	 * Add optimus_og_image value to $boards
	 *
	 * Добавляем optimus_og_image в массив $boards
	 */
	public function boardtreeBoard(array $row): void
	{
		Board::$loaded[$row['id_board']]['optimus_og_image'] = $row['optimus_og_image'];
	}

	public function editBoard(): void
	{
		Lang::load('Themes');

		Utils::$context['custom_board_settings'] = array_merge(
			[
				[
					'dt' => '
						<strong>' . Lang::$txt['og_image'] . ':</strong><br>
						<span class="smalltext">' . Lang::$txt['og_image_desc'] . '</span><br>',
					'dd' => '
						<input
							type="url"
							name="optimus_og_image"
							id="optimus_og_image"
							value="' . (Utils::$context['board']['optimus_og_image'] ?? '') . '"
							style="width: 100%"
						>',
				]
			],
			Utils::$context['custom_board_settings'] ?? []
		);
	}

	/**
	 * Update board optimus_og_image value
	 *
	 * Обновляем значение optimus_og_image для раздела
	 */
	public function modifyBoard(
		int $id,
		array $boardOptions,
		array &$boardUpdates,
		array &$boardUpdateParameters
	): void
	{
		if (Input::isPost('optimus_og_image')) {
			$boardUpdates[] = 'optimus_og_image = {string:og_image}';
			$boardUpdateParameters['og_image'] = Input::filter('optimus_og_image', 'url');
		}
	}
}
