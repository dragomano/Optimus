<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Theme;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\MetaHandler;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
	$this->handler = new MetaHandler();

	Utils::$context['robot_no_index'] = false;

	Utils::$context['current_action'] = '';

	Utils::$context['optimus_og_type'] = [];

	Utils::$context['meta_tags'] = [];
});

test('handle with forum index', function () {
	Config::$modSettings['optimus_forum_index'] = true;

	Utils::$context['page_title_html_safe'] = 'bar';

	Utils::$context['robot_no_index'] = true;

	$this->handler->handle();

	expect(Utils::$context['page_title_html_safe'])
		->toBe('decoded');
});

test('handle with disabled forum index', function () {
	Config::$modSettings['optimus_forum_index'] = false;

	Utils::$context['page_title_html_safe'] = 'bar';

	Utils::$context['robot_no_index'] = true;

	$this->handler->handle();

	expect(Utils::$context['page_title_html_safe'])
		->toBe('bar');
});

test('handle with meta tags', function () {
	Utils::$context['meta_tags'] = [
		[
			'property' => 'og:title',
			'content' => 'foo bar',
		]
	];

	$this->handler->handle();

	expect(Utils::$context['meta_tags'][0])
		->toHaveKey('prefix');
});

test('handle with og image', function () {
	Utils::$context['optimus_og_image'] = [
		'width' => 600,
		'height' => 400,
		'mime' => 'image/png',
	];

	Utils::$context['meta_tags'] = [
		[
			'property' => 'og:image',
			'content' => 'https://dummyimage.com/600x400/000/fff',
		]
	];

	$this->handler->handle();

	expect(Utils::$context['meta_tags'][1]['property'])->toBe('og:image:type')
		->and(Utils::$context['meta_tags'][2]['content'])->toBe(600)
		->and(Utils::$context['meta_tags'][3]['content'])->toBe(400);

	unset(Utils::$context['optimus_og_image']);
});

test('handle with og type', function () {
	Utils::$context['optimus_og_type']['article'] = [
		'published_time' => time(),
		'modified_time'  => null,
		'author'         => 'John Doe',
		'section'        => 'foo',
		'tag'            => ['foo', 'bar'],
	];

	$this->handler->handle();

	expect(Utils::$context['meta_tags'][0]['content'])->toBe('article')
		->and(Utils::$context['meta_tags'][1]['property'])->toBe('article:published_time')
		->and(Utils::$context['meta_tags'][2]['content'])->toBe('John Doe')
		->and(Utils::$context['meta_tags'][3]['property'])->toBe('article:section')
		->and(Utils::$context['meta_tags'][4]['content'])->toBe('foo')
		->and(Utils::$context['meta_tags'][5]['content'])->toBe('bar');

	unset(Utils::$context['optimus_og_type']);
});

test('handle with profile', function () {
	Utils::$context['current_action'] = 'profile';

	$this->request = Request::createFromGlobals();
	$this->request->request->set('u', 1);
	$this->request->overrideGlobals();

	$this->handler->handle();

	expect(Utils::$context['meta_tags'][0]['content'])
		->toBe('profile');
});

describe('handle with twitter cards', function () {
	test('with og_image', function () {
		Config::$modSettings['optimus_tw_cards'] = true;

		Utils::$context['canonical_url'] = 'https://foo.bar/some';

		Theme::$current->settings['og_image'] = 'https://foo.bar/image.png';

		$this->handler->handle();

		expect(Utils::$context['meta_tags'][0]['content'])->toBe('summary')
			->and(Utils::$context['meta_tags'][1]['property'])->toBe('twitter:site')
			->and(Utils::$context['meta_tags'][2]['property'])->toBe('twitter:image');

		Config::$modSettings['optimus_tw_cards'] = false;

		unset(Theme::$current->settings['og_image']);
	});

	test('without og_image', function () {
		Config::$modSettings['optimus_tw_cards'] = true;

		Utils::$context['canonical_url'] = 'https://foo.bar/some';

		$this->handler->handle();

		expect(Utils::$context['meta_tags'][0]['content'])->toBe('summary')
			->and(Utils::$context['meta_tags'][1]['property'])->toBe('twitter:site')
			->and(isset(Utils::$context['meta_tags'][2]))->toBeFalse();

		Config::$modSettings['optimus_tw_cards'] = false;
	});
});

test('handle with facebook app id', function () {
	Config::$modSettings['optimus_fb_appid'] = 'foo';

	$this->handler->handle();

	expect(Utils::$context['meta_tags'][0]['property'])->toBe('fb:app_id')
		->and(Utils::$context['meta_tags'][0]['content'])->toBe('foo');

	Config::$modSettings['optimus_fb_appid'] = false;
});

test('handle with custom tags', function () {
	Config::$modSettings['optimus_meta'] = serialize([
		'foo' => 'bar',
		'key' => 'value',
	]);

	$this->handler->handle();

	expect(Utils::$context['meta_tags'][0]['name'])->toBe('foo')
		->and(Utils::$context['meta_tags'][1]['content'])->toBe('value');
});
