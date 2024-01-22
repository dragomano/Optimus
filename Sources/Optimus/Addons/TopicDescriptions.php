<?php declare(strict_types=1);

/**
 * TopicDescriptions.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

if (! defined('SMF'))
	die('No direct access...');

/**
 * Work with Topic Descriptions mod
 */
class TopicDescriptions extends AbstractAddon
{
	public function __construct()
	{
		global $context;

		parent::__construct();

		if (! empty($context['topic_description']))
			$context['meta_description'] = $context['topic_description'];
	}
}
