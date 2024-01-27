<?php declare(strict_types=1);

/**
 * TagHandler.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Optimus\Utils\Copyright;
use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...');

final class TagHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_actions', self::class . '::actions#', false, __FILE__);
		add_integration_function('integrate_menu_buttons', self::class . '::menuButtons#', false, __FILE__);
		add_integration_function('integrate_current_action', self::class . '::currentAction#', false, __FILE__);
		add_integration_function('integrate_load_permissions', self::class . '::loadPermissions#', false, __FILE__);
		add_integration_function('integrate_optimus_basic_settings', self::class . '::basicSettings#', false, __FILE__);
		add_integration_function('integrate_messageindex_buttons', self::class . '::messageindexButtons#', false, __FILE__);
		add_integration_function('integrate_display_topic', self::class . '::displayTopic#', false, __FILE__);
		add_integration_function('integrate_prepare_display_context', self::class . '::prepareDisplayContext#', false, __FILE__);
		add_integration_function('integrate_create_topic', self::class . '::createTopic#', false, __FILE__);
		add_integration_function('integrate_post_end', self::class . '::postEnd#', false, __FILE__);
		add_integration_function('integrate_modify_post', self::class . '::modifyPost#', false, __FILE__);
		add_integration_function('integrate_remove_topics', self::class . '::removeTopics#', false, __FILE__);
	}

	public function actions(array &$actions): void
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_topic_keywords']) && empty($modSettings['optimus_show_keywords_block']))
			return;

		$actions['keywords'] = [false, [$this, 'showTheSame']];
	}

	public function menuButtons(array &$buttons): void
	{
		global $context;

		if (isset($buttons['home']) && $context['current_action'] === 'keywords')
			$buttons['home']['action_hook'] = true;
	}

	public function currentAction(string &$current_action): void
	{
		global $context;

		if ($context['current_action'] === 'keywords')
			$current_action = 'home';
	}

	public function loadPermissions(array $permissionGroups, array &$permissionList): void
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_topic_keywords']))
			return;

		$permissionList['membergroup']['optimus_add_keywords'] = [true, 'general', 'view_basic_info'];
	}

	public function basicSettings(array &$config_vars): void
	{
		global $txt;

		$counter = 0;
		foreach ($config_vars as $key => $dump) {
			if (isset($dump[1]) && $dump[1] === 'optimus_topic_extend_title') {
				$counter = $key + 1;
				break;
			}
		}

		$config_vars = array_merge(
			array_slice($config_vars, 0, $counter, true),
			[
				'',
				['check', 'optimus_allow_change_topic_keywords', 'subtext' => $txt['optimus_allow_change_topic_keywords_subtext']],
				['check', 'optimus_show_keywords_block'],
				['check', 'optimus_show_keywords_on_message_index'],
				['check', 'optimus_allow_keyword_phrases'],
				['check', 'optimus_use_color_tags'],
			],
			array_slice($config_vars, $counter, null, true)
		);
	}

	public function messageindexButtons(): void
	{
		global $modSettings, $context, $scripturl;

		if (empty($modSettings['optimus_show_keywords_on_message_index']) || empty($context['topics']))
			return;

		addInlineCss('.optimus_keywords:visited { color: transparent }');

		foreach ($context['topics'] as $topic => &$data) {
			$keywords = $this->getKeywords()[$topic] ?? [];

			foreach ($keywords as $id => $key) {
				$data['first_post']['link'] .= ' <a class="optimus_keywords amt" href="' . $scripturl . '?action=keywords;id=' . $id . '" style="' . $this->getRandomColor($key) . '">' . $key . '</a>';
			}
		}
	}

	public function prepareDisplayContext(array $output): void
	{
		global $context, $modSettings, $options, $txt, $scripturl;

		if (empty($context['optimus_keywords']) || empty($modSettings['optimus_show_keywords_block']))
			return;

		$counter = empty($options['view_newest_first']) ? $context['start'] : $context['total_visible_posts'] - $context['start'];

		if ($counter == $output['counter'] && empty($context['start'])) {
			$keywords = '<fieldset class="roundframe" style="overflow: unset"><legend class="amt" style="padding: .2em .4em"> ' . $txt['optimus_seo_keywords'] . ' </legend>';

			$class = empty($modSettings['optimus_use_color_tags']) ? 'button' : 'descbox';

			foreach ($context['optimus_keywords'] as $id => $keyword) {
				$href = $scripturl . '?action=keywords;id=' . $id;
				$style = 'margin-right: 2px;' . $this->getRandomColor($keyword);
				$keywords .= '<a class="' . $class . '" href="' . $href . '" style="' . $style . '">' . $keyword . '</a>';
			}

			$keywords .= '</fieldset>';

			echo $keywords;
		}
	}

	public function createTopic(array $msgOptions, array $topicOptions, array $posterOptions): void
	{
		if (! $this->canChange())
			return;

		$keywords = Input::xss(Input::request('optimus_keywords', []));

		$this->add($keywords, $topicOptions['id'], $posterOptions['id']);
	}

	public function postEnd(): void
	{
		global $context, $txt, $modSettings;

		if (! $this->canChange())
			return;

		if ($context['is_new_topic']) {
			$context['optimus']['keywords'] = Input::xss(Input::request('optimus_keywords', ''));
		} else {
			$this->displayTopic();
			$context['optimus']['keywords'] = empty($context['optimus_keywords']) ? [] : array_values($context['optimus_keywords']);
		}

		if (empty($context['is_first_post']))
			return;

		$context['posting_fields']['optimus_keywords'] = [
			'label' => [
				'text' => $txt['optimus_seo_keywords']
			],
			'input' => [
				'type' => 'select',
				'attributes' => [
					'id'       => 'optimus_keywords',
					'name'     => 'optimus_keywords[]',
					'multiple' => true
				],
				'options' => []
			]
		];

		if (! empty($context['optimus']['keywords'])) {
			foreach ($context['optimus']['keywords'] as $key) {
				$context['posting_fields']['optimus_keywords']['input']['options'][$key] = [
					'value'    => $key,
					'selected' => true
				];
			}
		}

		// Select2 https://select2.github.io/select2/
		loadCSSFile('https://cdn.jsdelivr.net/npm/select2@4/dist/css/select2.min.css', ['external' => true]);
		loadJavaScriptFile('https://cdn.jsdelivr.net/npm/select2@4/dist/js/select2.min.js', ['external' => true]);
		loadJavaScriptFile('https://cdn.jsdelivr.net/npm/select2@4/dist/js/i18n/' . $txt['lang_dictionary'] . '.js', ['external' => true]);
		addInlineJavaScript('
		jQuery(document).ready(function ($) {
			$("#optimus_keywords").select2({
				language: "' . $txt['lang_dictionary'] . '",
				placeholder: "' . $txt['optimus_enter_keywords'] . '",
				minimumInputLength: 2,
				width: "100%",
				cache: true,
				tags: true,' . ($context['right_to_left'] ? '
				dir: "rtl",' : '') . '
				tokenSeparators: [","' . (empty($modSettings['optimus_allow_keyword_phrases']) ? ', " "' : '') . '],
				ajax: {
					url: smf_scripturl + "?action=keywords;sa=search",
					type: "POST",
					delay: 250,
					dataType: "json",
					data: function (params) {
						return {
							q: params.term
						}
					},
					processResults: function (data, params) {
						return {
							results: data
						}
					}
				}
			});
		});', true);
	}

	public function modifyPost(array $messages_columns, array $update_parameters, array $msgOptions, array $topicOptions, array $posterOptions): void
	{
		if (empty($topicOptions['first_msg']) || $topicOptions['first_msg'] != $msgOptions['id'])
			return;

		$this->modify($topicOptions['id'], $posterOptions['id']);
	}

	public function removeTopics(array $topics): void
	{
		global $smcFunc;

		if (empty($topics))
			return;

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}optimus_log_keywords
			WHERE topic_id IN ({array_int:topics})',
			[
				'topics' => $topics
			]
		);
	}

	public function showTheSame(): void
	{
		global $context, $settings, $txt, $scripturl, $sourcedir;

		if ($context['current_subaction'] == 'search') {
			$this->prepareSearchData();
			return;
		}

		addInlineCss('
		.main_icons.optimus::before {
			background:url(' . $settings['default_images_url'] . '/optimus.png) no-repeat 0 0 !important;
		}');

		$context['optimus_keyword_id'] = (int) Input::request('id', 0);

		loadTemplate('Optimus');
		$context['template_layers'][] = 'keywords';

		if (empty($context['optimus_keyword_id'])) {
			$this->showAllWithFrequency();
			return;
		}

		$keyword_name             = $this->getNameById($context['optimus_keyword_id']);
		$context['page_title']    = sprintf($txt['optimus_topics_with_keyword'], $keyword_name);
		$context['canonical_url'] = $scripturl . '?action=keywords;id=' . $context['optimus_keyword_id'];

		if (empty($keyword_name)) {
			$context['page_title'] = $txt['optimus_404_page_title'];
			send_http_status(404);
		}

		$context['linktree'][] = [
			'name' => $txt['optimus_all_keywords'],
			'url'  => $scripturl . '?action=keywords'
		];

		$context['linktree'][] = [
			'name' => $context['page_title'],
			'url'  => $context['canonical_url']
		];

		$listOptions = [
			'id'               => 'topics',
			'items_per_page'   => 30,
			'title'            => '',
			'no_items_label'   => $txt['optimus_no_keywords'],
			'base_href'        => $scripturl . '?action=keywords;id=' . $context['optimus_keyword_id'],
			'default_sort_col' => 'topic',
			'get_items' => [
				'function' => [$this, 'getAllByKeyId']
			],
			'get_count' => [
				'function' => [$this, 'getTotalCountByKeyId']
			],
			'columns' => [
				'topic' => [
					'header' => [
						'value' => $txt['topic']
					],
					'data' => [
						'db' => 'topic'
					],
					'sort' => [
						'default' => 't.id_topic DESC',
						'reverse' => 't.id_topic'
					]
				],
				'board' => [
					'header' => [
						'value' => $txt['board']
					],
					'data' => [
						'db'    => 'board',
						'class' => 'centertext'
					],
					'sort' => [
						'default' => 'b.id_board DESC',
						'reverse' => 'b.id_board'
					]
				],
				'author' => [
					'header' => [
						'value' => $txt['author']
					],
					'data' => [
						'db'    => 'author',
						'class' => 'centertext'
					],
					'sort' => [
						'default' => 'm.real_name DESC',
						'reverse' => 'm.real_name'
					]
				]
			],
			'form' => [
				'href' => $scripturl . '?action=keywords;id=' . $context['optimus_keyword_id']
			],
			'additional_rows' => [
				[
					'position' => 'below_table_data',
					'value'    => 'Powered by ' . Copyright::getLink(),
					'class'    => 'smalltext centertext'
				]
			]
		];

		require_once($sourcedir . '/Subs-List.php');
		createList($listOptions);

		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'topics';
	}

	public function getAllByKeyId(int $start, int $items_per_page, string $sort): array
	{
		global $smcFunc, $context, $scripturl, $txt;

		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, ms.subject, b.id_board, b.name, m.id_member, m.id_group, m.real_name, mg.group_name
			FROM {db_prefix}topics AS t
				LEFT JOIN {db_prefix}optimus_log_keywords AS olk ON (t.id_topic = olk.topic_id)
				LEFT JOIN {db_prefix}optimus_keywords AS ok ON (olk.keyword_id = ok.id)
				LEFT JOIN {db_prefix}messages AS ms ON (t.id_first_msg = ms.id_msg)
				LEFT JOIN {db_prefix}boards AS b ON (ms.id_board = b.id_board)
				LEFT JOIN {db_prefix}members AS m ON (t.id_member_started = m.id_member)
				LEFT JOIN {db_prefix}membergroups AS mg ON (m.id_group = mg.id_group)
			WHERE ok.id = {int:keyword_id}
				AND {query_wanna_see_board}
			ORDER BY {raw:sort}, t.id_topic DESC
			LIMIT {int:start}, {int:limit}',
			[
				'keyword_id' => $context['optimus_keyword_id'],
				'sort'       => $sort,
				'start'      => $start,
				'limit'      => $items_per_page
			]
		);

		$topics = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$href = $scripturl . '?action=profile;u=' . $row['id_member'];

			$topics[] = [
				'topic'  => '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.0">' . $row['subject'] . '</a>',
				'board'  => '<a href="' . $scripturl . '?board=' . $row['id_board'] . '.0">' . $row['name'] . '</a>',
				'author' => empty($row['real_name']) ? $txt['guest'] : '<a href="' . $href . '">' . $row['real_name'] . '</a>'
			];
		}

		$smcFunc['db_free_result']($request);

		return $topics;
	}

	public function getTotalCountByKeyId(): int
	{
		global $smcFunc, $context;

		$request = $smcFunc['db_query']('', '
			SELECT COUNT(topic_id)
			FROM {db_prefix}optimus_log_keywords
			WHERE keyword_id = {int:keyword}
			LIMIT 1',
			[
				'keyword' => $context['optimus_keyword_id']
			]
		);

		[$num] = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return (int) $num;
	}

	public function showAllWithFrequency(): void
	{
		global $context, $txt, $scripturl, $sourcedir;

		$context['page_title']    = $txt['optimus_all_keywords'];
		$context['canonical_url'] = $scripturl . '?action=keywords';

		$context['linktree'][] = [
			'name' => $context['page_title'],
			'url'  => $context['canonical_url']
		];

		$listOptions = [
			'id'               => 'keywords',
			'items_per_page'   => 30,
			'title'            => '',
			'no_items_label'   => '',
			'base_href'        => $scripturl . '?action=keywords',
			'default_sort_col' => 'frequency',
			'get_items' => [
				'function' => [$this, 'getAll']
			],
			'get_count' => [
				'function' => [$this, 'getTotalCount']
			],
			'columns' => [
				'keyword' => [
					'header' => [
						'value' => $txt['optimus_keyword_column']
					],
					'data' => [
						'db' => 'keyword'
					],
					'sort' => [
						'default' => 'ok.name DESC',
						'reverse' => 'ok.name'
					]
				],
				'frequency' => [
					'header' => [
						'value' => $txt['optimus_frequency_column']
					],
					'data' => [
						'db'    => 'frequency',
						'class' => 'centertext'
					],
					'sort' => [
						'default' => 'frequency DESC',
						'reverse' => 'frequency'
					]
				]
			],
			'form' => [
				'href' => $scripturl . '?action=keywords'
			],
			'additional_rows' => [
				[
					'position' => 'below_table_data',
					'value'    => 'Powered by ' . Copyright::getLink(),
					'class'    => 'smalltext centertext',
				]
			]
		];

		require_once($sourcedir . '/Subs-List.php');
		createList($listOptions);

		if (! empty($context['current_page']) && $context['current_page'] != (int) Input::request('start'))
			send_http_status(404);

		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'keywords';
	}

	public function getAll(int $start, int $items_per_page, string $sort): array
	{
		global $smcFunc, $scripturl;

		$request = $smcFunc['db_query']('', '
			SELECT ok.id, ok.name, COUNT(olk.keyword_id) AS frequency
			FROM {db_prefix}optimus_keywords AS ok
				LEFT JOIN {db_prefix}optimus_log_keywords AS olk ON (ok.id = olk.keyword_id)
			GROUP BY ok.id, ok.name
			ORDER BY {raw:sort}
			LIMIT {int:start}, {int:limit}',
			[
				'sort'  => $sort,
				'start' => $start,
				'limit' => $items_per_page
			]
		);

		$keywords = [];
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$keywords[] = [
				'keyword'   => '<a href="' . $scripturl . '?action=keywords;id=' . $row['id'] . '">' . $row['name'] . '</a>',
				'frequency' => $row['frequency']
			];
		}

		$smcFunc['db_free_result']($request);

		return $keywords;
	}

	public function getTotalCount(): int
	{
		global $smcFunc;

		$request = $smcFunc['db_query']('', /** @lang text */ '
			SELECT COUNT(id)
			FROM {db_prefix}optimus_keywords
			LIMIT 1',
			[]
		);

		[$num] = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return (int) $num;
	}

	private function getNameById(int $id = 0): string
	{
		global $smcFunc;

		if (empty($id))
			return '';

		$request = $smcFunc['db_query']('', '
			SELECT name
			FROM {db_prefix}optimus_keywords
			WHERE id = {int:id}
			LIMIT 1',
			[
				'id' => $id
			]
		);

		[$name] = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return $name;
	}

	/**
	 * Request from the database 10 keywords similar to the entered
	 *
	 * Запрашиваем из базы данных 10 наиболее похожих ключевых слов (когда добавляем новое)
	 */
	private function prepareSearchData(): void
	{
		global $smcFunc;

		$query = $smcFunc['htmltrim'](Input::filter('q'));

		if (empty($query))
			exit;

		$request = $smcFunc['db_query']('', '
			SELECT name
			FROM {db_prefix}optimus_keywords
			WHERE name LIKE {string:search}
			ORDER BY name DESC
			LIMIT 10',
			[
				'search' => '%' . $query . '%'
			]
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

	public function displayTopic(): void
	{
		global $context, $modSettings;

		if (empty($modSettings['optimus_show_keywords_block']))
			return;

		if (empty($context['current_topic']) || ! empty($context['optimus']['keywords']))
			return;

		$keywords = $this->getKeywords();

		$context['optimus_keywords'] = [];
		if (! empty($keywords[$context['current_topic']])) {
			$context['optimus_keywords']  = $keywords[$context['current_topic']];
			$modSettings['meta_keywords'] = implode(', ', $context['optimus_keywords']);
		}
	}

	private function getKeywords(): array
	{
		global $smcFunc;

		if (($keywords = cache_get_data('optimus_topic_keywords', 3600)) === null) {
			$request = $smcFunc['db_query']('', /** @lang text */ '
				SELECT k.id, k.name, lk.topic_id
				FROM {db_prefix}optimus_keywords AS k
					INNER JOIN {db_prefix}optimus_log_keywords AS lk ON (k.id = lk.keyword_id)
				ORDER BY lk.topic_id, k.id',
				[]
			);

			$keywords = [];
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$keywords[$row['topic_id']][$row['id']] = $row['name'];

			$smcFunc['db_free_result']($request);

			cache_put_data('optimus_topic_keywords', $keywords, 3600);
		}

		return $keywords;
	}

	private function getRandomColor(string $key): string
	{
		global $modSettings;

		if (empty($modSettings['optimus_use_color_tags']))
			return '';

		$hash = -105;
		for ($i = 0; $i < strlen($key); $i++)
			$hash += ord($key[$i]);

		$hsl = 'background-color: hsl(' . (($hash * 57) % 360) . ', 70%, 40%)';

		return $hsl . '; color: #fff';
	}

	private function add(array $keywords, int $topic, int $user): void
	{
		if (empty($keywords) || empty($topic) || empty($user))
			return;

		foreach ($keywords as $keyword) {
			$keyword_id = $this->getIdByName($keyword);

			if (empty($keyword_id))
				$keyword_id = $this->addToDatabase($keyword);

			$this->addNoteToLogTable($keyword_id, $topic, $user);
		}

		clean_cache();
	}

	private function getIdByName(string $name): int
	{
		global $smcFunc;

		$request = $smcFunc['db_query']('', '
			SELECT id
			FROM {db_prefix}optimus_keywords
			WHERE name = {string:name}
			LIMIT 1',
			[
				'name' => $name
			]
		);

		[$id] = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return (int) $id;
	}

	/**
	 * Add keyword to the optimus_keywords table, get id
	 *
	 * Добавляем ключевое слово в таблицу optimus_keywords и получаем его id
	 */
	private function addToDatabase(string $keyword): int
	{
		global $smcFunc;

		return $smcFunc['db_insert']('insert',
			'{db_prefix}optimus_keywords',
			[
				'name' => 'string-255'
			],
			[$keyword],
			['id'],
			1
		);
	}

	private function addNoteToLogTable(int $keyword_id, int $topic, int $user): void
	{
		global $smcFunc;

		$smcFunc['db_insert']('replace',
			'{db_prefix}optimus_log_keywords',
			[
				'keyword_id' => 'int',
				'topic_id'   => 'int',
				'user_id'    => 'int'
			],
			[
				$keyword_id,
				$topic,
				$user
			],
			[
				'keyword_id',
				'topic_id',
				'user_id'
			]
		);
	}

	private function modify(int $topic, int $user): void
	{
		global $context;

		if (! $this->canChange())
			return;

		$keywords = Input::xss(Input::request('optimus_keywords', []));

		// Check if the keywords have been changed
		$this->displayTopic();
		$current_keywords = empty($context['optimus_keywords']) ? [] : array_values($context['optimus_keywords']);

		if ($keywords == $current_keywords)
			return;

		$new_keywords = array_diff($keywords, $current_keywords);
		$this->add($new_keywords, $topic, $user);

		$del_keywords = array_diff($current_keywords, $keywords);
		$this->remove($del_keywords, $topic);
	}

	private function remove(array $keywords, int $topic): void
	{
		global $smcFunc;

		if (empty($keywords) || empty($topic))
			return;

		$request = $smcFunc['db_query']('', '
			SELECT lk.keyword_id, lk.topic_id
			FROM {db_prefix}optimus_log_keywords AS lk
				INNER JOIN {db_prefix}optimus_keywords AS k ON (lk.keyword_id = k.id
					AND lk.topic_id = {int:current_topic}
					AND k.name IN ({array_string:keywords})
				)',
			[
				'keywords'      => $keywords,
				'current_topic' => $topic
			]
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
			WHERE keyword_id IN ({array_int:keywords}) AND topic_id IN ({array_int:topics})',
			[
				'keywords' => $del_items['keywords'],
				'topics'   => $del_items['topics']
			]
		);

		$smcFunc['db_query']('', /** @lang text */ '
			DELETE FROM {db_prefix}optimus_keywords
			WHERE id NOT IN (SELECT keyword_id FROM {db_prefix}optimus_log_keywords)',
			[]
		);

		clean_cache();
	}

	private function canChange(): bool
	{
		global $context, $topic, $modSettings;

		if (! isset($context['user']['started']))
			$context['user']['started'] = empty($topic);

		if (empty($modSettings['optimus_allow_change_topic_keywords']))
			return false;

		return allowedTo('optimus_add_keywords_any')
			|| (allowedTo('optimus_add_keywords_own') && ! empty($context['user']['started']));
	}
}
