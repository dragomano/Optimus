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
		'optimus_sitemap_enable'             => true,
		'optimus_sitemap_items_display'      => 1000,
		'optimus_sitemap_add_found_images'   => false,
		'optimus_sitemap_topics_num_replies' => 0,
		'optimus_sitemap_all_topic_pages'    => false,
		'optimus_remove_previous_xml_files'  => true,
		'queryless_urls'                     => false,
		'defaultMaxMessages'                 => 20,
	];

	$this->dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [['loc' => 'https://example.com/board1', 'lastmod' => time()]];
		}

		public function getTopicLinks(): array {
			return [['loc' => 'https://example.com/topic1', 'lastmod' => time()]];
		}
	};

	$fileSystem       = new FileSystem($this->tempDir);
	$xmlGenerator     = new XmlGenerator(Config::$scripturl);
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

it('removes files left over from previous runs', function () {
	$staleFiles = ['sitemap_7.xml', 'sitemap_8.xml.gz'];

	foreach ($staleFiles as $file) {
		file_put_contents($this->tempDir . '/' . $file, 'old content');
		expect(file_exists($this->tempDir . '/' . $file))->toBeTrue();
	}

	expect($this->generator->generate())->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue();

	foreach ($staleFiles as $file) {
		expect(file_exists($this->tempDir . '/' . $file))->toBeFalse();
	}
});

it('keeps files from previous runs when removal is disabled', function () {
	Config::$modSettings['optimus_remove_previous_xml_files'] = false;

	$staleFiles = ['sitemap_7.xml', 'sitemap_8.xml.gz'];

	foreach ($staleFiles as $file) {
		file_put_contents($this->tempDir . '/' . $file, 'old content');
		expect(file_exists($this->tempDir . '/' . $file))->toBeTrue();
	}

	expect($this->generator->generate())->toBeTrue();

	foreach ($staleFiles as $file) {
		expect(file_exists($this->tempDir . '/' . $file))->toBeTrue();
	}
});

it('processes single sitemap correctly', function () {
	$items = [
		['loc' => 'https://example.com/page1', 'lastmod' => time()],
		['loc' => 'https://example.com/page2', 'lastmod' => time()],
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
		'loc'     => 'https://example.com/test',
		'lastmod' => time(),
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
		'loc'     => 'https://example.com/test',
		'lastmod' => time(),
		'image'   => ['url' => 'https://example.com/image.jpg'],
	];

	$method = new ReflectionMethod($this->generator, 'prepareEntry');
	$result = $method->invoke($this->generator, $entry);

	expect($result)
		->toHaveKey('image:image')
		->and($result['image:image'])->toBe(['url' => 'https://example.com/image.jpg']);
});

it('prepares entry with video data', function () {
	$entry = [
		'loc'     => 'https://example.com/test',
		'lastmod' => time(),
		'video'   => ['title' => 'Test Video'],
	];

	$method = new ReflectionMethod($this->generator, 'prepareEntry');
	$result = $method->invoke($this->generator, $entry);

	expect($result)
		->toHaveKey('video:video')
		->and($result['video:video'])->toBe(['title' => 'Test Video']);
});

it('prepares entry with both image and video data', function () {
	$entry = [
		'loc'     => 'https://example.com/test',
		'lastmod' => time(),
		'image'   => ['url' => 'https://example.com/image.jpg'],
		'video'   => ['title' => 'Test Video'],
	];

	$method = new ReflectionMethod($this->generator, 'prepareEntry');
	$result = $method->invoke($this->generator, $entry);

	expect($result)
		->toHaveKey('image:image')
		->and($result)->toHaveKey('video:video');
});

it('creates sitemap successfully', function () {
	expect($this->generator->generate())->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue();
});

it('falls back to the default chunk size when the setting is empty', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 0;

	expect($this->generator->generate())->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeFalse();
});

