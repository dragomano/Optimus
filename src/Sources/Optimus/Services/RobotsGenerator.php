<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Services;

use Bugo\Compat\{Config, IntegrationHook, Utils};
use Bugo\Compat\Parsers\BBCodeParser;
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Enums\Entity;
use Bugo\Optimus\Events\Dispatcher;
use Bugo\Optimus\Events\DispatcherFactory;

final class RobotsGenerator
{
	public const RULE_DISALLOW = 'Disallow';

	public const RULE_ALLOW = 'Allow';

	public array $actions = [
		'msg', 'profile', 'help', 'search', 'mlist', 'sort', 'recent',
		'unread', 'login', 'signup', 'groups', 'stats', 'prev_next', 'all',
	];

	public array $customRules = [];

	public bool $useSef = false;

	public string $urlPath = '';

	private array $rules = [
		'*' => [
			self::RULE_DISALLOW => [],
			self::RULE_ALLOW    => [],
		],
	];

	private array $sitemaps = [];

	private readonly Dispatcher $dispatcher;

	public function __construct()
	{
		$this->dispatcher = (new DispatcherFactory())();

		$this->urlPath = parse_url(Config::$boardurl, PHP_URL_PATH) ?? '';
	}

	public function generate(): void
	{
		clearstatcache();

		// You can change generated rules
		$this->dispatcher->dispatchEvent(AddonInterface::ROBOTS_RULES, $this);

		// External integrations
		IntegrationHook::call('integrate_optimus_robots_rules', [&$this->customRules, $this->urlPath]);

		$this->addBaseRules();
		$this->addFeeds();
		$this->addAssets();
		$this->addSitemaps();

		$content = $this->generateContent();
		Utils::$context['new_robots_content'] = BBCodeParser::load()->parse('[code]' . $content . '[/code]');
	}

	private function addBaseRules(): void
	{
		$this->addActionsRules();
		$this->addSessionRules();
		$this->addFrontPageRule();
		$this->addContentRules();

		foreach ($this->customRules as $userAgent => $rules) {
			if (! isset($this->rules[$userAgent])) {
				$this->rules[$userAgent] = [self::RULE_DISALLOW => [], self::RULE_ALLOW => []];
			}

			$this->rules[$userAgent][self::RULE_DISALLOW] = array_merge(
				$this->rules[$userAgent][self::RULE_DISALLOW],
				$rules[self::RULE_DISALLOW] ?? []
			);

			$this->rules[$userAgent][self::RULE_ALLOW] = array_merge(
				$this->rules[$userAgent][self::RULE_ALLOW],
				$rules[self::RULE_ALLOW] ?? []
			);
		}
	}

	private function addActionsRules(): void
	{
		if ($this->useSef) {
			foreach ($this->actions as $action) {
				$this->rules['*'][self::RULE_DISALLOW][] = $this->urlPath . '/' . $action . '/';
			}
		} else {
			$this->rules['*'][self::RULE_DISALLOW][] = $this->urlPath . '/*action';
		}
	}

	private function addSessionRules(): void
	{
		$this->rules['*'][self::RULE_DISALLOW][] = $this->urlPath . '/*PHPSESSID';
	}

	private function addFrontPageRule(): void
	{
		$this->rules['*'][self::RULE_ALLOW][] = $this->urlPath . '/$';
	}

	private function addContentRules(): void
	{
		if ($this->useSef)
			return;

		$this->rules['*'][self::RULE_DISALLOW][] = $this->urlPath . '/*;';

		$this->rules['*'][self::RULE_ALLOW][] = $this->urlPath . Entity::BOARD->buildPattern();
		$this->rules['*'][self::RULE_ALLOW][] = $this->urlPath . Entity::TOPIC->buildPattern();
		$this->rules['*'][self::RULE_ALLOW][] = $this->urlPath . Entity::MSG->buildPattern();
	}

	private function addFeeds(): void
	{
		if (empty(Config::$modSettings['xmlnews_enable']))
			return;

		$this->rules['*'][self::RULE_ALLOW][] = $this->urlPath . '/*.xml';
	}

	private function addAssets(): void
	{
		$assets = ['.css$', '.js$', '.png$', '.jpg$', '.gif$'];

		foreach ($assets as $asset) {
			$this->rules['*'][self::RULE_ALLOW][] = '/*' . $asset;
		}
	}

	private function addSitemaps(): void
	{
		$mapFile = file_exists(Config::$boarddir . '/' . SitemapGenerator::XML_FILE)
			? Config::$boardurl . '/' . SitemapGenerator::XML_FILE
			: '';

		$mapGzFile = file_exists(Config::$boarddir . '/' . SitemapGenerator::XML_GZ_FILE)
			? Config::$boardurl . '/' . SitemapGenerator::XML_GZ_FILE
			: '';

		if (! empty($mapFile) || ! empty($mapGzFile)) {
			if (! empty($mapFile)) {
				$this->sitemaps[] = $mapFile;
			}

			if (! empty($mapGzFile)) {
				$this->sitemaps[] = $mapGzFile;
			}
		}
	}

	private function generateContent(): string
	{
		$output = '';

		foreach ($this->rules as $userAgent => $rules) {
			$output .= "User-agent: $userAgent\n";

			foreach ([self::RULE_DISALLOW, self::RULE_ALLOW] as $ruleType) {
				foreach ($rules[$ruleType] as $rule) {
					$output .= $ruleType . ": $rule\n";
				}
			}

			$output .= "\n";
		}

		foreach ($this->sitemaps as $sitemap) {
			$output .= "Sitemap: $sitemap\n";
		}

		return $output;
	}
}
