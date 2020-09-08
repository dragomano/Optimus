<?php

namespace Bugo\Optimus;

/**
 * Sitemap.php
 *
 * @package SMF Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.6.6
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Map generation class
 */
class Sitemap
{
	/**
	 * The maximum number of items per page
	 *
	 * @var int
	 */
	public static $max_items = 50000;

	/**
	 * Create sitemap XML
	 *
	 * @return bool
	 */
	public static function createXml()
	{
		global $modSettings, $context, $scripturl, $boardurl, $boarddir;

		if (@ini_get('memory_limit') < 256)
			@ini_set('memory_limit', '256M');

		ignore_user_abort(true);
		@set_time_limit(600);

		$modSettings['disableQueryCheck'] = true;
		$modSettings['pretty_bufferusecache'] = false;

		$items = [];
		$max_items = $modSettings['optimus_sitemap_items_display'] ?: self::$max_items;
		$sitemap_counter = 0;

		$links = self::getLinks();
		if (empty($links))
			return false;

		foreach ($links as $counter => $entry) {
			if (!empty($counter) && $counter % $max_items == 0)
				$sitemap_counter++;

			$items[$sitemap_counter][] = array(
				'loc'        => $entry['loc'],
				'lastmod'    => self::getDate($entry['lastmod']),
				'changefreq' => self::getFrequency($entry['lastmod']),
				'priority'   => self::getPriority($entry['lastmod'])
			);
		}

		unset($links);

		// The update frequency of the main page
		if (empty($modSettings['optimus_main_page_frequency']))
			$items[0][0]['changefreq'] = 'always';

		// The priority of the main page
		$items[0][0]['priority'] = '1.0';

		loadTemplate('Optimus');

		if ($sitemap_counter > 0) {
			$gz_maps = [];
			for ($number = 0; $number <= $sitemap_counter; $number++) {
				$context['sitemap']['items'] = $items[$number];

				ob_start();
				template_sitemap_xml();
				$content = ob_get_clean();

				Subs::runAddons('sitemapRewriteContent', array(&$content));

				$gz_maps[$number] = self::createFile($boarddir . '/sitemap_' . $number . '.xml', $content);
			}

			$context['sitemap']['items'] = [];
			for ($number = 0; $number <= $sitemap_counter; $number++)
				$context['sitemap']['items'][$number]['loc'] = $boardurl . '/sitemap_' . $number . '.xml' . (!empty($gz_maps[$number]) ? '.gz' : '');

			ob_start();
			template_sitemapindex_xml();
			$content = ob_get_clean();

			self::createFile($boarddir . '/sitemap.xml', $content);
		} else {
			$context['sitemap']['items'] = $items[0];

			ob_start();
			template_sitemap_xml();
			$content = ob_get_clean();

			Subs::runAddons('sitemapRewriteContent', array(&$content));

			self::createFile($boarddir . '/sitemap.xml', $content);
		}

		ignore_user_abort(false);

		return true;
	}

	/**
	 * Find the most recent date in the array of links for the map
	 *
	 * @param array $links
	 * @return null|int
	 */
	public static function getLastDate($links)
	{
		if (empty($links))
			return null;

		$data = array_values(array_values($links));

		$dates = [];
		foreach ($data as $value)
			$dates[] = $value['lastmod'];

		return max($dates);
	}

	/**
	 * Get an array of forum links to create a Sitemap
	 *
	 * @return array
	 */
	public static function getLinks()
	{
		global $context, $modSettings, $boardurl;

		if (!isset($context['optimus_ignored_boards']))
			$context['optimus_ignored_boards'] = [];

		if (!empty($modSettings['recycle_board']))
			$context['optimus_ignored_boards'][] = (int) $modSettings['recycle_board'];

		$links = [];

		// Possibility for the mod authors to add their own links or process them
		Subs::runAddons('sitemap', array(&$links));

		// Adding the main page
		$home = array(
			'loc'     => $boardurl . '/',
			'lastmod' => !empty($modSettings['optimus_main_page_frequency']) ? self::getLastDate($links) : time()
		);

		array_unshift($links, $home);

		return $links;
	}

	/**
	 * Date processing
	 *
	 * @param int $timestamp
	 * @return string
	 */
	private static function getDate($timestamp = 0)
	{
		if (empty($timestamp))
			return '';

		$gmt    = substr(date("O", $timestamp), 0, 3) . ':00';
		$result = date('Y-m-d\TH:i:s', $timestamp) . $gmt;

		return $result;
	}

	/**
	 * Determine the frequency of updates
	 *
	 * @param int $timestamp
	 * @return string
	 */
	private static function getFrequency($timestamp)
	{
		$frequency = time() - $timestamp;

		if ($frequency < (24 * 60 * 60))
			return 'hourly';
		elseif ($frequency < (24 * 60 * 60 * 7))
			return 'daily';
		elseif ($frequency < (24 * 60 * 60 * 7 * (52 / 12)))
			return 'weekly';
		elseif ($frequency < (24 * 60 * 60 * 365))
			return 'monthly';

		return 'yearly';
	}

	/**
	 * Determine the priority of indexing
	 *
	 * @param int $timestamp
	 * @return string
	 */
	private static function getPriority($timestamp)
	{
		$diff = floor((time() - $timestamp) / 60 / 60 / 24);

		if ($diff <= 30)
			return '0.8';
		elseif ($diff <= 60)
			return '0.6';
		elseif ($diff <= 90)
			return '0.4';

		return '0.2';
	}

	/**
	 * Создаем файл карты
	 *
	 * @param string $path — путь к файлу
	 * @param string $data — содержимое
	 * @return bool if true then we have gz-version
	 */
	private static function createFile($path, $data)
	{
		if (!$fp = fopen($path, 'w'))
			return false;

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		// Если размер файла превышает 50 МБ, создадим упакованную gz-версию
		if (filesize($path) > (50 * 1024 * 1024)) {
			$data   = implode('', file($path));
			$gzdata = gzencode($data, 9);
			$fp     = fopen($path . '.gz', 'w');
			fwrite($fp, $gzdata);
			fclose($fp);

			return true;
		}

		return false;
	}
}
