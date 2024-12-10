<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\DbFuncMapper;
use Bugo\Compat\Lang;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\TagHandler;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestDbMapper;

beforeEach(function () {
	$this->handler = new TagHandler();

	loadLanguage('Optimus/Optimus');

	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			if (str_contains($query, 'SELECT t.id_topic, ms.subject')) {
				return [
					[
						'id_topic' => '1',
						'subject' => 'Test Topic',
						'id_board' => '1',
						'name' => 'Test Board',
						'id_member' => '1',
						'id_group' => '1',
						'real_name' => 'Test User',
						'group_name' => 'Test Group',
					],
					[
						'id_topic' => '2',
						'subject' => 'Another Topic',
						'id_board' => '2',
						'name' => 'Another Board',
						'id_member' => '2',
						'id_group' => '2',
						'real_name' => 'Another User',
						'group_name' => 'Another Group',
					],
				];
			}

			if (str_contains($query, 'SELECT COUNT(topic_id)')) {
				return ['1'];
			}

			if (str_contains($query, 'SELECT ok.id, ok.name, COUNT(olk.keyword_id) AS frequency')) {
				return [
					['id' => '1', 'name' => 'Keyword 1', 'frequency' => '2'],
					['id' => '2', 'name' => 'Keyword 2', 'frequency' => '4'],
				];
			}

			if (str_contains($query, 'SELECT COUNT(id)')) {
				return ['1'];
			}

			if (str_contains($query, 'SELECT k.id, k.name, lk.topic_id')) {
				return [
					['id' => '1', 'name' => 'Keyword 1', 'topic_id' => '1'],
					['id' => '2', 'name' => 'Keyword 2', 'topic_id' => '2'],
				];
			}

			if (str_contains($query, 'SELECT name')) {
				return ['Keyword 1'];
			}

			if (str_contains($query, 'WHERE name LIKE {string:search}')) {
				return [
					['id' => '1', 'name' => 'Keyword 1'],
					['id' => '2', 'name' => 'Keyword 2'],
				];
			}

			if (str_contains($query, 'WHERE name = {string:name}')) {
				return ['id' => '1'];
			}

			return [];
		}
	};
});

afterEach(function () {
	Db::$db = new DbFuncMapper();
});

describe('actions method', function () {
	it('checks basic usage', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		$actions = [];

		$this->handler->actions($actions);

		expect($actions)->toHaveKey('keywords');
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = false;
		Config::$modSettings['optimus_show_keywords_block'] = false;

		$actions = [];

		$this->handler->actions($actions);

		expect($actions)->toBeEmpty();
	});
});

test('menuButtons method', function () {
	Utils::$context['current_action'] = 'keywords';

	$buttons = ['home' => []];

	$this->handler->menuButtons($buttons);

	expect($buttons['home'])->toHaveKey('action_hook');
});

test('currentAction method', function () {
	Utils::$context['current_action'] = 'keywords';

	$current_action = '';

	$this->handler->currentAction($current_action);

	expect($current_action)->toBe('home');
});

describe('loadPermissions method', function () {
	it('checks basic usage', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		$permissionList = [];

		$this->handler->loadPermissions([], $permissionList);

		expect($permissionList['membergroup'])->toHaveKey('optimus_add_keywords');
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = false;

		$permissionList = [];

		$this->handler->loadPermissions([], $permissionList);

		expect($permissionList)->toBeEmpty();
	});
});

test('basicSettings method', function () {
	$config_vars = [
		['check', 'optimus_topic_extend_title']
	];

	$this->handler->basicSettings($config_vars);

	expect($config_vars)->not->toBeEmpty();
});

test('messageindexButtons method', function () {
	Utils::$context['topics'] = [];

	expect($this->handler->messageindexButtons())->toBeNull();

	Config::$modSettings['optimus_show_keywords_on_message_index'] = true;

	Utils::$context['topics'] = [
		1 => [
			'first_post' => [
				'link' => '<a href="https://example.com/index.php?topic=1.0">Test Topic</a>',
			]
		],
		2 => [
			'first_post' => [
				'link' => '<a href="https://example.com/index.php?topic=2.0">Another Topic</a>',
			]
		],
	];

	$this->handler->messageindexButtons();

	expect(Utils::$context['css_header'])
		->toContain('.optimus_keywords:visited { color: transparent }');
});

