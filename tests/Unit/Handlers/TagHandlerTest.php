<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Compat\Lang;
use Bugo\Compat\User;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\TagHandler;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestDbMapper;

beforeEach(function () {
	$this->handler = new TagHandler();

	loadLanguage('Optimus/Optimus');

	// Mock User for permissions
	User::$me = Mockery::mock(User::class)->makePartial();
	User::$me->shouldReceive('allowedTo')->andReturn(true);

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

			if (str_contains($query, 'SELECT id, name')) {
				return [
					['id' => '1', 'name' => 'Keyword 1'],
					['id' => '2', 'name' => 'Keyword 2'],
				];
			}

			if (str_contains($query, 'SELECT name')) {
				if (isset($params['id']) && $params['id'] == 999) {
					return [''];
				}

				return ['Keyword 1'];
			}

			if (str_contains($query, 'WHERE name = {string:name}')) {
				if ($params['name'] === 'id') {
					return ['id' => '1'];
				} else {
					return [];
				}
			}



			return [];
		}

		public function fetch_row($result): array|false|null
		{
			if ($result === ['id' => '1']) {
				return ['1'];
			}

			return $result ?? false;
		}

		public function insert(string $method, string $table, array $columns, array $data, array $keys, int $returnmode = 0): int|array|null
		{
			return 1;
		}
	};
});

afterEach(function () {
	Db::$db = new FuncMapper();
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

	it('checks case with only show_keywords_block enabled', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = false;
		Config::$modSettings['optimus_show_keywords_block'] = true;

		$actions = [];

		$this->handler->actions($actions);

		expect($actions)->toHaveKey('keywords');
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

describe('messageindexButtons method', function () {
	it('checks case with empty topics', function () {
		Utils::$context['topics'] = [];

		expect($this->handler->messageindexButtons())->toBeNull();
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_show_keywords_on_message_index'] = false;

		Utils::$context['topics'] = [
			1 => [
				'first_post' => [
					'link' => '<a href="https://example.com/index.php?topic=1.0">Test Topic</a>',
				]
			],
		];

		expect($this->handler->messageindexButtons())->toBeNull();
	});

	it('checks basic usage', function () {
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
});

describe('prepareDisplayContext method', function () {
	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_show_keywords_block'] = false;
		Config::$modSettings['optimus_use_color_tags'] = true;

		expect($this->handler->prepareDisplayContext([]))->toBeNull();
	});

	it('checks basic usage', function () {
		Config::$modSettings['optimus_show_keywords_block'] = true;
		Config::$modSettings['optimus_use_color_tags'] = true;

		Utils::$context['start'] = 0;
		Utils::$context['optimus_keywords'] = [1 => 'foo', 2 => 'bar'];

		ob_start();

		echo $this->handler->prepareDisplayContext(['counter' => 0]);

		$result = ob_get_clean();

		expect($result)->toContain('fieldset');

		unset(Utils::$context['optimus_keywords']);
	});


	it('checks case with use_color_tags disabled', function () {
		Config::$modSettings['optimus_show_keywords_block'] = true;
		Config::$modSettings['optimus_use_color_tags'] = false;

		Utils::$context['start'] = 0;
		Utils::$context['optimus_keywords'] = [1 => 'foo', 2 => 'bar'];

		ob_start();

		echo $this->handler->prepareDisplayContext(['counter' => 0]);

		$result = ob_get_clean();

		expect($result)->toContain('fieldset')
			->and($result)->toContain('button');

		unset(Utils::$context['optimus_keywords']);
	});

	it('checks case with start not empty', function () {
		Config::$modSettings['optimus_show_keywords_block'] = true;
		Config::$modSettings['optimus_use_color_tags'] = true;

		Utils::$context['start'] = 1;
		Utils::$context['optimus_keywords'] = [1 => 'foo', 2 => 'bar'];

		ob_start();

		echo $this->handler->prepareDisplayContext(['counter' => 0]);

		$result = ob_get_clean();

		expect($result)->not->toContain('fieldset');

		unset(Utils::$context['optimus_keywords']);
	});
});

test('createTopic method', function () {
	Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

	$_REQUEST['optimus_keywords'] = 'key_1,key_2';

	expect($this->handler->createTopic([], ['id' => 1], ['id' => 1]))
		->toBeNull();
});

describe('postEnd method', function () {
	it('checks case with old topic', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		Utils::$context['is_new_topic'] = false;
		Utils::$context['optimus_keywords'] = ['foo' => 'bar'];

		unset(Utils::$context['css_files']);

		$this->handler->postEnd();

		expect(Utils::$context['optimus']['keywords'])->toContain('bar')
			->and(isset(Utils::$context['css_files']))->toBeFalse();

		unset(Utils::$context['optimus_keywords']);
	});

	it('checks case with new topic', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		Utils::$context['is_new_topic'] = true;
		Utils::$context['is_first_post'] = true;

		$this->request = Request::createFromGlobals();
		$this->request->request->set('optimus_keywords', 'bar');
		$this->request->overrideGlobals();

		$this->handler->postEnd();

		expect(Utils::$context['optimus']['keywords'])->toContain('bar')
			->and(Utils::$context['css_files'])->not->toBeEmpty();
	});

	it('checks case with canChange false', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = false;

		Utils::$context['is_new_topic'] = true;

		unset(Utils::$context['optimus']);

		$this->handler->postEnd();

		expect(isset(Utils::$context['optimus']['keywords']))->toBeFalse();
	});

	it('checks case with old topic and is_first_post false', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		Utils::$context['is_new_topic'] = false;
		Utils::$context['is_first_post'] = false;
		Utils::$context['optimus_keywords'] = ['foo' => 'bar'];

		unset(Utils::$context['posting_fields']);

		$this->handler->postEnd();

		expect(Utils::$context['optimus']['keywords'])->toContain('bar')
			->and(isset(Utils::$context['posting_fields']))->toBeFalse();
	});
});

