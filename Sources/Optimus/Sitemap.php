<?php

namespace Bugo\Optimus;

/**
 * Sitemap.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.6.1
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Map generation class
 */
class Sitemap
{
	const TAB = "\t";

	/**
	 * Maximum number links
	 *
	 * @var int
	 */
	private $count = 50000;

	/**
	 * Links for sitemap
	 *
	 * @var array
	 */
	protected $links = [];

	/**
	 * XML namespace
	 *
	 * @var string
	 */
	protected $xmlns = 'http://www.sitemaps.org/schemas/sitemap/0.9';

	/**
	 * XML filename
	 *
	 * @var string
	 */
	protected $name = 'sitemap';

	/**
	 * Class constructor
	 *
	 * @param array $links
	 * @param string $xmlns
	 * @param string $name
	 */
	public function __construct($links = [], $xmlns = '', $name = '')
	{
		$this->links = $links;

		if (!empty($xmlns))
			$this->xmlns = $xmlns;

		if (!empty($name))
			$this->name = $name;
	}

	/**
	 * Setter for $count field
	 *
	 * @param int $value
	 * @return void
	 */
	public function setCount($value)
	{
		$this->count = $value;
	}

	/**
	 * Getter for $count field
	 *
	 * @return int
	 */
	public function getCount()
	{
		return (int) $this->count;
	}

	/**
	 * Map generation
	 *
	 * @return bool
	 */
	public function generate()
	{
		global $modSettings, $boarddir, $boardurl;

		if (empty($this->links))
			return false;

		$urls = array();

		$sitemap_counter = 0;
		foreach ($this->links as $link_counter => $entry) {
			if (!empty($link_counter) && $link_counter % $this->getCount() == 0)
				$sitemap_counter++;

			$urls[$sitemap_counter][] = array(
				'loc'        => $entry['url'],
				'lastmod'    => $this->getDate($entry['date']),
				'changefreq' => $this->getFrequency($entry['date']),
				'priority'   => $this->getPriority($entry['date'])
			);
		}

		// The update frequency of the main page
		if (empty($modSettings['optimus_main_page_frequency']))
			$urls[0][0]['changefreq'] = 'always';

		// The priority of the main page
		$urls[0][0]['priority'] = '1.0';

		// Remove the previous sitemaps
		array_map("unlink", glob($boarddir . '/' . $this->name . '*.xml'));

		// Create the sitemap
		$header = '<' . '?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

		// If links more than $this->count, then make the sitemapindex file
		if ($sitemap_counter > $this->getCount()) {
			for ($number = 0; $number <= $sitemap_counter; $number++) {
				$content  = self::prepareContent($urls[$number]);
				$content  = $header . '<urlset xmlns="' . $this->xmlns . '">' . PHP_EOL . $content . '</urlset>';
				$filename = $boarddir . '/' . $this->name . '_' . $number . '.xml';
				$this->createFile($filename, $content);
			}

			// Create a Sitemap index file
			$content = '';

			for ($number = 0; $number <= $sitemap_counter; $number++) {
				$content .= self::TAB . '<sitemap>' . PHP_EOL;
				$content .= self::TAB . self::TAB . '<loc>' . $boardurl . '/' . $this->name . '_' . $number . '.xml</loc>' . PHP_EOL;
				$content .= self::TAB . self::TAB . '<lastmod>' . $this->getDate() . '</lastmod>' . PHP_EOL;
				$content .= self::TAB . '</sitemap>' . PHP_EOL;
			}

			$content  = $header . '<sitemapindex xmlns="' . $this->xmlns . '">' . PHP_EOL . $content . '</sitemapindex>';
			$filename = $boarddir . '/' . $this->name . '.xml';
			$this->createFile($filename, $content);
		} else {
			$content  = self::prepareContent($urls[0]);
			$content  = $header . '<urlset xmlns="' . $this->xmlns . '">' . PHP_EOL . $content . '</urlset>';
			$filename = $boarddir . '/' . $this->name . '.xml';
			$this->createFile($filename, $content);
		}

		return true;
	}

	/**
	 * Preparing links for the sitemap
	 *
	 * @param array $data
	 * @return string
	 */
	private function prepareContent($data)
	{
		$content = '';

		foreach ($data as $entry) {
			$content .= self::TAB . '<url>' . PHP_EOL;
			$content .= self::TAB . self::TAB . '<loc>' . $entry['loc'] . '</loc>' . PHP_EOL;

			if (!empty($entry['lastmod']))
				$content .= self::TAB . self::TAB . '<lastmod>' . $entry['lastmod'] . '</lastmod>' . PHP_EOL;

			if (!empty($entry['changefreq']))
				$content .= self::TAB . self::TAB . '<changefreq>' . $entry['changefreq'] . '</changefreq>' . PHP_EOL;

			if (!empty($entry['priority']))
				$content .= self::TAB . self::TAB . '<priority>' . $entry['priority'] . '</priority>' . PHP_EOL;

			$content .= self::TAB . '</url>' . PHP_EOL;
		}

		// We make mass processing of links
		Subs::runAddons('prepareContent', array(&$content));

		return $content;
	}

	/**
	 * Create a map file
	 *
	 * @param string $path file path
	 * @param string $data content
	 * @return bool
	 */
	private function createFile($path, $data)
	{
		if (!$fp = fopen($path, 'w'))
			return false;

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		// If the file size exceeds 10 MB, we also create a packaged gz-version
		if (filesize($path) > (10 * 1024 * 1024)) {
			$data = implode('', file($path));
			$gzdata = gzencode($data, 9);
			$fp = fopen($path . '.gz', 'w');
			fwrite($fp, $gzdata);
			fclose($fp);
		}

		return true;
	}

	/**
	 * Date processing
	 *
	 * @param int $timestamp
	 * @return string
	 */
	private function getDate($time = 0)
	{
		$timestamp = $time ?: time();
		$gmt       = substr(date("O", $timestamp), 0, 3) . ':00';
		$result    = date('Y-m-d\TH:i:s', $timestamp) . $gmt;

		return $result;
	}

	/**
	 * Determine the frequency of updates
	 *
	 * @param int $time
	 * @return string
	 */
	private function getFrequency($time)
	{
		$frequency = time() - $time;

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
	 * @param int $time
	 * @return float
	 */
	private function getPriority($time)
	{
		$diff = floor((time() - $time) / 60 / 60 / 24);

		if ($diff <= 30)
			return '0.8';
		elseif ($diff <= 60)
			return '0.6';
		elseif ($diff <= 90)
			return '0.4';
		else
			return '0.2';
	}
}
