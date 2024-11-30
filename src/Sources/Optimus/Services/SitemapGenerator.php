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

namespace Bugo\Optimus\Services;

use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Compat\{Config, ErrorHandler};
use Bugo\Compat\{IntegrationHook, Sapi, Theme};
use Bugo\Optimus\Events\AddonEvent;
use Exception;
use League\Event\EventDispatcher;

class SitemapGenerator
{
	public const MAX_ITEMS = 50_000;

	public const MAX_FILESIZE = 50 * 1024 * 1024;

	public array $links = [];

	public function __construct(
		private readonly SitemapDataService    $dataService,
		private readonly FileSystemInterface   $fileSystem,
		private readonly XmlGeneratorInterface $xmlGenerator,
		private readonly EventDispatcher       $dispatcher,
		public readonly int                    $startYear = 0,
	) {}

	public function generate(): bool
	{
		try {
			if (empty(Config::$modSettings['optimus_sitemap_enable']))
				return false;

			$this->initialize();
			$this->removeOldFiles();
			$this->createXml();

			return true;
		} catch (Exception $e) {
			ErrorHandler::log(OP_NAME . ' says: Sitemap generation failed. ' . $e->getMessage(), 'critical');

			return false;
		}
	}

	private function initialize(): void
	{
		@ini_set('opcache.enable', '0');

		Theme::loadEssential();

		Sapi::setTimeLimit();

		Config::$modSettings['disableQueryCheck'] = true;
	}

	private function removeOldFiles(): void
	{
		if (empty(Config::$modSettings['optimus_remove_previous_xml_files']))
			return;

		array_map('unlink', glob(Config::$boarddir . '/sitemap*.xml*'));
	}

	private function createXml(): void
	{
		$maxItems = Config::$modSettings['optimus_sitemap_items_display'] ?? self::MAX_ITEMS;

		$sitemapCounter = 0;

		$getLinks = fn() => yield from $this->getLinks();

		$items = [];
		foreach ($getLinks() as $counter => $entry) {
			if (! empty($counter) && $counter % $maxItems == 0) {
				$sitemapCounter++;
			}

			$items[$sitemapCounter][] = $this->prepareEntry($entry);
		}

		if (empty($items))
			return;

		$this->processItems($items, $sitemapCounter);
	}

	private function prepareEntry(array $entry): array
	{
		$entry['lastmod'] = (int) ($entry['lastmod'] ?? 0);

		return array_merge([
			'loc'        => $entry['loc'],
			'lastmod'    => $entry['lastmod'] ? $this->getDateIso8601($entry['lastmod']) : null,
			'changefreq' => $entry['lastmod'] ? $this->getFrequency($entry['lastmod']) : null,
			'priority'   => $entry['lastmod'] ? $this->getPriority($entry['lastmod']) : null,
		], $this->getImageData($entry));
	}

	private function getImageData(array $entry): array
	{
		return empty($entry['image']) ? [] : ['image:image' => $entry['image']];
	}

	private function processItems(array $items, int $sitemapCounter): void
	{
		if (empty(Config::$modSettings['optimus_main_page_frequency'])) {
			$items[0][0]['changefreq'] = 'always';
		}

		$items[0][0]['priority'] = '1.0';

		if ($sitemapCounter > 0) {
			$this->processMultipleSitemaps($items, $sitemapCounter);
		} else {
			$this->processSingleSitemap($items[0]);
		}
	}

	private function processMultipleSitemaps(array $items, int $sitemapCounter): void
	{
		$gzMaps = [];
		$sitemapIndex = [];

		for ($i = 0; $i <= $sitemapCounter; $i++) {
			if (empty($items[$i]))
				continue;

			$filename = 'sitemap_' . $i . '.xml';

			try {
				$xml = $this->xmlGenerator->generate($items[$i], [
					'mobile' => ! empty(Config::$modSettings['optimus_sitemap_mobile']),
					'images' => ! empty(Config::$modSettings['optimus_sitemap_add_found_images']),
				]);

				$this->prepareContent($xml);

				$this->fileSystem->writeFile($filename, $xml);

				if (function_exists('gzencode') && strlen($xml) > (self::MAX_FILESIZE)) {
					$this->fileSystem->writeGzFile($filename . '.gz', $xml);
					$gzMaps[] = $filename . '.gz';
				}

				$sitemapIndex[] = [
					'loc'     => Config::$boardurl . '/' . $filename,
					'lastmod' => date('Y-m-d'),
				];
			} catch (Exception $e) {
				ErrorHandler::log(OP_NAME . ' says: Error creating ' . $filename . '. ' . $e->getMessage(), 'critical');
			}
		}

		if (empty($sitemapIndex))
			return;

		try {
			$indexXml = $this->xmlGenerator->generate($sitemapIndex, [
				'rootElement' => 'sitemapindex',
				'isIndex'     => true,
			]);

			$this->fileSystem->writeFile('sitemap.xml', $indexXml);

			if (! empty($gzMaps)) {
				$this->fileSystem->writeGzFile('sitemap.xml.gz', $indexXml);
			}
		} catch (Exception $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap index. ' . $e->getMessage(), 'critical');
		}
	}

	private function processSingleSitemap(array $items): void
	{
		try {
			$xml = $this->xmlGenerator->generate($items, [
				'mobile' => ! empty(Config::$modSettings['optimus_sitemap_mobile']),
				'images' => ! empty(Config::$modSettings['optimus_sitemap_add_found_images']),
			]);

			$this->prepareContent($xml);

			$this->fileSystem->writeFile('sitemap.xml', $xml);

			if (function_exists('gzencode') && strlen($xml) > (self::MAX_FILESIZE)) {
				$this->fileSystem->writeGzFile('sitemap.xml.gz', $xml);
			}
		} catch (Exception $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap: ' . $e->getMessage(), 'critical');
		}
	}

	private function getLinks(): array
	{
		$this->links = array_merge($this->dataService->getBoardLinks(), $this->dataService->getTopicLinks());

		// You can add custom links
		$this->dispatcher->dispatch(new AddonEvent(AddonInterface::SITEMAP_LINKS, $this));

		// External integrations
		IntegrationHook::call('integrate_optimus_sitemap_links', [&$this->links]);

		// Adding the main page
		$home = [
			'loc'     => Config::$boardurl . '/',
			'lastmod' => empty(Config::$modSettings['optimus_main_page_frequency'])
				? time()
				: $this->getLastDate($this->links)
		];

		// You can process links with SEF handler
		$this->dispatcher->dispatch(new AddonEvent(AddonInterface::CREATE_SEF_URLS, $this));

		array_unshift($this->links, $home);

		return $this->links;
	}

	private function getLastDate(array $links): int
	{
		if (empty($links))
			return time();

		$data = array_values(array_values($links));

		$dates = [];
		foreach ($data as $value) {
			$dates[] = (int) $value['lastmod'];
		}

		return max($dates);
	}

	private function getDateIso8601(int $timestamp): string
	{
		if (empty($timestamp))
			return '';

		$gmt = substr(date('O', $timestamp), 0, 3) . ':00';

		return date('Y-m-d\TH:i:s', $timestamp) . $gmt;
	}

	private function getFrequency(int $timestamp): string
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

	private function getPriority(int $timestamp): string
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

	private function prepareContent(string &$xml): void
	{
		// Some mods want to rewrite whole content (PrettyURLs)
		$this->dispatcher->dispatch(new AddonEvent(AddonInterface::SITEMAP_CONTENT, new SitemapContent($xml)));
	}
}
