<?php declare(strict_types=1);

use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\CreditsHandler;

beforeEach(function () {
	$this->handler = new CreditsHandler();
});

it('adds copyright', function () {
	$this->handler->credits();

	expect(Utils::$context['credits_modifications'])
		->not->toBeEmpty();
});
