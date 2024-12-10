<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC1
 */

namespace Bugo\Optimus\Utils;

use Bugo\Compat\Utils;

if (! defined('SMF'))
	die('No direct access...');

final class Input
{
	public static function request(string|array $name, mixed $defaultValue = false): mixed
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				$_REQUEST[$key] = $value;
			}

			return false;
		}

		return $_REQUEST[$name] ?? $defaultValue;
	}

	public static function post(string|array $name, mixed $defaultValue = false): mixed
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				$_POST[$key] = $value;
			}

			return false;
		}

		return $_POST[$name] ?? $defaultValue;
	}

	public static function server(string $name = ''): mixed
	{
		if (empty($name))
			return $_SERVER;

		if ($name !== 'argv') {
			$name = strtoupper($name);
		}

		return $_SERVER[$name] ?? getenv($name) ?? null;
	}

	public static function session(array|string $name = ''): mixed
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				$_SESSION[$key] = $value;
			}

			return true;
		}

		return $_SESSION[$name] ?? null;
	}

	public static function isRequest($name): bool
	{
		return isset($_REQUEST[$name]);
	}

	public static function isPost($name): bool
	{
		return isset($_POST[$name]);
	}

	public static function isGet($name): bool
	{
		return isset($_GET[$name]);
	}

	public static function xss(string|array $data): string|array
	{
		if (is_array($data)) {
			return array_map(self::xss(...), $data);
		}

		return Utils::htmlspecialchars($data, ENT_QUOTES);
	}

	public static function filter(string $key, string $type = 'string'): mixed
	{
		$filter = match ($type) {
			'url'   => FILTER_VALIDATE_URL,
			default => FILTER_DEFAULT,
		};

		if (! self::isPost($key)) {
			return null;
		}

		$result = filter_var($_POST[$key], $filter);

		return empty($result) ? $result : self::xss($result);
	}
}
