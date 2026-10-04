<?php declare(strict_types=1);

use Bugo\Compat\{Db, Theme, Utils};
use Bugo\Compat\Db\FuncMapper;
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\TinyPortal;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;
use Tests\TestDbMapper;

beforeEach(function () {
	$GLOBALS['test_integration_hooks'] = [];

	Utils::$context = [];
});

afterEach(function () {
	Db::$db = new FuncMapper();

	Mockery::close();
});

test('metadata', function () {
	expect(TinyPortal::PACKAGE_ID)->toBe('bloc:tinyportal')
		->and(TinyPortal::$events)->toBe([
			AddonInterface::HOOK_EVENT,
			AddonInterface::ROBOTS_RULES,
			AddonInterface::SITEMAP_LINKS,
		]);
});

test('postInit registers the tp post init hook', function () {
	(new TinyPortal())->postInit();

	$hooks = array_column($GLOBALS['test_integration_hooks'] ?? [], 0);

	expect($hooks)->toContain('integrate_tp_post_init');
});

test('changeRobots allows the portal articles', function () {
	$generator = new RobotsGenerator();

	(new TinyPortal())->changeRobots($generator);

	expect($generator->customRules['*'][RobotsGenerator::RULE_ALLOW])->toBe(['/*page']);
});

test('changeSitemap adds links to the approved articles', function () {
	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			return [
				['id' => 1, 'date' => '2026-01-01', 'shortname' => 'first_article'],
				['id' => 2, 'date' => '2026-02-01', 'shortname' => ''],
			];
		}
	};

	$generator = Mockery::mock(SitemapGenerator::class)->makePartial();
	$generator->shouldReceive('getStartDate')->andReturn(0);
	$generator->links = [];

	(new ReflectionProperty(SitemapGenerator::class, 'startYear'))->setValue($generator, 0);

	(new TinyPortal())->changeSitemap($generator);

	expect($generator->links)->toBe([
		['loc' => 'https://example.com/index.php?page=first_article', 'lastmod' => '2026-01-01'],
		['loc' => 'https://example.com/index.php?page=2', 'lastmod' => '2026-02-01'],
	]);
});

test('prepareArticleMeta fills the article meta tags', function () {
	$_GET['page'] = 1;

	Utils::$context['TPortal']['article'] = [
		'rendertype'    => 'bbc',
		'body'          => '[img]https://example.com/pic.png[/img]',
		'intro'         => '',
		'date'          => 1767000000,
		'category_name' => 'News',
		'shortname'     => 'my_article',
	];

	(new TinyPortal())->prepareArticleMeta();

	expect(Theme::$current->settings['og_image'])->toBe('https://example.com/pic.png')
		->and(Utils::$context['meta_description'])->toBeString()
		->and(Utils::$context['canonical_url'])->toBe('https://example.com/index.php?page=my_article')
		->and(Utils::$context['optimus_og_type']['article']['published_time'])
		->toBe(date('Y-m-d\TH:i:s', 1767000000))
		->and(Utils::$context['optimus_og_type']['article']['section'])->toBe('News');
});

test('prepareArticleMeta does nothing without the page param', function () {
	(new TinyPortal())->prepareArticleMeta();

	expect(Utils::$context['meta_description'] ?? null)->toBeNull();
});
