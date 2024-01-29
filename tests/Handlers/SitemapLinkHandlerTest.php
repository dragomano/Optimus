<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\SitemapLinkHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class SitemapLinkHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		parent::setUp();

		$this->handler = new SitemapLinkHandler();
	}

	/**
	 * @covers SitemapLinkHandler::actions
	 */
	public function testActions()
	{
		$actions = [];

		$this->handler->actions($actions);

		$this->assertArrayHasKey('sitemap_xsl', $actions);
	}

	/**
	 * @covers SitemapLinkHandler::xsl
	 */
	public function testXsl()
	{
		$this->assertTrue(
			method_exists(SitemapLinkHandler::class, 'xsl')
		);
	}

	/**
	 * @covers SitemapLinkHandler::preLogStats
	 */
	public function testPreLogStats()
	{
		$no_stat_actions = [];

		$this->handler->preLogStats($no_stat_actions);

		$this->assertArrayHasKey('sitemap_xsl', $no_stat_actions);
	}

	/**
	 * @covers SitemapLinkHandler::addLink
	 */
	public function testAddLink()
	{
		global $modSettings, $txt, $forum_copyright, $context;

		$modSettings['optimus_sitemap_link'] = true;

		$txt['optimus_sitemap_title'] = 'foo';

		$context['html_headers'] = '';

		$this->handler->addLink();

		$this->assertStringContainsString($txt['optimus_sitemap_title'], $forum_copyright);
		$this->assertNotEmpty($context['html_headers']);
	}
}