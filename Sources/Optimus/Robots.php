<?php

declare(strict_types=1);

namespace Bugo\Optimus;

/**
 * Robots.php
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

final class Robots
{
	private array $rules = [];
	private array $custom_rules = [];
	private array $actions = [
		'msg', 'profile', 'help', 'search', 'mlist', 'sort', 'recent',
		'unread', 'login', 'signup', 'groups', 'stats', 'prev_next', 'all'
	];
	private bool $use_sef = false;
	private string $url_path = '';

	public function generate()
	{
		global $boardurl, $context;

		clearstatcache();

		$this->url_path = parse_url($boardurl, PHP_URL_PATH) ?? '';
		$this->rules[] = 'User-agent: *';

		// Mod authors can add or change generated rules
		call_integration_hook('integrate_optimus_robots', array(&$this->custom_rules, $this->url_path,
			&$this->use_sef));

		$this->addRules();
		$this->addNews();
		$this->addAssets();
		$this->addSitemaps();

		$new_robots = implode('<br>', str_replace("|", '', $this->rules));
		$context['new_robots_content'] = parse_bbc('[code]' . $new_robots . '[/code]');
	}

	private function addRules()
	{
		if ($this->use_sef) {
			foreach ($this->actions as $action)
				$this->rules[] = 'Disallow: ' . $this->url_path . '/' . $action . '/';
		} else {
			$this->rules[] = 'Disallow: ' . $this->url_path . '/*action';
		}

		$this->rules[] = 'Disallow: ' . $this->url_path . '/*PHPSESSID';

		if (! $this->use_sef) {
			$this->rules[] = 'Disallow: ' . $this->url_path . '/*;';
		}

		// Front page
		$this->rules[] = 'Allow: ' . $this->url_path . '/$';

		// Content
		if (! $this->use_sef) {
			if (is_on('queryless_urls')) {
				$this->rules[] = 'Allow: ' . $this->url_path . "/*board,*.0.html$\nAllow: " . $this->url_path . '/*topic,*.0.html$';
			} else {
				$this->rules[] = 'Allow: ' . $this->url_path . "/*board=*.0$\nAllow: " . $this->url_path . '/*topic=*.0$';
			}
		}

		// Add custom rules
		$this->rules = array_merge($this->rules, $this->custom_rules);
	}

	private function addNews()
	{
		$this->rules[] = is_on('xmlnews_enable') ? 'Allow: ' . $this->url_path . '/*.xml' : '';
	}

	private function addAssets()
	{
		$this->rules[] = "Allow: /*.css$\nAllow: /*.js$\nAllow: /*.png$\nAllow: /*.jpg$\nAllow: /*.gif$";
	}

	private function addSitemaps()
	{
		global $boardurl, $boarddir, $sourcedir, $scripturl;

		$mapFile = 'sitemap.xml';
		$gzFile = 'sitemap.xml.gz';

		$mapFile = file_exists($boarddir . '/' . $mapFile) ? $boardurl . '/' . $mapFile : '';
		$gzFile = file_exists($boarddir . '/' . $gzFile) ? $boardurl . '/' . $gzFile : '';

		$sitemapModInstalled = file_exists($sourcedir . '/Sitemap.php');

		if ($sitemapModInstalled)
			$this->rules[] = 'Allow: ' . $this->url_path . '/*sitemap';

		if (! empty($mapFile) || ! empty($gzFile) || $sitemapModInstalled)
			$this->rules[] = '|';

		if ($sitemapModInstalled)
			$this->rules[] = 'Sitemap: ' . $scripturl . '?action=sitemap;xml';

		if (! empty($gzFile))
			$this->rules[] = 'Sitemap: ' . $gzFile;
		elseif (! empty($mapFile))
			$this->rules[] = 'Sitemap: ' . $mapFile;
	}
}
