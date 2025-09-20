<?php declare(strict_types=1);

use Bugo\Compat\Board;
use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Compat\Theme;
use Bugo\Compat\User;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\TopicHandler;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestDbMapper;

beforeEach(function () {
	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			if (str_contains($query, 'SELECT optimus_description, id_member_started')) {
				return ['Some description', '2'];
			}

			return [];
		}
	};

	Board::$info = ['name' => 'Test Board'];

	$this->handler = new TopicHandler();

	// Mock User for permissions
	User::$me = Mockery::mock(User::class)->makePartial();
	User::$me->shouldReceive('allowedTo')->andReturn(true);
});

afterEach(function () {
	Db::$db = new FuncMapper();
});

test('prepareOgImage method with empty optimus_og_image', function () {
	Config::$modSettings['optimus_og_image'] = false;

	expect($this->handler->prepareOgImage())->toBeNull();
});

test('prepareOgImage method', function () {
	Config::$modSettings['optimus_og_image'] = true;

	Utils::$context['topicinfo']['id_first_msg'] = 1;
	Utils::$context['current_topic'] = 1;
	Utils::$context['loaded_attachments'][1] = [
		[
			'width' => 600,
			'height' => 400,
			'mime_type' => 'image/png',
		]
	];

	$this->handler->prepareOgImage();

	expect(Utils::$context['optimus_og_image']['url'])
		->toBe(Theme::$current->settings['og_image']);
});

test('prepareOgImage method with topic_first_image', function () {
	Utils::$context['optimus_og_image'] = '';
	Utils::$context['loaded_attachments'] = [];
	Utils::$context['topicinfo']['topic_first_message']
		= '[img]https://picsum.photos/seed/wPSdlIrIS3LM5vKU/400/200[/img][br]Deserunt velit sed inventore nostrum. Saepe ea quas occaecati non provident maiores';

	$this->handler->prepareOgImage();

	expect(Theme::$current->settings['og_image'])->toBe('https://picsum.photos/seed/wPSdlIrIS3LM5vKU/400/200');
});

test('loadPermissions method', function () {
	$permissionList = [];

	Config::$modSettings['optimus_allow_change_topic_desc'] = false;

	expect($this->handler->loadPermissions([], $permissionList))->toBeNull();

	Config::$modSettings['optimus_allow_change_topic_desc'] = true;

	$this->handler->loadPermissions([], $permissionList);

	expect($permissionList['membergroup'])
		->toHaveKey('optimus_add_descriptions');
});

test('basicSettings method', function () {
	$config_vars = [];

	$this->handler->basicSettings($config_vars);

	expect($config_vars)->not->toBeEmpty();
});

test('displayTopic method', function () {
	Config::$modSettings['optimus_allow_change_topic_desc'] = true;

	$columns = [];

	$this->handler->displayTopic($columns);

	expect($columns)->toContain('t.optimus_description');

	$columns = ['ms.modified_time AS topic_modified_time'];

	$this->handler->displayTopic($columns);

	expect($columns)->toContain('ms.modified_time AS topic_modified_time');

	Config::$modSettings['optimus_topic_description'] = false;
	Config::$modSettings['optimus_og_image'] = false;

	expect($this->handler->displayTopic($columns))->toBeNull();

	Config::$modSettings['optimus_topic_description'] = true;
	Config::$modSettings['optimus_og_image'] = true;

	$columns = ['ms.body AS topic_first_message'];

	$this->handler->displayTopic($columns);

	expect($columns)->toContain('ms.body AS topic_first_message');
});

test('menuButtons method', function () {
	Utils::$context['first_message'] = 1;

	Config::$modSettings['optimus_topic_description'] = true;

	Utils::$context['topicinfo'] = [
		'topic_first_message' => 'bar',
		'topic_started_name' => 'foo',
		'topic_started_time' => time(),
		'topic_modified_time' => time(),
	];

	$this->handler->menuButtons();

	expect(Utils::$context['meta_description'])->toBe('bar')
		->and(Utils::$context['optimus_og_type']['article'])->not->toBeEmpty();
});

test('beforeCreateTopic method', function () {
	Config::$modSettings['optimus_allow_change_topic_desc'] = true;

	$topic_columns = $topic_parameters = [];

	$this->request = Request::createFromGlobals();
	$this->request->request->set('optimus_description', 'bar');
	$this->request->overrideGlobals();

	$this->handler->beforeCreateTopic([], [], [], $topic_columns, $topic_parameters);

	expect($topic_columns)->toHaveKey('optimus_description')
		->and($topic_parameters)->toContain('bar');
});

test('modifyPost method', function () {
	expect($this->handler->modifyPost([], [], ['id' => 10], ['id' => 1, 'first_msg' => 10]))->toBeNull();
});

test('postEnd method - create', function () {
	Utils::$context['is_new_topic'] = true;

	$this->request = Request::createFromGlobals();
	$this->request->request->set('optimus_description', 'bar');
	$this->request->overrideGlobals();

	$this->handler->postEnd();

	expect(Utils::$context['optimus']['description'])->toBe('bar');
});

test('postEnd method - edit', function () {
	Utils::$context['is_new_topic'] = false;
	Utils::$context['user']['id'] = 1;
	Utils::$context['user']['is_guest'] = false;

	$this->request = Request::createFromGlobals();
	$this->request->request->set('optimus_description', 'Some description');
	$this->request->overrideGlobals();

	$this->handler->postEnd();

	expect(Utils::$context['optimus']['description'])->toBe('Some description');
});

