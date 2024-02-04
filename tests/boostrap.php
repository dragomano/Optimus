<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Theme;
use Bugo\Compat\User;
use Bugo\Compat\Utils;

User::$info['language'] = 'english';

Config::$sourcedir = __DIR__ . '/files';

Theme::$current->settings['default_images_url'] = '';

Utils::$smcFunc['substr'] = fn($string, $offset, $length) => substr($string, $offset, $length);
Utils::$smcFunc['strlen'] = fn($string) => strlen($string);
Utils::$smcFunc['htmltrim'] = fn($string) => trim($string);
Utils::$smcFunc['htmlspecialchars'] = fn($string, $flags) => htmlspecialchars($string, $flags);
Utils::$smcFunc['db_query'] = fn(...$params) => ['foo' => 'bar'];
Utils::$smcFunc['db_fetch_assoc'] = fn($result) => ['foo' => 'bar'];
Utils::$smcFunc['db_fetch_row'] = fn($result) => ['bar'];
Utils::$smcFunc['db_free_result'] = fn($result) => true;
Utils::$smcFunc['db_insert'] = fn(...$params) => true;
