<?php declare(strict_types=1);

use Bugo\Compat\Lang;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\SettingHandler;

beforeEach(function () {
	$this->handler = new SettingHandler();

	Lang::setTxt('meta_keywords_note', '');
	Lang::setTxt('admin_maintenance', '');
	Lang::setTxt('maintain_recount', '');

	Utils::$smcFunc['db_query'] = fn(...$params) => new stdClass();
	Utils::$smcFunc['db_get_version'] = fn() => 'v1.0';
	Utils::$smcFunc['db_title'] = 'mysql';
});

test('modifyBasicSettings method', function () {
	$config_vars = [
		['text', 'meta_keywords'],
	];

	$this->handler->modifyBasicSettings($config_vars);

	expect($config_vars)->toBeEmpty();
});

test('adminAreas method', function () {
	$admin_areas = [];

	$_REQUEST['area'] = 'optimus';

	$this->handler->adminAreas($admin_areas);

	expect($admin_areas)->not->toBeEmpty();

	unset($_REQUEST['area']);
});

test('adminSearch method', function () {
	$settings_search = [];

	$this->handler->adminSearch([], [], $settings_search);

	expect($settings_search)->not->toBeEmpty();
});

test('actions method', function () {
	$_REQUEST['area'] = 'optimus';

	$this->handler->actions();

	expect(Utils::$context['template_layers'])->toContain('tips')
		->and(Utils::$context['sub_template'])->toBe('show_settings');

	unset($_REQUEST['area']);
});

describe('Tabs', function () {
	beforeEach(function () {
		$_GET['save'] = true;
	});

	test('basicTabSettings method', function () {
		expect($this->handler->basicTabSettings())->toBeNull();

		unset($_GET['save']);

		expect($this->handler->basicTabSettings(true))->toBeArray();
	});

	test('extraTabSettings method', function () {
		expect($this->handler->extraTabSettings())->toBeNull();

		unset($_GET['save']);

		expect($this->handler->extraTabSettings(true))->toBeArray();
	});

	test('faviconTabSettings method', function () {
		expect($this->handler->faviconTabSettings())->toBeNull();

		unset($_GET['save']);

		expect($this->handler->faviconTabSettings(true))->toBeArray();
	});

	test('metatagsTabSettings method', function () {
		expect($this->handler->metatagsTabSettings())->toBeNull();
	});

	test('redirectTabSettings method', function () {
		expect($this->handler->redirectTabSettings())->toBeNull();
	});

	test('counterTabSettings method', function () {
		expect($this->handler->counterTabSettings())->toBeNull();
	});

	test('robotsTabSettings method', function () {
		expect($this->handler->robotsTabSettings())->toBeNull();
	});

	test('htaccessTabSettings method', function () {
		$_POST['optimus_htaccess'] = '# comment';

		expect($this->handler->htaccessTabSettings())->toBeNull();
	});

	test('sitemapTabSettings method', function () {
		expect($this->handler->sitemapTabSettings())->toBeNull()
			->and($this->handler->sitemapTabSettings(true))->toBeArray();
	});

	afterEach(function () {
		unset($_GET['save']);
	});
});
