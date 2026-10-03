<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Compat\Lang;
use Bugo\Compat\User;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\AddonSettingsHandler;
use Tests\TestDbMapper;

beforeEach(function () {
	$this->handler = new AddonSettingsHandler();

	Lang::setTxt('optimus_addons_title', 'Addons');
	Lang::setTxt('optimus_addons_settings_title', 'Settings of the "%s" addon');
	Lang::setTxt('optimus_addons_back', 'Back to the addon list');

	Utils::$context = [];
	Utils::$context['page_title'] = OP_NAME;
	Utils::$context['session_var'] = 'sesc';
	Utils::$context['session_id'] = 'test_session';

	Utils::$smcFunc['random_bytes'] = fn(int $length) => random_bytes($length);

	Config::$modSettings = [];
	Config::$scripturl = 'https://example.com';
	Config::$boarddir = sys_get_temp_dir() . '/optimus_addons_test_' . uniqid();
	mkdir(Config::$boarddir, 0777, true);

	User::$me = Mockery::mock(User::class)->makePartial();
	User::$me->shouldReceive('isAllowedTo')->andReturn(true);
	User::$me->shouldReceive('checkSession')->andReturn('session_id');

	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			return [];
		}
	};

	$_GET = [];
	$_POST = [];
	$_REQUEST = [];
});

afterEach(function () {
	Mockery::close();

	Db::$db = new FuncMapper();

	$_GET = [];
	$_POST = [];
	$_REQUEST = [];

	if (is_dir(Config::$boarddir)) {
		foreach (glob(Config::$boarddir . '/*') ?: [] as $file) {
			@unlink($file);
		}

		@rmdir(Config::$boarddir);
	}
});

test('handle renders the addon list', function () {
	$this->handler->handle();

	$rows = Utils::$context['optimus_addons'];

	$withSettings = [];
	$withoutSettings = [];

	foreach ($rows as $row) {
		if ($row['has_settings']) {
			$withSettings[] = $row;
		} else {
			$withoutSettings[] = $row;
		}
	}

	expect(Utils::$context['sub_template'])->toBe('addons')
		->and($rows)->not->toBeEmpty()
		->and(Utils::$context['page_title'])->toContain('Addons')
		->and($withSettings)->not->toBeEmpty()
		->and($withoutSettings[0]['settings_html'] ?? null)->toBeNull()
		// All settings forms must carry the same (last created) token
		// with the same random input name
		->and($withSettings[0]['settings_html'])->toBe($withSettings[1]['settings_html'])
		->and($withSettings[0]['settings_html'])->toContain('<input type="hidden"');
});

test('handle saves settings of the addon', function () {
	$_REQUEST['addon'] = 'Optimus:IndexNow';
	$_GET['save'] = true;
	$_POST['optimus_index_now_key'] = 'abcdef1234567890';

	$this->handler->handle();

	expect(is_file(Config::$boarddir . '/abcdef1234567890.txt'))->toBeTrue();
});

test('handle redirects when addon param is present without save', function () {
	$_REQUEST['addon'] = 'Optimus:IndexNow';

	$this->handler->handle();

	expect(Utils::$context['sub_template'] ?? null)->toBeNull()
		->and(Utils::$context['optimus_addons'] ?? null)->toBeNull();
});

test('handle redirects when addon has no settings', function () {
	$_REQUEST['addon'] = 'Optimus:BuiltInSEF';
	$_GET['save'] = true;

	$this->handler->handle();

	expect(Utils::$context['settings_title'] ?? null)->toBeNull()
		->and(Utils::$context['post_url'] ?? null)->toBeNull();
});

test('handle redirects when addon is unknown', function () {
	$_REQUEST['addon'] = 'Unknown:Whatever';
	$_GET['save'] = true;

	$this->handler->handle();

	expect(Utils::$context['settings_title'] ?? null)->toBeNull()
		->and(Utils::$context['post_url'] ?? null)->toBeNull();
});

test('handle toggles the addon and renders the list', function () {
	$_GET['toggle'] = 'Optimus:IndexNow';

	$this->handler->handle();

	expect(Utils::$context['sub_template'])->toBe('addons')
		->and(Utils::$context['optimus_addons'])->not->toBeEmpty();
});
