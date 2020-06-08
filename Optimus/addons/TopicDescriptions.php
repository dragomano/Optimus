<?php

namespace Bugo\Optimus\Addons;

/**
 * TopicDescriptions.php
 *
 * @package Optimus
 *
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
			$context['optimus_description'] = $context['topic_description'];
	}
}
