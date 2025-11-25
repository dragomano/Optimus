<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Optimus\Services\SitemapDataService;
use Tests\TestDbMapper;

beforeEach(function () {
	Config::$modSettings = [
		'queryless_urls'                     => null,
		'recycle_board'                      => null,
		'optimus_sitemap_boards'             => true,
		'optimus_sitemap_topics_num_replies' => 0,
		'optimus_sitemap_all_topic_pages'    => false,
		'optimus_sitemap_add_found_images'   => false,
	];

	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			if (str_contains($query, 'SELECT b.id_board')) {
				$data = [
					['id_board' => '1', 'last_date' => time()],
					['id_board' => '2', 'last_date' => time()],
				];

				return empty($params['ignored_boards'])
					? $data
					: array_filter($data, fn($board) => ! in_array($board['id_board'], $params['ignored_boards']));
			}

			if (str_contains($query, 'SELECT t.id_topic, t.id_board, t.num_replies')) {
				return [
					[
						'id_topic'    => '1',
						'id_board'    => '1',
						'num_replies' => '5',
						'last_date'   => time(),
						'subject'     => 'Test Topic',
						'id_attach'   => '1',
						'fileext'     => 'jpg',
					],
					[
						'id_topic'    => '2',
						'id_board'    => '2',
						'num_replies' => '3',
						'last_date'   => time(),
						'subject'     => 'Another Topic',
						'id_attach'   => null,
						'fileext'     => null,
					],
				];
			}

			return [];
		}
	};

	$this->sitemapDataService = new SitemapDataService(2020);
});

afterEach(function () {
	Db::$db = new FuncMapper();
});

