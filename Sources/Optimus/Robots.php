<?php

namespace Bugo\Optimus;

/**
 * Robots.php
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
 * The class to work with robots.txt
 */
class Robots
{
	private $map = 'sitemap.xml';
	private $url_path;
	public $rules = [];

	/**
	 * Getter for $map field
	 *
	 * @return string
	 */
	protected function getMap()
	{
		return $this->map;
	}

	/**
	 * Setter for $map field
	 *
	 * @param string $value
	 * @return void
	 */
	protected function setMap($value)
	{
		$this->map = $value;
	}

	/**
	 * Getter for $url_path field
	 *
	 * @return void
	 */
	protected function getUrlPath()
	{
		return $this->url_path;
	}

	/**
	 * Generation of robots.txt
	 *
	 * @return bool
	 */
	public function generate()
	{
		global $modSettings, $boardurl, $context;

		clearstatcache();

		if (!empty($modSettings['optimus_sitemap_name']))
			$this->setMap($modSettings['optimus_sitemap_name'] . '.xml');

		$this->url_path = parse_url($boardurl, PHP_URL_PATH);
		$this->rules[] = "User-agent: *";

		self::sefRules();

		// Ability for the mod authors to add or change generated rules
		Subs::runAddons('robots', array(&$this->rules, $this->getUrlPath()));

		self::rssRules();
		self::assetsRules();
		self::sitemapRules();

		$new_robots = array();
		foreach ($this->rules as $line) {
			if (!empty($line))
				$new_robots[] = $line;
		}

		$new_robots = implode("<br>", str_replace("|", "", $new_robots));
		$context['new_robots_content'] = parse_bbc('[code]' . $new_robots . '[/code]');
	}

	/**
	 * Special rules for Pretty URLs or SimpleSEF and also $modSettings['queryless_urls'] option
	 *
	 * @return void
	 */
	private function sefRules()
	{
		global $modSettings, $sourcedir;

		// Is any SEF mod enabled?
		$pretty_urls = !empty($modSettings['pretty_enable_filters']) && file_exists($sourcedir . '/PrettyUrls-Filters.php');
		$simplesef   = !empty($modSettings['simplesef_enable']) && file_exists($sourcedir . '/SimpleSEF.php');
		$sef_enabled = $pretty_urls || $simplesef;

		$actions = array('msg','profile','help','search','mlist','sort','recent','unread','login','signup','groups','stats','prev_next','all');

		if ($sef_enabled) {
			foreach ($actions as $action)
				$this->rules[] = "Disallow: " . $this->getUrlPath() . '/' . $action . '/';
		} else
			$this->rules[] = "Disallow: " . $this->getUrlPath() . "/*action";

		if (!empty($modSettings['queryless_urls']) || $sef_enabled)
			$this->rules[] = "";
		else
			$this->rules[] = "Disallow: " . $this->getUrlPath() . "/*topic=*.msg\nDisallow: " . $this->getUrlPath() . "/*topic=*.new";

		$this->rules[] = "Disallow: " . $this->getUrlPath() . "/*PHPSESSID";
		$this->rules[] = $sef_enabled ? "" : "Disallow: " . $this->getUrlPath() . "/*;";

		// Front page
		$this->rules[] = "Allow: " . $this->getUrlPath() . "/$";

		// Content
		if (!empty($modSettings['queryless_urls']))
			$this->rules[] = ($sef_enabled ? "" : "Allow: " . $this->getUrlPath() . "/*board*.html$\nAllow: " . $this->getUrlPath() . "/*topic*.html$");
		else
			$this->rules[] = ($sef_enabled ? "" : "Allow: " . $this->getUrlPath() . "/*board\nAllow: " . $this->getUrlPath() . "/*topic");
	}

	/**
	 * RSS rules
	 *
	 * @return void
	 */
	private function rssRules()
	{
		global $modSettings;

		$this->rules[] = !empty($modSettings['xmlnews_enable']) ? "Allow: " . $this->getUrlPath() . "/*.xml" : "";
	}

	/**
	 * We have nothing to hide ;)
	 *
	 * @return void
	 */
	private function assetsRules()
	{
		$this->rules[] = "Allow: /*.css$\nAllow: /*.js$\nAllow: /*.png$\nAllow: /*.jpg$\nAllow: /*.gif$";
	}

	/**
	 * Sitemap rules
	 *
	 * @return void
	 */
	private function sitemapRules()
	{
		global $boardurl, $boarddir, $sourcedir, $scripturl;

		$path_map    = $boardurl . '/' . $this->getMap();
		$temp_map    = file_exists($boarddir . '/' . $this->getMap());
		$temp_map_gz = file_exists($boarddir . '/' . $this->getMap() . '.gz');

		$this->setMap($temp_map ? $path_map : '');

		$map_gz = $temp_map_gz ? $path_map . '.gz' : '';

		$sitemap = file_exists($sourcedir . '/Sitemap.php');

		$this->rules[] = !empty($this->getMap()) || file_exists($sourcedir . '/Sitemap.php') ? "Allow: " . $this->getUrlPath() . "/*sitemap" : "";
		$this->rules[] = !empty($this->getMap()) || !empty($map_gz) || $sitemap ? "|" : "";
		$this->rules[] = $sitemap ? "Sitemap: " . $scripturl . "?action=sitemap;xml" : "";
		$this->rules[] = !empty($this->getMap()) ? "Sitemap: " . $this->getMap() : "";
		$this->rules[] = !empty($map_gz) ? "Sitemap: " . $map_gz : "";
	}
}