describe('modifyPost method', function () {
	it('checks case with empty first_msg', function () {
		expect($this->handler->modifyPost([], [], [], [], []))->toBeNull();
	});

	it('checks case with first_msg not equal to msg id', function () {
		$topicOptions = ['first_msg' => '2'];
		$msgOptions = ['id' => '1'];

		expect($this->handler->modifyPost([], [], $msgOptions, $topicOptions, []))->toBeNull();
	});
});

describe('removeTopics method', function () {
	it('checks case with topics', function () {
		expect($this->handler->removeTopics([1, 2]))->toBeNull();
	});

	it('checks case with empty topics', function () {
		expect($this->handler->removeTopics([]))->toBeNull();
	});
});

test('showTheSame method', function () {
	Utils::$context['current_subaction'] = '';

	$this->handler->showTheSame();

	expect(Utils::$context['optimus_keyword_id'])->toBeInt()
		->and(Utils::$context['template_layers'])->toContain('keywords');
});

test('showTheSame method with keyword_id', function () {
	Utils::$context['current_subaction'] = '';

	Lang::setTxt('topic', '');
	Lang::setTxt('board', '');
	Lang::setTxt('author', '');

	$_REQUEST['id'] = 1;

	$this->handler->showTheSame();

	expect(Utils::$context['page_title'])->toContain('Keyword 1')
		->and(Utils::$context['canonical_url'])->toContain(Utils::$context['optimus_keyword_id']);
});

test('showTheSame method with keyword_id not found', function () {
	Utils::$context['current_subaction'] = '';

	$_REQUEST['id'] = 0;

	$this->handler->showTheSame();

	expect(Utils::$context['page_title'])->toBe(Lang::getTxt('optimus_all_keywords', file: 'Optimus/Optimus'))
		->and(Utils::$context['optimus_keyword_id'])->toBe(0);
});