test('menuButtons method with no first_message', function () {
	Utils::$context['first_message'] = false;

	expect($this->handler->menuButtons())->toBeNull();
});

test('displayTopic method with optimus_allow_change_topic_desc false', function () {
	Config::$modSettings['optimus_allow_change_topic_desc'] = false;
	Config::$modSettings['optimus_topic_description'] = true;
	Config::$modSettings['optimus_og_image'] = true;

	$columns = [];

	$this->handler->displayTopic($columns);

	expect($columns)->not->toContain('t.optimus_description');
});

test('beforeCreateTopic method when cannot change description', function () {
	Config::$modSettings['optimus_allow_change_topic_desc'] = false;

	$topic_columns = $topic_parameters = [];

	$this->handler->beforeCreateTopic([], [], [], $topic_columns, $topic_parameters);

	expect($topic_columns)->not->toHaveKey('optimus_description');
});

test('menuButtons method with empty topic_first_message', function () {
	Config::$modSettings['optimus_topic_description'] = true;

	Utils::$context['first_message'] = 1;
	Utils::$context['topicinfo']['topic_first_message'] = '';
	Utils::$context['topicinfo']['optimus_description'] = 'some desc';

	$this->handler->menuButtons();

	expect(Utils::$context['meta_description'])->toBe('some desc');
});

test('menuButtons method with empty optimus_description', function () {
	Config::$modSettings['optimus_topic_description'] = true;

	Utils::$context['first_message'] = 1;
	Utils::$context['topicinfo']['optimus_description'] = '';
	Utils::$context['topicinfo']['topic_first_message'] = '';

	unset(Utils::$context['meta_description']);

	$this->handler->menuButtons();

	expect(isset(Utils::$context['meta_description']))->toBeFalse();
});

test('canChangeDescription method various scenarios', function () {
	$reflection = new ReflectionClass($this->handler);
	$method = $reflection->getMethod('canChangeDescription');

    Config::$modSettings['optimus_allow_change_topic_desc'] = true;
	Utils::$context['user']['started'] = true;

	// Mock User permissions
	User::$me = Mockery::mock(User::class)->makePartial();
	User::$me->shouldReceive('allowedTo')
		->with('optimus_add_descriptions_any')
		->andReturn(true);
	User::$me->shouldReceive('allowedTo')
		->with('optimus_add_descriptions_own')
		->andReturn(false);

	expect($method->invoke($this->handler))->toBeTrue();

	Config::$modSettings['optimus_allow_change_topic_desc'] = false;

	expect($method->invoke($this->handler))->toBeFalse();

	Config::$modSettings['optimus_allow_change_topic_desc'] = true;

	User::$me = Mockery::mock(User::class)->makePartial();
	User::$me->shouldReceive('allowedTo')
		->with('optimus_add_descriptions_any')
		->andReturn(false);
	User::$me->shouldReceive('allowedTo')
		->with('optimus_add_descriptions_own')
		->andReturn(true);

	expect($method->invoke($this->handler))->toBeTrue();

	Utils::$context['user']['started'] = false;

	expect($method->invoke($this->handler))->toBeFalse();

	Mockery::close();
});

test('postEnd method with adding fields', function () {
	Config::$modSettings['optimus_allow_change_topic_desc'] = true;
	Utils::$context['is_new_topic'] = false;
	Utils::$context['is_first_post'] = true;
	Utils::$context['user']['id'] = 2;
	Utils::$context['user']['is_guest'] = false;
	Utils::$context['user']['started'] = true;

	User::$me = Mockery::mock(User::class)->makePartial();
	User::$me->shouldReceive('allowedTo')
		->with('optimus_add_descriptions_any')
		->andReturn(true);

	$this->handler->postEnd();

	expect(Utils::$context['posting_fields'])->toHaveKey('optimus_description');

	Mockery::close();
});

test('modifyPost method with modifyDescription call', function () {
	Config::$modSettings['optimus_allow_change_topic_desc'] = true;
	Utils::$context['user']['started'] = true;

	User::$me = Mockery::mock(User::class)->makePartial();
	User::$me->shouldReceive('allowedTo')
		->with('optimus_add_descriptions_any')
		->andReturn(true);

	$this->request = Request::createFromGlobals();
	$this->request->request->set('optimus_description', 'New description');
	$this->request->overrideGlobals();

	$this->handler->modifyPost([], [], ['id' => 10], ['id' => 1, 'first_msg' => 10]);

	// Since Db is mocked, we can't check the query, but we can check that it doesn't throw
	expect(true)->toBeTrue();

	Mockery::close();
});

test('prepareOgImage method when og_image already set', function () {
	Config::$modSettings['optimus_og_image'] = true;

	Utils::$context['topicinfo']['id_first_msg'] = 1;
	Utils::$context['optimus_og_image'] = ['url' => 'existing'];
	Utils::$context['topicinfo']['topic_first_message'] = '[img]http://example.com/image.jpg[/img]';
	Utils::$context['loaded_attachments'] = [];

	Theme::$current->settings['og_image'] = '';

	$this->handler->prepareOgImage();

	expect(Theme::$current->settings['og_image'])->toBe('');
});

test('basicSettings method with config_vars containing optimus_topic_extend_title', function () {
	$config_vars = [
		['check', 'optimus_topic_extend_title'],
		['text', 'some_other_setting'],
	];

	$this->handler->basicSettings($config_vars);

	expect($config_vars)->toHaveCount(5)
		->and($config_vars[1])->toBe('')
		->and($config_vars[2][1])->toBe('optimus_topic_description');
});
