<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Subs;

/**
 * TinyPortal.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.5
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * TinyPortal addon for Optimus
 */
class TinyPortal
{
	/**
	 * Let's check if the portal tables exist in the database
	 *
	 * @return boolean
	 */
	private static function arePortalTablesExist()
	{
		global $smcFunc, $db_prefix;

		if (!function_exists('addTPActions'))
			return false;

		db_extend();

		$tp_articles  = $smcFunc['db_list_tables'](false, $db_prefix . 'tp_articles');
		$tp_variables = $smcFunc['db_list_tables'](false, $db_prefix . 'tp_variables');

		return !empty($tp_articles) && !empty($tp_variables);
	}

	/**
	 * Get an array of portal articles ([] = array('url' => link, 'date' => date))
	 *
	 * @param array $links
	 * @return void
	 */
	public static function sitemap(&$links)
	{
		global $smcFunc, $scripturl;

		if (empty(self::arePortalTablesExist()))
			return;

		$request = $smcFunc['db_query']('', '
			SELECT a.id, a.date, a.shortname
			FROM {db_prefix}tp_articles AS a
				INNER JOIN {db_prefix}tp_variables AS v ON (v.id = a.category)
			WHERE a.approved = {int:approved}
				AND a.off = {int:off_status}
				AND {int:guests} IN (v.value3)
			ORDER BY a.id',
			array(
				'approved'   => 1, // The article must be approved
				'off_status' => 0, // The article must be active
				'guests'     => -1 // The article category must be available to guests
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$links[] = array(
				'url'  => $scripturl . '?page=' . ($row['shortname'] ?: $row['id']),
				'date' => $row['date']
			);

		$smcFunc['db_free_result']($request);
	}

	/**
	 * The description and canonical url of the portal article
	 *
	 * @return void
	 */
	public static function meta()
	{
		global $smcFunc, $settings, $txt, $context, $scripturl;

		if (!isset($_GET['page']) || empty(self::arePortalTablesExist()))
			return;

		$page_is_num = is_numeric($_GET['page']);

		$request = $smcFunc['db_query']('substring', '
			SELECT a.id, a.date, a.body, a.intro, a.useintro, a.shortname, a.type, v.value1 AS cat_name
			FROM {db_prefix}tp_articles AS a
				INNER JOIN {db_prefix}tp_variables AS v ON (v.id = a.category)
			WHERE a.' . ($page_is_num ? 'id = {int' : 'shortname = {string') . ':page}
			LIMIT 1',
			array(
				'page' => filter_input(INPUT_GET, 'page', $page_is_num ? FILTER_VALIDATE_INT : FILTER_SANITIZE_STRING)
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);

			$body = $row['type'] == 'bbc' ? parse_bbc($row['body'], false) : ($row['type'] == 'php' ? '<?php' . $row['body'] : $row['body']);

			// Looking for an image in the text of the page
			$first_post_image = preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $body, $value);
			$settings['og_image'] = $first_post_image ? array_pop($value) : null;

			if ($row['useintro'] && !empty($row['intro'])) {
				$intro = $row['type'] == 'bbc' ? parse_bbc($row['intro'], false) : ($row['type'] == 'php' ? '<?php' . $row['intro'] : $row['intro']);
				$intro = Subs::getTeaser($intro);
				$intro = explode('&nbsp;', $intro)[0];
				$intro = shorten_subject($intro, 130);
			} else {
				$body = Subs::getTeaser($body);
				$body = str_replace($txt['quote'], '', $body);
				$body = explode('&nbsp;', $body)[0];
				$body = shorten_subject($body, 130);
			}

			// If there is an intro, use it as a description, otherwise - an excerpt from the text of the page
			$context['meta_description'] = !empty($intro) ? $intro : $body;

			$context['optimus_og_type']['article']['published_time'] = date('Y-m-d\TH:i:s', $row['date']);
			$context['optimus_og_type']['article']['section'] = $row['cat_name'];

			$context['canonical_url'] = $scripturl . '?page=' . ($row['shortname'] ?: $row['id']);
		}

		$smcFunc['db_free_result']($request);
	}
}
