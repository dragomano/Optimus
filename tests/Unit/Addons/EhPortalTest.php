<?php declare(strict_types=1);

use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\EhPortal;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;
use Tests\TestDbMapper;

afterEach(function () {
	Db::$db = new FuncMapper();

	Mockery::close();
});

test('metadata', function () {
	expect(EhPortal::PACKAGE_ID)->toBe('[ChenZhen]:EhPortal')
		->and(EhPortal::$events)->toBe([AddonInterface::ROBOTS_RULES, AddonInterface::SITEMAP_LINKS]);
});

test('changeRobots allows the portal pages', function () {
	$generator = new RobotsGenerator();

	(new EhPortal())->changeRobots($generator);

	expect($generator->customRules['*'][RobotsGenerator::RULE_ALLOW])->toBe(['/*page=*']);
});

test('changeSitemap adds links to the active pages', function () {
	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			return [
				['namespace' => 'first_page'],
				['namespace' => 'second_page'],
			];
		}
	};

	$generator = Mockery::mock(SitemapGenerator::class)->makePartial();
	$generator->links = [];

	(new EhPortal())->changeSitemap($generator);

	expect($generator->links)->toBe([
		['loc' => 'https://example.com/index.php?page=first_page'],
		['loc' => 'https://example.com/index.php?page=second_page'],
	]);
});
