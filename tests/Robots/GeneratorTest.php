<?php declare(strict_types=1);

use Bugo\Compat\Utils;
use Bugo\Optimus\Robots\Generator;

beforeEach(function () {
	$this->generator = new Generator();
});

it('generator method', function () {
	$this->generator->generate();

	expect(Utils::$context['new_robots_content'])
		->toContain('User-agent: *');
});
