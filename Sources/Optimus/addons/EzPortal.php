<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Subs;

/**
 * EzPortal.php
 *
 * @package Optimus
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * EzPortal addon for Optimus
 */
class EzPortal
{
	/**
	 * Let's check if the portal installed
	 *
	 * @return boolean
	 */
	private static function isInstalled()
	{
		return function_exists('EzPortalMain');
	}

	/**
	 * The description and canonical url of the portal article
	 *
	 * @return void
	 */
	public static function meta()
	{
		global $smcFunc, $context, $ezpSettings, $boardurl, $scripturl;

		if (!isset($_GET['p']) || empty(self::isInstalled()))
			return;

		$item = (int) $_GET['p'];

		$request = $smcFunc['db_query']('substring', '
			SELECT id_page, date, title, description, content
			FROM {db_prefix}ezp_page
			WHERE id_page = {int:item}
			LIMIT 1',
			array(
				'item' => $item
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$content = un_htmlspecialchars($row['content']);

			// Looking for an image in the text of the page
			$first_post_image = preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $content, $value);
			$context['optimus_og_image'] = $first_post_image ? array_pop($value) : null;

			$context['optimus_description'] = Subs::getTeaser($row['description'] ?: $content);
			$context['optimus_og_type']['article']['published_time'] = date('Y-m-d\TH:i:s', $row['date']);

			if (!empty($ezpSettings['ezp_pages_seourls']))
				$context['canonical_url'] = $boardurl . '/pages/' . MakeSEOUrl($row['title']) . '-' . $row['id_page'];
			else
				$context['canonical_url'] = $scripturl . '?action=ezportal;sa=page;p=' . $row['id_page'];
		}

		$smcFunc['db_free_result']($request);
	}

	/**
	 * Make rules for robots.txt
	 *
	 * @param string $common_rules
	 * @param string $url_path
	 * @return void
	 */
	public static function robots(&$common_rules, $url_path)
	{
		global $ezpSettings;

		if (empty(self::isInstalled()))
			return;

		if (!empty($ezpSettings['ezp_pages_seourls']))
			$common_rules[] = "Allow: " . $url_path . "/pages/";
		else
			$common_rules[] = "Allow: " . $url_path . "/*ezportal;sa=page;p=*";
	}

	/**
	 * Get an array of portal articles ([] = array('url' => link, 'date' => date))
	 *
	 * @param array $links
	 * @return void
	 */
	public static function sitemap(&$links)
	{
		global $smcFunc, $ezpSettings, $boardurl, $scripturl;

		if (empty(self::isInstalled()))
			return;

		$request = $smcFunc['db_query']('', '
			SELECT id_page, date, title, permissions
			FROM {db_prefix}ezp_page
			WHERE {int:guests} IN (permissions)
			ORDER BY id_page DESC',
			array(
				'guests' => -1 // The page must be available to guests
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			if (!empty($ezpSettings['ezp_pages_seourls'])) {
				$url = $boardurl . '/pages/' . MakeSEOUrl($row['title']) . '-' . $row['id_page'];
			} else {
				$url = $scripturl . '?action=ezportal;sa=page;p=' . $row['id_page'];
				Subs::runAddons('createSefUrl', array(&$url));
			}

			$links[] = array(
				'loc'     => $url,
				'lastmod' => $row['date']
			);
		}

		$smcFunc['db_free_result']($request);
	}
}
