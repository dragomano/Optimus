<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Lang;
use Bugo\Compat\Theme;
use Bugo\Compat\User;
use Bugo\Compat\Utils;

uses()->beforeEach(function () {
	require_once dirname(__DIR__) . '/src/Sources/Optimus/app.php';

	User::$me->language = 'english';

	Lang::setTxt('lang_dictionary', 'en');
	Lang::setTxt('no_matches', 'No matches');
	Lang::setTxt('search', 'Search');
	Lang::setTxt('remove', 'Remove');

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

if (! function_exists('add_integration_function')) {
	function add_integration_function(...$params): void
	{
	}
}

if (! function_exists('call_integration_hook')) {
	function call_integration_hook(string $hook, array $args): array
	{
		return [$hook => $args];
	}
}

if (! function_exists('send_http_status')) {
	function send_http_status(...$params): void
	{
	}
}

if (! function_exists('loadTemplate')) {
	function loadTemplate(string $name): void
	{
	}
}

if (! function_exists('loadLanguage')) {
	function loadLanguage(string $lang): string
	{
		global $txt;

		$file = dirname(__DIR__) . '/src/Themes/default/languages/' . $lang . '.english.php';

		if (is_file($file) && isset($txt)) {
			require_once $file;
		}

		return $lang;
	}
}

if (! function_exists('allowedTo')) {
	function allowedTo(string $permission): bool
	{
		return !!$permission;
	}
}

if (! function_exists('isAllowedTo')) {
	function isAllowedTo(string|array $permission): bool
	{
		return !!$permission;
	}
}

if (! function_exists('db_extend')) {
	function db_extend(string $type): void
	{
	}
}

if (! function_exists('un_htmlspecialchars')) {
	function un_htmlspecialchars(string $string): string
	{
		return htmlspecialchars_decode($string);
	}
}

if (! function_exists('cache_get_data')) {
	function cache_get_data(string $key, int $ttl = 120): ?array
	{
		global $cacheGetDataReturn;

		if ($key == 'optimus_search_terms') return null;
		if ($key == 'optimus_topic_keywords') return null;
		if ($key == 'optimus_all_keywords') return null;

		if (array_key_exists($key, (array) $cacheGetDataReturn)) return $cacheGetDataReturn[$key];

		return [];
	}
}

if (! function_exists('cache_put_data')) {
	function cache_put_data(string $key, mixed $value, int $ttl = 120): void
	{
	}
}

if (! function_exists('clean_cache')) {
	function clean_cache(): void
	{
	}
}

if (! function_exists('updateSettings')) {
	function updateSettings(array $settings): void
	{
	}
}

if (! function_exists('loadCSSFile')) {
	function loadCSSFile(string $fileName): void
	{
		$id = (empty($id) ? strtr(str_replace('.css', '', basename($fileName)), '?', '_') : $id) . '_css';

		Utils::$context['css_files'][$id] = ['fileName' => $fileName];
	}
}

if (! function_exists('loadJavaScriptFile')) {
	function loadJavaScriptFile(string $fileName): void
	{
		$id = (empty($id) ? strtr(str_replace('.js', '', basename($fileName)), '?', '_') : $id) . '_js';

		Utils::$context['javascript_files'][$id] = ['fileName' => $fileName];
	}
}

if (! function_exists('addInlineJavaScript')) {
	function addInlineJavaScript(string $javascript, bool $defer = false): bool
	{
		if (empty($javascript)) {
			return false;
		}

		Utils::$context['javascript_inline'][($defer === true ? 'defer' : 'standard')][] = $javascript;

		return true;
	}
}

if (! function_exists('addInlineCss')) {
	function addInlineCss(string $css): bool
	{
		if (empty($css)) {
			return false;
		}

		Utils::$context['css_header'][] = $css;

		return true;
	}
}

if (! function_exists('createList')) {
	function createList(array $options): void
	{
	}
}

if (! function_exists('obExit')) {
	function obExit(...$params): void
	{
	}
}

if (! function_exists('parse_bbc')) {
	function parse_bbc(string $string): string
	{
		return $string;
	}
}

if (! function_exists('shorten_subject')) {
	function shorten_subject(string $subject, int $length): string
	{
		if (Utils::$smcFunc['strlen']($subject) <= $length) {
			return $subject;
		}

		return Utils::$smcFunc['substr']($subject, 0, $length) . '...';
	}
}

if (! function_exists('checkSession')) {
	function checkSession(string $type = 'post'): string
	{
		return $type;
	}
}

if (! function_exists('redirectexit')) {
	function redirectexit(string $url = ''): void
	{
	}
}

if (! function_exists('smf_chmod')) {
	function smf_chmod(string $file): bool
	{
		global $makeWritableReturn;

		return $makeWritableReturn ?? !!$file;
	}
}

$makeWritableReturn = true;

if (! function_exists('log_error')) {
	function log_error(string $message, string $level = 'user'): string
	{
		return $message;
	}
}

if (! function_exists('fatal_lang_error')) {
	function fatal_lang_error(...$params): void
	{
		Utils::$context['error_title'] = Lang::getTxt($params[0]);
	}
}

if (! function_exists('httpsOn')) {
	function httpsOn(): bool
	{
		return true;
	}
}

if (! function_exists('filter_input_array')) {
	function filter_input_array($type) {
		return $GLOBALS['_POST'] ?? [];
	}
}

if (! function_exists('prepareDBSettingContext')) {
	function prepareDBSettingContext(array &$vars): void
	{
	}
}

if (! function_exists('saveDBSettings')) {
	function saveDBSettings(array &$vars): void
	{
	}
}
