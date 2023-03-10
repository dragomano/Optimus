<?php

declare(strict_types=1);

namespace Bugo\Optimus;

/**
 * Subs.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.10
 */

if (! defined('SMF'))
	die('No direct access...');

abstract class Subs
{
	/**
	 * Get an excerpt of the text (2 sentences by default)
	 *
	 * Получаем отрывок текста (по умолчанию 2 предложения)
	 */
	public static function getTeaser(string $text, int $num_sentences = 2, int $length = 252): string
	{
		global $smcFunc;

		$text = parse_bbc($text);

		// Replace all <br> and duplicate spaces
		$text = preg_replace('~\s+~', ' ', strip_tags(str_replace('<br>', ' ', $text)));

		// Remove all urls
		$text = preg_replace('~http(s)?://(.*)\s~U', '', $text);

		// Additional replacements
		$text = strtr($text, array('&nbsp;' => ' ', '&amp;nbsp;' => ' ', '&quot;' => ''));

		$sentences = preg_split('/(\.|\?|\!)(\s)/', $text);

		// Limit given text
		$text = shorten_subject($text, $length);

		if (count($sentences) <= $num_sentences)
			return trim($text);

		$stop_at = 0;
		foreach ($sentences as $i => $sentence) {
			$stop_at += $smcFunc['strlen']($sentence);
			if ($i >= $num_sentences - 1)
				break;
		}

		$stop_at += ($num_sentences * 2);

		return trim($smcFunc['substr']($text, 0, $stop_at));
	}

	public static function runAddons()
	{
		static $loadedAddons = [];

		$addons = self::getAddons();

		if (empty($addons))
			return;

		foreach ($addons as $addon) {
			$class = self::getClassName($addon);

			if (class_exists($class)) {
				self::loadAddonLanguages($addon);

				if (! isset($loadedAddons[$class])) {
					new $class;

					$loadedAddons[$class] = $addon;
				}
			}
		}
	}

	private static function getAddons(): ?array
	{
		if (! is_dir(__DIR__ . '/addons'))
			return [];

		if (($addons = cache_get_data('optimus_addons', 3600)) === null) {
			foreach ((new \FilesystemIterator(__DIR__ . '/addons')) as $object) {
				$filename = $object->getBasename();
				if ($object->isFile()) {
					$addons[] = str_replace('.php', '', $filename);
				}

				if ($object->isDir() && is_file($object->getPathname() . '/' . $filename . '.php')) {
					$addons[] = $filename . '|' . $filename;
				}
			}

			$addons = array_diff($addons, ['index']);

			cache_put_data('optimus_addons', $addons, 3600);
		}

		return $addons;
	}

	private static function getClassName(string $addon): string
	{
		return __NAMESPACE__ . '\Addons\\' . str_replace('|', '\\', $addon);
	}

	private static function loadAddonLanguages(string $addon)
	{
		global $user_info, $txt;

		if (empty($txt))
			return;

		$languages = array_merge(['english'], [$user_info['language'] ?? null]);
		$base_dir  = __DIR__ . '/addons/' . explode('|', $addon)[0] . '/langs/';

		foreach ($languages as $lang) {
			$lang_file = $base_dir . $lang . '.php';

			if (is_file($lang_file))
				require_once $lang_file;
		}
	}
}
