<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\Lang;
use Bugo\Compat\Theme;
use Bugo\Compat\User;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\SettingHandler;
use Tests\TestDbMapper;

beforeEach(function () {
	$this->handler = new SettingHandler();

	Lang::setTxt('meta_keywords_note', '');
	Lang::setTxt('admin_maintenance', '');
	Lang::setTxt('maintain_recount', '');
	Lang::setTxt('optimus_root_is_not_writable', 'optimus_root_is_not_writable');

	Utils::$smcFunc['db_query'] = fn(...$params) => new stdClass();
	Utils::$smcFunc['db_insert'] = fn(...$params) => 1;
	Utils::$smcFunc['db_get_version'] = fn() => 'v1.0';
	Utils::$smcFunc['db_title'] = 'mysql';

	$this->tempDir = sys_get_temp_dir() . '/optimus_test_' . uniqid();
	mkdir($this->tempDir, 0777, true);

	// Create dummy files
	file_put_contents($this->tempDir . '/robots.txt', '');
	file_put_contents($this->tempDir . '/.htaccess', '');

	Utils::$context = [];
	Utils::$context['boarddir'] = $this->tempDir;
	Utils::$context['forum_name'] = 'Test Forum';
	Utils::$context['template_layers'] = ['main'];
	Utils::$context['page_title'] = '';
	Utils::$context['admin_menu_name'] = 'admin';
	Utils::$context['error_title'] = '';

	Config::$boarddir = $this->tempDir;
	Config::$scripturl = 'https://example.com';
	Config::$modSettings = [];

	$GLOBALS['_POST'] = [];
	$_GET = [];
	$_REQUEST = [];

	// Set Theme properties
	Theme::$current = (object) ['settings' => ['default_images_url' => '/images', 'theme_id' => 1]];

	// Mock Db
	Db::$db = new class extends TestDbMapper {
		public function testQuery($query, $params = []): array
		{
			return [];
		}
	};

	// Mock User for permissions
	User::$me = Mockery::mock(User::class)->makePartial();
	User::$me->shouldReceive('isAllowedTo')->andReturn(true);
	User::$me->shouldReceive('isAllowedTo')->with('admin_forum')->andReturn(true);
	User::$me->shouldReceive('checkSession')->andReturn('session_id');
});

function deleteDir(string $dir): void
{
	if (! is_dir($dir)) return;

	$files = array_diff(scandir($dir), ['.', '..']);

	foreach ($files as $file) {
		$path = $dir . DIRECTORY_SEPARATOR . $file;
		is_dir($path) ? deleteDir($path) : unlink($path);
	}

	rmdir($dir);
}

afterEach(function () {
	Mockery::close();

	if (is_dir($this->tempDir)) {
		deleteDir($this->tempDir);
	}
});

test('modifyBasicSettings method', function () {
	$config_vars = [
		['text', 'meta_keywords'],
	];

	$this->handler->modifyBasicSettings($config_vars);

	expect($config_vars)->toBeEmpty();
});

test('adminAreas method', function () {
	$admin_areas = [];

	$_REQUEST['area'] = 'optimus';

	$this->handler->adminAreas($admin_areas);

	expect($admin_areas)->not->toBeEmpty();

	unset($_REQUEST['area']);
});

test('adminSearch method', function () {
	$settings_search = [];

	$this->handler->adminSearch([], [], $settings_search);

	expect($settings_search)->not->toBeEmpty();
});

test('actions method', function () {
	$_REQUEST['area'] = 'optimus';

	$this->handler->actions();

	expect(Utils::$context['template_layers'])->toContain('tips')
		->and(Utils::$context['sub_template'])->toBe('show_settings');

	unset($_REQUEST['area']);
});

