<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Lang;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\SitemapLinkHandler;

beforeEach(function () {
	$this->handler = new SitemapLinkHandler();

	Lang::setTxt('url', '');
});

test('actions method', function () {
	$actions = [];

	$this->handler->actions($actions);

	expect($actions)
		->toHaveKey('sitemap_xsl');
});

test('xsl method', function () {
	ob_start();

	$this->handler->xsl();

	$result = ob_get_clean();

	$this->assertStringContainsString(OP_NAME, $result);
});

test('preLogStats method', function () {
	$no_stat_actions = [];

	$this->handler->preLogStats($no_stat_actions);

	expect($no_stat_actions)
		->toHaveKey('sitemap_xsl');
});

test('addLink method', function () {
	Config::$modSettings['optimus_sitemap_link'] = true;

	Lang::setTxt('optimus_sitemap_title', 'foo');

	Utils::$context['html_headers'] = '';

	$this->handler->addLink();

	$this->assertStringContainsString(Lang::getTxt('optimus_sitemap_title'), Lang::$forum_copyright);
	expect(Utils::$context['html_headers'])->not->toBeEmpty();

	Config::$modSettings['optimus_sitemap_link'] = false;

	Utils::$context['html_headers'] = '';

	$this->handler->addLink();

	expect(Utils::$context['html_headers'])->toBeEmpty();
});

test('invoke method', function () {
	$this->handler->__invoke();

	expect(true)->toBeTrue();
});

test('addLink method with empty sitemap title', function () {
	Config::$modSettings['optimus_sitemap_link'] = true;

	Lang::setTxt('optimus_sitemap_title', '');

	Utils::$context['html_headers'] = '';

	$this->handler->addLink();

	expect(Utils::$context['html_headers'])->toBeEmpty();
});

test('addLink method with uninstalling context', function () {
	Config::$modSettings['optimus_sitemap_link'] = true;

	Lang::setTxt('optimus_sitemap_title', 'foo');

	Utils::$context['uninstalling'] = true;
	Utils::$context['html_headers'] = '';

	$this->handler->addLink();

	expect(Utils::$context['html_headers'])->toBeEmpty();
});

test('xsl method with compressed output', function () {
	Config::$modSettings['enableCompressedOutput'] = true;

	ob_start();

	$this->handler->xsl();

	$result = ob_get_clean();

	$this->assertStringContainsString(OP_NAME, $result);
});
