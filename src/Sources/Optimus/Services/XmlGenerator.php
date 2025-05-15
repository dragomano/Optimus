<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC4
 */

namespace Bugo\Optimus\Services;

use Bugo\Optimus\Enums\Action;
use DOMException;
use Spatie\ArrayToXml\ArrayToXml;

class XmlGenerator implements XmlGeneratorInterface
{
	private const DEFAULT_ROOT = [
		'rootElementName' => 'urlset',
		'_attributes' => [
			'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9'
		]
	];

	private const INDEX_ROOT = [
		'rootElementName' => 'sitemapindex',
		'_attributes' => [
			'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9'
		]
	];

	public function __construct(private readonly string $scripturl) {}

	public function generate(array $data, array $options = []): string
	{
		try {
			$root = $this->prepareRootElement($options);
			$array = $this->prepareData($data, $options);

			$arrayToXml = new ArrayToXml($array, $root, true, 'UTF-8');
			$this->addStylesheet($arrayToXml);

			return $arrayToXml->prettify()->toXml();
		} catch (DOMException $e) {
			throw new XmlGeneratorException('Failed to generate sitemap xml: ' . $e->getMessage());
		}
	}

	private function prepareRootElement(array $options): array
	{
		$isIndex = $options['isIndex'] ?? false;
		$root = $isIndex ? self::INDEX_ROOT : self::DEFAULT_ROOT;

		if (! $isIndex) {
			if ($options['mobile'] ?? false) {
				$root['_attributes']['xmlns:mobile'] = 'http://www.google.com/schemas/sitemap-mobile/1.0';
			}

			if ($options['images'] ?? false) {
				$root['_attributes']['xmlns:image'] = 'http://www.google.com/schemas/sitemap-image/1.1';
			}

			if ($options['videos'] ?? false) {
				$root['_attributes']['xmlns:video'] = 'http://www.google.com/schemas/sitemap-video/1.1';
			}
		}

		return $root;
	}

	/**
	 * @throws DOMException
	 */
	private function prepareData(array $data, array $options): array
	{
		$isIndex = $options['isIndex'] ?? false;
		$rootElement = $isIndex ? 'sitemap' : 'url';

		return [
			$rootElement => array_map(fn($item) => $this->prepareItem($item, $isIndex), $data)
		];
	}

	/**
	 * @throws DOMException
	 */
	private function prepareItem(array $item, bool $isIndex): array
	{
		if (empty($item['loc'])) {
			throw new DOMException('URL of the item is requred!');
		}

		$prepared = [
			'loc' => $item['loc'],
		];

		if (isset($item['lastmod'])) {
			$prepared['lastmod'] = $item['lastmod'];
		}

		if (! $isIndex) {
			if (isset($item['changefreq'])) {
				$prepared['changefreq'] = $item['changefreq'];
			}

			if (isset($item['priority'])) {
				$prepared['priority'] = $item['priority'];
			}

			if (isset($item['mobile:mobile'])) {
				$prepared['mobile:mobile'] = $item['mobile:mobile'];
			}

			if (isset($item['image:image'])) {
				$prepared['image:image'] = $item['image:image'];
			}

			if (isset($item['video:video'])) {
				$prepared['video:video'] = $item['video:video'];
			}
		}

		return array_filter($prepared);
	}

	private function addStylesheet(ArrayToXml $arrayToXml): void
	{
		$arrayToXml->addProcessingInstruction(
			'xml-stylesheet',
			'type="text/xsl" href="' . $this->scripturl . '?action=' . Action::XSL->value . '"'
		);
	}
}
