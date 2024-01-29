<?php declare(strict_types=1);

namespace Tests\Utils;

use Tests\AbstractBase;
use Bugo\Optimus\Utils\Str;

/**
 * @requires PHP 8.0
 */
class StrTest extends AbstractBase
{
	/**
	 * @covers Str::teaser
	 */
	public function testTeaserWithBrAndSpaces()
	{
		$source = 'foo<br>      bar';
		$this->assertSame('foo bar', Str::teaser($source));
	}

	/**
	 * @covers Str::teaser
	 */
	public function testTeaserWithUrls()
	{
		$source = 'foo https://some.site bar';
		$this->assertSame('foo bar', Str::teaser($source));
	}

	/**
	 * @covers Str::teaser
	 */
	public function testTeaserWithReplacements()
	{
		$source = 'foo&nbsp;&quot;bar&quot;&amp;nbsp;';
		$this->assertSame('foo bar', Str::teaser($source));
	}

	/**
	 * @covers Str::teaser
	 */
	public function testTeaserWithLimits()
	{
		$source = 'foo bar. foo bar.';
		$this->assertSame('foo bar.', Str::teaser($source, 1));

		$source = 'foo bar foo bar';
		$this->assertSame('foo...', Str::teaser($source, length: 3));
	}
}