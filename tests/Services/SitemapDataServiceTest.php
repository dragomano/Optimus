<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\DbFuncMapper;
use Bugo\Optimus\Services\SitemapDataService;
use Tests\TestDbMapper;

beforeEach(function () {
	Config::$modSettings = [
		'queryless_urls' => null,
		'recycle_board' => null,
		'optimus_sitemap_boards' => true,
		'optimus_sitemap_topics_num_replies' => 0,
		'optimus_sitemap_all_topic_pages' => false,
		'optimus_sitemap_add_found_images' => false,
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
						'id_topic' => '1',
						'id_board' => '1',
						'num_replies' => '5',
						'last_date' => time(),
						'subject' => 'Test Topic',
						'id_attach' => '1',
						'fileext' => 'jpg',
					],
					[
						'id_topic' => '2',
						'id_board' => '2',
						'num_replies' => '3',
						'last_date' => time(),
						'subject' => 'Another Topic',
						'id_attach' => null,
						'fileext' => null,
					],
				];
			}

			return [];
		}
	};

	$this->sitemapDataService = new SitemapDataService(2020);
});

afterEach(function () {
	Db::$db = new DbFuncMapper();
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

	it('processes topics with images correctly', function () {
		Config::$modSettings['optimus_sitemap_add_found_images'] = true;

		$imagesProperty = new ReflectionProperty($this->sitemapDataService, 'images');
		$processTopicBatch = new ReflectionMethod($this->sitemapDataService, 'processTopicBatch');
		$processTopicBatch->invoke($this->sitemapDataService, 0, 10);

		$images = $imagesProperty->getValue($this->sitemapDataService);

		expect($images)->toHaveCount(1)
			->and($images[1])->toContain('https://example.com/index.php?action=dlattach;topic=1.0;attach=1;image');
	});
});
