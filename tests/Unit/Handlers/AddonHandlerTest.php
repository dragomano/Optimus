<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Optimus\Addons\IndexNow\IndexNow;
use Bugo\Optimus\Addons\PrettyUrls;
use Bugo\Optimus\Handlers\AddonHandler;
use League\Event\ListenerRegistry;
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

	Config::$modSettings = [];
});

afterEach(function () {
	Db::$db = new FuncMapper();
	Mockery::close();
});

test('handler subscribes only once', function () {
	$handler = new AddonHandler();
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	$property->setValue(null, false);

	$handler->__invoke();

	expect($property->getValue())->toBeTrue();
});

test('handler does not subscribe when already subscribed', function () {
	$handler = new AddonHandler();
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	$property->setValue(null, true);

	$handler->__invoke();

	expect($property->getValue())->toBeTrue();
});

test('getInstalledMods fetches from database when cache is null', function () {
	global $cacheGetDataReturn;

	$cacheGetDataReturn['optimus_installed_mods'] = null;

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'getInstalledMods');

	$result = $method->invoke($handler);

	expect($result)->toBeArray();

	unset($cacheGetDataReturn['optimus_installed_mods']);
});

test('mapNamespace returns empty string for Interface file', function () {
	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'mapNamespace');

	$result = $method->invoke($handler, OP_ADDONS . '/SomeInterface.php');

	expect($result)->toBe('');
});

test('mapNamespace returns empty string for AbstractAddon file', function () {
	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'mapNamespace');

	$result = $method->invoke($handler, OP_ADDONS . '/SomeAbstractAddon.php');

	expect($result)->toBe('');
});

test('mapNamespace returns empty string for index file', function () {
	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'mapNamespace');

	$result = $method->invoke($handler, OP_ADDONS . '/index.php');

	expect($result)->toBe('');
});

test('mapNamespace returns correct namespace for valid addon file', function () {
	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'mapNamespace');

	$result = $method->invoke($handler, OP_ADDONS . '/TestAddon.php');

	expect($result)->toBe('\Bugo\Optimus\Addons\TestAddon');
});

test('mapNamespace returns correct namespace for addon in subdirectory', function () {
	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'mapNamespace');

	$result = $method->invoke($handler, OP_ADDONS . '/subdir/TestAddon.php');

	expect($result)->toBe('\Bugo\Optimus\Addons\subdir\TestAddon');
});

test('subscribeListeners subscribes built-in addons and skips external ones', function () {
	$registry = Mockery::mock(ListenerRegistry::class);

	// The PrettyUrls modification is not installed, so its addon must not be subscribed
	$registry->shouldReceive('subscribeTo')
		->with(Mockery::any(), Mockery::type(PrettyUrls::class), Mockery::any())
		->never();

	$registry->shouldReceive('subscribeTo')
		->with(Mockery::any(), Mockery::any(), Mockery::any())
		->atLeast()->once()
		->andReturn();

	$handler = new AddonHandler();

	$handler->subscribeListeners($registry);

	$property = new ReflectionProperty($handler, 'hasSubscribed');

	expect($property->getValue())->toBeTrue();
});

test('subscribeListeners skips disabled addons', function () {
	Config::$modSettings['optimus_disabled_addons'] = 'Optimus:IndexNow,Optimus:ExtraSettings';

	$registry = Mockery::mock(ListenerRegistry::class);
	$registry->shouldReceive('subscribeTo')
		->with(Mockery::any(), Mockery::type(IndexNow::class), Mockery::any())
		->never();
	$registry->shouldReceive('subscribeTo')
		->with(Mockery::any(), Mockery::any(), Mockery::any())
		->atLeast()->once()
		->andReturn();

	(new AddonHandler())->subscribeListeners($registry);
});

test('getInstalledMods handles database error', function () {
	// Mock Db to throw exception
	Db::$db = new class {
		public function query() {
			throw new Exception('Database error');
		}
		public function fetch_assoc() {}
		public function free_result() {}
	};

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'getInstalledMods');

	// Should handle exception gracefully and return empty array
	$result = $method->invoke($handler);
	expect($result)->toBe([]);
});

test('getDisabledAddons parses the mod setting', function () {
	Config::$modSettings['optimus_disabled_addons'] = 'Optimus:IndexNow, Optimus:ExtraSettings ,';

	expect((new AddonHandler())->getDisabledAddons())->toBe(['Optimus:IndexNow', 'Optimus:ExtraSettings']);
});

test('getDisabledAddons returns empty array by default', function () {
	expect((new AddonHandler())->getDisabledAddons())->toBe([]);
});

test('toggle disables and enables the addon', function () {
	expect((new AddonHandler())->toggle('Optimus:IndexNow'))->toBeTrue();
})->skip(! class_exists(IndexNow::class), 'The premium IndexNow addon sources are not part of this repository');

