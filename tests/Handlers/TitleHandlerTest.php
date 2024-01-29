<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\TitleHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class TitleHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		global $board_info, $context;

		parent::setUp();

		$this->handler = new TitleHandler();

		$board_info['name'] = 'lol';

		$context['forum_name'] = 'foo';

		$context['page_title_html_safe'] = 'bar';
	}

	protected function tearDown(): void
	{
		global $context, $board_info;

		unset($context['page_title_html_safe']);

		unset($context['first_message']);

		unset($board_info['total_topics']);
	}

	/**
	 * @covers TitleHandler::handle
	 */
	public function testHandleBoardTitlesWithFirstOption()
	{
		global $modSettings, $board_info, $context;

		$modSettings['optimus_board_extend_title'] = 1;

		$board_info['total_topics'] = 10;

		$this->handler->handle();

		$this->assertSame('foo - bar', $context['page_title_html_safe']);
	}

	/**
	 * @covers TitleHandler::handle
	 */
	public function testHandleBoardTitlesWithSecondOption()
	{
		global $modSettings, $board_info, $context;

		$modSettings['optimus_board_extend_title'] = 2;

		$board_info['total_topics'] = 10;

		$this->handler->handle();

		$this->assertSame('bar - foo', $context['page_title_html_safe']);
	}

	/**
	 * @covers TitleHandler::handle
	 */
	public function testHandleTopicTitlesWithFirstOption()
	{
		global $modSettings, $context;

		$modSettings['optimus_topic_extend_title'] = 1;

		$context['first_message'] = 1;

		$this->handler->handle();

		$this->assertSame('foo - lol - bar', $context['page_title_html_safe']);
	}

	/**
	 * @covers TitleHandler::handle
	 */
	public function testHandleTopicTitlesWithSecondOption()
	{
		global $modSettings, $context;

		$modSettings['optimus_topic_extend_title'] = 2;

		$context['first_message'] = 1;

		$this->handler->handle();

		$this->assertSame('bar - lol - foo', $context['page_title_html_safe']);
	}
}