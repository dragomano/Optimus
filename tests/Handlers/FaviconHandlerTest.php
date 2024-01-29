<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\FaviconHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class FaviconHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		global $settings;

		parent::setUp();

		$this->handler = new FaviconHandler();

		$settings['og_image'] = '';
	}

	/**
	 * @covers FaviconHandler::handle
	 */
	public function testHandle()
	{
		global $modSettings, $context;

		$modSettings['optimus_favicon_text'] = 'bar';

		$context['html_headers'] = '';

		$this->handler->handle();

		$this->assertStringContainsString('bar', $context['html_headers']);
	}

	/**
	 * @covers FaviconHandler::handle
	 */
	public function testHandleWithDisabledSetting()
	{
		global $modSettings, $context;

		$modSettings['optimus_favicon_text'] = false;

		$context['html_headers'] = '';

		$this->handler->handle();

		$this->assertEmpty($context['html_headers']);
	}
}