<?php declare(strict_types=1);

use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Addons\BuiltInSEF;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use League\Event\ListenerPriority;

afterEach(function () {
	Mockery::close();
});

test('metadata', function () {
	expect(BuiltInSEF::PACKAGE_ID)->toBe('Optimus:BuiltInSEF')
		->and(BuiltInSEF::PRIORITY)->toBe(ListenerPriority::HIGH)
		->and(BuiltInSEF::$events)->toBe([AddonInterface::ROBOTS_RULES, AddonInterface::SITEMAP_CONTENT]);
});

test('__invoke does nothing on SMF 2.1', function () {
	$generator = new RobotsGenerator();

	(new BuiltInSEF())(new AddonEvent(AddonInterface::ROBOTS_RULES, $generator));

	expect($generator->useSef)->toBeFalse()
		->and($generator->customRules)->toBeEmpty();
});

test('changeRobots allows the main entities', function () {
	$generator = new RobotsGenerator();

	(new BuiltInSEF())->changeRobots($generator);

	expect($generator->useSef)->toBeTrue()
		->and($generator->customRules['*'][RobotsGenerator::RULE_ALLOW])->toHaveCount(3);
});
