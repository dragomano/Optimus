<?php declare(strict_types=1);

namespace Tests;

use Bugo\Optimus\Prime;

/**
 * @requires PHP 8.0
 */
class PrimeTest extends AbstractBase
{
	protected function setUp(): void
	{
		parent::setUp();

		$this->prime = new Prime();
	}

	/**
	 * @covers Prime::loadTheme
	 */
    public function testLoadTheme()
    {
		global $txt;

	    $this->prime->loadTheme();

		$this->assertSame('Search Engine Optimization', $txt['optimus_title']);
    }

	/**
	 * @covers Prime::credits
	 */
	public function testCredits()
	{
		global $context;

		$this->prime->credits();

		$this->assertNotEmpty($context['credits_modifications']);
	}
}