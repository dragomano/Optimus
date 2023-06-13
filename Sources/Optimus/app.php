<?php

if (! defined('SMF'))
	die('No direct access...');

defined('OP_NAME') || define('OP_NAME', 'Optimus for SMF');
defined('OP_VERSION') || define('OP_VERSION', '2.12');

function optimus_autoloader($classname)
{
	if (strpos($classname, 'Bugo\Optimus') === false)
		return false;

	$classname = str_replace('\\', '/', str_replace('Bugo\Optimus\\', '', $classname));
	$classname = str_replace('Addons/', 'addons/', $classname);
	$file_path = __DIR__ . '/' . $classname . '.php';

	if (! file_exists($file_path))
		return false;

	require_once $file_path;
}

spl_autoload_register('optimus_autoloader');

if (! function_exists('op_teaser')) {
	function op_teaser($text): string
	{
		return \Bugo\Optimus\Subs::getTeaser($text);
	}
}

if (! function_exists('op_snake')) {
	function op_snake($string): string
	{
		return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
	}
}

if (! function_exists('op_camel')) {
	function op_camel($string): string
	{
		return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
	}
}

if (! function_exists('op_global')) {
	function op_global($name)
	{
		return $GLOBALS[$name] ?? null;
	}
}

if (! function_exists('op_server')) {
	function op_server($name = '')
	{
		if (empty($name))
			return $_SERVER;

		$name = strtoupper($name);

		return $_SERVER[$name] ?? getenv($name) ?? null;
	}
}

if (! function_exists('op_session')) {
	function op_session($name)
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				$_SESSION[$key] = $value;
			}

			return;
		}

		return $_SESSION[$name] ?? null;
	}
}

if (! function_exists('op_is_request')) {
	function op_is_request($name): bool
	{
		return isset($_REQUEST[$name]);
	}
}

if (! function_exists('op_is_post')) {
	function op_is_post($name): bool
	{
		return isset($_POST[$name]);
	}
}

if (! function_exists('op_is_get')) {
	function op_is_get($name): bool
	{
		return isset($_GET[$name]);
	}
}

if (! function_exists('op_request')) {
	function op_request($name, $defaultValue = false)
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				$_REQUEST[$key] = $value;
			}

			return false;
		}

		return $_REQUEST[$name] ?? $defaultValue;
	}
}

if (! function_exists('is_on')) {
	function is_on($setting): bool
	{
		$modSettings = op_global('modSettings');

		return ! empty($modSettings[$setting]);
	}
}

if (! function_exists('is_off')) {
	function is_off($setting): bool
	{
		return ! is_on($setting);
	}
}

if (! function_exists('is_set')) {
	function is_set($setting): bool
	{
		$modSettings = op_global('modSettings');

		return isset($modSettings[$setting]);
	}
}

if (! function_exists('op_config')) {
	function op_config($setting, $defaultValue = null)
	{
		$modSettings = op_global('modSettings');

		return $modSettings[$setting] ?? $defaultValue;
	}
}

if (! function_exists('op_set_settings')) {
	function op_set_settings($options)
	{
		if (empty($options))
			return;

		$vars = [];
		foreach ($options as $key => $value) {
			if (op_config($key) === null)
				$vars[$key] = $value;
		}

		updateSettings($vars);
	}
}

if (! function_exists('op_xss')) {
	function op_xss($data)
	{
		$smcFunc = op_global('smcFunc');

		if (is_array($data))
			return array_map('op_xss', $data);

		return $smcFunc['htmlspecialchars']($data, ENT_QUOTES);
	}
}

if (! function_exists('op_filter')) {
	function op_filter($key, $filter = 'string', $input = INPUT_POST)
	{
		switch ($filter) {
			case 'int':
				$filter = FILTER_VALIDATE_INT;
				break;

			case 'bool':
				$filter = FILTER_VALIDATE_BOOLEAN;
				break;

			case 'url':
				$filter = FILTER_VALIDATE_URL;
				break;

			default:
				$filter = FILTER_DEFAULT;
		}

		return op_xss(filter_input($input, $key, $filter));
	}
}

if (! function_exists('op_link')) {
	function op_link(): string
	{
		$user_info = op_global('user_info');

		$link = $user_info['language'] === 'russian' ? 'https://dragomano.ru/mods/optimus' : 'https://custom.simplemachines.org/mods/index.php?mod=2659';

		return '<a href="' . $link . '" target="_blank" rel="noopener" title="' . OP_VERSION . '">' . OP_NAME . '</a>';
	}
}

new \Bugo\Optimus\Integration();
