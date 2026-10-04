<?php declare(strict_types=1);

use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\LightPortal;

// The addon behavior depends on the external Light Portal package
// (the LightPortal\Enums classes and the LP_PAGE_PARAM constant),
// which is not available in the test environment, so only the
// metadata is checked here

test('metadata', function () {
	expect(LightPortal::PACKAGE_ID)->toBe('Bugo:LightPortal')
		->and(LightPortal::$events)->toBe([AddonInterface::ROBOTS_RULES, AddonInterface::SITEMAP_LINKS]);
});
