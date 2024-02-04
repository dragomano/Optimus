<?php declare(strict_types=1);

use Bugo\Optimus\Utils\Str;

it('checks teaser with br and spaces', function () {
	$source = 'foo<br>      bar';

	expect(Str::teaser($source))
		->toBe('foo bar');
});

it('checks teaser with urls', function () {
	$source = 'foo https://some.site bar';

	expect(Str::teaser($source))
		->toBe('foo bar');
});

it('checks teaser with replacements', function () {
	$source = 'foo&nbsp;&quot;bar&quot;&amp;nbsp;';

	expect(Str::teaser($source))
		->toBe('foo bar');
});

it('checks teaser with limits', function () {
	$source = 'foo bar. foo bar.';

	expect(Str::teaser($source, 1))
		->toBe('foo bar.');

	$source = 'foo bar foo bar';

	expect(Str::teaser($source, length: 3))
		->toBe('foo...');
});
