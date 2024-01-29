<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\FrontPageHandler;
use Bugo\Optimus\Utils\Input;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class FrontPageHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		global $settings;

		parent::setUp();

		$this->handler = new FrontPageHandler();

		$settings['og_image'] = '';
	}

	/**
	 * @covers FrontPageHandler::changeTitle
	 */
	public function testChangeTitle()
	{
		global $modSettings, $txt;

		$modSettings['optimus_forum_index'] = 'bar';

		$this->handler->changeTitle();

		$this->assertSame('bar', $txt['forum_index']);
	}

	/**
	 * @covers FrontPageHandler::changeTitle
	 */
	public function testChangeTitleWithDisabledSetting()
	{
		global $modSettings, $txt;

		$modSettings['optimus_forum_index'] = false;

		$txt['forum_index'] = '';

		$this->handler->changeTitle();

		$this->assertEmpty($txt['forum_index']);
	}

	/**
	 * @covers FrontPageHandler::addDescription
	 */
	public function testAddDescription()
	{
		global $modSettings, $context;

		$modSettings['optimus_description'] = 'bar';

		$context['meta_description'] = $context['current_action'] = '';

		$this->handler->addDescription();

		$this->assertSame(
			Input::xss($modSettings['optimus_description']),
			$context['meta_description']
		);
	}

	/**
	 * @covers FrontPageHandler::addDescription
	 */
	public function testAddDescriptionWithDisabledSetting()
	{
		global $modSettings, $context;

		$modSettings['optimus_description'] = false;

		$context['meta_description'] = '';

		$this->handler->addDescription();

		$this->assertEmpty($context['meta_description']);
	}
}