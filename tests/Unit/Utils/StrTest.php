<?php declare(strict_types=1);

use Bugo\Optimus\Utils\Str;

it('checks teaser with br and spaces', function () {
	$source = 'foo<br>      bar';

	expect(Str::teaser($source))->toBe('foo bar');
});

it('checks teaser with urls', function () {
	$source = 'foo https://some.site bar';

	expect(Str::teaser($source))->toBe('foo bar');
});

it('checks teaser with replacements', function () {
	$source = 'foo&nbsp;&quot;bar&quot;&amp;nbsp;';

	expect(Str::teaser($source))->toBe('foo bar');
});

it('checks teaser with limits', function () {
	$source = 'foo bar. foo bar.';

	expect(Str::teaser($source, 1))->toBe('foo bar.');

	$source = 'foo bar foo bar';

	expect(Str::teaser($source, length: 3))->toBe('foo...');
});

it('checks teaser with multiple sentences', function () {
	$source = 'First sentence. Second sentence. Third sentence.';

	expect(Str::teaser($source, 2))->toBe('First sentence. Second sentence.');
});

it('checks teaser with empty string', function () {
	expect(Str::teaser(''))->toBe('');
});

it('creates html element', function () {
	$el = Str::html('div');

	expect($el)->toBeInstanceOf(\Nette\Utils\Html::class);
	expect($el->getName())->toBe('div');
});

it('creates html element with params', function () {
	$el = Str::html('div', ['class' => 'test', 'id' => 'mydiv']);

	expect($el)->toBeInstanceOf(\Nette\Utils\Html::class);
	expect($el->getAttribute('class'))->toBe('test');
	expect($el->getAttribute('id'))->toBe('mydiv');
});
