<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Utils;
use Bugo\Optimus\Services\RobotsGenerator;

beforeEach(function () {
	$this->generator = new RobotsGenerator();

	$this->tempDir = sys_get_temp_dir() . '/sitemaps';
	mkdir($this->tempDir, 0777, true);

	file_put_contents($this->tempDir . '/sitemap.xml', '');
	file_put_contents($this->tempDir . '/sitemap.xml.gz', '');

	Config::$boarddir = $this->tempDir;
});

afterEach(function () {
	if (is_dir($this->tempDir)) {
		array_map('unlink', glob($this->tempDir . '/*'));
		rmdir($this->tempDir);
	}
});

it('generates correct links', function () {
	$this->generator->generate();

	Config::$modSettings['xmlnews_enable'] = true;

	expect(Utils::$context['new_robots_content'])
		->toContain('User-agent: *');
});

it('generates correct links with SEF enabled', function () {
	$this->generator->useSef = true;
	$this->generator->generate();

	expect(Utils::$context['new_robots_content'])
		->toContain('/help');

	$this->generator->useSef = false;
});

it('generates correct links with queryless_urls enabled', function () {
	Config::$modSettings['queryless_urls'] = true;

	$this->generator->generate();

	expect(Utils::$context['new_robots_content'])
		->toContain('/*board,*.0.html');

	Config::$modSettings['queryless_urls'] = false;
});

it('can process custom rules', function () {
	$this->generator->customRules['GoogleBot']['disallow'][] = '/';
	$this->generator->generate();

	expect(Utils::$context['new_robots_content'])
		->toContain('User-agent: GoogleBot');
});
