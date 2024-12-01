<?php declare(strict_types=1);

use Bugo\Compat\Board;
use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\Lang;
use Bugo\Compat\Theme;
use Bugo\Compat\Topic;
use Bugo\Compat\User;
use Bugo\Compat\Utils;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

uses()->beforeAll(function () {
	array_map(fn($u) => new $u(), [
		Db::class,
		Lang::class,
		User::class,
		Theme::class,
		Board::class,
		Topic::class,
		Utils::class,
		Config::class,
	]);

	require_once dirname(__DIR__) . '/src/Sources/Optimus/app.php';

	User::$info['language'] = 'english';

	Lang::$txt['lang_dictionary'] = 'en';

	Config::$sourcedir = __DIR__ . '/files';

	Theme::$current->settings['default_images_url'] = '';
	Theme::$current->settings['theme_id'] = 'foo_bar';
	Theme::$current->settings['theme_url'] = '';
	Theme::$current->settings['default_theme_dir'] = dirname(__DIR__) . '/src/Themes/default';

	Utils::$context['forum_name'] = 'Foo Bar';
	Utils::$context['admin_menu_name'] = 'admin';

	Utils::$smcFunc['substr'] = fn($string, $offset, $length) => substr($string, $offset, $length);
	Utils::$smcFunc['strlen'] = fn($string) => strlen($string);
	Utils::$smcFunc['htmltrim'] = fn($string) => trim($string);
	Utils::$smcFunc['htmlspecialchars'] = fn($string, $flags) => htmlspecialchars($string, $flags);
	Utils::$smcFunc['db_query'] = fn(...$params) => new stdClass();
	Utils::$smcFunc['db_fetch_assoc'] = fn($result) => ['foo' => 'bar'];
	Utils::$smcFunc['db_fetch_row'] = fn($result) => ['bar'];
	Utils::$smcFunc['db_free_result'] = fn($result) => true;
	Utils::$smcFunc['db_insert'] = fn(...$params) => count($params);
	Utils::$smcFunc['db_get_version'] = fn() => '';
	Utils::$smcFunc['db_title'] = 'mysql';

	loadLanguage('Optimus/Optimus');
})->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function add_integration_function(...$params): void
{
}

function call_integration_hook(string $hook, array $args): array
{
	return [$hook => $args];
}

function send_http_status(...$params): void
{
}

function loadTemplate(string $name): void
{
}

function loadLanguage(string $lang): void
{
	global $txt;

	$file = dirname(__DIR__) . '/src/Themes/default/languages/' . $lang . '.english.php';

	if (is_file($file) && isset($txt))
		require_once $file;
}

function allowedTo(string $permission): bool
{
	return !!$permission;
}

function isAllowedTo(string|array $permission): bool
{
	return !!$permission;
}

function db_extend(string $type): void
{
}

function un_htmlspecialchars(string $string): string
{
	return 'decoded';
}

function cache_get_data(...$params): array
{
	return [$params];
}

function cache_put_data(...$params): void
{
}

function updateSettings(array $settings): void
{
}

function clean_cache(): void
{
}

function loadCSSFile(string $name): void
{
}

function loadJavaScriptFile(string $name): void
{
}

function addInlineJavaScript(string $javascript, bool $defer = false): bool
{
	if (empty($javascript))
		return false;

	Utils::$context['javascript_inline'][($defer === true ? 'defer' : 'standard')][] = $javascript;

	return true;
}

function addInlineCss(string $css): bool
{
	if (empty($css))
		return false;

	Utils::$context['css_header'][] = $css;

	return true;
}

function createList(array $options): void
{
}

function obExit(...$params): void
{
}

function parse_bbc(string $string): string
{
	return $string;
}

function shorten_subject(string $subject, int $length): string
{
	if (Utils::$smcFunc['strlen']($subject) <= $length)
		return $subject;

	return Utils::$smcFunc['substr']($subject, 0, $length) . '...';
}

function checkSession(string $type = 'post'): string
{
	return $type;
}

function redirectexit(string $url = ''): void
{
}

function smf_chmod(string $file): bool
{
	return true;
}

function log_error(string $message, string $level = 'user'): string
{
	return $message;
}
