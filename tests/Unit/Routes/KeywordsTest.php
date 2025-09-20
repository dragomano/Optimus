<?php declare(strict_types=1);

use Bugo\Optimus\Routes\Keywords;

beforeEach(function () {
	$this->keywords = new Keywords();
});

test('buildRoute method', function () {
	$params = [
		'action' => 'keywords',
	];

	$expected = [
		'route'  => ['keywords', 'all'],
		'params' => [],
	];

	expect($this->keywords::buildRoute($params))->toBe($expected);

	$params = [
		'action' => 'keywords',
		'id'     => 10,
	];

	$expected = [
		'route'  => ['keywords', 10],
		'params' => [],
	];

	expect($this->keywords::buildRoute($params))->toBe($expected);

	$params = [
		'action' => 'keywords',
		'id'     => 10,
		'start'  => 2,
	];

	$expected = [
		'route'  => ['keywords', 10, 2],
		'params' => [],
	];

	expect($this->keywords::buildRoute($params))->toBe($expected);
});

test('parseRoute method', function () {
	$route = ['keywords', 10, 2];

	$params = [];

	$expected = [
		'action' => 'keywords',
		'id'     => 10,
		'start'  => 2,
	];

	expect($this->keywords::parseRoute($route, $params))->toBe($expected);
});
