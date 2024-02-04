<?php declare(strict_types=1);

use Bugo\Optimus\Handlers\RedirectHandler;

beforeEach(function () {
	$this->handler = new RedirectHandler();
});

test('handle method', function () {
	expect(method_exists(RedirectHandler::class, 'handle'))
		->toBeTrue();
});
