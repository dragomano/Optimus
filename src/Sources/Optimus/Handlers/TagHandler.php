<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\{Cache\CacheApi, Config, IntegrationHook, Db, ItemList};
use Bugo\Compat\{Lang, QueryString, Theme, Topic, User, Utils};
use Bugo\Optimus\Routes\Keywords;
use Bugo\Optimus\Utils\{Input, Str};

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
			'integrate_parse_route', self::class . '::parseRoute#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_current_action', self::class . '::currentAction#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_load_permissions', self::class . '::loadPermissions#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_permissions_list', self::class . '::permissionsList#', false, __FILE__
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
		) {
			return;
		}

		$actions['keywords'] = [false, $this->showTheSame(...)];
	}

	public function parseRoute(): void
	{
		QueryString::$route_parsers['keywords'] = Keywords::class;
	}

	public function currentAction(string &$action): void
	{
		if (Utils::$context['current_action'] === 'keywords') {
			$action = 'home';
		}
	}

	public function loadPermissions(array $permissionGroups, array &$permissionList): void
	{
		if (empty(Config::$modSettings['optimus_allow_change_topic_keywords']))
			return;

		$permissionList['membergroup']['optimus_add_keywords'] = [true, 'general', 'view_basic_info'];
	}

	public function permissionsList(array &$permissions): void
	{
		if (empty(Config::$modSettings['optimus_allow_change_topic_keywords']))
			return;

		$permissions['optimus_add_keywords_own'] = [
			'generic_name' => 'optimus_add_keywords',
			'own_any'      => 'own',
			'view_group'   => 'general',
			'scope'        => 'global',
			'never_guests' => true,
		];

		$permissions['optimus_add_keywords_any'] = [
			'generic_name' => 'optimus_add_keywords',
			'own_any'      => 'any',
			'view_group'   => 'general',
			'scope'        => 'global',
			'never_guests' => true,
		];
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
					'subtext' => Lang::getTxt('optimus_allow_change_topic_keywords_subtext')
				],
				['check', 'optimus_show_keywords_block'],
				['check', 'optimus_show_keywords_on_message_index'],
				['check', 'optimus_use_color_tags'],
				['int', 'optimus_max_allowed_tags', 'min' => 1],
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

				$data['first_post']['link'] .= ' ' . Str::html('a', $key)
					->class('optimus_keywords amt')
					->href($link)
					->setAttribute('style', $style);
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
			$keywords = Str::html('fieldset')->class('roundframe')
				->setAttribute('style', 'overflow: unset');

			$class = empty(Config::$modSettings['optimus_use_color_tags']) ? 'button' : 'descbox';

			foreach (Utils::$context['optimus_keywords'] as $id => $keyword) {
				$href = Config::$scripturl . '?action=keywords;id=' . $id;
				$style = 'margin-right: 2px;' . $this->getRandomColor($keyword);

				$link = Str::html('a', $keyword)
					->class($class)
					->href($href)
					->setAttribute('style', $style);

				$keywords->addHtml($link);
			}

			echo $keywords;
		}
	}

	public function createTopic(array $msgOptions, array $topicOptions, array $posterOptions): void
	{
		if (! $this->canChange())
			return;

		$keywords = $this->preparedKeywords();

		$this->add($keywords, $topicOptions['id'], $posterOptions['id']);
	}

	public function postEnd(): void
	{
		if (! $this->canChange())
			return;

		if (Utils::$context['is_new_topic']) {
			$keywords = Input::xss(Input::request('optimus_keywords', []));
		} else {
			$this->displayTopic();

			$keywords = Utils::$context['optimus_keywords'] ?? [];
		}

		Utils::$context['optimus']['keywords'] = (array) $keywords;

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
		if (empty($topicOptions['first_msg']) || (int) $topicOptions['first_msg'] !== $msgOptions['id'])
			return;

		$this->modify($topicOptions['id'], $posterOptions['id']);
	}

	public function removeTopics(array $topics): void
	{
		if (empty($topics))
			return;

		Db::$db->query('
			DELETE FROM {db_prefix}optimus_log_keywords
			WHERE topic_id IN ({array_int:topics})',
			[
				'topics' => $topics,
			]
		);
	}

	public function showTheSame(): void
	{
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

		Utils::$context['page_title']    = sprintf(Lang::getTxt('optimus_topics_with_keyword', file: 'Optimus/Optimus'), $keywordName);
		Utils::$context['canonical_url'] = Config::$scripturl . '?action=keywords;id=' . Utils::$context['optimus_keyword_id'];

		if (empty($keywordName)) {
			Utils::$context['page_title'] = Lang::getTxt('optimus_404_page_title');

			Utils::sendHttpStatus(404);
		}

		Utils::$context['linktree'][] = [
			'name' => Lang::getTxt('optimus_all_keywords'),
			'url'  => Config::$scripturl . '?action=keywords',
		];

		Utils::$context['linktree'][] = [
			'name' => Utils::$context['page_title'],
			'url'  => Utils::$context['canonical_url'],
		];

		$listOptions = [
			'id'               => 'topics',
			'items_per_page'   => 30,
			'title'            => '',
			'no_items_label'   => Lang::getTxt('optimus_no_keywords'),
			'base_href'        => Config::$scripturl . '?action=keywords;id=' . Utils::$context['optimus_keyword_id'],
			'default_sort_col' => 'topic',
			'get_items' => [
				'function' => $this->getAllByKeyId(...)
			],
			'get_count' => [
				'function' => $this->getTotalCountByKeyId(...)
			],
			'columns' => [
				'topic' => [
					'header' => [
						'value' => Lang::getTxt('topic')
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
						'value' => Lang::getTxt('board')
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
						'value' => Lang::getTxt('author')
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
		];

		new ItemList($listOptions);

		Utils::$context['sub_template'] = 'show_list';
		Utils::$context['default_list'] = 'topics';
	}

	public function getAllByKeyId(int $start, int $limit, string $sort): array
	{
		$result = Db::$db->query('
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
				'limit'      => $limit,
			]
		);

		$topics = [];
		while ($row = Db::$db->fetch_assoc($result)) {
			$href = Config::$scripturl . '?action=profile;u=' . $row['id_member'];

			$topics[] = [
				'topic'  => Str::html('a', $row['subject'])
					->href(Config::$scripturl . '?topic=' . $row['id_topic'] . '.0'),
				'board'  => Str::html('a', $row['name'])
					->href(Config::$scripturl . '?board=' . $row['id_board'] . '.0'),
				'author' => empty($row['real_name']) ? Lang::getTxt('guest') : Str::html('a', $row['real_name'])
					->href($href)
			];
		}

		Db::$db->free_result($result);

		return $topics;
	}

	public function getTotalCountByKeyId(): int
	{
		$result = Db::$db->query('
			SELECT COUNT(topic_id)
			FROM {db_prefix}optimus_log_keywords
			WHERE keyword_id = {int:keyword}
			LIMIT 1',
			[
				'keyword' => Utils::$context['optimus_keyword_id'],
			]
		);

		[$count] = Db::$db->fetch_row($result);
		Db::$db->free_result($result);

		return (int) $count;
	}

	public function showAllWithFrequency(): void
	{
		Utils::$context['page_title']    = Lang::getTxt('optimus_all_keywords', file: 'Optimus/Optimus');
		Utils::$context['canonical_url'] = Config::$scripturl . '?action=keywords';

		Utils::$context['linktree'][] = [
			'name' => Utils::$context['page_title'],
			'url'  => Utils::$context['canonical_url'],
		];

		$listOptions = [
			'id'               => 'keywords',
			'items_per_page'   => 30,
			'title'            => '',
			'no_items_label'   => '',
			'base_href'        => Utils::$context['canonical_url'],
			'default_sort_col' => 'frequency',
			'get_items' => [
				'function' => $this->getAll(...)
			],
			'get_count' => [
				'function' => $this->getTotalCount(...)
			],
			'columns' => [
				'keyword' => [
					'header' => [
						'value' => Lang::getTxt('optimus_keyword_column')
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
						'value' => Lang::getTxt('optimus_frequency_column')
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
				'href' => Utils::$context['canonical_url']
			],
		];

		new ItemList($listOptions);

		if (
			! empty(Utils::$context['current_page'])
			&& Utils::$context['current_page'] !== (int) Input::request('start', 0)
		) {
			Utils::sendHttpStatus(404);
		}

		Utils::$context['sub_template'] = 'show_list';
		Utils::$context['default_list'] = 'keywords';
	}

	public function getAll(int $start, int $limit, string $sort): array
	{
		$result = Db::$db->query('
			SELECT ok.id, ok.name, COUNT(olk.keyword_id) AS frequency
			FROM {db_prefix}optimus_keywords AS ok
				LEFT JOIN {db_prefix}optimus_log_keywords AS olk ON (ok.id = olk.keyword_id)
			GROUP BY ok.id, ok.name
			ORDER BY {raw:sort}
			LIMIT {int:start}, {int:limit}',
			[
				'sort'  => $sort,
				'start' => $start,
				'limit' => $limit,
			]
		);

		$keywords = [];
		while ($row = Db::$db->fetch_assoc($result)) {
			$link = Config::$scripturl . '?action=keywords;id=' . $row['id'];

			$keywords[] = [
				'keyword'   => Str::html('a', $row['name'])->href($link),
				'frequency' => $row['frequency'],
			];
		}

		Db::$db->free_result($result);

		return $keywords;
	}

	public function getTotalCount(): int
	{
		$result = Db::$db->query(/** @lang text */ '
			SELECT COUNT(id)
			FROM {db_prefix}optimus_keywords
			LIMIT 1',
		);

		[$count] = Db::$db->fetch_row($result);
		Db::$db->free_result($result);

		return (int) $count;
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
			$result = Db::$db->query(/** @lang text */ '
				SELECT k.id, k.name, lk.topic_id
				FROM {db_prefix}optimus_keywords AS k
					INNER JOIN {db_prefix}optimus_log_keywords AS lk ON (k.id = lk.keyword_id)
				ORDER BY lk.topic_id, k.id',
			);

			$keywords = [];
			while ($row = Db::$db->fetch_assoc($result)) {
				$keywords[$row['topic_id']][$row['id']] = $row['name'];
			}

			Db::$db->free_result($result);

			CacheApi::put('optimus_topic_keywords', $keywords, 3600);
		}

		return $keywords;
	}

	private function getAllKeywords(): array
	{
		if (($keywords = CacheApi::get('optimus_all_keywords', 3600)) === null) {
			$result = Db::$db->query(/** @lang text */ '
				SELECT id, name
				FROM {db_prefix}optimus_keywords',
			);

			$keywords = [];
			while ($row = Db::$db->fetch_assoc($result)) {
				$keywords[$row['id']] = $row['name'];
			}

			Db::$db->free_result($result);

			CacheApi::put('optimus_all_keywords', $keywords, 3600);
		}

		return $keywords;
	}

	private function getNameById(int $id = 0): string
	{
		if (empty($id)) {
			return '';
		}

		$result = Db::$db->query('
			SELECT name
			FROM {db_prefix}optimus_keywords
			WHERE id = {int:id}
			LIMIT 1',
			[
				'id' => $id,
			]
		);

		[$name] = Db::$db->fetch_row($result);
		Db::$db->free_result($result);

		return $name;
	}

	private function getRandomColor(string $key): string
	{
		if (empty(Config::$modSettings['optimus_use_color_tags'])) {
			return '';
		}

		$hash = -105;
		for ($i = 0; $i < strlen($key); $i++) {
			$hash += ord($key[$i]);
		}

		$hsl = 'background-color: hsl(' . (($hash * 57) % 360) . ', 70%, 40%)';

		return $hsl . '; color: #fff';
	}

	private function addFields(): void
	{
		if (empty(Utils::$context['is_first_post']))
			return;

		Utils::$context['posting_fields']['optimus_keywords']['label']['html'] = Lang::getTxt(
			'optimus_seo_keywords', file: 'Optimus/Optimus'
		);

		Utils::$context['posting_fields']['optimus_keywords']['input']['html'] = Str::html('div')
			->id('optimus_keywords')
			->name('optimus_keywords');

		$this->loadAssets();
	}

	private function loadAssets(): void
	{
		Theme::loadCSSFile(
			'https://cdn.jsdelivr.net/npm/virtual-select-plugin@1/dist/virtual-select.min.css',
			['external' => true]
		);

		Theme::loadJavaScriptFile(
			'https://cdn.jsdelivr.net/npm/virtual-select-plugin@1/dist/virtual-select.min.js',
			['external' => true]
		);

		$data = $values = [];
		foreach ($this->getAllKeywords() as $id => $name) {
			$data[] = [
				'label' => $name,
				'value' => 'key_' . $id,
			];

			if (isset(Utils::$context['optimus_keywords'][$id])) {
				$values[] = Utils::escapeJavaScript('key_' . $id);
			}
		}

		$maxTags = Config::$modSettings['optimus_max_allowed_tags'] ?? 10;

		Theme::addInlineJavaScript('
		VirtualSelect.init({
			ele: "#optimus_keywords",' . (Utils::$context['right_to_left'] ? '
			textDirection: "rtl",' : '') . '
			dropboxWrapper: "body",
			zIndex: 1000,
			maxWidth: "100%",
			multiple: true,
			search: true,
			markSearchResults: true,
			showValueAsTags: true,
			allowNewOption: true,
			showSelectedOptionsFirst: true,
			placeholder: "' . Lang::getTxt('optimus_enter_keywords') . '",
			noSearchResultsText: "' . Lang::getTxt('no_matches') . '",
			searchPlaceholderText: "' . Lang::getTxt('search') . '",
			clearButtonText: "' . Lang::getTxt('remove') . '",
			maxValues: ' . $maxTags . ',
			options: ' . json_encode($data) . ',
			selectedValue: [' . implode(',', $values) . ']
		});', true);
	}

	private function getIdByName(string $name): int
	{
		$result = Db::$db->query('
			SELECT id
			FROM {db_prefix}optimus_keywords
			WHERE name = {string:name}
			LIMIT 1',
			[
				'name' => $name,
			]
		);

		[$id] = Db::$db->fetch_row($result);
		Db::$db->free_result($result);

		return (int) $id;
	}

	private function addToDatabase(string $keyword): int
	{
		return Db::$db->insert('insert',
			'{db_prefix}optimus_keywords',
			[
				'name' => 'string-255',
			],
			[$keyword],
			['id'],
			1
		);
	}

	/**
	 * @codeCoverageIgnore
	 */
	private function addNoteToLogTable(int $keyword_id, int $topic, int $user): void
	{
		Db::$db->insert('replace',
			'{db_prefix}optimus_log_keywords',
			[
				'keyword_id' => 'int',
				'topic_id'   => 'int',
				'user_id'    => 'int',
			],
			[
				$keyword_id,
				$topic,
				$user,
			],
			[
				'keyword_id',
				'topic_id',
				'user_id',
			]
		);
	}

	private function modify(int $topic, int $user): void
	{
		if (! $this->canChange())
			return;

		$keywords = $this->preparedKeywords();

		$this->displayTopic();

		$currentKeywords = Utils::$context['optimus_keywords'] ?? [];

		$newKeywords = [];
		foreach ($keywords as $id) {
			if (! isset($currentKeywords[$id])) {
				$newKeywords[] = $id;
			}
		}

		$this->add($newKeywords, $topic, $user);

		$delKeywords = [];
		foreach ($currentKeywords as $id => $name) {
			if (! in_array('key_' . $id, $keywords)) {
				$delKeywords[] = $id;
			}
		}

		$this->remove($delKeywords, $topic);
	}

	private function add(array $keywords, int $topic, int $user): void
	{
		if (empty($keywords) || empty($topic) || empty($user))
			return;

		foreach ($keywords as $keyword) {
			$id = str_starts_with($keyword, 'key_')
				? (int) ltrim($keyword, 'key_')
				: $this->getIdByName($keyword);

			if (empty($id)) {
				$id = $this->addToDatabase($keyword);
			}

			$this->addNoteToLogTable($id, $topic, $user);
		}

		CacheApi::clean();
	}

	private function remove(array $keywords, int $topic): void
	{
		if (empty($keywords) || empty($topic))
			return;

		Db::$db->query('
			DELETE FROM {db_prefix}optimus_log_keywords
			WHERE keyword_id IN ({array_int:keywords}) AND topic_id = {int:topic}',
			[
				'keywords' => $keywords,
				'topic'    => $topic,
			]
		);

		Db::$db->query(/** @lang text */ '
			DELETE FROM {db_prefix}optimus_keywords
			WHERE id NOT IN (SELECT keyword_id FROM {db_prefix}optimus_log_keywords)',
		);

		CacheApi::clean();
	}

	private function preparedKeywords(): array
	{
		$keywords = Input::xss(Input::request('optimus_keywords', ''));

		return array_filter(explode(',', $keywords));
	}

	private function canChange(): bool
	{
		if (! isset(Utils::$context['user']['started'])) {
			Utils::$context['user']['started'] = empty(Topic::$id);
		}

		if (empty(Config::$modSettings['optimus_allow_change_topic_keywords'])) {
			return false;
		}

		return User::$me->allowedTo('optimus_add_keywords_any')
			|| (User::$me->allowedTo('optimus_add_keywords_own') && ! empty(Utils::$context['user']['started']));
	}
}
