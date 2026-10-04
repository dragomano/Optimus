<?php declare(strict_types=1);

use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\EzPortal;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;
use Tests\TestDbMapper;

beforeEach(function () {
	global $ezpSettings;

	$ezpSettings = [];
});

afterEach(function () {
	Db::$db = new FuncMapper();

	Mockery::close();
});

test('metadata', function () {
	expect(EzPortal::PACKAGE_ID)->toBe('vbgamer45:ezportal')
		->and(EzPortal::$events)->toBe([AddonInterface::ROBOTS_RULES, AddonInterface::SITEMAP_LINKS]);
});

test('changeRobots allows the portal pages', function () {
	$generator = new RobotsGenerator();

	(new EzPortal())->changeRobots($generator);

	expect($generator->customRules['*'][RobotsGenerator::RULE_ALLOW])->toBe(['/*ezportal;sa=page;p=*']);
});

test('changeRobots allows the seo urls when enabled', function () {
	global $ezpSettings;

	$ezpSettings['ezp_pages_seourls'] = 1;

	$generator = new RobotsGenerator();

	(new EzPortal())->changeRobots($generator);

	expect($generator->customRules['*'][RobotsGenerator::RULE_ALLOW])->toBe(['/pages/']);
});

test('changeSitemap adds links to the available pages', function () {
	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			return [
				['id_page' => 5, 'date' => '2026-01-01', 'title' => 'Test Page', 'permissions' => [-1]],
			];
		}
	};

	$generator = Mockery::mock(SitemapGenerator::class)->makePartial();
	$generator->shouldReceive('getStartDate')->andReturn(0);
	$generator->links = [];

	(new ReflectionProperty(SitemapGenerator::class, 'startYear'))->setValue($generator, 0);

	(new EzPortal())->changeSitemap($generator);

	expect($generator->links)->toBe([
		['loc' => 'https://example.com/index.php?action=ezportal;sa=page;p=5', 'lastmod' => '2026-01-01'],
	]);
});
