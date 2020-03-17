<?php

namespace Bugo\Optimus;

/**
 * BoardHooks.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7.2
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Optimus settings
 */
class BoardHooks
{
	/**
	 * Select optimus_og_image column from boards table
	 *
	 * @param array $custom_column_selects
	 * @return void
	 */
	public static function loadBoard(&$custom_column_selects)
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_board_og_image']))
			return;

		$custom_column_selects[] = 'b.optimus_og_image';
	}

	/**
	 * Extend $board_info of the current board
	 *
	 * @param array $board_info
	 * @param array $row
	 * @return void
	 */
	public static function boardInfo(&$board_info, $row)
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_board_og_image']))
			return;

		$board_info['og_image'] = $row['optimus_og_image'];
	}

	/**
	 * Add optimus_og_image to the common query
	 *
	 * @param array $boardColumns
	 * @return void
	 */
	public static function preBoardtree(&$boardColumns)
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_board_og_image']))
			return;

		$boardColumns[] = 'b.optimus_og_image';
	}

	/**
	 * Save optimus_og_image value to a session
	 *
	 * @param array $row
	 * @return void
	 */
	public static function boardtreeBoard($row)
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_board_og_image']))
			return;

		if (!isset($_SESSION['optimus_board_og_image']))
			$_SESSION['optimus_board_og_image'] = $row['optimus_og_image'];
	}

	/**
	 * Add/edit board
	 *
	 * @return void
	 */
	public static function editBoard()
	{
		global $modSettings, $context, $txt;

		if (empty($modSettings['optimus_allow_change_board_og_image']))
			return;

		loadLanguage('Themes');

		if ($_REQUEST['sa'] == 'newboard')
			$context['board']['optimus_og_image'] = '';
		else
			$context['board']['optimus_og_image'] = $_SESSION['optimus_board_og_image'] ?? '';

		if (empty($context['custom_board_settings']))
			$context['custom_board_settings'] = array();

		$context['custom_board_settings'] = array_merge(array(
			array(
				'dt' => '
					<strong>' . $txt['og_image'] . ':</strong><br>
					<span class="smalltext">' . $txt['og_image_desc']. '</span><br>',
				'dd' => '
					<input type="url" size="40" name="optimus_og_image" id="optimus_og_image" value="' . (!empty($context['board']['optimus_og_image']) ? $context['board']['optimus_og_image'] : '') . '">',
			)
		), $context['custom_board_settings']);
	}

	/**
	 * Update board optimus_og_image value
	 *
	 * @param int $id
	 * @param array $boardOptions
	 * @param array $boardUpdates
	 * @param array $boardUpdateParameters
	 * @return void
	 */
	public static function modifyBoard($id, $boardOptions, &$boardUpdates, &$boardUpdateParameters)
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_board_og_image']))
			return;

		if (isset($_REQUEST['optimus_og_image'])) {
			$boardUpdates[] = 'optimus_og_image = {string:og_image}';
			$boardUpdateParameters['og_image'] = Subs::xss($_REQUEST['optimus_og_image']);
		}

		unset($_SESSION['optimus_board_og_image']);
	}
}
