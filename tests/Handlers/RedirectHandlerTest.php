<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\RedirectHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class RedirectHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		parent::setUp();

		$this->handler = new RedirectHandler();
	}

	/**
	 * @covers RedirectHandler::handle
	 */
	public function testHandle()
	{
		$this->assertTrue(
			method_exists(RedirectHandler::class, 'handle')
		);
	}
}