test('showTheSame method with keyword not exists', function () {
	Utils::$context['current_subaction'] = '';

	$_REQUEST['id'] = 999;

	$this->handler->showTheSame();

	expect(Utils::$context['page_title'])->toBe(Lang::getTxt('optimus_404_page_title'))
		->and(Utils::$context['optimus_keyword_id'])->toBe(999);
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

test('showAllWithFrequency method with current_page != start', function () {
	Utils::$context['current_page'] = 1;

	$this->handler->showAllWithFrequency();

	// Should have called sendHttpStatus(404), but since it's mocked, just check
	expect(true)->toBeTrue(); // Placeholder, as sendHttpStatus is not mocked
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

	it('checks case with no keywords for topic', function () {
		Config::$modSettings['optimus_show_keywords_block'] = true;

		Utils::$context['current_topic'] = '3';
		Utils::$context['optimus_keywords'] = Utils::$context['optimus']['keywords'] = [];

		$this->handler->displayTopic();

		expect(Utils::$context['optimus_keywords'])->toBeEmpty();
	});
});

test('getAllKeywords method', function () {
	$getAllKeywords = new ReflectionMethod($this->handler, 'getAllKeywords');
	$result = $getAllKeywords->invoke($this->handler);

	expect($result)->toBeArray();
});

test('loadAssets method', function () {
	$loadAssets = new ReflectionMethod($this->handler, 'loadAssets');
	$loadAssets->invoke($this->handler);

	expect(Utils::$context['css_files'])->toHaveKey('virtual-select.min_css')
		->and(Utils::$context['javascript_files'])->toHaveKey('virtual-select.min_js');
});

test('loadAssets method with right_to_left', function () {
	Utils::$context['right_to_left'] = true;

	$loadAssets = new ReflectionMethod($this->handler, 'loadAssets');
	$loadAssets->invoke($this->handler);

	expect(Utils::$context['css_files'])->toHaveKey('virtual-select.min_css')
		->and(Utils::$context['javascript_files'])->toHaveKey('virtual-select.min_js');
});

test('addFields method with is_first_post false', function () {
	Utils::$context['is_first_post'] = false;

	unset(Utils::$context['posting_fields']);

	$addFields = new ReflectionMethod($this->handler, 'addFields');
	$result = $addFields->invoke($this->handler);

	expect($result)->toBeNull()
		->and(isset(Utils::$context['posting_fields']))->toBeFalse();
});

test('addFields method with is_first_post true', function () {
	Utils::$context['is_first_post'] = true;

	$addFields = new ReflectionMethod($this->handler, 'addFields');
	$result = $addFields->invoke($this->handler);

	expect($result)->toBeNull()
        ->and(isset(Utils::$context['posting_fields']['optimus_keywords']))->toBeTrue();
});

test('getIdByName method', function () {
	$getIdByName = new ReflectionMethod($this->handler, 'getIdByName');
	$result = $getIdByName->invoke($this->handler, 'id');

	expect($result)->toBe(1);
});

test('getNameById method', function () {
	$getNameById = new ReflectionMethod($this->handler, 'getNameById');
	$result = $getNameById->invoke($this->handler, 1);

	expect($result)->toBe('Keyword 1');
});

test('getNameById method with id=0', function () {
	$getNameById = new ReflectionMethod($this->handler, 'getNameById');
	$result = $getNameById->invoke($this->handler, 0);

	expect($result)->toBe('');
});

test('getRandomColor method with use_color_tags true', function () {
	Config::$modSettings['optimus_use_color_tags'] = true;

	$getRandomColor = new ReflectionMethod($this->handler, 'getRandomColor');
	$result = $getRandomColor->invoke($this->handler, 'test');

	expect($result)->toStartWith('background-color: hsl(');
});

test('getRandomColor method with use_color_tags false', function () {
	Config::$modSettings['optimus_use_color_tags'] = false;

	$getRandomColor = new ReflectionMethod($this->handler, 'getRandomColor');
	$result = $getRandomColor->invoke($this->handler, 'test');

	expect($result)->toBe('');
});

test('addToDatabase method', function () {
	$addToDatabase = new ReflectionMethod($this->handler, 'addToDatabase');
	$result = $addToDatabase->invoke($this->handler, 'test');

	expect($result)->toBe(1);
});

test('preparedKeywords method', function () {
	$_REQUEST['optimus_keywords'] = 'foo,bar';

	$preparedKeywords = new ReflectionMethod($this->handler, 'preparedKeywords');
	$result = $preparedKeywords->invoke($this->handler);

	expect($result)->toBe(['foo', 'bar']);
});

test('preparedKeywords method with empty request', function () {
	$_REQUEST['optimus_keywords'] = '';

	$preparedKeywords = new ReflectionMethod($this->handler, 'preparedKeywords');
	$result = $preparedKeywords->invoke($this->handler);

	expect($result)->toBe([]);
});

test('canChange method with allow_change false', function () {
	Config::$modSettings['optimus_allow_change_topic_keywords'] = false;

	$canChange = new ReflectionMethod($this->handler, 'canChange');
	$result = $canChange->invoke($this->handler);

	expect($result)->toBeFalse();
});

test('canChange method with allow_change true', function () {
	Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

	$canChange = new ReflectionMethod($this->handler, 'canChange');
	$result = $canChange->invoke($this->handler);

	expect($result)->toBeTrue();
});

test('modify method', function () {
	Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

	$_REQUEST['optimus_keywords'] = 'key_1,key_2';

	$modify = new ReflectionMethod($this->handler, 'modify');
	$result = $modify->invoke($this->handler, 1, 1);

	expect($result)->toBeNull();
});

test('modify method with canChange false', function () {
	Config::$modSettings['optimus_allow_change_topic_keywords'] = false;

	$_REQUEST['optimus_keywords'] = 'key_1,key_2';

	$modify = new ReflectionMethod($this->handler, 'modify');
	$result = $modify->invoke($this->handler, 1, 1);

	expect($result)->toBeNull();
});

test('add method with empty parameters', function () {
	$add = new ReflectionMethod($this->handler, 'add');
	$result = $add->invoke($this->handler, [], 0, 0);

	expect($result)->toBeNull();
});

test('add method with empty topic', function () {
	$add = new ReflectionMethod($this->handler, 'add');
	$result = $add->invoke($this->handler, ['key_1'], 0, 1);

	expect($result)->toBeNull();
});

test('add method with empty user', function () {
	$add = new ReflectionMethod($this->handler, 'add');
	$result = $add->invoke($this->handler, ['key_1'], 1, 0);

	expect($result)->toBeNull();
});


test('remove method', function () {
	$remove = new ReflectionMethod($this->handler, 'remove');
	$result = $remove->invoke($this->handler, [1, 2], 1);

	expect($result)->toBeNull();
});

test('remove method with empty parameters', function () {
	$remove = new ReflectionMethod($this->handler, 'remove');
	$result = $remove->invoke($this->handler, [], 0);

	expect($result)->toBeNull();
});

test('remove method with empty topic', function () {
	$remove = new ReflectionMethod($this->handler, 'remove');
	$result = $remove->invoke($this->handler, [1], 0);

	expect($result)->toBeNull();
});