test('prepareDisplayContext method', function () {
	Config::$modSettings['optimus_show_keywords_block'] = false;

	expect($this->handler->prepareDisplayContext([]))->toBeNull();

	Config::$modSettings['optimus_show_keywords_block'] = true;

	Utils::$context['start'] = 0;
	Utils::$context['optimus_keywords'] = [1 => 'foo', 2 => 'bar'];

	ob_start();

	echo $this->handler->prepareDisplayContext(['counter' => 0]);

	$result = ob_get_clean();

	expect($result)->toContain('fieldset');

	unset(Utils::$context['optimus_keywords']);
});

test('createTopic method', function () {
	expect($this->handler->createTopic([], [], []))
		->toBeNull();
});

describe('postEnd method', function () {
	it('checks case with old topic', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		Utils::$context['is_new_topic'] = false;
		Utils::$context['optimus_keywords'] = ['foo' => 'bar'];

		$this->handler->postEnd();

		expect(Utils::$context['optimus']['keywords'])->toContain('bar')
			->and(Utils::$context['css_files'])->not->toBeEmpty();

		unset(Utils::$context['optimus_keywords']);
	});

	it('checks case with new topic', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		Utils::$context['is_new_topic'] = true;

		$this->request = Request::createFromGlobals();
		$this->request->request->set('optimus_keywords', 'bar');
		$this->request->overrideGlobals();

		$this->handler->postEnd();

		expect(Utils::$context['optimus']['keywords'])->toContain('bar')
			->and(Utils::$context['css_files'])->not->toBeEmpty();
	});
});

test('modifyPost method', function () {
	expect($this->handler->modifyPost([], [], [], [], []))->toBeNull();
});

test('removeTopics method', function () {
	expect($this->handler->removeTopics([1, 2]))->toBeNull();
});

test('showTheSame method', function () {
	Utils::$context['current_subaction'] = '';

	$this->handler->showTheSame();

	expect(Utils::$context['optimus_keyword_id'])->toBeInt()
		->and(Utils::$context['template_layers'])->toContain('keywords');
});

test('showTheSame method with keyword_id', function () {
	Utils::$context['current_subaction'] = '';

	Lang::$txt['topic'] = Lang::$txt['board'] = Lang::$txt['author'] = '';

	$_REQUEST['id'] = 1;

	$this->handler->showTheSame();

	expect(Utils::$context['page_title'])->toContain('Keyword 1')
		->and(Utils::$context['canonical_url'])->toContain(Utils::$context['optimus_keyword_id']);
});

test('getAllByKeyId method', function () {
	Utils::$smcFunc['db_fetch_assoc'] = fn($result) => [];

	expect($this->handler->getAllByKeyId(0, 0, 'subject'))->toBeArray();
});

test('getTotalCountByKeyId method', function () {
	Utils::$smcFunc['db_fetch_row'] = fn($result) => [0];

	expect($this->handler->getTotalCountByKeyId())->toBeInt();
});

test('showAllWithFrequency method', function () {
	$this->handler->showAllWithFrequency();

	$this->assertStringContainsString('?action=keywords', Utils::$context['canonical_url']);
});

test('getAll method', function () {
	Utils::$smcFunc['db_fetch_assoc'] = fn($result) => [];

	expect($this->handler->getAll(0, 0, 'frequency'))->toBeArray();
});

test('getTotalCount method', function () {
	Utils::$smcFunc['db_fetch_row'] = fn($result) => [0];

	expect($this->handler->getTotalCount())->toBeInt();
});

describe('displayTopic method', function () {
	it('checks basic usage', function () {
		Config::$modSettings['optimus_show_keywords_block'] = true;

		Utils::$context['current_topic'] = '1';
		Utils::$context['optimus_keywords'] = Utils::$context['optimus']['keywords'] = [];

		$this->handler->displayTopic();

		expect(Utils::$context['optimus_keywords'])->toBe(['1' => 'Keyword 1']);

		unset(Utils::$context['optimus_keywords']);
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_show_keywords_block'] = false;

		unset(Utils::$context['current_topic']);

		$this->handler->displayTopic();

		expect(isset(Utils::$context['optimus_keywords']))->toBeFalse();
	});
});
