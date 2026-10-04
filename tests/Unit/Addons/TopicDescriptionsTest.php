<?php declare(strict_types=1);

use Bugo\Compat\Utils;
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\TopicDescriptions;
use Bugo\Optimus\Events\AddonEvent;

beforeEach(function () {
	$GLOBALS['test_integration_hooks'] = [];

	Utils::$context = [];
});

afterEach(function () {
	Mockery::close();
});

test('metadata', function () {
	expect(TopicDescriptions::PACKAGE_ID)->toBe('runic:TopicDescriptions')
		->and(TopicDescriptions::$events)->toBe([AddonInterface::HOOK_EVENT]);
});

test('__invoke registers the menu buttons hook', function () {
	(new TopicDescriptions())(new AddonEvent(AddonInterface::HOOK_EVENT, null));

	$hooks = array_column($GLOBALS['test_integration_hooks'] ?? [], 0);

	expect($hooks)->toContain('integrate_menu_buttons');
});

test('uses the topic description as the meta description', function () {
	Utils::$context['topicinfo'] = ['description' => 'A long topic description'];

	(new TopicDescriptions())->useTopicDescription();

	expect(Utils::$context['meta_description'])->toBe('A long topic description');
});

test('does nothing without the topic description', function () {
	Utils::$context['topicinfo'] = [];

	(new TopicDescriptions())->useTopicDescription();

	expect(Utils::$context['meta_description'] ?? null)->toBeNull();
});
