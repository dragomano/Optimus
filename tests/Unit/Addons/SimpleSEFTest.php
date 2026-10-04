<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\SimpleSEF;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use League\Event\ListenerPriority;

afterEach(function () {
	Config::$modSettings = [];

	Mockery::close();
});

test('metadata', function () {
	expect(SimpleSEF::PACKAGE_ID)->toBe('slammeddime:simplesef')
		->and(SimpleSEF::PRIORITY)->toBe(ListenerPriority::HIGH)
		->and(SimpleSEF::$events)->toBe([AddonInterface::ROBOTS_RULES, AddonInterface::SITEMAP_URL_REWRITER]);
});

test('__invoke does nothing when the SimpleSEF mod is disabled', function () {
	$generator = new RobotsGenerator();

	(new SimpleSEF())(new AddonEvent(AddonInterface::ROBOTS_RULES, $generator));

	expect($generator->useSef)->toBeFalse()
		->and($generator->customRules)->toBeEmpty();
});

test('changeRobots keeps the default rules when the SimpleSEF sources are absent', function () {
	Config::$modSettings['simplesef_enable'] = 1;

	$generator = new RobotsGenerator();

	(new SimpleSEF())->changeRobots($generator);

	expect($generator->useSef)->toBeFalse();
});
