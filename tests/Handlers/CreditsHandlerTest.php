<?php declare(strict_types=1);

use Bugo\Compat\Lang;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\CoreHandler;

beforeEach(function () {
	$this->handler = new CoreHandler();
});

it('loads languages', function () {
	$this->handler->loadTheme();

	expect(Lang::$txt['optimus_title'])
		->toEqual('Search Engine Optimization');
});

it('adds copyright', function () {
	$this->handler->credits();

	expect(Utils::$context['credits_modifications'])
		->not->toBeEmpty();
});
