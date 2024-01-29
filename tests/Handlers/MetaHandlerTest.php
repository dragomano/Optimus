<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\MetaHandler;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class MetaHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		global $context;

		parent::setUp();

		$this->handler = new MetaHandler();

		$context['robot_no_index'] = false;

		$context['current_action'] = '';

		$context['optimus_og_type'] = [];

		$context['meta_tags'] = [];
	}

	/**
	 * @covers MetaHandler::handle
	 */
	public function testHandle()
	{
		global $modSettings, $context;

		$modSettings['optimus_forum_index'] = true;

		$context['page_title_html_safe'] = 'bar';

		$context['robot_no_index'] = true;

		$this->handler->handle();

		$this->assertSame('bar', $context['page_title_html_safe']);
	}

	/**
	 * @covers MetaHandler::handle
	 */
	public function testHandleWithMetaTags()
	{
		global $context;

		$context['meta_tags'] = [
			[
				'property' => 'og:title',
				'content' => 'foo bar',
			]
		];

		$this->handler->handle();

		$this->assertArrayHasKey('prefix', $context['meta_tags'][0]);
	}

	/**
	 * @covers MetaHandler::handle
	 */
	public function testHandleWithOgImage()
	{
		global $context;

		$context['optimus_og_image'] = [
			'width' => 600,
			'height' => 400,
			'mime' => 'image/png',
		];

		$context['meta_tags'] = [
			[
				'property' => 'og:image',
				'content' => 'https://dummyimage.com/600x400/000/fff',
			]
		];

		$this->handler->handle();

		$this->assertSame('og:image:type', $context['meta_tags'][1]['property']);
		$this->assertSame(600, $context['meta_tags'][2]['content']);
		$this->assertSame(400, $context['meta_tags'][3]['content']);
	}

	/**
	 * @covers MetaHandler::handle
	 */
	public function testHandleWithOgType()
	{
		global $context;

		$context['optimus_og_type']['article'] = [
			'published_time' => time(),
			'modified_time'  => null,
			'author'         => 'John Doe',
			'section'        => 'foo',
			'tag'            => 'bar',
		];

		$this->handler->handle();

		$this->assertSame('article', $context['meta_tags'][0]['content']);
		$this->assertSame('article:published_time', $context['meta_tags'][1]['property']);
		$this->assertSame('John Doe', $context['meta_tags'][2]['content']);
		$this->assertSame('article:section', $context['meta_tags'][3]['property']);
		$this->assertSame('bar', $context['meta_tags'][4]['content']);
	}

	/**
	 * @covers MetaHandler::handle
	 */
	public function testHandleWithProfile()
	{
		global $context;

		$context['current_action'] = 'profile';

		$this->request->request->set('u', 1);
		$this->request->overrideGlobals();

		$this->handler->handle();

		$this->assertSame('profile', $context['meta_tags'][0]['content']);
	}

	/**
	 * @covers MetaHandler::handle
	 */
	public function testHandleWithTwitterCards()
	{
		global $modSettings, $context;

		$modSettings['optimus_tw_cards'] = true;

		$context['canonical_url'] = 'https://foo.bar/some';

		$this->handler->handle();

		$this->assertSame('summary', $context['meta_tags'][0]['content']);
		$this->assertSame('twitter:site', $context['meta_tags'][1]['property']);

		$modSettings['optimus_tw_cards'] = false;
	}

	/**
	 * @covers MetaHandler::handle
	 */
	public function testHandleWithFacebookAppId()
	{
		global $modSettings, $context;

		$modSettings['optimus_fb_appid'] = 'foo';

		$this->handler->handle();

		$this->assertSame('fb:app_id', $context['meta_tags'][0]['property']);
		$this->assertSame('foo', $context['meta_tags'][0]['content']);

		$modSettings['optimus_fb_appid'] = false;
	}

	/**
	 * @covers MetaHandler::handle
	 */
	public function testHandleWithCustomTags()
	{
		global $modSettings, $context;

		$modSettings['optimus_meta'] = serialize([
			'foo' => 'bar',
			'key' => 'value',
		]);

		$this->handler->handle();

		$this->assertSame('foo', $context['meta_tags'][0]['name']);
		$this->assertSame('value', $context['meta_tags'][1]['content']);
	}
}