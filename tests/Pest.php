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
	Lang::$txt['no_matches'] = 'No matches';
	Lang::$txt['search'] = 'Search';
	Lang::$txt['remove'] = 'Remove';

	Config::$boardurl = 'https://example.com';
	Config::$scripturl = Config::$boardurl . '/index.php';
	Config::$sourcedir = __DIR__ . '/files';

	Theme::$current->settings['default_images_url'] = '';
	Theme::$current->settings['theme_id'] = 'foo_bar';
	Theme::$current->settings['theme_url'] = '';
	Theme::$current->settings['default_theme_dir'] = dirname(__DIR__) . '/src/Themes/default';

	Utils::$context['forum_name'] = 'Test Forum';
	Utils::$context['admin_menu_name'] = 'admin';
	Utils::$context['right_to_left'] = false;

	Utils::$smcFunc['substr'] = fn($string, $offset, $length) => substr($string, $offset, $length);
	Utils::$smcFunc['strlen'] = fn($string) => strlen($string);
	Utils::$smcFunc['htmltrim'] = fn($string) => trim($string);
	Utils::$smcFunc['htmlspecialchars'] = fn($string, $flags) => htmlspecialchars($string, $flags);

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
	return htmlspecialchars_decode($string);
}

function cache_get_data(string $key, int $ttl = 120): ?array
{
	if ($key == 'optimus_search_terms') return null;
	if ($key == 'optimus_topic_keywords') return null;
	if ($key == 'optimus_all_keywords') return null;

	return [];
}

function cache_put_data(string $key, mixed $value, int $ttl = 120): void
{
}

function clean_cache(): void
{
}

function updateSettings(array $settings): void
{
}

function loadCSSFile(string $fileName): void
{
	$id = (empty($id) ? strtr(str_replace('.css', '', basename($fileName)), '?', '_') : $id) . '_css';

	Utils::$context['css_files'][$id] = ['fileName' => $fileName];
}

function loadJavaScriptFile(string $fileName): void
{
	$id = (empty($id) ? strtr(str_replace('.js', '', basename($fileName)), '?', '_') : $id) . '_js';

	Utils::$context['javascript_files'][$id] = ['fileName' => $fileName];
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
	return !!$file;
}

function log_error(string $message, string $level = 'user'): string
{
	return $message;
}

function fatal_lang_error(...$params): void
{
	Utils::$context['error_title'] = $params[0];
}
