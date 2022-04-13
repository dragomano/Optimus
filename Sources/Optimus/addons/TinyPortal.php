<?php

namespace Bugo\Optimus\Addons;

/**
 * TinyPortal.php
 *
 * @package Optimus
 */

if (! defined('SMF'))
	die('No direct access...');

/**
 * TinyPortal addon for Optimus
 */
class TinyPortal
{
	public function __construct()
	{
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::prepareArticleMeta', false, __FILE__, true);
		add_integration_function('integrate_optimus_robots', __CLASS__ . '::optimusRobots', false, __FILE__, true);
		add_integration_function('integrate_optimus_sitemap', __CLASS__ . '::optimusSitemap', false, __FILE__, true);
	}

	public function prepareArticleMeta()
	{
		global $context, $settings, $scripturl;

		if (! op_is_get('page') || empty($context['TPortal']['article']))
			return;

		$pattern = $context['TPortal']['article']['rendertype'] == 'bbc' ? '/\[img.*]([^\]\[]+)\[\/img\]/U' : '/<img(.*)src(.*)=(.*)"(.*)"/U';
		$first_post_image = preg_match($pattern, $context['TPortal']['article']['body'], $value);
		$settings['og_image'] = $first_post_image ? array_pop($value) : null;

		$context['meta_description'] = op_teaser(empty($context['TPortal']['article']['intro']) ? $context['TPortal']['article']['body'] : $context['TPortal']['article']['intro']);
		$context['optimus_og_type']['article']['published_time'] = date('Y-m-d\TH:i:s', $context['TPortal']['article']['date']);
		$context['optimus_og_type']['article']['section'] = $context['TPortal']['article']['category_name'] ?? '';
		$context['canonical_url'] = $scripturl . '?page=' . ($context['TPortal']['article']['shortname'] ?: $context['TPortal']['article']['id']);
	}

	public function optimusRobots(array &$common_rules, string $url_path)
	{
		if (! function_exists('TPortal'))
			return;

		$common_rules[] = "Allow: " . $url_path . "/*page";
	}

	public function optimusSitemap(array &$links)
	{
		global $smcFunc, $scripturl;

		if (! class_exists('\TinyPortal\Integrate'))
			return;

		$start_year = (int) op_config('optimus_start_year', 0);

		$request = $smcFunc['db_query']('', '
			SELECT a.id, a.date, a.shortname
			FROM {db_prefix}tp_articles AS a
				INNER JOIN {db_prefix}tp_variables AS v ON (a.category = v.id)
			WHERE a.approved = {int:approved}
				AND a.off = {int:off_status}
				AND {int:guests} IN (v.value3)' . ($start_year ? '
				AND YEAR(FROM_UNIXTIME(a.date)) >= {int:start_year}' : '') . '
			ORDER BY a.id DESC',
			array(
				'approved'   => 1, // The article must be approved
				'off_status' => 0, // The article must be active
				'guests'     => -1, // The article category must be available to guests
				'start_year' => $start_year
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$url = $scripturl . '?page=' . ($row['shortname'] ?: $row['id']);

			call_integration_hook('integrate_optimus_create_sef_url', array(&$url));

			$links[] = array(
				'loc'     => $url,
				'lastmod' => $row['date']
			);
		}

		$smcFunc['db_free_result']($request);
	}
}
