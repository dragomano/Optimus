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

use Bugo\Compat\{CacheApi, Config, IntegrationHook};
use Bugo\Compat\{ItemList, Lang, Theme, Topic, User, Utils};
use Bugo\Optimus\Utils\{Copyright, Input};

if (! defined('SMF'))
	die('No direct access...');

final class TagHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_actions', self::class . '::actions#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::menuButtons#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_current_action', self::class . '::currentAction#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_load_permissions', self::class . '::loadPermissions#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_optimus_basic_settings', self::class . '::basicSettings#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_messageindex_buttons', self::class . '::messageindexButtons#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_display_topic', self::class . '::displayTopic#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_prepare_display_context', self::class . '::prepareDisplayContext#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_create_topic', self::class . '::createTopic#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_post_end', self::class . '::postEnd#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_modify_post', self::class . '::modifyPost#', false, __FILE__
		);
		
		IntegrationHook::add(
			'integrate_remove_topics', self::class . '::removeTopics#', false, __FILE__
		);
	}

	public function actions(array &$actions): void
	{
		if (
			empty(Config::$modSettings['optimus_allow_change_topic_keywords'])
			&& empty(Config::$modSettings['optimus_show_keywords_block'])
		)
			return;

		$actions['keywords'] = [false, [$this, 'showTheSame']];
	}

	public function menuButtons(array &$buttons): void
	{
		if (isset($buttons['home']) && Utils::$context['current_action'] === 'keywords')
			$buttons['home']['action_hook'] = true;
	}

	public function currentAction(string &$current_action): void
	{
		if (Utils::$context['current_action'] === 'keywords')
			$current_action = 'home';
	}

	public function loadPermissions(array $permissionGroups, array &$permissionList): void
	{
		if (empty(Config::$modSettings['optimus_allow_change_topic_keywords']))
			return;

		$permissionList['membergroup']['optimus_add_keywords'] = [true, 'general', 'view_basic_info'];
	}

	public function basicSettings(array &$config_vars): void
	{
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
				[
					'check',
					'optimus_allow_change_topic_keywords',
					'subtext' => Lang::$txt['optimus_allow_change_topic_keywords_subtext']
				],
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
		if (empty(Config::$modSettings['optimus_show_keywords_on_message_index']) || empty(Utils::$context['topics']))
			return;

		Theme::addInlineCss('.optimus_keywords:visited { color: transparent }');

		foreach (Utils::$context['topics'] as $topic => &$data) {
			$keywords = $this->getKeywords()[$topic] ?? [];

			foreach ($keywords as $id => $key) {
				$link = Config::$scripturl . '?action=keywords;id=' . $id;
				$style = ' style="' . $this->getRandomColor($key) . '"';

				$data['first_post']['link'] .= /** @lang text */
					' <a class="optimus_keywords amt" href="' . $link . '"' . $style . '>' . $key . '</a>';
			}
		}
	}

	public function prepareDisplayContext(array $output): void
	{
		if (empty(Utils::$context['optimus_keywords']) || empty(Config::$modSettings['optimus_show_keywords_block']))
			return;

		$counter = empty(Theme::$current->options['view_newest_first'])
			? Utils::$context['start'] : Utils::$context['total_visible_posts'] - Utils::$context['start'];

		if ($counter == $output['counter'] && empty(Utils::$context['start'])) {
			$keywords = '<fieldset class="roundframe" style="overflow: unset">
				<legend class="amt" style="padding: .2em .4em"> ' . Lang::$txt['optimus_seo_keywords'] . ' </legend>';

			$class = empty(Config::$modSettings['optimus_use_color_tags']) ? 'button' : 'descbox';

			foreach (Utils::$context['optimus_keywords'] as $id => $keyword) {
				$href = Config::$scripturl . '?action=keywords;id=' . $id;
				$style = ' style="margin-right: 2px;' . $this->getRandomColor($keyword) . '"';
				$keywords .= '<a class="' . $class . '" href="' . $href . '"' . $style . '>' . $keyword . '</a>';
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
		if (! $this->canChange())
			return;

		if (Utils::$context['is_new_topic']) {
			Utils::$context['optimus']['keywords'] = Input::xss(Input::request('optimus_keywords', ''));
		} else {
			$this->displayTopic();

			Utils::$context['optimus']['keywords'] = empty(Utils::$context['optimus_keywords'])
				? [] : array_values(Utils::$context['optimus_keywords']);
		}

		$this->addFields();
	}

	public function modifyPost(
		array $messages_columns,
		array $update_parameters,
		array $msgOptions,
		array $topicOptions,
		array $posterOptions
	): void
	{
		if (empty($topicOptions['first_msg']) || $topicOptions['first_msg'] != $msgOptions['id'])
			return;

		$this->modify($topicOptions['id'], $posterOptions['id']);
	}

	public function removeTopics(array $topics): void
	{
		if (empty($topics))
			return;

		Utils::$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}optimus_log_keywords
			WHERE topic_id IN ({array_int:topics})',
			[
				'topics' => $topics
			]
		);
	}

	public function showTheSame(): void
	{
		if (Utils::$context['current_subaction'] == 'search') {
			$this->prepareSearchData();
			return;
		}

		Theme::addInlineCss('
		.main_icons.optimus::before {
			background:url(' . Theme::$current->settings['default_images_url'] . '/optimus.png) no-repeat 0 0 !important;
		}');

		Utils::$context['optimus_keyword_id'] = (int) Input::request('id', 0);

		Theme::loadTemplate('Optimus');
		Utils::$context['template_layers'][] = 'keywords';

		if (empty(Utils::$context['optimus_keyword_id'])) {
			$this->showAllWithFrequency();
			return;
		}

		$keywordName = $this->getNameById(Utils::$context['optimus_keyword_id']);
		
		Utils::$context['page_title']    = sprintf(Lang::$txt['optimus_topics_with_keyword'], $keywordName);
		Utils::$context['canonical_url'] = Config::$scripturl . '?action=keywords;id=' . Utils::$context['optimus_keyword_id'];

		if (empty($keywordName)) {
			Utils::$context['page_title'] = Lang::$txt['optimus_404_page_title'];
			Utils::sendHttpStatus(404);
		}

		Utils::$context['linktree'][] = [
			'name' => Lang::$txt['optimus_all_keywords'],
			'url'  => Config::$scripturl . '?action=keywords'
		];

		Utils::$context['linktree'][] = [
			'name' => Utils::$context['page_title'],
			'url'  => Utils::$context['canonical_url']
		];

		$listOptions = [
			'id'               => 'topics',
			'items_per_page'   => 30,
			'title'            => '',
			'no_items_label'   => Lang::$txt['optimus_no_keywords'],
			'base_href'        => Config::$scripturl . '?action=keywords;id=' . Utils::$context['optimus_keyword_id'],
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
						'value' => Lang::$txt['topic']
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
						'value' => Lang::$txt['board']
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
						'value' => Lang::$txt['author']
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
				'href' => Config::$scripturl . '?action=keywords;id=' . Utils::$context['optimus_keyword_id']
			],
			'additional_rows' => [
				[
					'position' => 'below_table_data',
					'value'    => 'Powered by ' . Copyright::getLink(),
					'class'    => 'smalltext centertext'
				]
			]
		];

		new ItemList($listOptions);

		Utils::$context['sub_template'] = 'show_list';
		Utils::$context['default_list'] = 'topics';
	}

	public function getAllByKeyId(int $start, int $items_per_page, string $sort): array
	{
		$request = Utils::$smcFunc['db_query']('', '
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
				'keyword_id' => Utils::$context['optimus_keyword_id'],
				'sort'       => $sort,
				'start'      => $start,
				'limit'      => $items_per_page
			]
		);

		$topics = [];
		while ($row = Utils::$smcFunc['db_fetch_assoc']($request)) {
			$href = Config::$scripturl . '?action=profile;u=' . $row['id_member'];

			$topics[] = [
				'topic'  => '<a href="' . Config::$scripturl . '?topic=' . $row['id_topic'] . '.0">' . $row['subject'] . '</a>',
				'board'  => '<a href="' . Config::$scripturl . '?board=' . $row['id_board'] . '.0">' . $row['name'] . '</a>',
				'author' => empty($row['real_name']) ? Lang::$txt['guest'] : '<a href="' . $href . '">' . $row['real_name'] . '</a>'
			];
		}

		Utils::$smcFunc['db_free_result']($request);

		return $topics;
	}

	public function getTotalCountByKeyId(): int
	{
		$request = Utils::$smcFunc['db_query']('', '
			SELECT COUNT(topic_id)
			FROM {db_prefix}optimus_log_keywords
			WHERE keyword_id = {int:keyword}
			LIMIT 1',
			[
				'keyword' => Utils::$context['optimus_keyword_id']
			]
		);

		[$num] = Utils::$smcFunc['db_fetch_row']($request);
		Utils::$smcFunc['db_free_result']($request);

		return (int) $num;
	}

	public function showAllWithFrequency(): void
	{
		Utils::$context['page_title']    = Lang::$txt['optimus_all_keywords'];
		Utils::$context['canonical_url'] = Config::$scripturl . '?action=keywords';

		Utils::$context['linktree'][] = [
			'name' => Utils::$context['page_title'],
			'url'  => Utils::$context['canonical_url']
		];

		$listOptions = [
			'id'               => 'keywords',
			'items_per_page'   => 30,
			'title'            => '',
			'no_items_label'   => '',
			'base_href'        => Config::$scripturl . '?action=keywords',
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
						'value' => Lang::$txt['optimus_keyword_column']
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
						'value' => Lang::$txt['optimus_frequency_column']
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
				'href' => Config::$scripturl . '?action=keywords'
			],
			'additional_rows' => [
				[
					'position' => 'below_table_data',
					'value'    => 'Powered by ' . Copyright::getLink(),
					'class'    => 'smalltext centertext',
				]
			]
		];

		new ItemList($listOptions);

		if (! empty(Utils::$context['current_page']) && Utils::$context['current_page'] != (int) Input::request('start'))
			Utils::sendHttpStatus(404);

		Utils::$context['sub_template'] = 'show_list';
		Utils::$context['default_list'] = 'keywords';
	}

	public function getAll(int $start, int $items_per_page, string $sort): array
	{
		$request = Utils::$smcFunc['db_query']('', '
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
		while ($row = Utils::$smcFunc['db_fetch_assoc']($request)) {
			$link = Config::$scripturl . '?action=keywords;id=' . $row['id'];

			$keywords[] = [
				'keyword'   => '<a href="' . $link . '">' . $row['name'] . '</a>',
				'frequency' => $row['frequency']
			];
		}

		Utils::$smcFunc['db_free_result']($request);

		return $keywords;
	}

	public function getTotalCount(): int
	{
		$request = Utils::$smcFunc['db_query']('', /** @lang text */ '
			SELECT COUNT(id)
			FROM {db_prefix}optimus_keywords
			LIMIT 1',
			[]
		);

		[$num] = Utils::$smcFunc['db_fetch_row']($request);
		Utils::$smcFunc['db_free_result']($request);

		return (int) $num;
	}

	public function displayTopic(): void
	{
		if (empty(Config::$modSettings['optimus_show_keywords_block']))
			return;

		if (empty(Utils::$context['current_topic']) || ! empty(Utils::$context['optimus']['keywords']))
			return;

		$keywords = $this->getKeywords();

		Utils::$context['optimus_keywords'] = [];

		if (empty($keywords[Utils::$context['current_topic']]))
			return;

		Utils::$context['optimus_keywords'] = $keywords[Utils::$context['current_topic']];

		Config::$modSettings['meta_keywords'] = implode(', ', Utils::$context['optimus_keywords']);
	}

	private function getKeywords(): array
	{
		if (($keywords = CacheApi::get('optimus_topic_keywords', 3600)) === null) {
			$request = Utils::$smcFunc['db_query']('', /** @lang text */ '
				SELECT k.id, k.name, lk.topic_id
				FROM {db_prefix}optimus_keywords AS k
					INNER JOIN {db_prefix}optimus_log_keywords AS lk ON (k.id = lk.keyword_id)
				ORDER BY lk.topic_id, k.id',
				[]
			);

			$keywords = [];
			while ($row = Utils::$smcFunc['db_fetch_assoc']($request))
				$keywords[$row['topic_id']][$row['id']] = $row['name'];

			Utils::$smcFunc['db_free_result']($request);

			CacheApi::put('optimus_topic_keywords', $keywords, 3600);
		}

		return $keywords;
	}

	private function getNameById(int $id = 0): string
	{
		if (empty($id))
			return '';

		$request = Utils::$smcFunc['db_query']('', '
			SELECT name
			FROM {db_prefix}optimus_keywords
			WHERE id = {int:id}
			LIMIT 1',
			[
				'id' => $id
			]
		);

		[$name] = Utils::$smcFunc['db_fetch_row']($request);
		Utils::$smcFunc['db_free_result']($request);

		return $name;
	}

	/**
	 * Request from the database 10 keywords similar to the entered
	 *
	 * Запрашиваем из базы данных 10 наиболее похожих ключевых слов (когда добавляем новое)
	 */
	private function prepareSearchData(): void
	{
		$query = Utils::$smcFunc['htmltrim'](Input::filter('q'));

		if (empty($query))
			exit;

		$request = Utils::$smcFunc['db_query']('', '
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
		while ($row = Utils::$smcFunc['db_fetch_assoc']($request)) {
			$data[] = [
				'id'   => $row['name'],
				'text' => $row['name']
			];
		}

		Utils::$smcFunc['db_free_result']($request);

		exit(json_encode($data));
	}

	private function getRandomColor(string $key): string
	{
		if (empty(Config::$modSettings['optimus_use_color_tags']))
			return '';

		$hash = -105;
		for ($i = 0; $i < strlen($key); $i++)
			$hash += ord($key[$i]);

		$hsl = 'background-color: hsl(' . (($hash * 57) % 360) . ', 70%, 40%)';

		return $hsl . '; color: #fff';
	}

	private function addFields(): void
	{
		if (empty(Utils::$context['is_first_post']))
			return;

		Utils::$context['posting_fields']['optimus_keywords'] = [
			'label' => [
				'text' => Lang::$txt['optimus_seo_keywords']
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

		$this->loadAssets();

		if (empty(Utils::$context['optimus']['keywords']))
			return;

		foreach (Utils::$context['optimus']['keywords'] as $key) {
			Utils::$context['posting_fields']['optimus_keywords']['input']['options'][$key] = [
				'value'    => $key,
				'selected' => true
			];
		}
	}

	/**
	 * @see Select2 https://select2.github.io/select2/
	 */
	private function loadAssets(): void
	{
		Theme::loadCSSFile('https://cdn.jsdelivr.net/npm/select2@4/dist/css/select2.min.css', ['external' => true]);

		Theme::loadJavaScriptFile(
			'https://cdn.jsdelivr.net/npm/select2@4/dist/js/select2.min.js',
			['external' => true]
		);

		Theme::loadJavaScriptFile(
			'https://cdn.jsdelivr.net/npm/select2@4/dist/js/i18n/' . Lang::$txt['lang_dictionary'] . '.js',
			['external' => true]
		);

		Theme::addInlineJavaScript('
		jQuery(document).ready(function ($) {
			$("#optimus_keywords").select2({
				language: "' . Lang::$txt['lang_dictionary'] . '",
				placeholder: "' . Lang::$txt['optimus_enter_keywords'] . '",
				minimumInputLength: 2,
				width: "100%",
				cache: true,
				tags: true,' . (Utils::$context['right_to_left'] ? '
				dir: "rtl",' : '') . '
				tokenSeparators: [","' . (empty(Config::$modSettings['optimus_allow_keyword_phrases']) ? ', " "' : '') . '],
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

		CacheApi::clean();
	}

	private function getIdByName(string $name): int
	{
		$request = Utils::$smcFunc['db_query']('', '
			SELECT id
			FROM {db_prefix}optimus_keywords
			WHERE name = {string:name}
			LIMIT 1',
			[
				'name' => $name
			]
		);

		[$id] = Utils::$smcFunc['db_fetch_row']($request);
		Utils::$smcFunc['db_free_result']($request);

		return (int) $id;
	}

	/**
	 * Add keyword to the optimus_keywords table, get id
	 *
	 * Добавляем ключевое слово в таблицу optimus_keywords и получаем его id
	 */
	private function addToDatabase(string $keyword): int
	{
		return Utils::$smcFunc['db_insert']('insert',
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
		Utils::$smcFunc['db_insert']('replace',
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
		if (! $this->canChange())
			return;

		$keywords = Input::xss(Input::request('optimus_keywords', []));

		// Check if the keywords have been changed
		$this->displayTopic();
		$currentKeywords = empty(Utils::$context['optimus_keywords'])
			? []
			: array_values(Utils::$context['optimus_keywords']);

		if ($keywords == $currentKeywords)
			return;

		$newKeywords = array_diff($keywords, $currentKeywords);
		$this->add($newKeywords, $topic, $user);

		$delKeywords = array_diff($currentKeywords, $keywords);
		$this->remove($delKeywords, $topic);
	}

	private function remove(array $keywords, int $topic): void
	{
		if (empty($keywords) || empty($topic))
			return;

		$request = Utils::$smcFunc['db_query']('', '
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

		$delItems = [];
		while ($row = Utils::$smcFunc['db_fetch_assoc']($request)) {
			$delItems['keywords'][] = $row['keyword_id'];
			$delItems['topics'][]   = $row['topic_id'];
		}

		Utils::$smcFunc['db_free_result']($request);

		if (empty($delItems))
			return;

		Utils::$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}optimus_log_keywords
			WHERE keyword_id IN ({array_int:keywords}) AND topic_id IN ({array_int:topics})',
			[
				'keywords' => $delItems['keywords'],
				'topics'   => $delItems['topics']
			]
		);

		Utils::$smcFunc['db_query']('', /** @lang text */ '
			DELETE FROM {db_prefix}optimus_keywords
			WHERE id NOT IN (SELECT keyword_id FROM {db_prefix}optimus_log_keywords)',
			[]
		);

		CacheApi::clean();
	}

	private function canChange(): bool
	{
		if (! isset(Utils::$context['user']['started']))
			Utils::$context['user']['started'] = empty(Topic::$id);

		if (empty(Config::$modSettings['optimus_allow_change_topic_keywords']))
			return false;

		return User::hasPermission('optimus_add_keywords_any')
			|| (User::hasPermission('optimus_add_keywords_own') && ! empty(Utils::$context['user']['started']));
	}
}
