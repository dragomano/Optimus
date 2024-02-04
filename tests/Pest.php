<?php declare(strict_types=1);

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

/*expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});*/
uses()->beforeAll(function () {
	require_once dirname(__DIR__) . '/src/Sources/Optimus/app.php';
	require_once __DIR__ . '/boostrap.php';

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

function un_htmlspecialchars(string $string): string
{
	return $string;
}

function cache_get_data(...$params): array
{
	return [$params];
}

function cache_put_data(...$params): void
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
