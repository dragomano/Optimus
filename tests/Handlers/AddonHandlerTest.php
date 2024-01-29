<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\AddonHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class AddonHandlerTest extends AbstractBase
{
	/**
	 * @covers AddonHandler::subscribeListeners
	 */
	public function testSubscribeListeners()
	{
		$this->assertTrue(
			method_exists(AddonHandler::class, 'subscribeListeners')
		);
	}

}