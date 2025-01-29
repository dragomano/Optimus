<?php declare(strict_types=1);

use Bugo\Optimus\Enums\Priority;

it('returns Prime for timestamps within 30 days', function () {
	$timestamp = time() - (29 * 86400);
	expect(Priority::fromTimestamp($timestamp))->toBe(Priority::Prime);
});

it('returns Elevated for timestamps within 31-60 days', function () {
	$timestamp = time() - (45 * 86400);
	expect(Priority::fromTimestamp($timestamp))->toBe(Priority::Elevated);
});

it('returns Base for timestamps within 61-90 days', function () {
	$timestamp = time() - (75 * 86400);
	expect(Priority::fromTimestamp($timestamp))->toBe(Priority::Base);
});

it('returns Minimal for timestamps older than 90 days', function () {
	$timestamp = time() - (120 * 86400);
	expect(Priority::fromTimestamp($timestamp))->toBe(Priority::Minimal);
});

it('handles exact boundary cases correctly', function () {
	expect(Priority::fromTimestamp(time() - (30 * 86400)))->toBe(Priority::Prime);
	expect(Priority::fromTimestamp(time() - (60 * 86400)))->toBe(Priority::Elevated);
	expect(Priority::fromTimestamp(time() - (90 * 86400)))->toBe(Priority::Base);
});
