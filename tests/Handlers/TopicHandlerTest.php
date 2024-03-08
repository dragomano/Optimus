<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Theme;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\TopicHandler;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
	$this->handler = new TopicHandler();
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
	Utils::$context['topicinfo']['topic_first_message'] = '[img]https://picsum.photos/seed/wPSdlIrIS3LM5vKU/400/200[/img][br]Deserunt velit sed inventore nostrum. Saepe ea quas occaecati non provident maiores';

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
	expect($this->handler->modifyPost([], [], [], []))->toBeNull();
});

test('postEnd method', function () {
	Utils::$context['is_new_topic'] = true;

	$this->request = Request::createFromGlobals();
	$this->request->request->set('optimus_description', 'bar');
	$this->request->overrideGlobals();

	$this->handler->postEnd();

	$this->assertStringContainsString('bar', Utils::$context['optimus']['description']);

	Utils::$context['is_new_topic'] = false;
	Utils::$context['user']['id'] = 1;
	Utils::$context['user']['is_guest'] = false;
	Utils::$smcFunc['db_fetch_row'] = fn($result) => ['bar', 1];

	$this->handler->postEnd();

	expect(Utils::$context['optimus']['description'])->toBe('bar');
});
