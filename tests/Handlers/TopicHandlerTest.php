<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\TopicHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class TopicHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		parent::setUp();

		$this->handler = new TopicHandler();
	}

	/**
	 * @covers TopicHandler::prepareOgImage
	 */
	public function testPrepareOgImage()
	{
		global $modSettings, $context, $settings;

		$modSettings['optimus_og_image'] = true;

		$context['topicinfo']['id_first_msg'] = 1;

		$context['current_topic'] = 1;

		$context['loaded_attachments'][1] = [
			[
				'width' => 600,
				'height' => 400,
				'mime_type' => 'image/png',
			]
		];

		$this->handler->prepareOgImage();

		$this->assertSame($settings['og_image'], $context['optimus_og_image']['url']);
	}

	/**
	 * @covers TopicHandler::loadPermissions
	 */
	public function testLoadPermissions()
	{
		global $modSettings;

		$modSettings['optimus_allow_change_topic_desc'] = true;

		$permissionList = [];

		$this->handler->loadPermissions([], $permissionList);

		$this->assertArrayHasKey('optimus_add_descriptions', $permissionList['membergroup']);
	}

	/**
	 * @covers TopicHandler::basicSettings
	 */
	public function testBasicSettings()
	{
		$config_vars = [];

		$this->handler->basicSettings($config_vars);

		$this->assertNotEmpty($config_vars);
	}

	/**
	 * @covers TopicHandler::displayTopic
	 */
	public function testDisplayTopic()
	{
		global $modSettings;

		$modSettings['optimus_allow_change_topic_desc'] = true;

		$topic_selects = [];

		$this->handler->displayTopic($topic_selects);

		$this->assertContains('t.optimus_description', $topic_selects);

		$topic_selects = ['ms.modified_time AS topic_modified_time'];

		$this->handler->displayTopic($topic_selects);

		$this->assertContains('ms.modified_time AS topic_modified_time', $topic_selects);

		$modSettings['optimus_topic_description'] = true;

		$modSettings['optimus_og_image'] = true;

		$topic_selects = ['ms.body AS topic_first_message'];

		$this->handler->displayTopic($topic_selects);

		$this->assertContains('ms.body AS topic_first_message', $topic_selects);
	}

	/**
	 * @covers TopicHandler::menuButtons
	 */
	public function testMenuButtons()
	{
		global $context, $modSettings;

		$context['first_message'] = 1;

		$modSettings['optimus_topic_description'] = true;

		$context['topicinfo'] = [
			'topic_first_message' => 'bar',
			'topic_started_name' => 'foo',
			'topic_started_time' => time(),
			'topic_modified_time' => time(),
		];

		$this->handler->menuButtons();

		$this->assertSame('bar', $context['meta_description']);
		$this->assertNotEmpty($context['optimus_og_type']['article']);
	}

	/**
	 * @covers TopicHandler::beforeCreateTopic
	 */
	public function testBeforeCreateTopic()
	{
		global $modSettings;

		$modSettings['optimus_allow_change_topic_desc'] = true;

		$topic_columns = $topic_parameters = [];

		$this->request->request->set('optimus_description', 'bar');
		$this->request->overrideGlobals();

		$this->handler->beforeCreateTopic([], [], [], $topic_columns, $topic_parameters);

		$this->assertArrayHasKey('optimus_description', $topic_columns);
		$this->assertContains('bar', $topic_parameters);
	}

	/**
	 * @covers TopicHandler::modifyPost
	 */
	public function testModifyPost()
	{
		$this->assertTrue(method_exists(TopicHandler::class, 'modifyPost'));
	}

	/**
	 * @covers TopicHandler::postEnd
	 */
	public function testPostEnd()
	{
		global $context;

		$context['is_new_topic'] = true;

		$this->request->request->set('optimus_description', 'bar');
		$this->request->overrideGlobals();

		$this->handler->postEnd();

		$this->assertStringContainsString('bar', $context['optimus']['description']);
	}
}