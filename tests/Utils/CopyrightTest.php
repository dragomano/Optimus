<?php declare(strict_types=1);

namespace Tests\Utils;

use Tests\AbstractBase;
use Bugo\Optimus\Utils\Copyright;

/**
 * @requires PHP 8.0
 */
class CopyrightTest extends AbstractBase
{
	/**
	 * @covers Copyright::getLink
	 */
    public function testGetLink()
    {
        $link = Copyright::getLink();

		$this->assertStringContainsString(
			'https://custom.simplemachines.org/mods/index.php?mod=2659',
			$link
		);
    }

	/**
	 * @covers Copyright::getYears
	 */
	public function testGetYears()
	{
		$years = Copyright::getYears();

		$this->assertSame(
			' &copy; 2010&ndash;' . date('Y') . ', Bugo',
			$years
		);
	}
}