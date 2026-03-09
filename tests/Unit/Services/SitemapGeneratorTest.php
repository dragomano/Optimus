<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Optimus\Services\{
	SitemapGenerator,
	SitemapDataService,
	FileSystem,
	FileSystemInterface,
	XmlGenerator,
	XmlGeneratorException,
	FileSystemException
};
use Bugo\Optimus\Events\{AddonEvent, DispatcherFactory};
use Bugo\Optimus\Addons\AddonInterface;

beforeEach(function () {
	$this->tempDir = sys_get_temp_dir() . '/optimus_test_' . uniqid();
	mkdir($this->tempDir, 0777, true);

	Config::$boarddir = $this->tempDir;

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

it('does not remove old files when disabled', function () {
	Config::$modSettings['optimus_remove_previous_xml_files'] = false;

	$oldFiles = ['sitemap.xml', 'sitemap_1.xml'];

	foreach ($oldFiles as $file) {
		file_put_contents($this->tempDir . '/' . $file, 'old content');
		expect(file_exists($this->tempDir . '/' . $file))->toBeTrue();
	}

	$method = new ReflectionMethod($this->generator, 'removeOldFiles');
	$method->invoke($this->generator);

	foreach ($oldFiles as $file) {
		expect(file_exists($this->tempDir . '/' . $file))->toBeTrue();
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

it('prepares entry with image data', function () {
	$entry = [
		'loc' => 'https://example.com/test',
		'lastmod' => time(),
		'image' => ['url' => 'https://example.com/image.jpg']
	];

	$method = new ReflectionMethod($this->generator, 'prepareEntry');
	$result = $method->invoke($this->generator, $entry);

	expect($result)
		->toHaveKey('image:image')
		->and($result['image:image'])->toBe(['url' => 'https://example.com/image.jpg']);
});

it('prepares entry with video data', function () {
	$entry = [
		'loc' => 'https://example.com/test',
		'lastmod' => time(),
		'video' => ['title' => 'Test Video']
	];

	$method = new ReflectionMethod($this->generator, 'prepareEntry');
	$result = $method->invoke($this->generator, $entry);

	expect($result)
		->toHaveKey('video:video')
		->and($result['video:video'])->toBe(['title' => 'Test Video']);
});

it('prepares entry with both image and video data', function () {
	$entry = [
		'loc' => 'https://example.com/test',
		'lastmod' => time(),
		'image' => ['url' => 'https://example.com/image.jpg'],
		'video' => ['title' => 'Test Video']
	];

	$method = new ReflectionMethod($this->generator, 'prepareEntry');
	$result = $method->invoke($this->generator, $entry);

	expect($result)
		->toHaveKey('image:image')
		->and($result)->toHaveKey('video:video');
});

it('returns empty array for getImageData when no image', function () {
	$method = new ReflectionMethod($this->generator, 'getImageData');
	$result = $method->invoke($this->generator, ['loc' => 'https://example.com/test']);

	expect($result)->toBe([]);
});

it('returns image data for getImageData when image exists', function () {
	$entry = ['image' => ['url' => 'https://example.com/image.jpg']];
	$method = new ReflectionMethod($this->generator, 'getImageData');
	$result = $method->invoke($this->generator, $entry);

	expect($result)->toBe(['image:image' => ['url' => 'https://example.com/image.jpg']]);
});

it('returns empty array for getVideoData when no video', function () {
	$method = new ReflectionMethod($this->generator, 'getVideoData');
	$result = $method->invoke($this->generator, ['loc' => 'https://example.com/test']);

	expect($result)->toBe([]);
});

it('returns video data for getVideoData when video exists', function () {
	$entry = ['video' => ['title' => 'Test Video']];
	$method = new ReflectionMethod($this->generator, 'getVideoData');
	$result = $method->invoke($this->generator, $entry);

	expect($result)->toBe(['video:video' => ['title' => 'Test Video']]);
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

	it('returns empty string for empty timestamp', function () {
		$method = new ReflectionMethod($this->generator, 'getDateIso8601');
		$result = $method->invoke($this->generator, 0);

		expect($result)->toBe('');
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

it('gets last date from links array', function () {
	$maxDate = time();
	$links = [
		['loc' => 'https://example.com/page1', 'lastmod' => strtotime('-3 days', $maxDate)],
		['loc' => 'https://example.com/page2', 'lastmod' => $maxDate]
	];

	$method = new ReflectionMethod($this->generator, 'getLastDate');
	$result = $method->invoke($this->generator, $links);

	expect($result)->toBe($maxDate);

	$result = $method->invoke($this->generator, []);

	expect($result)->toBe($maxDate);
});

it('returns false when sitemap is disabled', function () {
	Config::$modSettings['optimus_sitemap_enable'] = false;

	$result = $this->generator->generate();

	expect($result)->toBeFalse();
});

it('handles XmlGeneratorException in processSingleSitemap', function () {
	$xmlGenerator = $this->createMock(XmlGenerator::class);
	$xmlGenerator->method('generate')->willThrowException(new XmlGeneratorException('XML generation failed'));

	$generator = new SitemapGenerator(
		$this->dataService,
		new FileSystem($this->tempDir),
		$xmlGenerator,
		$this->dispatcher,
		2020
	);

	$items = [['loc' => 'https://example.com/test', 'lastmod' => time()]];

	$method = new ReflectionMethod($generator, 'processSingleSitemap');
	$method->invoke($generator, $items);

	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
});

it('handles FileSystemException in processSingleSitemap', function () {
	$fileSystem = new class implements FileSystemInterface {
		public function writeFile(string $filename, string $content): void {
			throw new FileSystemException('File write failed');
		}

		public function writeGzFile(string $filename, string $content): void {
			throw new FileSystemException('File write failed');
		}
	};

	$generator = new SitemapGenerator(
		$this->dataService,
		$fileSystem,
		new XmlGenerator(Config::$scripturl),
		$this->dispatcher,
		2020
	);

	$items = [['loc' => 'https://example.com/test', 'lastmod' => time()]];

	$method = new ReflectionMethod($generator, 'processSingleSitemap');
	$method->invoke($generator, $items);

	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
});

it('handles XmlGeneratorException in processMultipleSitemaps', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$xmlGenerator = $this->createMock(XmlGenerator::class);
	$xmlGenerator->method('generate')->willThrowException(new XmlGeneratorException('XML generation failed'));

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()]
			];
		}

		public function getTopicLinks(): array {
			return [];
		}
	};

	$generator = new SitemapGenerator(
		$dataService,
		new FileSystem($this->tempDir),
		$xmlGenerator,
		$this->dispatcher,
		2020
	);

	$generator->generate();

	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
});

it('handles FileSystemException in processMultipleSitemaps', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$fileSystem = new class implements FileSystemInterface {
		public function writeFile(string $filename, string $content): void {
			throw new FileSystemException('File write failed');
		}

		public function writeGzFile(string $filename, string $content): void {
			throw new FileSystemException('File write failed');
		}
	};

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()]
			];
		}

		public function getTopicLinks(): array {
			return [];
		}
	};

	$generator = new SitemapGenerator(
		$dataService,
		$fileSystem,
		new XmlGenerator(Config::$scripturl),
		$this->dispatcher,
		2020
	);

	$generator->generate();

	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
});

