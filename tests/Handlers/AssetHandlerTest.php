<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\AssetHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class AssetHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		parent::setUp();

		$this->handler = new AssetHandler();

		$_SERVER['HTTP_USER_AGENT'] = 'Unknown';
	}

	/**
	 * @covers AssetHandler::handle
	 */
	public function testHandleForLighthouse()
	{
		global $context, $modSettings;

		$_SERVER['HTTP_USER_AGENT'] = 'Lighthouse';

		$context['html_headers'] = '';

		$modSettings['optimus_head_code'] = 'bar';

		$this->handler->handle();

		$this->assertEmpty($context['html_headers']);
	}

	/**
	 * @covers AssetHandler::handle
	 */
	public function testHandleWithRequestXml()
	{
		global $context, $modSettings;

		$this->request->request->set('xml', true);
		$this->request->overrideGlobals();

		$context['html_headers'] = '';

		$modSettings['optimus_head_code'] = 'bar';

		$this->handler->handle();

		$this->assertEmpty($context['html_headers']);

		$this->request->request->remove('xml');
	}

	/**
	 * @covers AssetHandler::handle
	 */
	public function testHandleWithHeadCode()
	{
		global $context, $modSettings;

		$context['html_headers'] = '';

		$context['current_action'] = 'forum';

		$modSettings['optimus_head_code'] = 'bar';

		$this->handler->handle();

		$this->assertStringContainsString('bar', $context['html_headers']);
	}

	/**
	 * @covers AssetHandler::handle
	 */
	public function testHandleWithStatCode()
	{
		global $context, $modSettings;

		$context['insert_after_template'] = '';

		$context['current_action'] = 'forum';

		$modSettings['optimus_stat_code'] = 'bar';

		$this->handler->handle();

		$this->assertStringContainsString('bar', $context['insert_after_template']);
	}

	/**
	 * @covers AssetHandler::handle
	 */
	public function testHandleWithCountCode()
	{
		global $context, $modSettings;

		$context['template_layers'] = $context['css_header'] = [];

		$context['current_action'] = 'forum';

		$modSettings['optimus_count_code'] = 'bar';

		$modSettings['optimus_counters_css'] = 'bar';

		$this->handler->handle();

		$this->assertContains('footer_counters', $context['template_layers']);

		$this->assertContains('bar', $context['css_header']);
	}

	/**
	 * @covers AssetHandler::handle
	 */
	public function testHandleWithCountCodeWithoutCss()
	{
		global $context, $modSettings;

		$context['template_layers'] = $context['css_header'] = [];

		$context['current_action'] = 'forum';

		$modSettings['optimus_count_code'] = 'bar';

		$modSettings['optimus_counters_css'] = false;

		$this->handler->handle();

		$this->assertContains('footer_counters', $context['template_layers']);

		$this->assertEmpty($context['css_header']);
	}
}