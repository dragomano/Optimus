<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\BoardHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class BoardHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		global $settings;

		parent::setUp();

		$this->handler = new BoardHandler();

		$settings['og_image'] = '';
	}

	/**
	 * @covers BoardHandler::menuButtons
	 */
	public function testMenuButtons()
	{
		global $board_info, $settings;

		$board_info['og_image'] = 'bar';

		$this->handler->menuButtons();

		$this->assertSame('bar', $settings['og_image']);
	}

	/**
	 * @covers BoardHandler::menuButtons
	 */
	public function testMenuButtonsWithoutOgImage()
	{
		global $board_info, $settings;

		unset($board_info['og_image']);

		$this->handler->menuButtons();

		$this->assertEmpty($settings['og_image']);
	}

	/**
	 * @covers BoardHandler::loadBoard
	 */
	public function testLoadBoard()
	{
		$selects = [];

		$this->handler->loadBoard($selects);

		$this->assertContains('b.optimus_og_image', $selects);
	}

	/**
	 * @covers BoardHandler::boardInfo
	 */
	public function testBoardInfo()
	{
		$board_info = [];
		$row = ['optimus_og_image' => 'bar'];

		$this->handler->boardInfo($board_info, $row);

		$this->assertArrayHasKey('og_image', $board_info);
	}

	/**
	 * @covers BoardHandler::preBoardtree
	 */
	public function testPreBoardtree()
	{
		$boardColumns = [];

		$this->handler->preBoardtree($boardColumns);

		$this->assertContains('b.optimus_og_image', $boardColumns);
	}

	/**
	 * @covers BoardHandler::boardtreeBoard
	 */
	public function testBoardtreeBoard()
	{
		global $boards;

		$row = [
			'id_board' => 1,
			'optimus_og_image' => 'bar'
		];

		$this->handler->boardtreeBoard($row);

		$this->assertArrayHasKey('optimus_og_image', $boards[$row['id_board']]);
	}

	/**
	 * @covers BoardHandler::editBoard
	 */
	public function testEditBoard()
	{
		global $context, $txt;

		$context['custom_board_settings'] = [];

		$txt['og_image'] = $txt['og_image_desc'] = '';

		$this->handler->editBoard();

		$this->assertArrayHasKey(0, $context['custom_board_settings']);
	}

	/**
	 * @covers BoardHandler::modifyBoard
	 */
	public function testModifyBoard()
	{
		$this->request->request->set('optimus_og_image', 'bar');
		$this->request->overrideGlobals();

		$boardUpdates = $boardUpdateParameters = [];

		$this->handler->modifyBoard(0, [], $boardUpdates, $boardUpdateParameters);

		$this->assertContains('optimus_og_image = {string:og_image}', $boardUpdates);
		$this->assertArrayHasKey('og_image', $boardUpdateParameters);
	}

	/**
	 * @covers BoardHandler::modifyBoard
	 */
	public function testModifyBoardWithoutPostData()
	{
		$this->request->request->remove('optimus_og_image');
		$this->request->overrideGlobals();

		$boardUpdates = $boardUpdateParameters = [];

		$this->handler->modifyBoard(0, [], $boardUpdates, $boardUpdateParameters);

		$this->assertEmpty($boardUpdates);
		$this->assertEmpty($boardUpdateParameters);
	}
}