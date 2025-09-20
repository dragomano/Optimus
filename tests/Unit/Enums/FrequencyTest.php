<?php declare(strict_types=1);

use Bugo\Optimus\Enums\Frequency;

dataset('frequency timestamps', [
    [time() - (23 * 3600), Frequency::Hourly], // 23 hours ago
    [time() - (6 * 86400), Frequency::Daily],  // 6 days ago
    [time() - (14 * 86400), Frequency::Weekly], // 2 weeks ago
    [time() - (30 * 86400 * 6), Frequency::Monthly], // 6 months ago
    [time() - (400 * 86400), Frequency::Yearly], // More than a year ago
]);

dataset('boundary timestamps', [
    [time() - 86400, Frequency::Daily],  // Exactly 24 hours
    [time() - 604800, Frequency::Weekly], // Exactly 7 days
    [time() - 2628000, Frequency::Monthly], // Exactly 1 month
    [time() - 31536000, Frequency::Yearly], // Exactly 1 year
]);

it('returns correct frequency for given timestamp', function ($timestamp, $expected) {
    expect(Frequency::fromTimestamp($timestamp))->toBe($expected);
})->with('frequency timestamps');

it('handles exact boundary cases correctly', function ($timestamp, $expected) {
    expect(Frequency::fromTimestamp($timestamp))->toBe($expected);
})->with('boundary timestamps');
