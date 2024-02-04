<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Theme;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\FaviconHandler;

beforeEach(function () {
	$this->handler = new FaviconHandler();

	Theme::$current->settings['og_image'] = '';
});

test('handle', function () {
	Config::$modSettings['optimus_favicon_text'] = 'bar';

	Utils::$context['html_headers'] = '';

	$this->handler->handle();

	$this->assertStringContainsString('bar', Utils::$context['html_headers']);
});

test('handle with disabled setting', function () {
	Config::$modSettings['optimus_favicon_text'] = false;

	Utils::$context['html_headers'] = '';

	$this->handler->handle();

	expect(Utils::$context['html_headers'])
		->toBeEmpty();
});