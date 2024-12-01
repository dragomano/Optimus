<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Utils;
use Bugo\Optimus\Services\RobotsGenerator;

beforeEach(function () {
	$this->generator = new RobotsGenerator();
});

it('generator method', function () {
	$this->generator->generate();

	expect(Utils::$context['new_robots_content'])
		->toContain('User-agent: *');
});

it('generator method with SEF enabled', function () {
	$this->generator->useSef = true;
	$this->generator->generate();

	expect(Utils::$context['new_robots_content'])
		->toContain('/help');

	$this->generator->useSef = false;
});

it('generator method with queryless_urls enabled', function () {
	Config::$modSettings['queryless_urls'] = true;

	$this->generator->generate();

	expect(Utils::$context['new_robots_content'])
		->toContain('/*board,*.0.html');

	Config::$modSettings['queryless_urls'] = false;
});
