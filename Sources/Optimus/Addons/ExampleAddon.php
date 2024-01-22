<?php

/**
 * ExampleAddon.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

if (! defined('SMF'))
	die('No direct access...');

/**
 * Simple example of your addon
 */
class ExampleAddon extends AbstractAddon
{
	public function __construct()
	{
		parent::__construct();

		//$this->hideLockedTopicsForSpiders();
	}

	public function hideLockedTopicsForSpiders(): void
	{
		global $context;

		if (empty($context['topicinfo']))
			return;

		if ($context['topicinfo']['locked'] || $context['topicinfo']['num_replies'] < 2) {
			$context['meta_tags'][] = array('name' => 'robots', 'content' => 'noindex,nofollow');
		}
	}
}
