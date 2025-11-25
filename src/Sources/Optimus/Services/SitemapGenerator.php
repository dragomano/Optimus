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

use Bugo\Compat\{Config, ErrorHandler};
use Bugo\Compat\{IntegrationHook, Sapi, Theme};
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Enums\Frequency;
use Bugo\Optimus\Enums\Priority;
use Bugo\Optimus\Enums\SitemapFeature;
use Bugo\Optimus\Events\Dispatcher;

class SitemapGenerator
{
	public const MAX_ITEMS = 50_000;

	public const MAX_FILESIZE = 50 * 1024 * 1024;

	public const XML_FILE = 'sitemap.xml';

	public const XML_GZ_FILE = 'sitemap.xml.gz';

	public array $links = [];

	public string $content = '';

	public function __construct(
		private readonly SitemapDataService    $dataService,
		private readonly FileSystemInterface   $fileSystem,
		private readonly XmlGeneratorInterface $xmlGenerator,
		private readonly Dispatcher            $dispatcher,
		public readonly int                    $startYear = 0,
	) {}

	public function generate(): bool
	{
		if (empty(Config::$modSettings['optimus_sitemap_enable'])) {
			return false;
		}

		$this->initialize();
		$this->removeOldFiles();
		$this->createXml();

		return true;
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
			'changefreq' => $entry['lastmod'] ? Frequency::fromTimestamp($entry['lastmod'])->value : null,
			'priority'   => $entry['lastmod'] ? Priority::fromTimestamp($entry['lastmod'])->value : null,
		], $this->getImageData($entry), $this->getVideoData($entry));
	}

	private function getImageData(array $entry): array
	{
		return empty($entry['image']) ? [] : ['image:image' => $entry['image']];
	}

	private function getVideoData(array $entry): array
	{
		return empty($entry['video']) ? [] : ['video:video' => $entry['video']];
	}

	private function processItems(array $items, int $sitemapCounter): void
	{
		if (empty(Config::$modSettings['optimus_main_page_frequency'])) {
			$items[0][0]['changefreq'] = Frequency::Always->value;
		}

		$items[0][0]['priority'] = Priority::Supreme->value;

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
				$this->content = $this->xmlGenerator->generate($items[$i], SitemapFeature::getOptions());

				$this->handleContent();

				$this->fileSystem->writeFile($filename, $this->content);

				if (function_exists('gzencode') && strlen($this->content) > (self::MAX_FILESIZE)) {
					$this->fileSystem->writeGzFile($filename . '.gz', $this->content);
					$gzMaps[] = $filename . '.gz';
				}

				$sitemapIndex[] = [
					'loc'     => Config::$boardurl . '/' . $filename,
					'lastmod' => date('Y-m-d'),
				];
			} catch (XmlGeneratorException $e) {
				ErrorHandler::log(OP_NAME . ' says: ' . $e->getMessage(), 'critical');
			} catch (FileSystemException $e) {
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

			$this->fileSystem->writeFile(self::XML_FILE, $indexXml);

			if (! empty($gzMaps)) {
				$this->fileSystem->writeGzFile(self::XML_GZ_FILE, $indexXml);
			}
		} catch (XmlGeneratorException $e) {
			ErrorHandler::log(OP_NAME . ' says: ' . $e->getMessage(), 'critical');
		} catch (FileSystemException $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap index. ' . $e->getMessage(), 'critical');
		}
	}

	private function processSingleSitemap(array $items): void
	{
		try {
			$this->content = $this->xmlGenerator->generate($items, SitemapFeature::getOptions());

			$this->handleContent();

			$this->fileSystem->writeFile(self::XML_FILE, $this->content);

			if (function_exists('gzencode') && strlen($this->content) > (self::MAX_FILESIZE)) {
				$this->fileSystem->writeGzFile(self::XML_GZ_FILE, $this->content);
			}
		} catch (XmlGeneratorException $e) {
			ErrorHandler::log(OP_NAME . ' says: ' . $e->getMessage(), 'critical');
		} catch (FileSystemException $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap. ' . $e->getMessage(), 'critical');
		}
	}

	private function getLinks(): array
	{
		$this->links = array_merge($this->dataService->getBoardLinks(), $this->dataService->getTopicLinks());

		// You can add custom links
		$this->dispatcher->dispatchEvent(AddonInterface::SITEMAP_LINKS, $this);

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
		$this->dispatcher->dispatchEvent(AddonInterface::CREATE_SEF_URLS, $this);

		array_unshift($this->links, $home);

		return $this->links;
	}

	private function getLastDate(array $links): int
	{
		if (empty($links)) {
			return time();
		}

		$data = array_values(array_values($links));

		$dates = [];
		foreach ($data as $value) {
			$dates[] = (int) $value['lastmod'];
		}

		return max($dates);
	}

	private function getDateIso8601(int $timestamp): string
	{
		if (empty($timestamp)) {
			return '';
		}

		$gmt = substr(date('O', $timestamp), 0, 3) . ':00';

		return date('Y-m-d\TH:i:s', $timestamp) . $gmt;
	}

	private function handleContent(): void
	{
		// Some mods want to rewrite whole content (PrettyURLs)
		$this->dispatcher->dispatchEvent(AddonInterface::SITEMAP_CONTENT, $this);
	}
}
