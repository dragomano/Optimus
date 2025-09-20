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
	unset(Config::$modSettings['optimus_board_extend_title']);
	unset(Config::$modSettings['optimus_topic_extend_title']);
	Mockery::close();
});

test('__invoke method executes without error', function () {
	expect(fn() => $this->handler->__invoke())->not->toThrow(Exception::class);
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

	it('does nothing when total_topics is empty', function () {
		Config::$modSettings['optimus_board_extend_title'] = 1;

		// Board::$info['total_topics'] not set

		$this->handler->handle();

		expect(Utils::$context['page_title_html_safe'])
			->toBe('bar');
	});

	it('does nothing when optimus_board_extend_title is empty', function () {
		Board::$info['total_topics'] = 10;

		// Config::$modSettings['optimus_board_extend_title'] not set

		$this->handler->handle();

		expect(Utils::$context['page_title_html_safe'])
			->toBe('bar');
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

	it('does nothing when first_message is empty', function () {
		Config::$modSettings['optimus_topic_extend_title'] = 1;

		// Utils::$context['first_message'] not set

		$this->handler->handle();

		expect(Utils::$context['page_title_html_safe'])
			->toBe('bar');
	});

	it('does nothing when optimus_topic_extend_title is empty', function () {
		Utils::$context['first_message'] = 1;

		// Config::$modSettings['optimus_topic_extend_title'] not set

		$this->handler->handle();

		expect(Utils::$context['page_title_html_safe'])
			->toBe('bar');
	});
});
