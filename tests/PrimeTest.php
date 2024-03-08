<?php declare(strict_types=1);

use Bugo\Compat\Lang;
use Bugo\Compat\Utils;
use Bugo\Optimus\Prime;

beforeEach(function () {
    $this->prime = new Prime();
});

it('runs __invoke', function () {
	expect((new Prime())())->toBeNull();
});

it('loads languages', function () {
	$this->prime->loadTheme();

	expect(Lang::$txt['optimus_title'])
		->toEqual('Search Engine Optimization');
});

it('adds copyright', function () {
	$this->prime->credits();

	expect(Utils::$context['credits_modifications'])
		->not->toBeEmpty();
});
