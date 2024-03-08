<?php declare(strict_types=1);

use Bugo\Optimus\Handlers\AddonHandler;

it('subscribeListeners method', function () {
	expect(method_exists(AddonHandler::class, 'subscribeListeners'))
		->toBeTrue();
});