describe('SitemapDataService', function () {
	it('gets board links correctly', function () {
		$links = $this->sitemapDataService->getBoardLinks();

		expect($links)->toBeArray()
			->and(count($links))->toBe(2)
			->and($links[0]['loc'])->toBe('https://example.com/index.php?board=1.0')
			->and($links[1]['loc'])->toBe('https://example.com/index.php?board=2.0');

		$openBoards = new ReflectionProperty($this->sitemapDataService, 'openBoards');

		expect($openBoards->getValue($this->sitemapDataService))->toBe([1, 2]);
	});

	it('ignores recycle board if set', function () {
		Config::$modSettings['recycle_board'] = 1;

		$links = $this->sitemapDataService->getBoardLinks();

		expect($links)->toBeArray()
			->and(count($links))->toBe(1)
			->and($links[0]['loc'])->toBe('https://example.com/index.php?board=2.0');

		$openBoards = new ReflectionProperty($this->sitemapDataService, 'openBoards');

		expect($openBoards->getValue($this->sitemapDataService))->toBe([2]);
	});

	it('gets topic links correctly', function () {
		$this->sitemapDataService->getBoardLinks();
		$links = $this->sitemapDataService->getTopicLinks();

		expect($links)->toBeArray();
	});

	it('processes topic batch correctly', function () {
		$linksProperty = new ReflectionProperty($this->sitemapDataService, 'links');
		$processTopicBatch = new ReflectionMethod($this->sitemapDataService, 'processTopicBatch');
		$processTopicBatch->invoke($this->sitemapDataService, 0, 10);

		$links = $linksProperty->getValue($this->sitemapDataService);

		expect($links)->toBeArray()
			->and(count($links))->toBe(2)
			->and($links[1]['loc'])->toBe('https://example.com/index.php?topic=1.0')
			->and($links[2]['loc'])->toBe('https://example.com/index.php?topic=2.0');
	});

	it('processes all topic pages correctly', function () {
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = true;

		$topicsProperty = new ReflectionProperty($this->sitemapDataService, 'topics');
		$processTopicBatch = new ReflectionMethod($this->sitemapDataService, 'processTopicBatch');
		$processTopicBatch->invoke($this->sitemapDataService, 0, 10);

		$topics = $topicsProperty->getValue($this->sitemapDataService);

		expect($topics)->toHaveCount(2)
			->and($topics[1])->toHaveKeys(['url', 'last_date', 'num_replies', 'subject'])
			->and($topics[1]['subject'])->toBe('Test Topic')
			->and($topics[2]['subject'])->toBe('Another Topic');

		$this->sitemapDataService->getBoardLinks();
		$links = $this->sitemapDataService->getTopicLinks();

		expect($links)->toHaveCount(2)
			->and($links[0]['loc'])->toBe($topics[1]['url'])
			->and($links[1]['loc'])->toBe($topics[2]['url']);
	});

	it('processes multiple topic pages when num_replies is high', function () {
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = true;
		Config::$modSettings['defaultMaxMessages'] = 2;
		Config::$modSettings['totalMessages'] = 100; // Ensure totalRows > 0

		$this->sitemapDataService->getBoardLinks();

		// Mock data with high num_replies
		$mockDb = new class extends TestDbMapper {
			public function testQuery($query, $params = []): array
			{
				if (str_contains($query, 'SELECT t.id_topic, t.id_board, t.num_replies')) {
					return [
						[
							'id_topic'    => '1',
							'id_board'    => '1',
							'num_replies' => '10', // High replies to create multiple pages
							'last_date'   => time(),
							'subject'     => 'Test Topic',
							'id_attach'   => null,
							'fileext'     => null,
						],
					];
				}
				return [];
			}
		};

		Db::$db = $mockDb;

		$links = $this->sitemapDataService->getTopicLinks();

		expect($links)->toHaveCount(6); // ceil((10+1)/2) = 6 pages
	});

	it('handles multiple batches in getTopicLinks when totalRows > limit', function () {
		Config::$modSettings['totalTopics'] = 1500; // > 100 limit
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = false;

		$this->sitemapDataService->getBoardLinks();

		// Mock to return data only for first batch
		$callCount = 0;
		$mockDb = new class($callCount) extends TestDbMapper {
			public function __construct(private int &$callCount) {}

			public function testQuery($query, $params = []): array
			{
				if (str_contains($query, 'SELECT t.id_topic, t.id_board, t.num_replies')) {
					$this->callCount++;
					if ($params['start'] == 0) {
						return [
							[
								'id_topic'    => '1',
								'id_board'    => '1',
								'num_replies' => '5',
								'last_date'   => time(),
								'subject'     => 'Test Topic',
								'id_attach'   => null,
								'fileext'     => null,
							],
						];
					}
					return []; // No more data for second batch
				}
				return [];
			}
		};

		Db::$db = $mockDb;

		$links = $this->sitemapDataService->getTopicLinks();

		expect($links)->toHaveCount(1)
			->and($callCount)->toBe(intval(Config::$modSettings['totalTopics'] / 100));
	});

	it('processes topics with images correctly', function () {
		Config::$modSettings['optimus_sitemap_add_found_images'] = true;

		$imagesProperty = new ReflectionProperty($this->sitemapDataService, 'images');
		$processTopicBatch = new ReflectionMethod($this->sitemapDataService, 'processTopicBatch');
		$processTopicBatch->invoke($this->sitemapDataService, 0, 10);

		$images = $imagesProperty->getValue($this->sitemapDataService);

		expect($images)->toHaveCount(1)
			->and($images[1])->toContain('https://example.com/index.php?action=dlattach;topic=1.0;attach=1;image');
	});

	it('does not add board links when optimus_sitemap_boards is false', function () {
		Config::$modSettings['optimus_sitemap_boards'] = false;

		$links = $this->sitemapDataService->getBoardLinks();

		expect($links)->toBeArray()
			->and(count($links))->toBe(0);

		$openBoards = new ReflectionProperty($this->sitemapDataService, 'openBoards');
		expect($openBoards->getValue($this->sitemapDataService))->toBe([1, 2]);
	});

	it('returns empty array when openBoards is empty for getTopicLinks', function () {
		$links = $this->sitemapDataService->getTopicLinks();

		expect($links)->toBeArray()
			->and(count($links))->toBe(0);
	});

	it('does nothing in processTopicPages when topics is empty', function () {
		$processTopicPages = new ReflectionMethod($this->sitemapDataService, 'processTopicPages');
		$processTopicPages->invoke($this->sitemapDataService);

		$linksProperty = new ReflectionProperty($this->sitemapDataService, 'links');
		$links = $linksProperty->getValue($this->sitemapDataService);

		expect($links)->toBeArray()
			->and(count($links))->toBe(0);
	});

	it('handles startYear = 0 correctly', function () {
		$sitemapDataService = new SitemapDataService(0);

		$links = $sitemapDataService->getBoardLinks();

		expect($links)->toBeArray();
	});

	it('calculates total rows correctly when optimus_sitemap_all_topic_pages is true', function () {
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = true;
		Config::$modSettings['totalTopics'] = 100;
		Config::$modSettings['totalMessages'] = 500;

		$getTotalRows = new ReflectionMethod($this->sitemapDataService, 'getTotalRows');
		$totalRows = $getTotalRows->invoke($this->sitemapDataService);

		expect($totalRows)->toBe(500);
	});

	it('calculates total rows correctly when optimus_sitemap_all_topic_pages is false', function () {
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = false;
		Config::$modSettings['totalTopics'] = 100;
		Config::$modSettings['totalMessages'] = 500;

		$getTotalRows = new ReflectionMethod($this->sitemapDataService, 'getTotalRows');
		$totalRows = $getTotalRows->invoke($this->sitemapDataService);

		expect($totalRows)->toBe(100);
	});

	it('builds topic url correctly', function () {
		$buildTopicUrl = new ReflectionMethod($this->sitemapDataService, 'buildTopicUrl');
		$url = $buildTopicUrl->invoke($this->sitemapDataService, '1');

		expect($url)->toBe('https://example.com/index.php?topic=1.0');
	});

	it('recognizes image files correctly', function () {
		$isImageFile = new ReflectionMethod($this->sitemapDataService, 'isImageFile');

		expect($isImageFile->invoke($this->sitemapDataService, 'jpg'))->toBeTrue()
			->and($isImageFile->invoke($this->sitemapDataService, 'png'))->toBeTrue()
			->and($isImageFile->invoke($this->sitemapDataService, 'txt'))->toBeFalse();
	});

	it('builds topic page url correctly', function () {
		$buildTopicPageUrl = new ReflectionMethod($this->sitemapDataService, 'buildTopicPageUrl');
		$url = $buildTopicPageUrl->invoke($this->sitemapDataService, 1, 0, 20);

		expect($url)->toBe('https://example.com/index.php?topic=1.0');

		$url = $buildTopicPageUrl->invoke($this->sitemapDataService, 1, 1, 20);

		expect($url)->toBe('https://example.com/index.php?topic=1.20');
	});
});
