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
	public function testTeaser()
	{
		// Replace all <br> and duplicate spaces
		$source = 'foo<br>      bar';
		$this->assertSame('foo bar', Str::teaser($source));

		// Remove all urls
		$source = 'foo https://some.site bar';
		$this->assertSame('foo bar', Str::teaser($source));

		// Additional replacements
		$source = 'foo&nbsp;&quot;bar&quot;&amp;nbsp;';
		$this->assertSame('foo bar', Str::teaser($source));

		// Limits
		$source = 'foo bar. foo bar.';
		$this->assertSame('foo bar.', Str::teaser($source, 1));

		$source = 'foo bar foo bar';
		$this->assertSame('foo...', Str::teaser($source, length: 3));
	}
}