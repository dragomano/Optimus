<?php declare(strict_types=1);

use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Events\Dispatcher;

it('dispatches event and returns AddonEvent', function () {
    $dispatcher = new Dispatcher();

    $result = $dispatcher->dispatchEvent('test_event', 'test_target');

    expect($result)->toBeInstanceOf(AddonEvent::class);
    expect($result->eventName())->toBe('test_event');
    expect($result->getTarget())->toBe('test_target');
});

it('dispatches event with null target', function () {
    $dispatcher = new Dispatcher();

    $result = $dispatcher->dispatchEvent('null_event', null);

    expect($result)->toBeInstanceOf(AddonEvent::class);
    expect($result->eventName())->toBe('null_event');
    expect($result->getTarget())->toBeNull();
});

it('dispatches event with empty name', function () {
    $dispatcher = new Dispatcher();

    $result = $dispatcher->dispatchEvent('', 'target');

    expect($result)->toBeInstanceOf(AddonEvent::class);
    expect($result->eventName())->toBe('');
    expect($result->getTarget())->toBe('target');
});

it('dispatches event with array target', function () {
    $dispatcher = new Dispatcher();
    $target = ['key' => 'value'];

    $result = $dispatcher->dispatchEvent('array_event', $target);

    expect($result)->toBeInstanceOf(AddonEvent::class);
    expect($result->eventName())->toBe('array_event');
    expect($result->getTarget())->toBe($target);
});
