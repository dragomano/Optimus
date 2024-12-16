<?php declare(strict_types=1);

use Bugo\Optimus\Prime;

beforeEach(function () {
	$this->prime = new Prime();
});

it('runs __invoke', function () {
	expect((new Prime())())->toBeNull();
});