test('toggle rejects an unknown package id', function () {
	expect((new AddonHandler())->toggle('Unknown:NonexistentAddon'))->toBeFalse();
});

test('toggle enables the previously disabled addon', function () {
	// The disabled list is not persisted by the updateSettings stub,
	// so pre-seed it to hit the re-enabling branch
	Config::$modSettings['optimus_disabled_addons'] = 'Optimus:IndexNow';

	expect((new AddonHandler())->toggle('Optimus:IndexNow'))->toBeTrue();
})->skip(! class_exists(IndexNow::class), 'The premium IndexNow addon sources are not part of this repository');

test('getAddonData returns metadata for all detected addons', function () {
	$data = (new AddonHandler())->getAddonData();

	$prettyUrls = [];

	foreach ($data as $item) {
		if ($item['package_id'] === 'el:prettyurls') {
			$prettyUrls = $item;
		}
	}

	expect($data)->not->toBeEmpty()
		->and($prettyUrls['is_builtin'])->toBeFalse()
		->and($prettyUrls['is_active'])->toBeFalse();
});

test('getAddonData marks the IndexNow addon as built-in', function () {
	$row = [];

	foreach ((new AddonHandler())->getAddonData() as $item) {
		if ($item['package_id'] === 'Optimus:IndexNow') {
			$row = $item;
		}
	}

	expect($row['name'])->toBe('IndexNow')
		->and($row['is_builtin'])->toBeTrue()
		->and($row['is_active'])->toBeTrue()
		->and($row['is_disabled'])->toBeFalse()
		->and($row['has_settings'])->toBeTrue()
		->and($row['is_downloadable'])->toBeFalse()
		->and($row['description'])->toBeString();
})->skip(! class_exists(IndexNow::class), 'The premium IndexNow addon sources are not part of this repository');

test('getAddonData shows the IndexNow addon as downloadable without its sources', function () {
	$row = [];

	foreach ((new AddonHandler())->getAddonData() as $item) {
		if ($item['package_id'] === 'Optimus:IndexNow') {
			$row = $item;
		}
	}

	expect($row['name'])->toBe('IndexNow')
		->and($row['is_builtin'])->toBeFalse()
		->and($row['is_active'])->toBeFalse()
		->and($row['has_settings'])->toBeFalse()
		->and($row['is_downloadable'])->toBeTrue();
})->skip(class_exists(IndexNow::class), 'The premium IndexNow addon sources are present in this environment');

test('getAddonData sorts active addons first', function () {
	$data = (new AddonHandler())->getAddonData();

	$firstInactiveIndex = null;
	$prettyUrlsIndex = null;

	foreach ($data as $i => $row) {
		if ($firstInactiveIndex === null && ! $row['is_active']) {
			$firstInactiveIndex = $i;
		}

		if ($row['package_id'] === 'el:prettyurls') {
			$prettyUrlsIndex = $i;
		}
	}

	expect($prettyUrlsIndex)->toBeGreaterThanOrEqual($firstInactiveIndex);
});

test('addDownloadable appends registry entries absent on the forum', function () {
	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'addDownloadable');

	$result = $method->invoke($handler, [
		['name' => 'IndexNow', 'package_id' => 'Optimus:IndexNow', 'is_downloadable' => false],
	]);

	$extra = array_values(array_filter($result, fn($row) => $row['is_downloadable']));
	$safeLinks = array_values(array_filter($extra, fn($row) => $row['name'] === 'SafeLinks'));

	expect($result)->toHaveCount(5)
		->and($extra)->toHaveCount(4)
		->and($safeLinks[0]['package_id'])->toBe('Optimus:SafeLinks')
		->and($safeLinks[0]['url'])->toContain('github.com')
		->and($safeLinks[0]['is_active'])->toBeFalse()
		->and($safeLinks[0]['has_settings'])->toBeFalse();
});

test('addDownloadable skips addons present on the forum', function () {
	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'addDownloadable');

	$result = $method->invoke($handler, [
		['name' => 'IndexNow', 'package_id' => 'Optimus:IndexNow', 'is_downloadable' => false],
		['name' => 'SafeLinks', 'package_id' => 'Optimus:SafeLinks', 'is_downloadable' => false],
		['name' => 'ExtraSettings', 'package_id' => 'Optimus:ExtraSettings', 'is_downloadable' => false],
		['name' => 'StructuredData', 'package_id' => 'Optimus:StructuredData', 'is_downloadable' => false],
		['name' => 'ExampleAddon', 'package_id' => 'Optimus:ExampleAddon', 'is_downloadable' => false],
	]);

	expect($result)->toHaveCount(5)
		->and(array_filter($result, fn($row) => $row['is_downloadable']))->toBeEmpty();
});
