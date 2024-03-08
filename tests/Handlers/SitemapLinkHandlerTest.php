<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Lang;
use Bugo\Compat\Utils;
use Bugo\Compat\Theme;
use Bugo\Optimus\Handlers\SitemapLinkHandler;

beforeEach(function () {
	$this->handler = new SitemapLinkHandler();

	Lang::$txt['url'] = '';
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

	Lang::$txt['optimus_sitemap_title'] = 'foo';

	Utils::$context['html_headers'] = '';

	$this->handler->addLink();

	$this->assertStringContainsString(Lang::$txt['optimus_sitemap_title'], Lang::$forum_copyright);
	expect(Utils::$context['html_headers'])->not->toBeEmpty();

	Config::$modSettings['optimus_sitemap_link'] = false;

	Utils::$context['html_headers'] = '';

	$this->handler->addLink();

	expect(Utils::$context['html_headers'])->toBeEmpty();
});
