<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Subs;

/**
 * BoardLinks.php
 *
 * @package SMF Optimus
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class BoardLinks
{
	/**
	 * Get an array of forum boards ([] = array('url' => link, 'date' => date))
	 *
	 * @param array $links
	 * @return array
	 */
	public static function sitemap(&$links)
	{
		global $smcFunc, $context, $modSettings, $scripturl;

		$request = $smcFunc['db_query']('', '
			SELECT b.id_board, GREATEST(m.poster_time, m.modified_time) AS last_date
			FROM {db_prefix}boards AS b
				LEFT JOIN {db_prefix}messages AS m ON (m.id_msg = b.id_last_msg)
			WHERE FIND_IN_SET(-1, b.member_groups) != 0' . (!empty($context['optimus_ignored_boards']) ? '
				AND b.id_board NOT IN ({array_int:ignored_boards})' : '') . '
				AND b.redirect = {string:empty_string}
				AND b.num_posts > {int:num_posts}
			ORDER BY b.id_board DESC',
			array(
				'ignored_boards' => $context['optimus_ignored_boards'],
				'empty_string'   => '',
				'num_posts'      => 0
			)
		);

		$context['optimus_open_boards'] = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['optimus_open_boards'][] = $row['id_board'];

			if (!empty($modSettings['optimus_sitemap_boards'])) {
				$board_url = $scripturl . '?board=' . $row['id_board'] . '.0';

				if (!empty($modSettings['queryless_urls']))
					$board_url = $scripturl . '/board,' . $row['id_board'] . '.0.html';

				Subs::runAddons('createSefUrl', array(&$board_url));

				$links[] = array(
					'loc'     => $board_url,
					'lastmod' => $row['last_date']
				);
			}
		}

		$smcFunc['db_free_result']($request);

		return $links;
	}
}
