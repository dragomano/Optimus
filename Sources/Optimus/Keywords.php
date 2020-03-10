<?php

namespace Bugo\Optimus;

/**
 * Keywords.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Special class to work with topic keywords
 */
class Keywords
{
	/**
	 * Get all keywords for current topic
	 *
	 * @return void
	 */
	public static function getAll()
	{
		global $context, $smcFunc, $modSettings;

		if (empty($context['current_topic']) || !empty($context['optimus']['keywords']))
			return;

		if (($keywords = cache_get_data('optimus_topic_' . $context['current_topic'] . '_keywords', 3600)) == null) {
			$request = $smcFunc['db_query']('', '
				SELECT k.id, k.name
				FROM {db_prefix}optimus_keywords AS k
					INNER JOIN {db_prefix}optimus_log_keywords AS lk ON (lk.keyword_id = k.id AND lk.topic_id = {int:current_topic})',
				array(
					'current_topic' => $context['current_topic']
				)
			);

			$keywords = [];
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$keywords[$row['id']] = $row['name'];

			$smcFunc['db_free_result']($request);

			cache_put_data('optimus_topic_' . $context['current_topic'] . '_keywords', $keywords, 3600);
		}

		$context['optimus_keywords']  = $keywords;
		$modSettings['meta_keywords'] = implode(', ', $keywords);
	}

	/**
	 * Add keywords
	 *
	 * @param array $keywords
	 * @param int $topic
	 * @param int $user
	 * @return void
	 */
	public static function add($keywords, $topic, $user)
	{
		if (empty($keywords) || empty($topic) || empty($user))
			return;

		foreach ($keywords as $keyword) {
			$keyword_id = self::getKeywordIdByName($keyword);

			if (empty($keyword_id))
				$keyword_id = self::addKeywordToTable($keyword);

			self::addNoteToLogTable($keyword_id, $topic, $user);
		}

		clean_cache();
	}

	/**
	 * Check if the keyword already exists
	 *
	 * @param string $name
	 * @return int
	 */
	private static function getKeywordIdByName($name)
	{
		global $smcFunc;

		$request = $smcFunc['db_query']('', '
			SELECT id
			FROM {db_prefix}optimus_keywords
			WHERE name = {string:name}',
			array(
				'name' => $name
			)
		);

		list ($keyword_id) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return (int) $keyword_id;
	}

	/**
	 * Add keyword to the optimus_keywords table, get id
	 *
	 * @param string $keyword
	 * @return int
	 */
	private static function addKeywordToTable($keyword)
	{
		global $smcFunc;

		return $smcFunc['db_insert']('',
			'{db_prefix}optimus_keywords',
			array(
				'name' => 'string-255'
			),
			array(
				$keyword
			),
			array('id'),
			1
		);
	}

	/**
	 * Add/change the record of the created keyword in the smf_optimus_log_keywords table
	 *
	 * @param int $keyword_id
	 * @param int $topic
	 * @param int $user
	 * @return void
	 */
	private static function addNoteToLogTable($keyword_id, $topic, $user)
	{
		global $smcFunc;

		$smcFunc['db_insert']('',
			'{db_prefix}optimus_log_keywords',
			array(
				'keyword_id' => 'int',
				'topic_id'   => 'int',
				'user_id'    => 'int'
			),
			array(
				$keyword_id,
				$topic,
				$user
			),
			array(
				'keyword_id',
				'topic_id',
				'user_id'
			)
		);
	}

	/**
	 * Remove keywords
	 *
	 * @param array $keywords
	 * @param int $topic
	 * @return void
	 */
	public static function remove($keywords, $topic)
	{
		global $smcFunc;

		if (empty($keywords) || empty($topic))
			return;

		$request = $smcFunc['db_query']('', '
			SELECT lk.keyword_id, lk.topic_id
			FROM {db_prefix}optimus_log_keywords AS lk
				INNER JOIN {db_prefix}optimus_keywords AS k ON (k.id = lk.keyword_id AND k.name IN ({array_string:keywords}) AND lk.topic_id = {int:current_topic})',
			array(
				'keywords'      => $keywords,
				'current_topic' => $topic
			)
		);

		$del_items = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$del_items['keywords'][] = $row['keyword_id'];
			$del_items['topics'][]   = $row['topic_id'];
		}

		$smcFunc['db_free_result']($request);

