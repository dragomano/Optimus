<?php

namespace Bugo\Optimus;

/**
 * Sitemap-Addons.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2019 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.1
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class SitemapAddons
{
	public static function getTPLinks()
	{
		global $smcFunc, $scripturl;

		$tp_articles_exists = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}tp_articles'", array());
		$tp_variables_exists = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}tp_variables'", array());
		$result = $smcFunc['db_num_rows']($tp_articles_exists) != 0 && $smcFunc['db_num_rows']($tp_variables_exists) != 0;

		if (empty($result))
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
				'approved'   => 1, // Статья должна быть одобрена
				'off_status' => 0, // Статья должна быть активна
				'guests'     => -1 // Категория статьи должна быть доступна гостям
			)
		);

		$articles = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$articles[] = $row;

		$smcFunc['db_free_result']($request);

		if (!empty($articles)) {
			foreach ($articles as $entry)	{
				$links[] = array(
					'url'  => $scripturl . '?page=' . ($entry['shortname'] ?: $entry['id']),
					'date' => $entry['date']
				);
			}
		}

		return $links;
	}
}
