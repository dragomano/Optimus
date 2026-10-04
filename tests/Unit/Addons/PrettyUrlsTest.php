<?php declare(strict_types=1);

use Bugo\Compat\Utils;
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\PrettyUrls;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use League\Event\ListenerPriority;

afterEach(function () {
	Utils::$context = [];

	Mockery::close();
});

test('metadata', function () {
	expect(PrettyUrls::PACKAGE_ID)->toBe('el:prettyurls')
		->and(PrettyUrls::PRIORITY)->toBe(ListenerPriority::HIGH)
		->and(PrettyUrls::$events)->toBe([
			AddonInterface::HOOK_EVENT,
			AddonInterface::ROBOTS_RULES,
			AddonInterface::SITEMAP_CONTENT,
		]);
});

test('addSupportKeywordsAction extends the pretty actions', function () {
	Utils::$context['pretty']['action_array'] = ['some_action'];

	(new PrettyUrls())->addSupportKeywordsAction();

	expect(Utils::$context['pretty']['action_array'])->toContain('keywords');
});

test('addSupportKeywordsAction does nothing without the pretty actions', function () {
	(new PrettyUrls())->addSupportKeywordsAction();

	expect(Utils::$context['pretty'] ?? null)->toBeNull();
});

test('changeRobots keeps the default rules when the PrettyUrls filters are absent', function () {
	$generator = new RobotsGenerator();

	(new PrettyUrls())->changeRobots($generator);

	expect($generator->useSef)->toBeFalse();
});
