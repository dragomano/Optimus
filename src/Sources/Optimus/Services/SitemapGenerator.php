<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2026 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0
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

	public const GZIP_THRESHOLD = 1024 * 1024;

	public const XML_FILE = 'sitemap.xml';

	public const XML_GZ_FILE = 'sitemap.xml.gz';

	public array $links = [];

	public string $content = '';

	private array $writtenFiles = [];

	public function __construct(
		private readonly SitemapDataService $dataService,
		private readonly FileSystemInterface $fileSystem,
		private readonly XmlGeneratorInterface $xmlGenerator,
		private readonly Dispatcher $dispatcher,
		public readonly int $startYear = 0,
	) {}

	public function generate(): bool
	{
		if (empty(Config::$modSettings['optimus_sitemap_enable'])) {
			return false;
		}

		$this->initialize();

		if (! $this->createXml()) {
			return false;
		}

		$this->removeStaleFiles();

		return true;
	}

	protected function getLinks(): array
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

	private function initialize(): void
	{
		@ini_set('opcache.enable', '0');

		Theme::loadEssential();

		Sapi::setTimeLimit();

		Config::$modSettings['disableQueryCheck'] = true;
	}

	private function createXml(): bool
	{
		$maxItems = (int) (Config::$modSettings['optimus_sitemap_items_display'] ?? 0);
		$maxItems = $maxItems > 0 ? $maxItems : self::MAX_ITEMS;

		$sitemapCounter = 0;

		$items    = [];
		$lastmods = [];
		foreach ($this->getLinks() as $counter => $entry) {
			if (! empty($counter) && $counter % $maxItems === 0) {
				$sitemapCounter++;
			}

			$items[$sitemapCounter][] = $this->prepareEntry($entry);

			$lastmods[$sitemapCounter] = max($lastmods[$sitemapCounter] ?? 0, (int) ($entry['lastmod'] ?? 0));
		}

		// The prepared items hold everything we still need, so the raw links can be released
		$this->links = [];

		if (empty($items))
			return false;

		return $this->processItems($items, $lastmods);
	}

	private function prepareEntry(array $entry): array
	{
		$entry['lastmod'] = (int) ($entry['lastmod'] ?? 0);

		$result = [
			'loc'        => $entry['loc'],
			'lastmod'    => $entry['lastmod'] ? $this->getDateIso8601($entry['lastmod']) : null,
			'changefreq' => $entry['lastmod'] ? Frequency::fromTimestamp($entry['lastmod'])->value : null,
			'priority'   => $entry['lastmod'] ? Priority::fromTimestamp($entry['lastmod'])->value : null,
		];

		if (! empty($entry['image'])) {
			$result['image:image'] = $entry['image'];
		}

		if (! empty($entry['video'])) {
			$result['video:video'] = $entry['video'];
		}

		return $result;
	}

	private function processItems(array $items, array $lastmods): bool
	{
		if (empty(Config::$modSettings['optimus_main_page_frequency'])) {
			$items[0][0]['changefreq'] = Frequency::Always->value;
		}

		$items[0][0]['priority'] = Priority::Supreme->value;

		return count($items) > 1
			? $this->processMultipleSitemaps($items, $lastmods)
			: $this->processSingleSitemap($items[0]);
	}

	private function processMultipleSitemaps(array $items, array $lastmods): bool
	{
		$sitemapIndex = [];

		foreach ($items as $i => $chunk) {
			$filename = 'sitemap_' . $i . '.xml';

			try {
				$this->content = $this->xmlGenerator->generate($chunk, SitemapFeature::getOptions());

				$this->handleContent();

				$this->writeSitemap($filename, $this->content);

				$sitemapIndex[] = [
					'loc'     => Config::$boardurl . '/' . $filename,
					'lastmod' => $this->getDateIso8601(empty($lastmods[$i]) ? time() : $lastmods[$i]),
				];
			} catch (XmlGeneratorException $e) {
				ErrorHandler::log(OP_NAME . ' says: ' . $e->getMessage(), 'critical');
			} catch (FileSystemException $e) {
				ErrorHandler::log(OP_NAME . ' says: Error creating ' . $filename . '. ' . $e->getMessage(), 'critical');
			}
		}

		if (empty($sitemapIndex))
			return false;

		try {
			$this->writeSitemap(self::XML_FILE, $this->xmlGenerator->generate($sitemapIndex, ['isIndex' => true]));
		} catch (XmlGeneratorException $e) {
			ErrorHandler::log(OP_NAME . ' says: ' . $e->getMessage(), 'critical');

			return false;
		} catch (FileSystemException $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap index. ' . $e->getMessage(), 'critical');

			return false;
		}

		return true;
	}

	private function processSingleSitemap(array $items): bool
	{
		try {
			$this->content = $this->xmlGenerator->generate($items, SitemapFeature::getOptions());

			$this->handleContent();

			$this->writeSitemap(self::XML_FILE, $this->content);
		} catch (XmlGeneratorException $e) {
			ErrorHandler::log(OP_NAME . ' says: ' . $e->getMessage(), 'critical');

			return false;
		} catch (FileSystemException $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap. ' . $e->getMessage(), 'critical');

			return false;
		}

		return true;
	}

	/**
	 * @throws FileSystemException
	 */
	private function writeSitemap(string $filename, string $content): void
	{
		$this->fileSystem->writeFile($filename, $content);

		$this->writtenFiles[] = $filename;

		if (strlen($content) < self::GZIP_THRESHOLD)
			return;

		$this->fileSystem->writeGzFile($filename . '.gz', $content);

		$this->writtenFiles[] = $filename . '.gz';
	}

	/**
	 * Drops the files left over from previous runs, keeping the ones we have just published
	 */
	private function removeStaleFiles(): void
	{
		if (empty(Config::$modSettings['optimus_remove_previous_xml_files']))
			return;

		foreach (glob(Config::$boarddir . '/sitemap*.xml*') ?: [] as $file) {
			if (in_array(basename($file), $this->writtenFiles, true))
				continue;

			unlink($file);
		}
	}

	private function getLastDate(array $links): int
	{
		if (empty($links)) {
			return time();
		}

		$data = array_values($links);

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
