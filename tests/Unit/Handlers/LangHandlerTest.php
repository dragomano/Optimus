<?php declare(strict_types=1);

use Bugo\Compat\Lang;
use Bugo\Optimus\Handlers\LangHandler;

beforeEach(function () {
	$this->handler = new LangHandler();
});

test('handle with forum index', function () {
	$this->handler->loadTheme();

	expect(Lang::$txt['optimus_title'])->toBeString();
});
