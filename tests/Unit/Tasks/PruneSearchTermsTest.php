<?php declare(strict_types=1);

use Bugo\Compat\{Config, Db};
use Bugo\Optimus\Tasks\PruneSearchTerms;
use Tests\TestDbMapper;

beforeEach(function () {
	Config::$modSettings = [
		'optimus_log_search'         => true,
		'optimus_search_terms_limit' => 10,
	];

	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			return [];
		}
	};

	$this->task = new PruneSearchTerms(['id' => 1]);
});

it('returns false when search terms logging is disabled', function () {
	Config::$modSettings['optimus_log_search'] = false;

	expect($this->task->execute())->toBeFalse();
});

it('deletes one-time phrases', function () {
	$queries = [];

	Db::$db = new class($queries) extends TestDbMapper {
		public array $queries;

		public function __construct(&$queries) {
			$this->queries = &$queries;
		}

		public function testQuery($query, $params = []): array
		{
			$this->queries[] = $query;

			return [];
		}

		public function insert(
			string $method = '',
			string $table = '',
			array $columns = [],
			array $data = [],
			array $keys = [],
			int $returnmode = 0
		): int {
			return 1;
		}
	};

	$this->task->execute();

	$deleteQueries = array_values(array_filter(
		$queries,
		fn($query) => str_contains($query, 'DELETE FROM {db_prefix}optimus_search_terms')
			&& str_contains($query, 'hit <= 1')
	));

	expect($deleteQueries)->toHaveCount(1);
});

it('deletes overflow when limit is set', function () {
	$queries = [];

	Db::$db = new class($queries) extends TestDbMapper {
		public array $queries;

		public function __construct(&$queries) {
			$this->queries = &$queries;
		}

		public function testQuery($query, $params = []): array
		{
			$this->queries[] = $query;

			return [];
		}

		public function insert(
			string $method = '',
			string $table = '',
			array $columns = [],
			array $data = [],
			array $keys = [],
			int $returnmode = 0
		): int {
			return 1;
		}
	};

	$this->task->execute();

	$overflowQueries = array_values(array_filter(
		$queries,
		fn($query) => str_contains($query, 'id_term NOT IN')
			&& str_contains($query, 'ORDER BY hit DESC')
	));

	expect($overflowQueries)->toHaveCount(1);
});

it('skips overflow deletion when limit is not set', function () {
	Config::$modSettings['optimus_search_terms_limit'] = 0;

	$queries = [];

	Db::$db = new class($queries) extends TestDbMapper {
		public array $queries;

		public function __construct(&$queries) {
			$this->queries = &$queries;
		}

		public function testQuery($query, $params = []): array
		{
			$this->queries[] = $query;

			return [];
		}

		public function insert(
			string $method = '',
			string $table = '',
			array $columns = [],
			array $data = [],
			array $keys = [],
			int $returnmode = 0
		): int {
			return 1;
		}
	};

	$this->task->execute();

	$overflowQueries = array_values(array_filter(
		$queries,
		fn($query) => str_contains($query, 'id_term NOT IN')
	));

	expect($overflowQueries)->toBeEmpty();
});

it('reschedules itself with weekly interval', function () {
	$insertData = [];

	Db::$db = new class($insertData) extends TestDbMapper {
		public array $insertData;
		public int $deletedTasks = 0;

		public function __construct(&$insertData) {
			$this->insertData = &$insertData;
		}

		public function testQuery($query, $params = []): array
		{
			if (str_contains($query, 'DELETE FROM {db_prefix}background_tasks')) {
				$this->deletedTasks++;
			}

			return [];
		}

		public function insert(
			string $method = '',
			string $table = '',
			array $columns = [],
			array $data = [],
			array $keys = [],
			int $returnmode = 0
		): int {
			$this->insertData = [
				'table' => $table,
				'data' => $data,
			];

			return 1;
		}
	};

	$this->task->execute();

	$data = Db::$db->insertData['data'];

	expect(Db::$db->deletedTasks)->toBe(1)
		->and(Db::$db->insertData['table'])->toBe('{db_prefix}background_tasks')
		->and($data[1])->toContain('PruneSearchTerms')
		->and($data[3])->toBeGreaterThanOrEqual(time() + 7 * 24 * 60 * 60);
});

it('returns true on success', function () {
	expect($this->task->execute())->toBeTrue();
});
