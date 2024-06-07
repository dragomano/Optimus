<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Robots;

use Bugo\Compat\{BBCodeParser, Config, Utils};
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Events\DispatcherFactory;
use League\Event\EventDispatcher;

if (! defined('SMF'))
	die('No direct access...');

final class Generator
{
	public const MAP_FILE = 'sitemap.xml';

	public const MAP_GZ_FILE = 'sitemap.xml.gz';

	public array $actions = [
		'msg', 'profile', 'help', 'search', 'mlist', 'sort', 'recent',
		'unread', 'login', 'signup', 'groups', 'stats', 'prev_next', 'all',
	];

	public array $customRules = [];

	public bool $useSef = false;

	public string $urlPath = '';

	private array $rules = [];

	private EventDispatcher $dispatcher;

	public function __construct()
	{
		$this->dispatcher = (new DispatcherFactory())();
	}

	public function generate(): void
	{
		clearstatcache();

		$this->urlPath = parse_url(Config::$boardurl, PHP_URL_PATH) ?? '';
		$this->rules[] = 'User-agent: *';

		// You can change generated rules
		$this->dispatcher->dispatch(new AddonEvent(AddonInterface::ROBOTS_RULES, $this));

		// External integrations
		call_integration_hook('integrate_optimus_robots_rules', [&$this->customRules, $this->urlPath]);

		$this->addRules();
		$this->addFeeds();
		$this->addAssets();
		$this->addSitemaps();

		$content = implode('<br>', str_replace("|", '', $this->rules));
		Utils::$context['new_robots_content'] = BBCodeParser::load()->parse('[code]' . $content . '[/code]');
	}

	private function addRules(): void
	{
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
			if (empty(Config::$modSettings['queryless_urls'])) {
				$this->rules[] = 'Allow: ' . $this->urlPath . "/*board=*.0$\nAllow: " . $this->urlPath . '/*topic=*.0$';
			} else {
				$this->rules[] = 'Allow: ' . $this->urlPath . "/*board,*.0.html$\nAllow: " . $this->urlPath . '/*topic,*.0.html$';
			}
		}

		// Add custom rules
		$this->rules = array_merge($this->rules, $this->customRules);
	}

	private function addFeeds(): void
	{
		$this->rules[] = empty(Config::$modSettings['xmlnews_enable']) ? '' : 'Allow: ' . $this->urlPath . '/*.xml';
	}

	private function addAssets(): void
	{
		$this->rules[] = "Allow: /*.css$\nAllow: /*.js$\nAllow: /*.png$\nAllow: /*.jpg$\nAllow: /*.gif$";
	}

	private function addSitemaps(): void
	{
		$mapFile = file_exists(Config::$boarddir . '/' . self::MAP_FILE)
			? Config::$boardurl . '/' . self::MAP_FILE
			: '';

		$mapGzFile = file_exists(Config::$boarddir . '/' . self::MAP_GZ_FILE)
			? Config::$boardurl . '/' . self::MAP_GZ_FILE
			: '';

		if (! empty($mapFile) || ! empty($mapGzFile))
			$this->rules[] = '|';

		if (! empty($mapGzFile))
			$this->rules[] = 'Sitemap: ' . $mapGzFile;
		elseif (! empty($mapFile))
			$this->rules[] = 'Sitemap: ' . $mapFile;
	}
}