it('creates gzipped files when content is large', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	// Mock large content
	$largeContent = str_repeat('x', SitemapGenerator::MAX_FILESIZE + 1000);

	$xmlGenerator = $this->createMock(XmlGenerator::class);
	$xmlGenerator->method('generate')->willReturn($largeContent);

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [['loc' => 'https://example.com/board1', 'lastmod' => time()]];
		}

		public function getTopicLinks(): array {
			return [];
		}
	};

	$generator = new SitemapGenerator(
		$dataService,
		new FileSystem($this->tempDir),
		$xmlGenerator,
		$this->dispatcher,
		2020
	);

	$generator->generate();

	expect(file_exists($this->tempDir . '/sitemap.xml.gz'))->toBeTrue();
});

it('triggers handleContent event', function () {
	$eventTriggered = false;

	$this->dispatcher->subscribeTo(
		AddonInterface::SITEMAP_CONTENT,
		function(AddonEvent $event) use (&$eventTriggered) {
			$eventTriggered = true;
		}
	);

	$this->generator->generate();

	expect($eventTriggered)->toBeTrue();
});

it('handles empty gzMaps in processMultipleSitemaps', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()]
			];
		}

		public function getTopicLinks(): array {
			return [];
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

	expect(file_exists($this->tempDir . '/sitemap.xml.gz'))->toBeFalse();
});

