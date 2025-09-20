<?php declare(strict_types=1);

use Bugo\Optimus\Events\Dispatcher;
use Bugo\Optimus\Events\DispatcherFactory;

function resetDispatcherFactory(): void
{
    $reflection = new ReflectionClass(DispatcherFactory::class);
    $property = $reflection->getProperty('dispatcher');
    $property->setValue(null, null);
}

beforeEach(function () {
    resetDispatcherFactory();
});

it('creates and returns a Dispatcher instance', function () {
    $factory = new DispatcherFactory();
    $dispatcher = $factory();

    expect($dispatcher)->toBeInstanceOf(Dispatcher::class);
});

it('returns the same Dispatcher instance on subsequent calls', function () {
    $factory = new DispatcherFactory();
    $dispatcher1 = $factory();
    $dispatcher2 = $factory();

    expect($dispatcher1)->toBe($dispatcher2);
});

it('creates a new Dispatcher instance when factory is called multiple times after reset', function () {
    $factory = new DispatcherFactory();
    $dispatcher1 = $factory();

    resetDispatcherFactory();

    $dispatcher2 = $factory();

    expect($dispatcher1)->not()->toBe($dispatcher2)
        ->and($dispatcher1)->toBeInstanceOf(Dispatcher::class)
        ->and($dispatcher2)->toBeInstanceOf(Dispatcher::class);
});