it('creates multiple sitemap files when needed', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 2;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()],
				['loc' => 'https://example.com/board3', 'lastmod' => time()],
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
				'loc'     => 'https://example.com/custom',
				'lastmod' => time(),
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
	$links   = [
		['loc' => 'https://example.com/page1', 'lastmod' => strtotime('-3 days', $maxDate)],
		['loc' => 'https://example.com/page2', 'lastmod' => $maxDate],
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
				['loc' => 'https://example.com/board2', 'lastmod' => time()],
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

	expect($generator->generate())->toBeFalse()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
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
				['loc' => 'https://example.com/board2', 'lastmod' => time()],
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

	expect($generator->generate())->toBeFalse()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
});

it('creates a gzipped copy when a sitemap is big enough', function () {
	$largeContent = str_repeat('x', SitemapGenerator::GZIP_THRESHOLD);

	$xmlGenerator = $this->createMock(XmlGenerator::class);
	$xmlGenerator->method('generate')->willReturn($largeContent);

	$generator = new SitemapGenerator(
		$this->dataService,
		new FileSystem($this->tempDir),
		$xmlGenerator,
		$this->dispatcher,
		2020
	);

	expect($generator->generate())->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml.gz'))->toBeTrue();
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

it('does not gzip small sitemaps', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
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

	expect($generator->generate())->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml.gz'))->toBeFalse()
		->and(file_exists($this->tempDir . '/sitemap_0.xml.gz'))->toBeFalse();
});

it('creates a file for every chunk', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()],
				['loc' => 'https://example.com/board3', 'lastmod' => time()],
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

	expect($generator->generate())->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_1.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_2.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_3.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap.xml'))->toBeTrue();
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

	// Call createXml directly - should return early without creating files
	$method = new ReflectionMethod($generator, 'createXml');

	expect($method->invoke($generator))->toBeFalse();

	// No sitemap files should be created because items array is empty
	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse()
		->and(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeFalse();
});

it('puts the last modification date of every chunk into the index', function () {
	$firstDate  = strtotime('2024-01-15 10:00:00');
	$secondDate = strtotime('2025-06-20 10:00:00');

	$items = [
		[['loc' => 'https://example.com/board1']],
		[['loc' => 'https://example.com/board2']],
	];

	$method = new ReflectionMethod($this->generator, 'processMultipleSitemaps');

	expect($method->invoke($this->generator, $items, [$firstDate, $secondDate]))->toBeTrue();

	$index = file_get_contents($this->tempDir . '/sitemap.xml');

	expect(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_1.xml'))->toBeTrue()
		->and($index)->toContain('<loc>https://example.com/sitemap_0.xml</loc>')
		->and($index)->toContain('<lastmod>' . date('Y-m-d', $firstDate) . 'T')
		->and($index)->toContain('<loc>https://example.com/sitemap_1.xml</loc>')
		->and($index)->toContain('<lastmod>' . date('Y-m-d', $secondDate) . 'T');
});

it('falls back to the current date for a chunk without dates', function () {
	$items = [
		[['loc' => 'https://example.com/board1']],
		[['loc' => 'https://example.com/board2']],
	];

	$method = new ReflectionMethod($this->generator, 'processMultipleSitemaps');

	expect($method->invoke($this->generator, $items, []))->toBeTrue()
		->and(file_get_contents($this->tempDir . '/sitemap.xml'))
		->toContain('<lastmod>' . date('Y-m-d') . 'T');
});

it('handles XmlGeneratorException when creating sitemap index', function () {
	Config::$modSettings['optimus_sitemap_items_display'] = 1;

	$dataService = new class(2020) extends SitemapDataService {
		public function getBoardLinks(): array {
			return [
				['loc' => 'https://example.com/board1', 'lastmod' => time()],
				['loc' => 'https://example.com/board2', 'lastmod' => time()],
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

	expect($generator->generate())->toBeFalse();

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
				['loc' => 'https://example.com/board2', 'lastmod' => time()],
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

	expect($generator->generate())->toBeFalse();

	// Individual sitemap files should be created
	expect(file_exists($this->tempDir . '/sitemap_0.xml'))->toBeTrue()
		->and(file_exists($this->tempDir . '/sitemap_1.xml'))->toBeTrue();

	// But index file should not be created due to exception
	expect(file_exists($this->tempDir . '/sitemap.xml'))->toBeFalse();
});
