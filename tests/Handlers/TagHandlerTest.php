<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\TagHandler;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
	$this->handler = new TagHandler();

	loadLanguage('Optimus/Optimus');
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

	expect($buttons['home'])
		->toHaveKey('action_hook');
});

test('currentAction method', function () {
	Utils::$context['current_action'] = 'keywords';

	$current_action = '';

	$this->handler->currentAction($current_action);

	expect($current_action)
		->toBe('home');
});

describe('loadPermissions method', function () {
	it('checks basic usage', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		$permissionList = [];

		$this->handler->loadPermissions([], $permissionList);

		expect($permissionList['membergroup'])
			->toHaveKey('optimus_add_keywords');
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = false;

		$permissionList = [];

		$this->handler->loadPermissions([], $permissionList);

		expect($permissionList)->toBeEmpty();
	});
});

test('basicSettings method', function () {
	$config_vars = [];

	$this->handler->basicSettings($config_vars);

	expect($config_vars)->not->toBeEmpty();
});

test('messageindexButtons method', function () {
	Config::$modSettings['optimus_show_keywords_on_message_index'] = true;

	Utils::$context['topics'] = ['foo' => 'bar'];

	$this->handler->messageindexButtons();

	expect(Utils::$context['css_header'])
		->toContain('.optimus_keywords:visited { color: transparent }');
});

test('prepareDisplayContext method', function () {
	expect(method_exists(TagHandler::class, 'prepareDisplayContext'))
		->toBeTrue();
});

test('createTopic method', function () {
	expect(method_exists(TagHandler::class, 'createTopic'))
		->toBeTrue();
});

describe('postEnd method', function () {
	it('checks case with old topic', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		Utils::$context['is_new_topic'] = false;

		Utils::$context['optimus_keywords'] = ['foo' => 'bar'];

		$this->handler->postEnd();

		expect(Utils::$context['optimus']['keywords'])
			->toContain('bar');

		unset(Utils::$context['optimus_keywords']);
	});

	it('checks case with new topic', function () {
		Config::$modSettings['optimus_allow_change_topic_keywords'] = true;

		Utils::$context['is_new_topic'] = true;

		$this->request = Request::createFromGlobals();
		$this->request->request->set('optimus_keywords', 'bar');
		$this->request->overrideGlobals();

		$this->handler->postEnd();

		expect(Utils::$context['optimus']['keywords'])
			->toBe('bar');
	});
});

test('modifyPost method', function () {
	expect(method_exists(TagHandler::class, 'modifyPost'))->toBeTrue();
});

test('removeTopics method', function () {
	expect(method_exists(TagHandler::class, 'removeTopics'))->toBeTrue();
});

test('showTheSame method', function () {
	Utils::$context['current_subaction'] = '';

	$this->handler->showTheSame();

	expect(Utils::$context['optimus_keyword_id'])->toBeInt()
		->and(Utils::$context['template_layers'])->toContain('keywords');
});

test('getAllByKeyId method', function () {
	expect(method_exists(TagHandler::class, 'getAllByKeyId'))
		->toBeTrue();
});

test('getTotalCountByKeyId method', function () {
	expect(method_exists(TagHandler::class, 'getTotalCountByKeyId'))
		->toBeTrue();
});

test('showAllWithFrequency method', function () {
	$this->handler->showAllWithFrequency();

	$this->assertStringContainsString('?action=keywords', Utils::$context['canonical_url']);
});

test('getAll method', function () {
	expect(method_exists(TagHandler::class, 'getAll'))
		->toBeTrue();
});

test('getTotalCount method', function () {
	expect(method_exists(TagHandler::class, 'getTotalCount'))
		->toBeTrue();
});

describe('displayTopic method', function () {
	it('checks basic usage', function () {
		Config::$modSettings['optimus_show_keywords_block'] = true;

		Utils::$context['current_topic'] = 1;

		Utils::$context['optimus_keywords'] = Utils::$context['optimus']['keywords'] = [];

		$this->handler->displayTopic();

		expect(Utils::$context['optimus_keywords'])
			->toBeEmpty();

		unset(Utils::$context['optimus_keywords']);
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_show_keywords_block'] = false;

		unset(Utils::$context['current_topic']);

		$this->handler->displayTopic();

		expect(isset(Utils::$context['optimus_keywords']))
			->toBeFalse();
	});
});
