<?php declare(strict_types=1);

use Bugo\Optimus\Handlers\AddonHandler;

it('subscribeListeners method', function () {
	expect(method_exists(AddonHandler::class, 'subscribeListeners'))
		->toBeTrue();
});

test('handler subscribes only once', function () {
    $handler = new AddonHandler();
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	$property->setValue(false);

	$handler->__invoke();

	expect($property->getValue($handler))->toBeTrue();
});
