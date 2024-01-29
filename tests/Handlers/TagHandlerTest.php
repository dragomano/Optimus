<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\TagHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class TagHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		parent::setUp();

		$this->handler = new TagHandler();

		loadLanguage('Optimus/Optimus');
	}

	/**
	 * @covers TagHandler::actions
	 */
	public function testActions()
	{
		global $modSettings;

		$modSettings['optimus_allow_change_topic_keywords'] = true;

		$actions = [];

		$this->handler->actions($actions);

		$this->assertArrayHasKey('keywords', $actions);
	}

	/**
	 * @covers TagHandler::actions
	 */
	public function testActionsWithDisabledSetting()
	{
		global $modSettings;

		$modSettings['optimus_allow_change_topic_keywords'] = false;

		$modSettings['optimus_show_keywords_block'] = false;

		$actions = [];

		$this->handler->actions($actions);

		$this->assertEmpty($actions);
	}

	/**
	 * @covers TagHandler::menuButtons
	 */
	public function testMenuButtons()
	{
		global $context;

		$context['current_action'] = 'keywords';

		$buttons = ['home' => []];

		$this->handler->menuButtons($buttons);

		$this->assertArrayHasKey('action_hook', $buttons['home']);
	}

	/**
	 * @covers TagHandler::currentAction
	 */
	public function testCurrentAction()
	{
		global $context;

		$context['current_action'] = 'keywords';

		$current_action = '';

		$this->handler->currentAction($current_action);

		$this->assertSame('home', $current_action);
	}

	/**
	 * @covers TagHandler::loadPermissions
	 */
	public function testLoadPermissions()
	{
		global $modSettings;

		$modSettings['optimus_allow_change_topic_keywords'] = true;

		$permissionList = [];

		$this->handler->loadPermissions([], $permissionList);

		$this->assertArrayHasKey(
			'optimus_add_keywords', $permissionList['membergroup']
		);
	}

	/**
	 * @covers TagHandler::loadPermissions
	 */
	public function testLoadPermissionsWithDisabledSetting()
	{
		global $modSettings;

		$modSettings['optimus_allow_change_topic_keywords'] = false;

		$permissionList = [];

		$this->handler->loadPermissions([], $permissionList);

		$this->assertEmpty($permissionList);
	}

	/**
	 * @covers TagHandler::basicSettings
	 */
	public function testBasicSettings()
	{
		$config_vars = [];

		$this->handler->basicSettings($config_vars);

		$this->assertNotEmpty($config_vars);
	}

	/**
	 * @covers TagHandler::messageindexButtons
	 */
	public function testMessageindexButtons()
	{
		global $modSettings, $context;

		$modSettings['optimus_show_keywords_on_message_index'] = true;

		$context['topics'] = ['foo' => 'bar'];

		$this->handler->messageindexButtons();

		$this->assertContains(
			'.optimus_keywords:visited { color: transparent }',
			$context['css_header']
		);
	}

	/**
	 * @covers TagHandler::prepareDisplayContext
	 */
	public function testPrepareDisplayContext()
	{
		$this->assertTrue(
			method_exists(TagHandler::class, 'prepareDisplayContext')
		);
	}

	/**
	 * @covers TagHandler::createTopic
	 */
	public function testCreateTopic()
	{
		$this->assertTrue(
			method_exists(TagHandler::class, 'createTopic')
		);
	}

	/**
	 * @covers TagHandler::postEnd
	 */
	public function testPostEnd()
	{
		global $modSettings, $context;

		$modSettings['optimus_allow_change_topic_keywords'] = true;

		$context['is_new_topic'] = false;

		$context['optimus_keywords'] = ['foo' => 'bar'];

		$this->handler->postEnd();

		$this->assertContains('bar', $context['optimus']['keywords']);

		unset($context['optimus_keywords']);
	}

	/**
	 * @covers TagHandler::postEnd
	 */
	public function testPostEndForNewTopic()
	{
		global $modSettings, $context;

		$modSettings['optimus_allow_change_topic_keywords'] = true;

		$context['is_new_topic'] = true;

		$this->request->request->set('optimus_keywords', 'bar');
		$this->request->overrideGlobals();

		$this->handler->postEnd();

		$this->assertSame('bar', $context['optimus']['keywords']);
	}

	/**
	 * @covers TagHandler::modifyPost
	 */
	public function testModifyPost()
	{
		$this->assertTrue(
			method_exists(TagHandler::class, 'modifyPost')
		);
	}

	/**
	 * @covers TagHandler::removeTopics
	 */
	public function testRemoveTopics()
	{
		$this->assertTrue(
			method_exists(TagHandler::class, 'removeTopics')
		);
	}

	/**
	 * @covers TagHandler::showTheSame
	 */
	public function testShowTheSame()
	{
		global $context;

		$context['current_subaction'] = '';

		$this->handler->showTheSame();

		$this->assertIsInt($context['optimus_keyword_id']);
		$this->assertContains('keywords', $context['template_layers']);
	}

	/**
	 * @covers TagHandler::getAllByKeyId
	 */
	public function testGetAllByKeyId()
	{
		$this->assertTrue(
			method_exists(TagHandler::class, 'getAllByKeyId')
		);
	}

	/**
	 * @covers TagHandler::getTotalCountByKeyId
	 */
	public function testGetTotalCountByKeyId()
	{
		$this->assertTrue(
			method_exists(TagHandler::class, 'getTotalCountByKeyId')
		);
	}

	/**
	 * @covers TagHandler::showAllWithFrequency
	 */
	public function testShowAllWithFrequency()
	{
		global $context;

		$this->handler->showAllWithFrequency();

		$this->assertStringContainsString('?action=keywords', $context['canonical_url']);
	}

	/**
	 * @covers TagHandler::getAll
	 */
	public function testGetAll()
	{
		$this->assertTrue(
			method_exists(TagHandler::class, 'getAll')
		);
	}

	/**
	 * @covers TagHandler::getTotalCount
	 */
	public function testGetTotalCount()
	{
		$this->assertTrue(
			method_exists(TagHandler::class, 'getTotalCount')
		);
	}

	/**
	 * @covers TagHandler::displayTopic
	 */
	public function testDisplayTopic()
	{
		global $modSettings, $context;

		$modSettings['optimus_show_keywords_block'] = true;

		$context['current_topic'] = 1;

		$context['optimus_keywords'] = $context['optimus']['keywords'] = [];

		$this->handler->displayTopic();

		$this->assertEmpty($context['optimus_keywords']);

		unset($context['optimus_keywords']);
	}

	/**
	 * @covers TagHandler::displayTopic
	 */
	public function testDisplayTopicWithDisabledSetting()
	{
		global $modSettings, $context;

		$modSettings['optimus_show_keywords_block'] = false;

		unset($context['current_topic']);

		$this->handler->displayTopic();

		$this->assertFalse(isset($context['optimus_keywords']));
	}
}