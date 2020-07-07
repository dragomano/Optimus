<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Subs;

/**
 * TopicLinks.php
 *
 * @package SMF Optimus
 *
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class TopicLinks
{
	/**
	 * Get an array of forum topics ([] = array('url' => link, 'date' => date))
	 *
	 * @param array $links
	 * @return array
	 */
	public static function sitemap(&$links)
	{
		global $db_temp_cache, $db_cache, $modSettings, $smcFunc, $context, $scripturl;

		$start = 0;
		$limit = 1000;

		// Don't allow the cache to get too full
		$db_temp_cache = $db_cache;
		$db_cache = [];

		while ($start < $modSettings['totalTopics']) {
			@set_time_limit(600);
			if (function_exists('apache_reset_timeout'))
				@apache_reset_timeout();

			$request = $smcFunc['db_query']('', '
				SELECT t.id_topic, GREATEST(m.poster_time, m.modified_time) AS last_date
				FROM {db_prefix}topics AS t
					INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_last_msg)
				WHERE t.id_board IN ({array_int:open_boards})
					AND t.num_replies > {int:num_replies}
					AND t.approved = {int:is_approved}
				ORDER BY t.id_topic DESC
				LIMIT {int:start}, {int:limit}',
				array(
					'open_boards' => $context['optimus_open_boards'],
					'num_replies' => !empty($modSettings['optimus_sitemap_topics_num_replies']) ? (int) $modSettings['optimus_sitemap_topics_num_replies'] : -1,
					'is_approved' => 1,
					'start'       => $start,
					'limit'       => $limit
				)
			);

			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				$topic_url = $scripturl . '?topic=' . $row['id_topic'] . '.0';

				if (!empty($modSettings['queryless_urls']))
					$topic_url = $scripturl . '/topic,' . $row['id_topic'] . '.0.html';

				Subs::runAddons('createSefUrl', array(&$topic_url));

				$links[] = array(
					'loc'     => $topic_url,
					'lastmod' => $row['last_date']
				);
			}

			$smcFunc['db_free_result']($request);

			$start = $start + $limit;
		}

		// Restore the cache
		$db_cache = $db_temp_cache;

		return $links;
	}
}
