<?php

namespace Bugo\Optimus\Addons;

/**
 * BoardNoIndex.php
 *
 * @package Optimus
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Support for BoardNoIndex
 */
class BoardNoIndex
{
	/**
	 * Добавляем разделы, которые отмечены как неиндексируемые, в игнорируемые
	 *
	 * @return void
	 */
	public static function meta()
	{
		global $context, $modSettings;

		if (!isset($context['optimus_ignored_boards']))
			$context['optimus_ignored_boards'] = array();

		if (!empty($modSettings['BoardNoIndex_enabled']) && !empty($modSettings['BoardNoIndex_select_boards']))
			$context['optimus_ignored_boards'] += unserialize($modSettings['BoardNoIndex_select_boards']);
	}
}
