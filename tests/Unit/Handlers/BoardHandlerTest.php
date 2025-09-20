<?php declare(strict_types=1);

use Bugo\Compat\Board;
use Bugo\Compat\Config;
use Bugo\Compat\Lang;
use Bugo\Compat\Theme;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\BoardHandler;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
	$this->handler = new BoardHandler();

	Theme::$current->settings['og_image'] = '';
});

it('checks __invoke method', function () {
	Config::$modSettings['optimus_allow_change_board_og_image'] = true;

	try {
		(new BoardHandler())();
		$result = 'success';
	} catch (Exception $e) {
		$result = $e->getMessage();
	}

	expect($result)->toEqual('success');

	unset(Config::$modSettings['optimus_allow_change_board_og_image']);
});

describe('menuButtons method', function () {
	it('checks basic usage', function () {
		Board::$info['og_image'] = 'bar';

		$this->handler->menuButtons();

		expect(Theme::$current->settings['og_image'])
			->toBe('bar');
	});

	it('checks case without og image', function () {
		unset(Board::$info['og_image']);

		$this->handler->menuButtons();

		expect(Theme::$current->settings['og_image'])
			->toBeEmpty();
	});
});

test('loadBoard method', function () {
	$selects = [];

	$this->handler->loadBoard($selects);

	expect($selects)
		->toContain('b.optimus_og_image');
});

test('boardInfo method', function () {
	$board_info = [];

	$row = ['optimus_og_image' => 'bar'];

	$this->handler->boardInfo($board_info, $row);

	expect($board_info)
		->toHaveKey('og_image');
});

test('preBoardtree method', function () {
	$boardColumns = [];

	$this->handler->preBoardtree($boardColumns);

	expect($boardColumns)
		->toContain('b.optimus_og_image');
});

test('boardtreeBoard method', function () {
	$row = [
		'id_board' => 1,
		'optimus_og_image' => 'bar'
	];

	$this->handler->boardtreeBoard($row);

	expect(Board::$loaded[$row['id_board']])
		->toHaveKey('optimus_og_image');
});

test('editBoard method', function () {
	Utils::$context['custom_board_settings'] = [];

	Lang::setTxt('og_image', '');
	Lang::setTxt('og_image_desc', '');

	$this->handler->editBoard();

	expect(Utils::$context['custom_board_settings'])
		->toHaveKey(0);
});

describe('modifyBoard method', function () {
	it('checks basic usage', function () {
		$this->request = Request::createFromGlobals();
		$this->request->request->set('optimus_og_image', 'bar');
		$this->request->overrideGlobals();

		$boardUpdates = $boardUpdateParameters = [];

		$this->handler->modifyBoard(0, [], $boardUpdates, $boardUpdateParameters);

		expect($boardUpdates)
			->toContain('optimus_og_image = {string:og_image}')
			->and($boardUpdateParameters)
			->toHaveKey('og_image');
	});

	it('checks saving without post data', function () {
		$this->request = Request::createFromGlobals();
		$this->request->request->remove('optimus_og_image');
		$this->request->overrideGlobals();

		$boardUpdates = $boardUpdateParameters = [];

		$this->handler->modifyBoard(0, [], $boardUpdates, $boardUpdateParameters);

		expect($boardUpdates)
			->toBeEmpty()
			->and($boardUpdateParameters)
			->toBeEmpty();
	});
});
