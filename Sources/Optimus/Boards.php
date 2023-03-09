<?php

declare(strict_types=1);

namespace Bugo\Optimus;

/**
 * Boards.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.10
 */

if (! defined('SMF'))
	die('No direct access...');

final class Boards
{
	public function hooks()
	{
		if (is_off('optimus_allow_change_board_og_image'))
			return;

		add_integration_function('integrate_menu_buttons', __CLASS__ . '::menuButtons', false, __FILE__, true);
		add_integration_function('integrate_load_board', __CLASS__ . '::loadBoard', false, __FILE__, true);
		add_integration_function('integrate_board_info', __CLASS__ . '::boardInfo', false, __FILE__, true);
		add_integration_function('integrate_pre_boardtree', __CLASS__ . '::preBoardtree', false, __FILE__, true);
		add_integration_function('integrate_boardtree_board', __CLASS__ . '::boardtreeBoard', false, __FILE__, true);
		add_integration_function('integrate_edit_board', __CLASS__ . '::editBoard', false, __FILE__, true);
		add_integration_function('integrate_modify_board', __CLASS__ . '::modifyBoard', false, __FILE__, true);
	}

	/**
	 * Looking for an image of the current board
	 */
	public function menuButtons()
	{
		global $board_info, $settings;

		if (! empty($board_info['og_image']))
			$settings['og_image'] = $board_info['og_image'];
	}

	/**
	 * Select optimus_og_image column from boards table
	 *
	 * Выбираем колонку optimus_og_image из таблицы boards
	 */
	public function loadBoard(array &$custom_column_selects)
	{
		$custom_column_selects[] = 'b.optimus_og_image';
	}

	/**
	 * Extend $board_info with og_image key
	 *
	 * Дополняем массив $board_info ключом og_image
	 */
	public function boardInfo(array &$board_info, array $row)
	{
		$board_info['og_image'] = $row['optimus_og_image'];
	}

	/**
	 * Add optimus_og_image to the common query
	 *
	 * Добавляем запрос поля optimus_og_image при просмотре раздела
	 */
	public function preBoardtree(array &$boardColumns)
	{
		$boardColumns[] = 'b.optimus_og_image';
	}

	/**
	 * Add optimus_og_image value to $boards
	 *
	 * Добавляем optimus_og_image в массив $boards
	 */
	public function boardtreeBoard(array $row)
	{
		global $boards;

		$boards[$row['id_board']]['optimus_og_image'] = $row['optimus_og_image'];
	}

	public function editBoard()
	{
		global $context, $txt;

		loadLanguage('Themes');

		$context['custom_board_settings'] = array_merge(
			array(
				array(
					'dt' => '
						<strong>' . $txt['og_image'] . ':</strong><br>
						<span class="smalltext">' . $txt['og_image_desc'] . '</span><br>',
					'dd' => '
						<input type="url" name="optimus_og_image" id="optimus_og_image" value="' . ($context['board']['optimus_og_image'] ?? '') . '" style="width: 100%">',
				)
			),
			$context['custom_board_settings'] ?? []
		);
	}

	/**
	 * Update board optimus_og_image value
	 *
	 * Обновляем значение optimus_og_image для раздела
	 */
	public function modifyBoard(int $id, array $boardOptions, array &$boardUpdates, array &$boardUpdateParameters)
	{
		if (op_is_post('optimus_og_image')) {
			$boardUpdates[] = 'optimus_og_image = {string:og_image}';
			$boardUpdateParameters['og_image'] = op_filter('optimus_og_image', 'url');
		}
	}
}
