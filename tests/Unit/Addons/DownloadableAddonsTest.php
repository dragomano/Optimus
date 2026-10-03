<?php declare(strict_types=1);

use Bugo\Optimus\Addons\DownloadableAddons;

test('all returns all five downloadable addons', function () {
	$addons = DownloadableAddons::all();

	expect($addons)->toHaveCount(5)
		->and(array_column($addons, 'name'))->toBe([
			'ExampleAddon', 'ExtraSettings', 'IndexNow', 'SafeLinks', 'StructuredData',
		]);
});

test('each addon entry has required data', function () {
	foreach (DownloadableAddons::all() as $addon) {
		expect($addon['package_id'])->toStartWith('Optimus:')
			->and($addon['description'])->not->toBeEmpty()
			->and($addon['url'])->toStartWith('https://');
	}
});

test('premium addons point to the premium page', function () {
	$premium = ['ExtraSettings', 'IndexNow', 'StructuredData'];

	foreach (DownloadableAddons::all() as $addon) {
		if (in_array($addon['name'], $premium)) {
			expect($addon['url'])->toBe(DownloadableAddons::PREMIUM_URL);
		} else {
			expect($addon['url'])->toContain('github.com');
		}
	}
});
