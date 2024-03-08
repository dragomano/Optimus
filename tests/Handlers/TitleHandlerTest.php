<?php declare(strict_types=1);

use Bugo\Compat\Board;
use Bugo\Compat\Config;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\TitleHandler;

beforeEach(function () {
	$this->handler = new TitleHandler();

	Board::$info['name'] = 'lol';

	Utils::$context['forum_name'] = 'foo';
	Utils::$context['page_title_html_safe'] = 'bar';
});

afterEach(function () {
	unset(Utils::$context['page_title_html_safe']);
	unset(Utils::$context['first_message']);
	unset(Board::$info['total_topics']);
});

describe('handleBoardTitles method', function () {
	it('checks case with first option', function () {
		Config::$modSettings['optimus_board_extend_title'] = 1;

		Board::$info['total_topics'] = 10;

		$this->handler->handle();

		expect(Utils::$context['page_title_html_safe'])
			->toBe('foo - bar');
	});

	it('checks case with second option', function () {
		Config::$modSettings['optimus_board_extend_title'] = 2;

		Board::$info['total_topics'] = 10;

		$this->handler->handle();

		expect(Utils::$context['page_title_html_safe'])
			->toBe('bar - foo');
	});
});

describe('handleTopicTitles method', function () {
	it('checks case with first option', function () {
		Config::$modSettings['optimus_topic_extend_title'] = 1;

		Utils::$context['first_message'] = 1;

		$this->handler->handle();

		expect(Utils::$context['page_title_html_safe'])
			->toBe('foo - lol - bar');
	});

	it('checks case with second option', function () {
		Config::$modSettings['optimus_topic_extend_title'] = 2;

		Utils::$context['first_message'] = 1;

		$this->handler->handle();

		expect(Utils::$context['page_title_html_safe'])
			->toBe('bar - lol - foo');
	});
});