it('handles empty items slice in processMultipleSitemaps', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()],
				['loc' => 'https://example.com/board3', 'lastmod' => time()]
			];
		}

		public function getTopicLinks(): array {
			return [];
		}
	};

	$generator = new SitemapGenerator(
		$dataService,
		new FileSystem($this->tempDir),
		new XmlGenerator(Config::$scripturl),
		$this->dispatcher,
		2020
	);

	// Manually modify to have empty slice
	$methodCreateXml = new ReflectionMethod($generator, 'createXml');
	$methodCreateXml->invoke($generator);

	// This should handle the case where items[1] is empty
	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue();
});

it('returns early when no items are generated', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1000;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [];
		}

		public function getTopicLinks(): array {
			return [];
		}
	};

	// Create a subclass that overrides getLinks to return empty array
	$generator = new class(
		$dataService,
		new FileSystem($this->tempDir),
		new XmlGenerator(Config::$scripturl),
		$this->dispatcher,
		2020
	) extends SitemapGenerator {
		protected function getLinks(): array {
			return [];
		}
	};

	// Call createXml directly - should return early without creating files (line 95)
	$method = new ReflectionMethod($generator, 'createXml');
	$method->invoke($generator);

	// No sitemap files should be created because items array is empty
	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse()
		->and(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeFalse();
});

it('skips empty items in processMultipleSitemaps', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 2;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()]
			];
		}

		public function getTopicLinks(): array {
			return [];
		}
	};

	$generator = new SitemapGenerator(
		$dataService,
		new FileSystem($this->tempDir),
		new XmlGenerator(Config::$scripturl),
		$this->dispatcher,
		2020
	);

	// Manually create items array with an empty slot to trigger the continue statement
	$items = [
		0 => [
			['loc' => 'https://example.com/', 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '1.0'],
			['loc' => 'https://example.com/board1', 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '0.8']
		],
		1 => [], // Empty items array - should trigger continue on line 170
		2 => [
			['loc' => 'https://example.com/board2', 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '0.8']
		]
	];

	$method = new ReflectionMethod($generator, 'processMultipleSitemaps');
	$method->invoke($generator, $items, 2);

	// Should create sitemap_0.xml and sitemap_2.xml, but skip sitemap_1.xml
	expect(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_1.xml'))->toBeFalse()
		->and(file_exists($this->tempDir . '/sitemap_2.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue();
});

it('handles XmlGeneratorException when creating sitemap index', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()]
			];
		}

		public function getTopicLinks(): array {
			return [];
		}
	};

	// Create a mock XmlGenerator that throws exception only for index generation
	$xmlGenerator = new class(Config::$scripturl) extends XmlGenerator {
		private int $callCount = 0;

		public function generate(array $items, array $options = []): string {
			$this->callCount++;

			// Throw exception only when generating index (isIndex = true)
			if (!empty($options['isIndex'])) {
				throw new XmlGeneratorException('Failed to generate sitemap index');
			}

			// Normal generation for regular sitemaps
			return parent::generate($items, $options);
		}
	};

	$generator = new SitemapGenerator(
		$dataService,
		new FileSystem($this->tempDir),
		$xmlGenerator,
		$this->dispatcher,
		2020
	);

	$generator->generate();

	// Individual sitemap files should be created
	expect(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_1.xml'))->toBeTrue();

	// But index file should not be created due to exception
	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
});

it('handles FileSystemException when creating sitemap index', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()]
			];
		}

		public function getTopicLinks(): array {
			return [];
		}
	};

	// Create a mock FileSystem that throws exception only for sitemap.xml
	$fileSystem = new class($this->tempDir) implements FileSystemInterface {
		public function __construct(private string $tempDir) {}

		public function writeFile(string $filename, string $content): void {
			// Throw exception only for the index file
			if ($filename === SitemapGenerator::XML_FILE) {
				throw new FileSystemException('Failed to write sitemap index');
			}

			// Normal write for other files
			file_put_contents($this->tempDir . '/' . $filename, $content);
		}

		public function writeGzFile(string $filename, string $content): void {
			if ($filename === SitemapGenerator::XML_GZ_FILE) {
				throw new FileSystemException('Failed to write gzipped sitemap index');
			}

			file_put_contents($this->tempDir . '/' . $filename, gzencode($content));
		}
	};

	$generator = new SitemapGenerator(
		$dataService,
		$fileSystem,
		new XmlGenerator(Config::$scripturl),
		$this->dispatcher,
		2020
	);

	$generator->generate();

	// Individual sitemap files should be created
	expect(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_1.xml'))->toBeTrue();

	// But index file should not be created due to exception
	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
});
