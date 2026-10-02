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

use Bugo\Optimus\Enums\Action;
use ValueError;
use XMLWriter;

class XmlGenerator implements XmlGeneratorInterface
{
	private const SITEMAP_ROOT = [
		'name'       => 'urlset',
		'item'       => 'url',
		'attributes' => [
			'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
		],
	];

	private const INDEX_ROOT = [
		'name'       => 'sitemapindex',
		'item'       => 'sitemap',
		'attributes' => [
			'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
		],
	];

	private const NAMESPACES = [
		'mobile' => ['mobile', 'http://www.google.com/schemas/sitemap-mobile/1.0'],
		'images' => ['image', 'http://www.google.com/schemas/sitemap-image/1.1'],
		'videos' => ['video', 'http://www.google.com/schemas/sitemap-video/1.1'],
	];

	public function __construct(private readonly string $scripturl) {}

	/**
	 * @throws XmlGeneratorException
	 */
	public function generate(array $data, array $options = []): string
	{
		$isIndex = (bool) ($options['isIndex'] ?? false);
		$root    = $isIndex ? self::INDEX_ROOT : self::SITEMAP_ROOT;

		$writer = new XMLWriter();
		$writer->openMemory();
		$writer->startDocument('1.0', 'UTF-8');
		$writer->setIndent(true);
		$writer->setIndentString('  ');

		$writer->writePI(
			'xml-stylesheet',
			'type="text/xsl" href="' . $this->scripturl . '?action=' . Action::XSL->value . '"'
		);

		$writer->startElement($root['name']);
		foreach ($root['attributes'] as $name => $value) {
			$writer->writeAttribute($name, $value);
		}

		if (! $isIndex) {
			foreach (self::NAMESPACES as $option => [$prefix, $uri]) {
				if (! empty($options[$option])) {
					$writer->writeAttribute('xmlns:' . $prefix, $uri);
				}
			}
		}

		foreach ($data as $item) {
			$this->writeItem($writer, $item, $root['item']);
		}

		$writer->endElement();
		$writer->endDocument();

		return $writer->outputMemory();
	}

	/**
	 * @throws XmlGeneratorException
	 */
	private function writeItem(XMLWriter $writer, array $item, string $name): void
	{
		if (empty($item['loc'])) {
			throw new XmlGeneratorException('URL of the item is required!');
		}

		$writer->startElement($name);

		foreach (array_filter($item) as $key => $value) {
			$this->writeElement($writer, (string) $key, $value);
		}

		$writer->endElement();
	}

	/**
	 * @throws XmlGeneratorException
	 */
	private function writeElement(XMLWriter $writer, string $name, mixed $value): void
	{
		try {
			$started = $writer->startElement($name);
		} catch (ValueError) {
			$started = false;
		}

		if (! $started) {
			throw new XmlGeneratorException('Failed to generate sitemap xml: invalid element ' . $name);
		}

		if (is_array($value)) {
			foreach ($value as $key => $child) {
				if (is_string($key)) {
					$this->writeElement($writer, $key, $child);
				}
			}
		} else {
			$writer->text((string) $value);
		}

		$writer->endElement();
	}
}
