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
				if (! empty($params['last_id'])) {
					return [];
				}

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

	it('streams topic links correctly', function () {
		$this->sitemapDataService->getBoardLinks();

		$links = iterator_to_array($this->sitemapDataService->getTopicLinks());

		expect($links)->toBeArray()
			->and(count($links))->toBe(2)
			->and($links[0]['loc'])->toBe('https://example.com/index.php?topic=1.0')
			->and($links[1]['loc'])->toBe('https://example.com/index.php?topic=2.0');
	});

	it('processes topic batch correctly', function () {
		$processTopicBatch = new ReflectionMethod($this->sitemapDataService, 'processTopicBatch');

		$batch = $processTopicBatch->invoke($this->sitemapDataService, null, 10, false);

		$links = iterator_to_array($batch);

		expect($links)->toBeArray()
			->and(count($links))->toBe(2)
			->and($links[0]['loc'])->toBe('https://example.com/index.php?topic=1.0')
			->and($links[1]['loc'])->toBe('https://example.com/index.php?topic=2.0')
			->and($batch->getReturn())->toBe(2);
	});

	it('processes all topic pages correctly', function () {
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = true;

		$this->sitemapDataService->getBoardLinks();

		$links = iterator_to_array($this->sitemapDataService->getTopicLinks());

		expect($links)->toHaveCount(2)
			->and($links[0]['loc'])->toBe('https://example.com/index.php?topic=1.0')
			->and($links[1]['loc'])->toBe('https://example.com/index.php?topic=2.0');
	});

	it('processes multiple topic pages when num_replies is high', function () {
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = true;
		Config::$modSettings['defaultMaxMessages'] = 2;

		$this->sitemapDataService->getBoardLinks();

		// Mock data with high num_replies
		$mockDb = new class extends TestDbMapper {
			public function testQuery($query, $params = []): array
			{
				if (str_contains($query, 'SELECT t.id_topic, t.id_board, t.num_replies')) {
					if (! empty($params['last_id'])) {
						return [];
					}

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

		$links = iterator_to_array($this->sitemapDataService->getTopicLinks());

		expect($links)->toHaveCount(6); // ceil((10+1)/2) = 6 pages
	});

	it('handles multiple batches in getTopicLinks until no more topics found', function () {
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = false;

		$this->sitemapDataService->getBoardLinks();

		$callCount = 0;
		$mockDb = new class($callCount) extends TestDbMapper {
			public function __construct(private int &$callCount) {}

			public function testQuery($query, $params = []): array
			{
				if (str_contains($query, 'SELECT t.id_topic, t.id_board, t.num_replies')) {
					$this->callCount++;

					if (empty($params['last_id'])) {
						return [
							[
								'id_topic'    => '2',
								'id_board'    => '1',
								'num_replies' => '5',
								'last_date'   => time(),
								'subject'     => 'Test Topic',
								'id_attach'   => null,
								'fileext'     => null,
							],
						];
					}

					if ($params['last_id'] == 2) {
						return [
							[
								'id_topic'    => '1',
								'id_board'    => '1',
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
				return [];
			}
		};

		Db::$db = $mockDb;

		$links = iterator_to_array($this->sitemapDataService->getTopicLinks());

		expect($links)->toHaveCount(2)
			->and($callCount)->toBe(3);
	});

	it('processes topics with images correctly', function () {
		Config::$modSettings['optimus_sitemap_add_found_images'] = true;

		$this->sitemapDataService->getBoardLinks();

		$links = iterator_to_array($this->sitemapDataService->getTopicLinks());

		expect($links)->toHaveCount(2)
			->and($links[0])->toHaveKey('image')
			->and($links[0]['image']['image:loc'])->toContain('https://example.com/index.php?action=dlattach;topic=1.0;attach=1;image')
			->and($links[1])->not->toHaveKey('image');
	});

	it('skips attachments that are not images', function () {
		Config::$modSettings['optimus_sitemap_add_found_images'] = true;

		Db::$db = new class extends TestDbMapper {
			public function testQuery($query, $params = []): array
			{
				if (str_contains($query, 'SELECT b.id_board')) {
					return [
						['id_board' => '1', 'last_date' => time()],
					];
				}

				if (str_contains($query, 'SELECT t.id_topic, t.id_board, t.num_replies')) {
					if (! empty($params['last_id'])) {
						return [];
					}

					return [
						[
							'id_topic'    => '1',
							'id_board'    => '1',
							'num_replies' => '5',
							'last_date'   => time(),
							'subject'     => 'Topic with an archive attached',
							'id_attach'   => '1',
							'fileext'     => 'zip',
						],
					];
				}
				return [];
			}
		};

		$sitemapDataService = new SitemapDataService(2020);
		$sitemapDataService->getBoardLinks();
		$links = iterator_to_array($sitemapDataService->getTopicLinks());

		expect($links)->toHaveCount(1)
			->and($links[0])->not->toHaveKey('image');
	});

	it('does not add board links when optimus_sitemap_boards is false', function () {
		Config::$modSettings['optimus_sitemap_boards'] = false;

		$links = $this->sitemapDataService->getBoardLinks();

		expect($links)->toBeArray()
			->and(count($links))->toBe(0);

		$openBoards = new ReflectionProperty($this->sitemapDataService, 'openBoards');
		expect($openBoards->getValue($this->sitemapDataService))->toBe([1, 2]);
	});

	it('returns empty list when openBoards is empty for getTopicLinks', function () {
		$links = iterator_to_array($this->sitemapDataService->getTopicLinks());

		expect($links)->toBeArray()
			->and(count($links))->toBe(0);
	});

	it('handles startYear = 0 correctly', function () {
		$sitemapDataService = new SitemapDataService(0);

		$links = $sitemapDataService->getBoardLinks();

		expect($links)->toBeArray();

		$sitemapDataService->getBoardLinks();

		expect(iterator_to_array($sitemapDataService->getTopicLinks()))->toBeArray();
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

	it('includes images in topic pages when optimus_sitemap_all_topic_pages is enabled', function () {
		Config::$modSettings['optimus_sitemap_all_topic_pages'] = true;
		Config::$modSettings['optimus_sitemap_add_found_images'] = true;
		Config::$modSettings['defaultMaxMessages'] = 20;

		// Mock data with topic that has an image
		Db::$db = new class extends TestDbMapper {
			public function testQuery($query, $params = []): array
			{
				if (str_contains($query, 'SELECT b.id_board')) {
					return [
						['id_board' => '1', 'last_date' => time()],
					];
				}

				if (str_contains($query, 'SELECT t.id_topic, t.id_board, t.num_replies')) {
					if (! empty($params['last_id'])) {
						return [];
					}

					return [
						[
							'id_topic'    => '1',
							'id_board'    => '1',
							'num_replies' => '5',
							'last_date'   => time(),
							'subject'     => 'Test Topic with Image',
							'id_attach'   => '123',
							'fileext'     => 'jpg',
						],
					];
				}
				return [];
			}
		};

		$sitemapDataService = new SitemapDataService(2020);
		$sitemapDataService->getBoardLinks();
		$links = iterator_to_array($sitemapDataService->getTopicLinks());

		// Should have 1 link with image data
		expect($links)->toHaveCount(1)
			->and($links[0])->toHaveKey('image')
			->and($links[0]['image'])->toHaveKey('image:loc')
			->and($links[0]['image']['image:loc'])->toContain('action=dlattach')
			->and($links[0]['image']['image:loc'])->toContain('topic=1.0')
			->and($links[0]['image']['image:loc'])->toContain('attach=123');
	});
});
