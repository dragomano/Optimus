<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Optimus\Services\{
	SitemapGenerator,
	SitemapDataService,
	FileSystem,
	XmlGenerator
};
use Bugo\Optimus\Events\{AddonEvent, DispatcherFactory};
use Bugo\Optimus\Addons\AddonInterface;

beforeEach(function () {
	$this->tempDir = sys_get_temp_dir() . '/optimus_test_' . uniqid();
	mkdir($this->tempDir, 0777, true);

	Config::$boarddir = $this->tempDir;
	Config::$boardurl = 'https://example.com';
	Config::$scripturl = 'https://example.com/index.php';

	Config::$modSettings = [
		'optimus_sitemap_enable' => true,
		'optimus_sitemap_items_display' => 1000,
		'optimus_sitemap_add_found_images' => false,
		'optimus_sitemap_topics_num_replies' => 0,
		'optimus_sitemap_all_topic_pages' => false,
		'optimus_remove_previous_xml_files' => true,
		'queryless_urls' => false,
		'defaultMaxMessages' => 20
	];

	$this->dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [['loc' => 'https://example.com/board1', 'lastmod' => time()]];
		}

		public function getTopicLinks(): array {
			return [['loc' => 'https://example.com/topic1', 'lastmod' => time()]];
		}
	};

	$fileSystem = new FileSystem($this->tempDir);
	$xmlGenerator = new XmlGenerator(Config::$scripturl);
	$this->dispatcher = (new DispatcherFactory())();

	$this->generator = new SitemapGenerator(
		$this->dataService,
		$fileSystem,
		$xmlGenerator,
		$this->dispatcher,
		2020
	);
});

afterEach(function () {
	if (is_dir($this->tempDir)) {
		array_map('unlink', glob($this->tempDir . '/*'));
		rmdir($this->tempDir);
	}
});

it('removes old sitemap files', function () {
	$oldFiles = ['sitemap.xml', 'sitemap_1.xml'];

	foreach ($oldFiles as $file) {
		file_put_contents($this->tempDir . '/' . $file, 'old content');
		expect(file_exists($this->tempDir . '/' . $file))->toBeTrue();
	}

	$method = new ReflectionMethod($this->generator, 'removeOldFiles');
	$method->invoke($this->generator);

	foreach ($oldFiles as $file) {
		expect(file_exists($this->tempDir . '/' . $file))->toBeFalse();
	}
});

it('processes single sitemap correctly', function () {
	$items = [
		['loc' => 'https://example.com/page1', 'lastmod' => time()],
		['loc' => 'https://example.com/page2', 'lastmod' => time()]
	];

	$method = new ReflectionMethod($this->generator, 'processSingleSitemap');
	$method->invoke($this->generator, $items);

	$content = file_get_contents($this->tempDir . '/sitemap.xml');

	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue()
		->and($content)->toContain('<loc>https://example.com/page1</loc>')
		->and($content)->toContain('<loc>https://example.com/page2</loc>')
		->and($content)->toContain('<?xml version="1.0" encoding="UTF-8"?>');
});

it('prepares entry correctly', function () {
	$entry = [
		'loc' => 'https://example.com/test',
		'lastmod' => time()
	];

	$method = new ReflectionMethod($this->generator, 'prepareEntry');
	$result = $method->invoke($this->generator, $entry);

	expect($result)
		->toHaveKey('loc')
		->toHaveKey('lastmod')
		->toHaveKey('changefreq')
		->toHaveKey('priority');
});

it('creates sitemap successfully', function () {
	expect($this->generator->generate())->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue();
});

it('creates multiple sitemap files when needed', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 2;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()],
				['loc' => 'https://example.com/board3', 'lastmod' => time()]
			];
		}

		public function getTopicLinks(): array {
			return [
				['loc' => 'https://example.com/topic1', 'lastmod' => time()]
			];
		}
	};

	$generator = new SitemapGenerator(
		$dataService,
		new FileSystem($this->tempDir),
		new XmlGenerator(Config::$scripturl),
		$this->dispatcher,
		2020
	);

	$generator->generate();

	expect(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_1.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue();
});

describe('Generator helper methods', function () {
	it('formats date correctly', function () {
		$timestamp = strtotime('2024-01-01 12:00:00');

		$method = new ReflectionMethod($this->generator, 'getDateIso8601');
		$result = $method->invoke($this->generator, $timestamp);

		expect($result)->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:00$/');
	});

	it('returns correct frequency', function () {
		$now = time();

		$method = new ReflectionMethod($this->generator, 'getFrequency');

		expect($method->invoke($this->generator, $now - 3600))->toBe('hourly')
			->and($method->invoke($this->generator, $now - 86400 * 2))->toBe('daily')
			->and($method->invoke($this->generator, $now - 86400 * 14))->toBe('weekly')
			->and($method->invoke($this->generator, $now - 86400 * 60))->toBe('monthly')
			->and($method->invoke($this->generator, $now - 86400 * 400))->toBe('yearly');
	});

	it('returns correct priority', function () {
		$now = time();

		$method = new ReflectionMethod($this->generator, 'getPriority');

		expect($method->invoke($this->generator, $now - 86400 * 15))->toBe('0.8')
			->and($method->invoke($this->generator, $now - 86400 * 45))->toBe('0.6')
			->and($method->invoke($this->generator, $now - 86400 * 75))->toBe('0.4')
			->and($method->invoke($this->generator, $now - 86400 * 100))->toBe('0.2');
	});
});

it('allows adding custom links through event dispatcher', function () {
	$testAddon = new class implements AddonInterface {
		public function __invoke(AddonEvent $event): void
		{
			match ($event->eventName()) {
				self::SITEMAP_LINKS => $this->changeSitemap($event->getTarget()),
			};
		}

		public function changeSitemap(SitemapGenerator $sitemap): void
		{
			$sitemap->links[] = [
				'loc' => 'https://example.com/custom',
				'lastmod' => time()
			];
		}
	};

	$this->dispatcher->subscribeTo(
		AddonInterface::SITEMAP_LINKS,
		function(AddonEvent $event) use ($testAddon) {
			$testAddon->changeSitemap($event->getTarget());
		}
	);

	$this->generator->generate();

	$content = file_get_contents($this->tempDir . '/sitemap.xml');
	expect($content)
		->toContain('<loc>https://example.com/custom</loc>')
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue();
});
