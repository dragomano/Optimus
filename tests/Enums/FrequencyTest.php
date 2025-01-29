<?php declare(strict_types=1);

use Bugo\Optimus\Enums\Frequency;

it('returns Hourly for timestamps within the last 24 hours', function () {
	$timestamp = time() - (23 * 3600); // 23 hours ago
	expect(Frequency::fromTimestamp($timestamp))->toBe(Frequency::Hourly);
});

it('returns Daily for timestamps within the last 7 days', function () {
	$timestamp = time() - (6 * 86400); // 6 days ago
	expect(Frequency::fromTimestamp($timestamp))->toBe(Frequency::Daily);
});

it('returns Weekly for timestamps within the last month', function () {
	$timestamp = time() - (14 * 86400); // 2 weeks ago
	expect(Frequency::fromTimestamp($timestamp))->toBe(Frequency::Weekly);
});

it('returns Monthly for timestamps within the last year', function () {
	$timestamp = time() - (30 * 86400 * 6); // 6 months ago
	expect(Frequency::fromTimestamp($timestamp))->toBe(Frequency::Monthly);
});

it('returns Yearly for timestamps older than a year', function () {
	$timestamp = time() - (400 * 86400); // More than a year ago
	expect(Frequency::fromTimestamp($timestamp))->toBe(Frequency::Yearly);
});

it('handles exact boundary cases correctly', function () {
	expect(Frequency::fromTimestamp(time() - 86400))->toBe(Frequency::Daily);  // Exactly 24 hours
	expect(Frequency::fromTimestamp(time() - 604800))->toBe(Frequency::Weekly); // Exactly 7 days
	expect(Frequency::fromTimestamp(time() - 2628000))->toBe(Frequency::Monthly); // Exactly 1 month
	expect(Frequency::fromTimestamp(time() - 31536000))->toBe(Frequency::Yearly); // Exactly 1 year
});
