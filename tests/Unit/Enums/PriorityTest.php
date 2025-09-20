<?php declare(strict_types=1);

use Bugo\Optimus\Enums\Priority;

dataset('priority timestamps', [
    [time() - (29 * 86400), Priority::Prime],     // Within 30 days
    [time() - (45 * 86400), Priority::Elevated],  // 31-60 days
    [time() - (75 * 86400), Priority::Base],      // 61-90 days
    [time() - (120 * 86400), Priority::Minimal],  // Older than 90 days
]);

dataset('boundary priorities', [
    [time() - (30 * 86400), Priority::Prime],
    [time() - (60 * 86400), Priority::Elevated],
    [time() - (90 * 86400), Priority::Base],
]);

it('returns correct priority for given timestamp', function ($timestamp, $expected) {
    expect(Priority::fromTimestamp($timestamp))->toBe($expected);
})->with('priority timestamps');

it('handles exact boundary cases correctly', function ($timestamp, $expected) {
    expect(Priority::fromTimestamp($timestamp))->toBe($expected);
})->with('boundary priorities');
