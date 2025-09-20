<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Optimus\Enums\Entity;

dataset('entity url configs', [
    [false, '?topic=123'],
    [true, '/topic,123.html'],
]);

dataset('entity pattern configs', [
    [false, '/*topic=*.0$'],
    [true, '/*topic,*.0.html$'],
]);

it('builds correct URL for given config', function ($queryless, $expected) {
    Config::$modSettings['queryless_urls'] = $queryless;

    expect(Entity::TOPIC->buildUrl('123'))->toBe('https://example.com/index.php' . $expected);
})->with('entity url configs');

it('builds correct pattern for given config', function ($queryless, $expected) {
    Config::$modSettings['queryless_urls'] = $queryless;

    expect(Entity::TOPIC->buildPattern())->toBe($expected);
})->with('entity pattern configs');
