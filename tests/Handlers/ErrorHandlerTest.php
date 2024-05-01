<?php declare(strict_types=1);

use Bugo\Compat\Board;
use Bugo\Compat\Config;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\ErrorHandler;

beforeEach(function () {
	$this->handler = new ErrorHandler();

	Utils::$context['error_link'] = '';
});

describe('fallbackAction method', function () {
	it('checks basic usage', function () {
		Config::$modSettings['optimus_errors_for_wrong_actions'] = true;

		$this->handler->fallbackAction();

		expect(Utils::$context['error_link'])
			->toBe('javascript:history.go(-1)');
	});
});

describe('handleStatusErrors method', function () {
	it('checks case when board_info[error] = exist', function () {
		Config::$modSettings['optimus_errors_for_wrong_boards_topics'] = true;

		Board::$info['error'] = 'exist';

		$this->handler->handleStatusErrors();

		expect(Utils::$context['error_link'])
			->toBe('javascript:history.go(-1)');
	});

	it('checks case when board_info[error] = access', function () {
		Config::$modSettings['optimus_errors_for_wrong_boards_topics'] = true;

		Board::$info['error'] = 'access';

		$this->handler->handleStatusErrors();

		expect(Utils::$context['error_link'])
			->toBe('javascript:history.go(-1)');
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_errors_for_wrong_boards_topics'] = false;

		$this->handler->handleStatusErrors();

		expect(Utils::$context['error_link'])
			->toBeEmpty();
	});
});
