<?php declare(strict_types=1);

/**
 * RobotsGenerator.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Robots;

use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Events\DispatcherFactory;

if (! defined('SMF'))
	die('No direct access...');

final class Generator
{
	private array $rules = [];

	public array $customRules = [];

	public array $actions = [
		'msg', 'profile', 'help', 'search', 'mlist', 'sort', 'recent',
		'unread', 'login', 'signup', 'groups', 'stats', 'prev_next', 'all'
	];

	public bool $useSef = false;

	public string $urlPath = '';

	public function generate(): void
	{
		global $boardurl, $context;

		clearstatcache();

		$this->urlPath = parse_url($boardurl, PHP_URL_PATH) ?? '';
		$this->rules[]  = 'User-agent: *';

		// Mod authors can add or change generated rules
		$dispatcher = (new DispatcherFactory())();
		$dispatcher->dispatch(new AddonEvent(AddonInterface::ROBOTS_RULES, $this));

		$this->addRules();
		$this->addNews();
		$this->addAssets();
		$this->addSitemaps();

		$new_robots = implode('<br>', str_replace("|", '', $this->rules));
		$context['new_robots_content'] = parse_bbc('[code]' . $new_robots . '[/code]');
	}

	private function addRules(): void
	{
		global $modSettings;

		if ($this->useSef) {
			foreach ($this->actions as $action) {
				$this->rules[] = 'Disallow: ' . $this->urlPath . '/' . $action . '/';
			}
		} else {
			$this->rules[] = 'Disallow: ' . $this->urlPath . '/*action';
		}

		$this->rules[] = 'Disallow: ' . $this->urlPath . '/*PHPSESSID';

		if (! $this->useSef) {
			$this->rules[] = 'Disallow: ' . $this->urlPath . '/*;';
		}

		// Front page
		$this->rules[] = 'Allow: ' . $this->urlPath . '/$';

		// Content
		if (! $this->useSef) {
			if (empty($modSettings['queryless_urls'])) {
				$this->rules[] = 'Allow: ' . $this->urlPath . "/*board=*.0$\nAllow: " . $this->urlPath . '/*topic=*.0$';
			} else {
				$this->rules[] = 'Allow: ' . $this->urlPath . "/*board,*.0.html$\nAllow: " . $this->urlPath . '/*topic,*.0.html$';
			}
		}

		// Add custom rules
		$this->rules = array_merge($this->rules, $this->customRules);
	}

	private function addNews(): void
	{
		global $modSettings;

		$this->rules[] = empty($modSettings['xmlnews_enable']) ? '' : 'Allow: ' . $this->urlPath . '/*.xml';
	}

	private function addAssets(): void
	{
		$this->rules[] = "Allow: /*.css$\nAllow: /*.js$\nAllow: /*.png$\nAllow: /*.jpg$\nAllow: /*.gif$";
	}

	private function addSitemaps(): void
	{
		global $boardurl, $boarddir, $sourcedir, $scripturl;

		$mapFile = 'sitemap.xml';
		$gzFile  = 'sitemap.xml.gz';

		$mapFile = file_exists($boarddir . '/' . $mapFile) ? $boardurl . '/' . $mapFile : '';
		$gzFile  = file_exists($boarddir . '/' . $gzFile) ? $boardurl . '/' . $gzFile : '';

		$sitemapModInstalled = file_exists($sourcedir . '/Sitemap.php');

		if ($sitemapModInstalled)
			$this->rules[] = 'Allow: ' . $this->urlPath . '/*sitemap';

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
