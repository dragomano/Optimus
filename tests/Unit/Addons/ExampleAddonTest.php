<?php declare(strict_types=1);

use Bugo\Compat\Utils;
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\ExampleAddon;
use Bugo\Optimus\Events\AddonEvent;

beforeEach(function () {
	$GLOBALS['test_integration_hooks'] = [];

	Utils::$context = [];
});

afterEach(function () {
	Mockery::close();
});

test('metadata', function () {
	expect(ExampleAddon::PACKAGE_ID)->toBe('Optimus:ExampleAddon')
		->and(ExampleAddon::$events)->toBe([AddonInterface::HOOK_EVENT]);
});

test('__invoke registers the theme context hook', function () {
	(new ExampleAddon())(new AddonEvent(AddonInterface::HOOK_EVENT, null));

	$hooks = array_column($GLOBALS['test_integration_hooks'] ?? [], 0);

	expect($hooks)->toContain('integrate_theme_context');
});

test('__invoke ignores other events', function () {
	$GLOBALS['test_integration_hooks'] = [];

	(new ExampleAddon())(new AddonEvent('some_other_event', null));

	$hooks = array_column($GLOBALS['test_integration_hooks'] ?? [], 0);

	expect($hooks)->not->toContain('integrate_theme_context');
});

test('hides a locked topic from spiders', function () {
	Utils::$context['topicinfo'] = ['locked' => true, 'num_replies' => 5];

	(new ExampleAddon())->hideSomeTopicsFromSpiders();

	expect(Utils::$context['meta_tags'])->toBe([['name' => 'robots', 'content' => 'noindex,nofollow']]);
});

test('hides a topic with few replies from spiders', function () {
	Utils::$context['topicinfo'] = ['locked' => false, 'num_replies' => 1];

	(new ExampleAddon())->hideSomeTopicsFromSpiders();

	expect(Utils::$context['meta_tags'])->toBe([['name' => 'robots', 'content' => 'noindex,nofollow']]);
});

test('does not touch a common topic', function () {
	Utils::$context['topicinfo'] = ['locked' => false, 'num_replies' => 5];

	(new ExampleAddon())->hideSomeTopicsFromSpiders();

	expect(Utils::$context['meta_tags'] ?? [])->toBeEmpty();
});

test('does nothing without the topic info', function () {
	(new ExampleAddon())->hideSomeTopicsFromSpiders();

	expect(Utils::$context['meta_tags'] ?? [])->toBeEmpty();
});
