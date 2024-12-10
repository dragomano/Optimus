<?php declare(strict_types=1);

use Bugo\Compat\CacheApi;
use Bugo\Compat\Db;
use Bugo\Compat\DbFuncMapper;
use Bugo\Optimus\Handlers\AddonHandler;
use Tests\TestDbMapper;

beforeEach(function () {
	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			if (str_contains($query, 'SELECT package_id')) {
				return [
					['package_id' => 'Optimus:ExampleAddon'],
				];
			}

			return [];
		}
	};
});

afterEach(function () {
	Db::$db = new DbFuncMapper();
});

test('handler subscribes only once', function () {
    $handler = new AddonHandler();
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	$property->setValue(false);

	$handler->__invoke();

	expect($property->getValue($handler))->toBeTrue();
});
