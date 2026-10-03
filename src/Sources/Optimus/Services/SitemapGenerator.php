<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2026 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.1
 */

namespace Bugo\Optimus\Services;

use Bugo\Compat\{Config, ErrorHandler};
use Bugo\Compat\{IntegrationHook, Sapi, Theme};
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Enums\Frequency;
use Bugo\Optimus\Enums\Priority;
use Bugo\Optimus\Enums\SitemapFeature;
use Bugo\Optimus\Events\Dispatcher;
use Generator;

class SitemapGenerator
{
	public const MAX_ITEMS = 50_000;

	public const GZIP_THRESHOLD = 1024 * 1024;

	public const XML_FILE = 'sitemap.xml';

	public const XML_GZ_FILE = 'sitemap.xml.gz';

	/**
	 * Custom links added by addons via the SITEMAP_LINKS event and the integrate_optimus_sitemap_links hook
	 */
	public array $links = [];

	/**
	 * The content of the sitemap currently being generated (available in SITEMAP_CONTENT handlers)
	 */
	public string $content = '';

	private array $writtenFiles = [];

	/**
	 * @var callable[] Per-URL rewriters registered via the SITEMAP_URL_REWRITER event
	 */
	private array $urlRewriters = [];

	private bool $legacySefMode = false;

	private int $maxLastmod = 0;

	private int $chunkCount = 0;

	private string $firstChunkContent = '';

	private int $firstChunkLastmod = 0;

	/**
	 * @var array[] Index entries of the already written chunks (except the first one)
	 */
	private array $indexEntries = [];

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

		$result = $this->createXml();

		if ($result) {
			$this->removeStaleFiles();
		}

