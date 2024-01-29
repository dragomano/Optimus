<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\ErrorHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class ErrorHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		global $context;

		parent::setUp();

		$this->handler = new ErrorHandler();

		$context['error_link'] = '';
	}

	/**
	 * @covers BoardHandler::fallbackAction
	 */
	public function testFallbackAction()
	{
		global $modSettings, $context;

		$modSettings['optimus_errors_for_wrong_actions'] = true;

		$this->handler->fallbackAction();

		$this->assertSame('javascript:history.go(-1)', $context['error_link']);
	}

	/**
	 * @covers BoardHandler::fallbackAction
	 */
	public function testFallbackActionWithDisabledSetting()
	{
		global $modSettings, $context;

		$modSettings['optimus_errors_for_wrong_actions'] = false;

		$this->handler->fallbackAction();

		$this->assertEmpty($context['error_link']);
	}

	/**
	 * @covers BoardHandler::handleStatusErrors
	 */
	public function testHandleStatusErrorsExist()
	{
		global $modSettings, $board_info, $context;

		$modSettings['optimus_errors_for_wrong_boards_topics'] = true;

		$board_info['error'] = 'exist';

		$this->handler->handleStatusErrors();

		$this->assertSame('javascript:history.go(-1)', $context['error_link']);
	}

	/**
	 * @covers BoardHandler::handleStatusErrors
	 */
	public function testHandleStatusErrorsAccess()
	{
		global $modSettings, $board_info, $context;

		$modSettings['optimus_errors_for_wrong_boards_topics'] = true;

		$board_info['error'] = 'access';

		$this->handler->handleStatusErrors();

		$this->assertSame('javascript:history.go(-1)', $context['error_link']);
	}

	/**
	 * @covers BoardHandler::handleStatusErrors
	 */
	public function testHandleStatusErrorsWithDisabledSetting()
	{
		global $modSettings, $context;

		$modSettings['optimus_errors_for_wrong_boards_topics'] = false;

		$this->handler->handleStatusErrors();

		$this->assertEmpty($context['error_link']);
	}
}