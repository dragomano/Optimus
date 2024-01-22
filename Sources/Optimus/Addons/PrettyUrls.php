<?php declare(strict_types=1);

/**
 * PrettyUrls.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

use function pretty_rewrite_buffer;

if (! defined('SMF'))
	die('No direct access...');

/**
 * Support for PrettyUrls
 */
class PrettyUrls extends AbstractAddon
{
	public function __construct()
	{
		parent::__construct();

		$this->addSupportKeywordsAction();

		$this->dispatcher->subscribeTo('robots.rules', [$this, 'changeRobots']);
		$this->dispatcher->subscribeTo('sitemap.rewrite_content', [$this, 'rewriteContent']);
	}

	public function addSupportKeywordsAction(): void
	{
		global $context;

		if (isset($context['pretty']['action_array']))
			$context['pretty']['action_array'][] = 'keywords';
	}

	public function changeRobots(object $object): void
	{
		global $modSettings;

		$object->getTarget()->useSef = ! empty($modSettings['pretty_enable_filters'])
			&& is_file(dirname(__DIR__, 2) . '/PrettyUrls-Filters.php');
	}

	public function rewriteContent(object $object): void
	{
		global $sourcedir, $modSettings, $context, $smcFunc;

		$pretty = $sourcedir . '/PrettyUrls-Filters.php';
		if (! file_exists($pretty) || empty($modSettings['pretty_enable_filters']))
			return;

		if (! function_exists('pretty_rewrite_buffer'))
			require_once($pretty);

		if (! isset($context['session_var']))
			$context['session_var'] = substr(md5($smcFunc['random_int']() . session_id() . $smcFunc['random_int']()), 0, rand(7, 12));

		$context['pretty']['search_patterns']  = ['~(<loc>)([^#<]+)~'];
		$context['pretty']['replace_patterns'] = ['~(<loc>)([^<]+)~'];

		if (function_exists('pretty_rewrite_buffer'))
			pretty_rewrite_buffer($object->getTarget()->content);
	}
}
