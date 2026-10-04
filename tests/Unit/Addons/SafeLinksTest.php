<?php declare(strict_types=1);

use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\SafeLinks;
use Bugo\Optimus\Events\AddonEvent;

beforeEach(function () {
	$GLOBALS['test_integration_hooks'] = [];
});

afterEach(function () {
	Mockery::close();
});

test('metadata', function () {
	expect(SafeLinks::PACKAGE_ID)->toBe('Optimus:SafeLinks')
		->and(SafeLinks::$events)->toBe([AddonInterface::HOOK_EVENT]);
});

test('__invoke registers the bbc codes hook', function () {
	(new SafeLinks())(new AddonEvent(AddonInterface::HOOK_EVENT, null));

	$hooks = array_column($GLOBALS['test_integration_hooks'] ?? [], 0);

	expect($hooks)->toContain('integrate_bbc_codes');
});

test('changes attributes of url links', function () {
	$codes = [
		['tag' => 'url', 'type' => 'unparsed_content', 'content' => '<a href="{url}" rel="noopener">{url}</a>'],
		['tag' => 'url', 'type' => 'unparsed_equals', 'before' => '<a href="$1" rel="noopener">', 'after' => '</a>'],
		['tag' => 'b', 'type' => 'unparsed_content', 'content' => 'rel="noopener"'],
	];

	(new SafeLinks())->changeAttributesForLinks($codes);

	expect($codes[0]['content'])->toContain('rel="noopener noreferrer nofollow"')
		->and($codes[1]['before'])->toContain('rel="noopener noreferrer nofollow"')
		->and($codes[2]['content'])->toBe('rel="noopener"');
});

test('keeps already protected links untouched', function () {
	$codes = [
		['tag' => 'url', 'type' => 'unparsed_content', 'content' => '<a href="{url}" rel="noopener noreferrer nofollow">{url}</a>'],
	];

	(new SafeLinks())->changeAttributesForLinks($codes);

	expect($codes[0]['content'])->toBe('<a href="{url}" rel="noopener noreferrer nofollow">{url}</a>');
});
