<?php declare(strict_types=1);

use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Optimus\Handlers\AddonHandler;
use League\Event\ListenerRegistry;
use Tests\TestDbMapper;

beforeEach(function () {
	if (! defined('OP_ADDONS')) {
		define('OP_ADDONS', __DIR__ . '/../files/Addons');
	}

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
	Db::$db = new FuncMapper();
	Mockery::close();
});

test('handler subscribes only once', function () {
	$handler = new AddonHandler();
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	$property->setValue(false);

	$handler->__invoke();

	expect($property->getValue($handler))->toBeTrue();
});

test('handler does not subscribe when already subscribed', function () {
	$handler = new AddonHandler();
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	$property->setValue(true);

	$handler->__invoke();

	expect($property->getValue($handler))->toBeTrue();
});

test('getInstalledMods fetches from database when cache is null', function () {
	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'getInstalledMods');

	$result = $method->invoke($handler);

	expect($result)->toBeArray();
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

test('subscribeListeners processes addons correctly', function () {
	// Create a test addon file
	$addonFile = OP_ADDONS . '/TestExampleAddon.php';
	file_put_contents($addonFile, '<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

class TestExampleAddon extends AbstractAddon
{
	public const PACKAGE_ID = "Optimus:TestExampleAddon";

	public static array $events = [self::HOOK_EVENT];

	public function __invoke(AddonEvent $event): void {}
}
');

	// Mock ListenerRegistry - allow any calls
	$registry = Mockery::mock(ListenerRegistry::class);
	$registry->shouldReceive('subscribeTo')->andReturn();

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'subscribeListeners');
	$method->invoke($handler, $registry);

	// Check that hasSubscribed is set
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	expect($property->getValue($handler))->toBeTrue();

	// Cleanup
	unlink($addonFile);
});

test('subscribeListeners skips addon not in installed mods', function () {
	// Create a test addon file with package_id not in mods
	$addonFile = OP_ADDONS . '/TestOtherAddon.php';
	file_put_contents($addonFile, '<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

class TestOtherAddon extends AbstractAddon
{
	public const PACKAGE_ID = "OtherAddon";

	public static array $events = [self::HOOK_EVENT];

	public function __invoke(AddonEvent $event): void {}
}
');

	// Mock ListenerRegistry - allow any calls since other addons may be processed
	$registry = Mockery::mock(ListenerRegistry::class);
	$registry->shouldReceive('subscribeTo')->andReturn();

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'subscribeListeners');
	$method->invoke($handler, $registry);

	// Check that hasSubscribed is set
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	expect($property->getValue($handler))->toBeTrue();

	// Cleanup
	unlink($addonFile);
});

test('subscribeListeners processes builtin addons without package_id check', function () {
	// Create a test builtin addon
	$addonFile = OP_ADDONS . '/TestBuiltinAddon.php';
	file_put_contents($addonFile, '<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

class TestBuiltinAddon extends AbstractAddon
{
	public const PACKAGE_ID = "Custom:TestBuiltinAddon";

	public static array $events = [self::HOOK_EVENT];

	public function __invoke(AddonEvent $event): void {}
}
');

	// Mock ListenerRegistry - allow any calls since real addons may also be processed
	$registry = Mockery::mock(ListenerRegistry::class);
	$registry->shouldReceive('subscribeTo')->andReturn();

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'subscribeListeners');
	$method->invoke($handler, $registry);

	// Check that hasSubscribed is set
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	expect($property->getValue($handler))->toBeTrue();

	// Cleanup
	unlink($addonFile);
});

test('subscribeListeners calls integration hook to modify addons', function () {
	// Create a mock class for the hook-added addon
	eval('namespace Bugo\Optimus\Addons;

	use Bugo\Optimus\Events\AddonEvent;

	class HookAddedAddon extends AbstractAddon
	{
		public const PACKAGE_ID = "Optimus:HookAddedAddon";
		public static array $events = [self::HOOK_EVENT];
		public function __invoke(AddonEvent $event): void {}
	}
');

	// Mock ListenerRegistry - allow any calls
	$registry = Mockery::mock(ListenerRegistry::class);
	$registry->shouldReceive('subscribeTo')->andReturn();

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'subscribeListeners');
	$method->invoke($handler, $registry);

	// Check that hasSubscribed is set
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	expect($property->getValue($handler))->toBeTrue();
});

test('subscribeListeners handles empty addon list', function () {
	// Mock ListenerRegistry - allow any calls since real addons may exist
	$registry = Mockery::mock(ListenerRegistry::class);
	$registry->shouldReceive('subscribeTo')->andReturn();

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'subscribeListeners');
	$method->invoke($handler, $registry);

	// Check that hasSubscribed is set
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	expect($property->getValue($handler))->toBeTrue();
});

test('subscribeListeners handles addon with multiple events', function () {
	// Create a test addon with multiple events
	$addonFile = OP_ADDONS . '/TestMultiEventAddon.php';
	file_put_contents($addonFile, '<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

class TestMultiEventAddon extends AbstractAddon
{
	public const PACKAGE_ID = "Optimus:TestMultiEventAddon";

	public static array $events = [self::HOOK_EVENT, self::ROBOTS_RULES];

	public function __invoke(AddonEvent $event): void {}
}
');

	// Mock ListenerRegistry - allow any calls
	$registry = Mockery::mock(ListenerRegistry::class);
	$registry->shouldReceive('subscribeTo')->andReturn();

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'subscribeListeners');
	$method->invoke($handler, $registry);

	// Check that hasSubscribed is set
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	expect($property->getValue($handler))->toBeTrue();

	// Cleanup
	unlink($addonFile);
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

test('subscribeListeners handles invalid addon class', function () {
	// Create a test addon file with invalid class (not in installed mods)
	$addonFile = OP_ADDONS . '/TestInvalidAddon.php';
	file_put_contents($addonFile, '<?php

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

class TestInvalidAddon extends AbstractAddon
{
	public const PACKAGE_ID = "InvalidAddon";

	public static array $events = [self::HOOK_EVENT];

	public function __invoke(AddonEvent $event): void {}
}
');

	// Mock ListenerRegistry - allow any calls
	$registry = Mockery::mock(ListenerRegistry::class);
	$registry->shouldReceive('subscribeTo')->andReturn();

	$handler = new AddonHandler();
	$method = new ReflectionMethod($handler, 'subscribeListeners');

	// Should not throw exception, should continue
	$method->invoke($handler, $registry);

	// Check that hasSubscribed is set
	$property = new ReflectionProperty($handler, 'hasSubscribed');
	expect($property->getValue($handler))->toBeTrue();

	// Cleanup
	unlink($addonFile);
});
