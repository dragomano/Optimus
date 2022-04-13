<?php

namespace Bugo\Optimus\Addons;

/**
 * TopicDescriptions.php
 *
 * @package Optimus
 */

if (! defined('SMF'))
	die('No direct access...');

/**
 * Work with Topic Descriptions mod
 */
class TopicDescriptions
{
	public function __construct()
	{
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::menuButtons', false, __FILE__, true);
	}

	public function menuButtons()
	{
		global $context;

		if (! empty($context['topic_description']))
			$context['meta_description'] = $context['topic_description'];
	}
}