		return $result;
	}

	public function getStartDate(): int
	{
		return $this->dataService->getStartDate();
	}

	/**
	 * Registers a rewriter that is applied to every URL flowing into the sitemap
	 */
	public function addUrlRewriter(callable $rewriter): void
	{
		$this->urlRewriters[] = $rewriter;
	}

	/**
	 * Streams all the links of the future sitemap one by one
	 */
	protected function getLinkStream(): Generator
	{
		// The lastmod of the home page can depend on the whole stream,
		// so when a fixed frequency is set, it goes last
		if (! $this->isFixedHomeFrequency()) {
			yield $this->getHomeLink();
		}

		if ($this->legacySefMode) {
			// Legacy listeners expect the complete list of links inside $this->links
			$this->links = array_merge(
				iterator_to_array($this->dataService->getBoardLinks()),
				$this->links,
				iterator_to_array($this->dataService->getTopicLinks())
			);

			$this->dispatcher->dispatchEvent(AddonInterface::CREATE_SEF_URLS, $this);

			yield from $this->links;
		} else {
			yield from $this->rewriteUrls($this->dataService->getBoardLinks());
			yield from $this->rewriteUrls($this->links);
			yield from $this->rewriteUrls($this->dataService->getTopicLinks());
		}

		if ($this->isFixedHomeFrequency()) {
			yield $this->getHomeLink();
		}
	}

	private function collectCustomLinks(): void
	{
		$this->links = [];

		// You can add custom links
		$this->dispatcher->dispatchEvent(AddonInterface::SITEMAP_LINKS, $this);

		// External integrations
		IntegrationHook::call('integrate_optimus_sitemap_links', [&$this->links]);
	}

	private function registerUrlRewriters(): void
	{
		// You can register a rewriter for every sitemap URL
		$this->dispatcher->dispatchEvent(AddonInterface::SITEMAP_URL_REWRITER, $this);

		$this->legacySefMode = $this->dispatcher->hasListeners(AddonInterface::CREATE_SEF_URLS, $this);
	}

	private function rewriteUrls(iterable $links): Generator
	{
		foreach ($links as $link) {
			foreach ($this->urlRewriters as $rewriter) {
				$link['loc'] = $rewriter($link['loc']);
			}

			yield $link;
		}
	}

	private function isFixedHomeFrequency(): bool
	{
		return ! empty(Config::$modSettings['optimus_main_page_frequency']);
	}

	private function getHomeLink(): array
	{
		return [
			'loc'     => Config::$boardurl . '/',
			'lastmod' => $this->isFixedHomeFrequency()
				? ($this->maxLastmod ?: time())
				: time(),
			'is_home' => true,
		];
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
		$this->collectCustomLinks();
		$this->registerUrlRewriters();

		$maxItems = (int) (Config::$modSettings['optimus_sitemap_items_display'] ?? 0);
		$maxItems = $maxItems > 0 ? $maxItems : self::MAX_ITEMS;

		$entries      = [];
		$chunkLastmod = 0;

		foreach ($this->getLinkStream() as $link) {
			$lastmod = (int) ($link['lastmod'] ?? 0);

			$this->maxLastmod = max($this->maxLastmod, $lastmod);

			$entries[] = $this->prepareEntry($link);

			$chunkLastmod = max($chunkLastmod, $lastmod);

			if (count($entries) >= $maxItems) {
				$this->processChunk($entries, $chunkLastmod);

				$entries      = [];
				$chunkLastmod = 0;
			}
		}

		if (! empty($entries)) {
			$this->processChunk($entries, $chunkLastmod);
		}

		if ($this->chunkCount === 0) {
			return false;
		}

		return $this->finalizeSitemaps();
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

		if (! empty($entry['is_home'])) {
			$result['priority'] = Priority::Supreme->value;

			if (! $this->isFixedHomeFrequency()) {
				$result['changefreq'] = Frequency::Always->value;
			}
		}

		if (! empty($entry['image'])) {
			$result['image:image'] = $entry['image'];
		}

		if (! empty($entry['video'])) {
			$result['video:video'] = $entry['video'];
		}

		return $result;
	}

	private function processChunk(array $entries, int $chunkLastmod): void
	{
		$index = $this->chunkCount;

		try {
			$this->content = $this->xmlGenerator->generate($entries, SitemapFeature::getOptions());

			$this->handleContent();
		} catch (XmlGeneratorException $e) {
			ErrorHandler::log(OP_NAME . ' says: ' . $e->getMessage(), 'critical');

			return;
		}

		// The name of the very first chunk depends on the total number of chunks,
		// so its content is only kept until the end of generation
		if ($index === 0) {
			$this->firstChunkContent = $this->content;
			$this->firstChunkLastmod = $chunkLastmod;

			$this->chunkCount++;

			return;
		}

		try {
			$this->writeSitemap('sitemap_' . $index . '.xml', $this->content);

			$this->indexEntries[] = [
				'loc'     => Config::$boardurl . '/sitemap_' . $index . '.xml',
				'lastmod' => $chunkLastmod,
			];
		} catch (FileSystemException $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap_' . $index . '.xml. ' . $e->getMessage(), 'critical');
		}

		$this->chunkCount++;
	}

	private function finalizeSitemaps(): bool
	{
		if ($this->chunkCount === 1) {
			try {
				$this->writeSitemap(self::XML_FILE, $this->firstChunkContent);
			} catch (FileSystemException $e) {
				ErrorHandler::log(OP_NAME . ' says: Error creating sitemap. ' . $e->getMessage(), 'critical');

				return false;
			}

			return true;
		}

		try {
			$this->writeSitemap('sitemap_0.xml', $this->firstChunkContent);

			array_unshift($this->indexEntries, [
				'loc'     => Config::$boardurl . '/sitemap_0.xml',
				'lastmod' => $this->firstChunkLastmod,
			]);
		} catch (FileSystemException $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap_0.xml. ' . $e->getMessage(), 'critical');
		}

		try {
			$indexEntries = array_map(fn(array $entry): array => [
				'loc'     => $entry['loc'],
				'lastmod' => $this->getDateIso8601($entry['lastmod'] ?: time()),
			], $this->indexEntries);

			$this->writeSitemap(self::XML_FILE, $this->xmlGenerator->generate($indexEntries, ['isIndex' => true]));
		} catch (XmlGeneratorException $e) {
			ErrorHandler::log(OP_NAME . ' says: ' . $e->getMessage(), 'critical');

			return false;
		} catch (FileSystemException $e) {
			ErrorHandler::log(OP_NAME . ' says: Error creating sitemap index. ' . $e->getMessage(), 'critical');

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
