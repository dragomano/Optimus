<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Subs;

/**
 * TinyPortal.php
 *
 * @package SMF Optimus
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * TinyPortal addon for Optimus
 */
class TinyPortal
{
	/**
	 * Let's check if the portal installed
	 *
	 * @return boolean
	 */
	private static function isInstalled()
	{
		return function_exists('TPortal_init');
	}

	/**
	 * The description and canonical url of the portal article
	 *
	 * @return void
	 */
	public static function meta()
	{
		global $smcFunc, $txt, $context, $scripturl;

		if (!isset($_GET['page']) || empty(self::isInstalled()))
			return;

		$page_is_num = is_numeric($_GET['page']);

		$request = $smcFunc['db_query']('substring', '
			SELECT a.id, a.date, a.body, a.intro, a.useintro, a.shortname, a.type, v.value1 AS cat_name
			FROM {db_prefix}tp_articles AS a
				INNER JOIN {db_prefix}tp_variables AS v ON (v.id = a.category)
			WHERE a.' . ($page_is_num ? 'id = {int' : 'shortname = {string') . ':page}
			LIMIT 1',
			array(
				'page' => $page_is_num ? (int) $_GET['page'] : (string) $_GET['page']
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			censorText($row['body']);

			$body = $row['type'] == 'bbc' ? parse_bbc($row['body'], false) : ($row['type'] == 'php' ? '<?php' . $row['body'] : $row['body']);

			// Looking for an image in the text of the page
			$first_post_image = preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $body, $value);
			$context['optimus_og_image'] = $first_post_image ? array_pop($value) : null;

			if ($row['useintro'] && !empty($row['intro'])) {
				$intro = $row['type'] == 'bbc' ? parse_bbc($row['intro'], false) : ($row['type'] == 'php' ? '<?php' . $row['intro'] : $row['intro']);
				$intro = Subs::getTeaser($intro);
				$intro = explode('&nbsp;', $intro)[0];
				$intro = shorten_subject(trim($intro), 130);
			} else {
				$body = Subs::getTeaser($body);
				$body = str_replace($txt['quote'], '', $body);
				$body = explode('&nbsp;', $body)[0];
				$body = shorten_subject(trim($body), 130);
			}

			// If there is an intro, use it as a description, otherwise - an excerpt from the text of the page
			$context['optimus_description'] = !empty($intro) ? $intro : $body;
			$context['optimus_og_type']['article']['published_time'] = date('Y-m-d\TH:i:s', $row['date']);
			$context['optimus_og_type']['article']['section'] = $row['cat_name'];
			$context['canonical_url'] = $scripturl . '?page=' . ($row['shortname'] ?: $row['id']);
		}

		$smcFunc['db_free_result']($request);
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

		if (empty(self::isInstalled()))
			return;

		$request = $smcFunc['db_query']('', '
			SELECT a.id, a.date, a.shortname
			FROM {db_prefix}tp_articles AS a
				INNER JOIN {db_prefix}tp_variables AS v ON (v.id = a.category)
			WHERE a.approved = {int:approved}
				AND a.off = {int:off_status}
				AND {int:guests} IN (v.value3)
			ORDER BY a.id DESC',
			array(
				'approved'   => 1, // The article must be approved
				'off_status' => 0, // The article must be active
				'guests'     => -1 // The article category must be available to guests
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$url = $scripturl . '?page=' . ($row['shortname'] ?: $row['id']);

			Subs::runAddons('createSefUrl', array(&$url));

			$links[] = array(
				'loc'     => $url,
				'lastmod' => $row['date']
			);
		}

		$smcFunc['db_free_result']($request);
	}
}