describe('Tabs', function () {
	beforeEach(function () {
		$_GET['save'] = true;
	});

	test('basicTabSettings method', function () {
		expect($this->handler->basicTabSettings())->toBeNull();

		unset($_GET['save']);

		expect($this->handler->basicTabSettings(true))->toBeArray();
	});

	test('basicTabSettings save with post data', function () {
		$_POST['optimus_forum_index'] = 'Test Forum';
		$_POST['optimus_description'] = 'Test Description';

		expect($this->handler->basicTabSettings())->toBeNull();

		unset($_POST['optimus_forum_index'], $_POST['optimus_description']);
	});

	test('extraTabSettings method', function () {
		expect($this->handler->extraTabSettings())->toBeNull();

		unset($_GET['save']);

		expect($this->handler->extraTabSettings(true))->toBeArray();
	});

	test('extraTabSettings save with post data', function () {
		$_POST['optimus_fb_appid'] = '123456';
		$_POST['optimus_tw_cards'] = '@testuser';

		expect($this->handler->extraTabSettings())->toBeNull();

		unset($_POST['optimus_fb_appid'], $_POST['optimus_tw_cards']);
	});

	test('faviconTabSettings method', function () {
		expect($this->handler->faviconTabSettings())->toBeNull();

		unset($_GET['save']);

		expect($this->handler->faviconTabSettings(true))->toBeArray();
	});

	test('metatagsTabSettings method', function () {
		expect($this->handler->metatagsTabSettings())->toBeNull();
	});

	test('metatagsTabSettings save with post data', function () {
		$_POST['custom_tag_name'] = ['test'];
		$_POST['custom_tag_value'] = ['value'];
		$GLOBALS['_POST'] = $_POST;

		expect($this->handler->metatagsTabSettings())->toBeNull();

		unset($_POST['custom_tag_name'], $_POST['custom_tag_value']);
		$GLOBALS['_POST'] = [];
	});

	test('redirectTabSettings method', function () {
		expect($this->handler->redirectTabSettings())->toBeNull();
	});

	test('redirectTabSettings save with post data', function () {
		$_POST['custom_redirect_from'] = ['old'];
		$_POST['custom_redirect_to'] = ['new'];
		$GLOBALS['_POST'] = $_POST;

		expect($this->handler->redirectTabSettings())->toBeNull();

		unset($_POST['custom_redirect_from'], $_POST['custom_redirect_to']);
		$GLOBALS['_POST'] = [];
	});

	test('counterTabSettings method', function () {
		expect($this->handler->counterTabSettings())->toBeNull();
	});

	test('robotsTabSettings method', function () {
		expect($this->handler->robotsTabSettings())->toBeNull();
	});

	test('robotsTabSettings save with robots content', function () {
		$_POST['optimus_robots'] = 'User-agent: *';

		expect($this->handler->robotsTabSettings())->toBeNull();

		unset($_POST['optimus_robots']);
	});

	test('htaccessTabSettings method', function () {
		$_POST['optimus_htaccess'] = '# comment';

		expect($this->handler->htaccessTabSettings())->toBeNull();
	});

	test('htaccessTabSettings save with backup creation', function () {
		// Create a temporary .htaccess file
		$htaccessPath = $this->tempDir . DIRECTORY_SEPARATOR . '.htaccess';
		file_put_contents($htaccessPath, 'original content');

		$_POST['optimus_htaccess'] = 'new content';

		$this->handler->htaccessTabSettings();

		expect(file_exists($htaccessPath . '.backup'))->toBeTrue();

		// Cleanup
		if (file_exists($htaccessPath)) {
			unlink($htaccessPath);
		}

		if (file_exists($htaccessPath . '.backup')) {
			unlink($htaccessPath . '.backup');
		}

		unset($_POST['optimus_htaccess']);
	});

	test('sitemapTabSettings method', function () {
		unset($_GET['save']);

		expect($this->handler->sitemapTabSettings())->toBeNull()
			->and($this->handler->sitemapTabSettings(true))->toBeArray();
	});

	test('sitemapTabSettings sets error when directory not writable', function () {
		$originalBoarddir = Config::$boarddir;
		Config::$boarddir = '/nonexistent';

		expect($this->handler->sitemapTabSettings())->toBeNull();

		Config::$boarddir = $originalBoarddir;
	});

	afterEach(function () {
		unset($_GET['save']);
	});
});
