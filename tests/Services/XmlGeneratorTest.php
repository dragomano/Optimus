<?php declare(strict_types=1);

use Bugo\Optimus\Services\XmlGenerator;
use Bugo\Optimus\Services\XmlGeneratorException;

beforeEach(function () {
	$this->xmlGenerator = new XmlGenerator('https://example.com');
});

it('generates XML from array with default root element', function () {
	$data = [
		['loc' => 'https://example.com/item1', 'lastmod' => date('Y-m-d')],
		['loc' => 'https://example.com/item2', 'lastmod' => date('Y-m-d')],
	];

	$xml = $this->xmlGenerator->generate($data);

	expect($xml)->toContain('<?xml version="1.0" encoding="UTF-8"?>')
		->and($xml)->toContain('<?xml-stylesheet type="text/xsl" href="https://example.com?action=sitemap_xsl"?>')
		->and($xml)->toContain('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
		->and($xml)->toContain('<loc>https://example.com/item1</loc>')
		->and($xml)->toContain('<lastmod>' . date('Y-m-d') . '</lastmod>')
		->and($xml)->toContain('<loc>https://example.com/item2</loc>')
		->and($xml)->toContain('<lastmod>' . date('Y-m-d') . '</lastmod>')
		->and($xml)->toContain('</urlset>');
});

it('generates XML with custom root element', function () {
	$data = [
		['loc' => 'https://example.com/item1', 'lastmod' => date('Y-m-d')],
	];

	$xml = $this->xmlGenerator->generate($data, ['isIndex' => true]);

	expect($xml)->toContain('<?xml version="1.0" encoding="UTF-8"?>')
		->and($xml)->toContain('<?xml-stylesheet type="text/xsl" href="https://example.com?action=sitemap_xsl"?>')
		->and($xml)->toContain('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
		->and($xml)->toContain('<loc>https://example.com/item1</loc>')
		->and($xml)->toContain('<lastmod>' . date('Y-m-d') . '</lastmod>')
		->and($xml)->toContain('</sitemapindex>');
});

it('generates XML with extended data types', function () {
	$data = [
		[
			'loc' => 'https://example.com/item1',
			'image:image' => ['image:loc' => 'https://example.com/image.png'],
			'mobile:mobile' => ['mobile:loc' => 'https://example.com/mobile'],
		],
	];

	$xml = $this->xmlGenerator->generate($data, ['mobile' => true, 'images' => true]);

	expect($xml)->toContain('http://www.google.com/schemas/sitemap-mobile/1.0')
		->and($xml)->toContain('http://www.google.com/schemas/sitemap-image/1.1')
		->and($xml)->toContain('<image:loc>https://example.com/image.png</image:loc>')
		->and($xml)->toContain('<mobile:loc>https://example.com/mobile</mobile:loc>');
});

it('generates empty XML node for invalid data', function () {
	$data = [['one', 'two', 'three']];

	expect($this->xmlGenerator->generate($data, ['isIndex' => true]))
		->toThrow(XmlGeneratorException::class);
})->throws(XmlGeneratorException::class);