		if (empty($del_items))
			return;

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}optimus_log_keywords
			WHERE keyword_id IN ({array_int:keywords})
				AND topic_id IN ({array_int:topics})',
			array(
				'keywords' => $del_items['keywords'],
				'topics'   => $del_items['topics']
			)
		);

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}optimus_keywords
			WHERE id NOT IN (SELECT keyword_id FROM {db_prefix}optimus_log_keywords)',
			array()
		);

		clean_cache();
	}

	/**
	 * Output topics with the same keywords
	 *
	 * @return void
	 */
	public static function showTableWithTheSameKeyword()
	{
		global $settings, $context, $txt, $scripturl, $sourcedir;

		if ($context['current_subaction'] == 'search') {
			self::getSearchData();
			return;
		}

		addInlineCss('
		.main_icons.optimus::before {
			background:url(' . $settings['default_images_url'] . '/optimus.png) no-repeat 0 0 !important;
		}');

		$context['optimus_keyword_id'] = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		if (empty($context['optimus_keyword_id'])) {
			self::showTableWithAll();
			return;
		}

		$keyword_name             = self::getNameById($context['optimus_keyword_id']);
		$context['page_title']    = sprintf($txt['optimus_topics_with_keyword'], $keyword_name);
		$context['canonical_url'] = $scripturl . '?action=keywords;id=' . $context['optimus_keyword_id'];

		if (empty($keyword_name)) {
			$context['page_title'] = $txt['optimus_404_page_title'];
			fatal_lang_error('optimus_no_keywords', false, null, 404);
		}

		$context['linktree'][] = array(
			'name' => $txt['optimus_all_keywords'],
			'url'  => $scripturl . '?action=keywords'
		);

		$context['linktree'][] = array(
			'name' => $context['page_title'],
			'url'  => $context['canonical_url']
		);

		$listOptions = array(
			'id'               => 'topics',
			'items_per_page'   => 30,
			'title'            => '',
			'no_items_label'   => '',
			'base_href'        => $scripturl . '?action=keywords;id=' . $context['optimus_keyword_id'],
			'default_sort_col' => 'topic',
			'get_items' => array(
				'function' => __CLASS__ . '::getTopicsByKeyId'
			),
			'get_count' => array(
				'function' => __CLASS__ . '::getNumTopicsByKeyId'
			),
			'columns' => array(
				'topic' => array(
					'header' => array(
						'value' => $txt['topic']
					),
					'data' => array(
						'db' => 'topic'
					),
					'sort' => array(
						'default' => 't.id_topic DESC',
						'reverse' => 't.id_topic'
					)
				),
				'board' => array(
					'header' => array(
						'value' => $txt['board']
					),
					'data' => array(
						'db'    => 'board',
						'class' => 'centertext'
					),
					'sort' => array(
						'default' => 'b.id_board DESC',
						'reverse' => 'b.id_board'
					)
				),
				'author' => array(
					'header' => array(
						'value' => $txt['author']
					),
					'data' => array(
						'db'    => 'author',
						'class' => 'centertext'
					),
					'sort' => array(
						'default' => 'm.real_name DESC',
						'reverse' => 'm.real_name'
					)
				)
			),
			'form' => array(
				'href' => $scripturl . '?action=keywords;id=' . $context['optimus_keyword_id']
			),
			'additional_rows' => array(
				array(
					'position' => 'top_of_list',
					'value'    => '
					<div class="cat_bar">
						<h3 class="catbg">
							<span class="main_icons optimus">' . $context['page_title'] . '</span>
						</h3>
					</div>'
				),
				array(
					'position' => 'below_table_data',
					'value'    => 'Powered by ' . Subs::getOptimusLink(),
					'class'    => 'smalltext centertext'
				)
			)
		);

		require_once($sourcedir . '/Subs-List.php');
		createList($listOptions);

		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'topics';
	}

	/**
	 * Get a list of topics with a given keyword ID
	 *
	 * @param int $start
	 * @param int $items_per_page
	 * @param string $sort
	 * @return array
	 */
	public static function getTopicsByKeyId($start, $items_per_page, $sort)
	{
		global $smcFunc, $context, $scripturl, $txt;

		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, ms.subject, b.id_board, b.name, m.id_member, m.id_group, m.real_name, mg.group_name
			FROM {db_prefix}topics AS t
				LEFT JOIN {db_prefix}optimus_log_keywords AS olk ON (olk.topic_id = t.id_topic)
				LEFT JOIN {db_prefix}optimus_keywords AS ok ON (ok.id = olk.keyword_id)
				LEFT JOIN {db_prefix}messages AS ms ON (ms.id_msg = t.id_first_msg)
				LEFT JOIN {db_prefix}boards AS b ON (b.id_board = ms.id_board)
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = t.id_member_started)
				LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = m.id_group)
			WHERE ok.id = {int:keyword_id}
				AND {query_wanna_see_board}
			ORDER BY ' . $sort . ', t.id_topic DESC
			LIMIT ' . $start . ', ' . $items_per_page,
			array(
				'keyword_id' => $context['optimus_keyword_id']
			)
		);

		$topics = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$topics[] = array(
				'topic'  => '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.0">' . $row['subject'] . '</a>',
				'board'  => '<a href="' . $scripturl . '?board=' . $row['id_board'] . '.0">' . $row['name'] . '</a>',
				'author' => empty($row['real_name']) ? $txt['guest'] : '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['real_name'] . '</a>'
			);
		}

		$smcFunc['db_free_result']($request);

		return $topics;
	}

	/**
	 * Count the total number of topics with a given keyword ID
	 *
	 * @return int
	 */
	public static function getNumTopicsByKeyId()
	{
		global $smcFunc, $context;

		$request = $smcFunc['db_query']('', '
			SELECT COUNT(topic_id)
			FROM {db_prefix}optimus_log_keywords
			WHERE keyword_id = {int:keyword}
			LIMIT 1',
			array(
				'keyword' => $context['optimus_keyword_id']
			)
		);

		list ($num_topics) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return $num_topics;
	}

	/**
	 * Get the keyword by its identifier in the smf_optimus_keywords table
	 *
	 * @param int $id
	 * @return void
	 */
	public static function getNameById($id)
	{
		global $smcFunc;

		if (empty($id))
			return;

		$request = $smcFunc['db_query']('', '
			SELECT name
			FROM {db_prefix}optimus_keywords
			WHERE id = {int:id}
			LIMIT 1',
			array(
				'id' => $id
			)
		);

		list ($name) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return $name;
	}

	/**
	 * Displays all keywords with the frequency of their use in forum topics
	 *
	 * @return void
	 */
	public static function showTableWithAll()
	{
		global $context, $txt, $scripturl, $sourcedir;

		$context['page_title']    = $txt['optimus_all_keywords'];
		$context['canonical_url'] = $scripturl . '?action=keywords';

		$context['linktree'][] = array(
			'name' => $context['page_title'],
			'url'  => $context['canonical_url']
		);

		$listOptions = array(
			'id'               => 'keywords',
			'items_per_page'   => 30,
			'title'            => '',
			'no_items_label'   => '',
			'base_href'        => $scripturl . '?action=keywords',
			'default_sort_col' => 'frequency',
			'get_items' => array(
				'function' => __CLASS__ . '::getAllTopicsWithKeywords'
			),
			'get_count' => array(
				'function' => __CLASS__ . '::getNumKeywords'
			),
			'columns' => array(
				'keyword' => array(
					'header' => array(
						'value' => $txt['optimus_keyword_column']
					),
					'data' => array(
						'db' => 'keyword'
					),
					'sort' => array(
						'default' => 'ok.name DESC',
						'reverse' => 'ok.name'
					)
				),
				'frequency' => array(
					'header' => array(
						'value' => $txt['optimus_frequency_column']
					),
					'data' => array(
						'db'    => 'frequency',
						'class' => 'centertext'
					),
					'sort' => array(
						'default' => 'frequency DESC',
						'reverse' => 'frequency'
					)
				)
			),
			'form' => array(
				'href' => $scripturl . '?action=keywords'
			),
			'additional_rows' => array(
				array(
					'position' => 'top_of_list',
					'value'    => '
					<div class="cat_bar">
						<h3 class="catbg">
							<span class="main_icons optimus">' . $context['page_title'] . '</span>
						</h3>
					</div>'
				),
				array(
					'position' => 'below_table_data',
					'value'    => 'Powered by ' . Subs::getOptimusLink(),
					'class'    => 'smalltext centertext',
				)
			)
		);

		require_once($sourcedir . '/Subs-List.php');
		createList($listOptions);

		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'keywords';
	}

	/**
	 * We get a list of all keywords with their frequency
	 *
	 * @param int $start
	 * @param int $items_per_page
	 * @param string $sort
	 * @return array
	 */
	public static function getAllTopicsWithKeywords($start, $items_per_page, $sort)
	{
		global $smcFunc, $scripturl;

		$request = $smcFunc['db_query']('', '
			SELECT ok.id, ok.name, COUNT(olk.keyword_id) AS frequency
			FROM {db_prefix}optimus_keywords AS ok
				LEFT JOIN {db_prefix}optimus_log_keywords AS olk ON (olk.keyword_id = ok.id)
			GROUP BY ok.id, ok.name
			ORDER BY ' . $sort . '
			LIMIT ' . $start . ', ' . $items_per_page,
			array()
		);

		$keywords = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$keywords[] = array(
				'keyword'   => '<a href="' . $scripturl . '?action=keywords;id=' . $row['id'] . '">' . $row['name'] . '</a>',
				'frequency' => $row['frequency']
			);
		}

		$smcFunc['db_free_result']($request);

		return $keywords;
	}

	/**
	 * Count the total number of keywords
	 *
	 * @return int
	 */
	public static function getNumKeywords()
	{
		global $smcFunc;

		$request = $smcFunc['db_query']('', '
			SELECT COUNT(id)
			FROM {db_prefix}optimus_keywords
			LIMIT 1',
			array()
		);

		list ($num_keywords) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return $num_keywords;
	}

	/**
	 * We request from the database 10 keywords similar to the entered
	 *
	 * @return void
	 */
	public static function getSearchData()
	{
		global $smcFunc;

		$query = $smcFunc['htmltrim']($smcFunc['htmlspecialchars'](filter_input(INPUT_POST, 'q', FILTER_SANITIZE_STRING)));

		if (empty($query))
			exit;

		$request = $smcFunc['db_query']('', '
			SELECT name
			FROM {db_prefix}optimus_keywords
			WHERE name LIKE {string:search}
			ORDER BY name DESC
			LIMIT 10',
			array(
				'search' => '%' . $query . '%'
			)
		);

		$data = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$data[] = [
				'id'   => $row['name'],
				'text' => $row['name']
			];
		}

		$smcFunc['db_free_result']($request);

		exit(json_encode($data));
	}
}
