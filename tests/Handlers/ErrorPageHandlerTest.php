<?php declare(strict_types=1);

use Bugo\Compat\Board;
use Bugo\Compat\Config;
use Bugo\Compat\Lang;
use Bugo\Compat\Theme;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\ErrorPageHandler;

beforeEach(function () {
	$this->handler = new ErrorPageHandler();

	Utils::$context['error_link'] = '';
});

describe('handleWrongActions method', function () {
	beforeEach(function () {
		Config::$modSettings['optimus_errors_for_wrong_actions'] = false;

		Theme::$current->settings['catch_action'] = [];
	});

	it('sets fatal_error template when setting is enabled', function () {
		Config::$modSettings['optimus_errors_for_wrong_actions'] = true;

		$this->handler->handleWrongActions();

		expect(Theme::$current->settings['catch_action']['sub_template'])
			->toBe('fatal_error');
	});

	it('does not set template when setting is disabled', function () {
		Config::$modSettings['optimus_errors_for_wrong_actions'] = false;

		$this->handler->handleWrongActions();

		expect(Theme::$current->settings['catch_action'])
			->toBeEmpty();
	});

	it('handles null catch_action settings', function () {
		Config::$modSettings['optimus_errors_for_wrong_actions'] = true;
		Theme::$current->settings['catch_action'] = null;

		$this->handler->handleWrongActions();

		expect(Theme::$current->settings['catch_action'])
			->toBeArray()
			->toHaveKey('sub_template', 'fatal_error');
	});
});

describe('handleWrongBoardsTopics method', function () {
	it('checks case when board does not exist', function () {
		Config::$modSettings['optimus_errors_for_wrong_boards_topics'] = true;

		Board::$info['error'] = 'exist';

		$this->handler->handleWrongBoardsTopics();

		expect(Utils::$context['page_title'])
			->toBe(Lang::getTxt('optimus_404_page_title'));
	});

	it('checks case when board has no access', function () {
		Config::$modSettings['optimus_errors_for_wrong_boards_topics'] = true;

		Board::$info['error'] = 'access';

		$this->handler->handleWrongBoardsTopics();

		expect(Utils::$context['page_title'])
			->toBe(Lang::getTxt('optimus_403_page_title'));
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_errors_for_wrong_boards_topics'] = false;

		$this->handler->handleWrongBoardsTopics();

		expect(Utils::$context['error_link'])
			->toBeEmpty();
	});
});
