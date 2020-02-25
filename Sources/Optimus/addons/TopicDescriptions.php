<?php

namespace Bugo\Optimus\Addons;

/**
 * TopicDescriptions.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.6.1
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Descriptions for topics (if the Topic Descriptions mod installed)
 */
class TopicDescriptions
{
	/**
	 * Make topic description
	 *
	 * @return void
	 */
	public static function meta()
	{
		global $context;

		if (!empty($context['topic_description']))
			$context['meta_description'] = $context['topic_description'];
	}
}
