<?php

global $txt, $context, $user_info, $modSettings, $smcFunc, $forum_copyright, $settings;

$txt = $context = $user_info = $modSettings = $smcFunc = [];

$forum_copyright = '';

$user_info['language'] = 'english';

$settings['default_images_url'] = '';

$smcFunc['substr'] = fn($string, $offset, $length) => substr($string, $offset, $length);
$smcFunc['strlen'] = fn($string) => strlen($string);
$smcFunc['htmltrim'] = fn($string) => trim($string);
$smcFunc['htmlspecialchars'] = fn($string, $flags) => htmlspecialchars($string, $flags);
$smcFunc['db_query'] = fn(...$params) => ['foo' => 'bar'];
$smcFunc['db_fetch_assoc'] = fn($result) => ['foo' => 'bar'];
$smcFunc['db_fetch_row'] = fn($result) => ['bar'];
$smcFunc['db_free_result'] = fn($result) => true;
$smcFunc['db_insert'] = fn(...$params) => true;

if (! function_exists('add_integration_function')) {
	function add_integration_function(string $hook, string $function, bool $permanent, string $file): void
	{
		assert(str_starts_with($hook, 'integrate_') && $permanent);
	}
}

if (! function_exists('call_integration_hook')) {
	function call_integration_hook(string $hook, array $args): void
	{
		assert(str_starts_with($hook, 'integrate_optimus') && $args);
	}
}

if (! function_exists('send_http_status')) {
	function send_http_status(int $code, string $status = ''): void
	{
		assert(!!$code);
	}
}

if (! function_exists('loadTemplate')) {
	function loadTemplate(string $name): void
	{
		assert(!!$name);
	}
}

if (! function_exists('loadLanguage')) {
	function loadLanguage(string $lang): void
	{
		global $txt;

		$file = dirname(__DIR__) . '/src/Themes/default/languages/' . $lang . '.english.php';

		if (is_file($file))
			require_once $file;

		assert($txt['optimus_title'] === 'Search Engine Optimization');
	}
}

if (! function_exists('allowedTo')) {
	function allowedTo(string $permission): bool
	{
		return !!$permission;
	}
}

if (! function_exists('un_htmlspecialchars')) {
	function un_htmlspecialchars(string $string): string
	{
		return $string;
	}
}

if (! function_exists('cache_get_data')) {
	function cache_get_data(string $key, int $ttl): array
	{
		return [];
	}
}

if (! function_exists('cache_put_data')) {
	function cache_put_data(string $key, mixed $value, int $ttl): void
	{
		assert($key);
	}
}

if (! function_exists('clean_cache')) {
	function clean_cache(): void
	{
		assert(true);
	}
}

if (! function_exists('loadCSSFile')) {
	function loadCSSFile(string $name): void
	{
		assert(!!$name);
	}
}

if (! function_exists('loadJavaScriptFile')) {
	function loadJavaScriptFile(string $name): void
	{
		assert(!!$name);
	}
}

if (! function_exists('addInlineJavaScript')) {
	function addInlineJavaScript(string $javascript, bool $defer = false): bool
	{
		global $context;

		if (empty($javascript))
			return false;

		$context['javascript_inline'][($defer === true ? 'defer' : 'standard')][] = $javascript;

		return true;
	}
}

if (! function_exists('addInlineCss')) {
	function addInlineCss(string $css): bool
	{
		global $context;

		if (empty($css))
			return false;

		$context['css_header'][] = $css;

		return true;
	}
}

if (! function_exists('createList')) {
	function createList(array $data): void
	{
		assert(!!$data);
	}
}

if (! function_exists('obExit')) {
	function obExit(?bool $header): void
	{
		assert(true);
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
		global $smcFunc;

		if ($smcFunc['strlen']($subject) <= $length)
			return $subject;

		return $smcFunc['substr']($subject, 0, $length) . '...';
	}
}
