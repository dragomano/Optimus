<?php

namespace Bugo\Optimus\Addons;

/**
 * ExampleAddon.php
 *
 * @package Optimus
 */

if (! defined('SMF'))
	die('No direct access...');

/**
 * Simple example of your addon
 */
class ExampleAddon
{
	public function __construct()
	{
		//add_integration_function('integrate_menu_buttons', __CLASS__ . '::menuButtons', false, __FILE__, true);
	}

	public function menuButtons()
	{
		global $context;

		if (empty($context['topicinfo']))
			return;

		if ($context['topicinfo']['locked'] || $context['topicinfo']['num_replies'] < 2) {
			$context['meta_tags'][] = array('name' => 'robots', 'content' => 'noindex,nofollow');
		}
	}
}
