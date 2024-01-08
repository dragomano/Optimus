<?php

namespace Bugo\Optimus\Addons;

/**
 * PrettyUrls.php
 *
 * @package Optimus
 */

if (! defined('SMF'))
	die('No direct access...');

/**
 * Support for PrettyUrls
 */
class PrettyUrls
{
	public function __construct()
	{
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::addSupportKeywordsAction#', false, __FILE__);
		add_integration_function('integrate_optimus_robots', __CLASS__ . '::optimusRobots#', false, __FILE__);
		add_integration_function('integrate_optimus_sitemap_rewrite_content#', __CLASS__ . '::optimusSitemapRewriteContent', false, __FILE__);
	}

	public function addSupportKeywordsAction()
	{
		global $context;

		if (isset($context['pretty']['action_array']))
			$context['pretty']['action_array'][] = 'keywords';
	}

	public function optimusRobots(array &$custom_rules, string $url_path, bool &$use_sef)
	{
		$use_sef = is_on('pretty_enable_filters') && is_file(dirname(__DIR__, 2) . '/PrettyUrls-Filters.php');
	}

	public function optimusSitemapRewriteContent(string &$content)
	{
		global $sourcedir, $context, $smcFunc;

		$pretty = $sourcedir . '/PrettyUrls-Filters.php';
		if (! file_exists($pretty) || is_off('pretty_enable_filters'))
			return;

		if (! function_exists('pretty_rewrite_buffer'))
			require_once($pretty);

		if (! isset($context['session_var']))
			$context['session_var'] = substr(md5($smcFunc['random_int']() . session_id() . $smcFunc['random_int']()), 0, rand(7, 12));

		$context['pretty']['search_patterns']  = ['~(<loc>)([^#<]+)~'];
		$context['pretty']['replace_patterns'] = ['~(<loc>)([^<]+)~'];

		if (function_exists('pretty_rewrite_buffer'))
			$content = pretty_rewrite_buffer($content);
	}
}
