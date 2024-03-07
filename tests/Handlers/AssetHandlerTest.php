<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\AssetHandler;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
	$this->handler = new AssetHandler();
});

test('handle for lighthouse', function () {
	$this->request = Request::createFromGlobals();
	$this->request->server->set('HTTP_USER_AGENT', 'Lighthouse');
	$this->request->overrideGlobals();

	Utils::$context['html_headers'] = '';

	Config::$modSettings['optimus_head_code'] = 'bar';

	$this->handler->handle();

	expect(Utils::$context['html_headers'])
		->toBeEmpty();

	$this->request->server->remove('HTTP_USER_AGENT');
	$this->request->overrideGlobals();
});

test('handle with request xml', function () {
	$this->request = Request::createFromGlobals();
	$this->request->request->set('xml', true);
	$this->request->overrideGlobals();

	Utils::$context['html_headers'] = '';

	Config::$modSettings['optimus_head_code'] = 'bar';

	$this->handler->handle();

	expect(Utils::$context['html_headers'])->toBeEmpty();

	$this->request->request->remove('xml');
	$this->request->overrideGlobals();
});

test('handle with ignored action', function () {
	Utils::$context['html_headers'] = '';
	Utils::$context['current_action'] = 'test';
	Config::$modSettings['optimus_ignored_actions'] = 'test';

	$this->handler->handle();

	expect(Utils::$context['html_headers'])->toBeEmpty();

	Config::$modSettings['optimus_ignored_actions'] = '';
});

test('handle with head code', function () {
	Utils::$context['html_headers'] = '';
	Utils::$context['current_action'] = 'forum';

	Config::$modSettings['optimus_head_code'] = 'bar';

	$this->handler->handle();

	$this->assertStringContainsString('bar', Utils::$context['html_headers']);
});

test('handle with empty head code', function () {
	Utils::$context['html_headers'] = '';
	Utils::$context['current_action'] = 'forum';

	Config::$modSettings['optimus_head_code'] = '';

	$this->handler->handle();

	expect(Utils::$context['html_headers'])->toBeEmpty();
});

test('handle with stat code', function () {
	Utils::$context['insert_after_template'] = '';
	Utils::$context['current_action'] = 'forum';

	Config::$modSettings['optimus_stat_code'] = 'bar';

	$this->handler->handle();

	$this->assertStringContainsString('bar', Utils::$context['insert_after_template']);
});

test('handle with count code', function () {
	Utils::$context['template_layers'] = Utils::$context['css_header'] = [];
	Utils::$context['current_action'] = 'forum';

	Config::$modSettings['optimus_count_code'] = 'bar';
	Config::$modSettings['optimus_counters_css'] = 'bar';

	$this->handler->handle();

	expect(Utils::$context['template_layers'])->toContain('footer_counters')
		->and(Utils::$context['css_header'])->toContain('bar');
});

test('handle with count code without css', function () {
	Utils::$context['template_layers'] = Utils::$context['css_header'] = [];
	Utils::$context['current_action'] = 'forum';

	Config::$modSettings['optimus_count_code'] = 'bar';
	Config::$modSettings['optimus_counters_css'] = false;

	$this->handler->handle();

	expect(Utils::$context['template_layers'])->toContain('footer_counters')
		->and(Utils::$context['css_header'])->toBeEmpty();
});
