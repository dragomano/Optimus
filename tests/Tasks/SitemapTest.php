<?php declare(strict_types=1);

use Bugo\Compat\{Config, Db};
use Bugo\Optimus\Tasks\Sitemap;

abstract class SMF_BackgroundTask
{
	protected array $_details;

	public function __construct(array $details)
	{
		$this->_details = $details;
	}
}

beforeEach(function () {
	$this->tempDir = sys_get_temp_dir() . '/optimus_test_' . uniqid();
	mkdir($this->tempDir, 0777, true);

	Config::$boarddir = $this->tempDir;
	Config::$boardurl = 'https://example.com';
	Config::$scripturl = 'https://example.com/index.php';

	Config::$modSettings = [
		'optimus_sitemap_enable' => true,
		'optimus_start_year' => 2020,
		'optimus_update_frequency' => 1,
	];

	Db::$db = new class {
		public function query(): object|bool { return true; }
		public function fetch_assoc(): array|false|null { return false; }
		public function free_result(): bool { return false; }
		public function insert(): int|array|null { return null; }
	};

	$this->sitemap = new Sitemap(['id' => 1]);
});

afterEach(function () {
	if (is_dir($this->tempDir)) {
		array_map('unlink', glob($this->tempDir . '/*'));
		rmdir($this->tempDir);
	}
});

it('returns false when sitemap is disabled', function () {
	Config::$modSettings['optimus_sitemap_enable'] = false;

	expect($this->sitemap->execute())->toBeFalse();
});

it('schedules next run after successful generation', function () {
	$insertCalled = false;

	Db::$db = new class($insertCalled) {
		private bool $insertCalled;

		public function __construct(&$insertCalled) {
			$this->insertCalled = &$insertCalled;
		}

		public function query(): object|bool { return true; }
		public function fetch_assoc(): array|false|null { return false; }
		public function free_result(): bool { return false; }

		public function insert(
			string $method = '',
			string $table = '',
			array $columns = [],
			array $data = [],
			array $keys = []
		): int|array|null {
			$this->insertCalled = true;

			expect($table)->toBe('{db_prefix}background_tasks')
				->and($data[0])->toContain('Sitemap.php')
				->and($data[1])->toContain('Sitemap')
				->and($data[3])->toBeGreaterThan(time());

			return 1;
		}
	};

	$this->sitemap->execute();

	expect($insertCalled)->toBeTrue();
});

it('uses correct update interval based on settings', function () {
	$intervals = [
		1 => 3 * 24 * 60 * 60,  // 3 days
		2 => 7 * 24 * 60 * 60,  // 7 days
		3 => 14 * 24 * 60 * 60, // 14 days
		4 => 30 * 24 * 60 * 60  // 30 days
	];

	foreach ($intervals as $setting => $expectedInterval) {
		Config::$modSettings['optimus_update_frequency'] = $setting;

		$nextRunTime = 0;

		Db::$db = new class($nextRunTime) {
			private int $nextRunTime;

			public function __construct(&$nextRunTime) {
				$this->nextRunTime = &$nextRunTime;
			}

			public function query(): object|bool { return true; }
			public function fetch_assoc(): array|false|null { return false; }
			public function free_result(): bool { return false; }

			public function insert(
				string $method = '',
				string $table = '',
				array $columns = [],
				array $data = [],
				array $keys = []
			): int|array|null {
				$this->nextRunTime = $data[3] - time();

				return 1;
			}
		};

		$this->sitemap->execute();

		expect($nextRunTime)->toBeGreaterThanOrEqual($expectedInterval - 1)
			->toBeLessThanOrEqual($expectedInterval + 1);
	}
});
