<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Lang;
use Bugo\Compat\Theme;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\FrontPageHandler;
use Bugo\Optimus\Utils\Input;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
	$this->handler = new FrontPageHandler();

	$this->request = Request::createFromGlobals();

	Theme::$current->settings['og_image'] = '';
});

describe('changeTitle method', function () {
	it('checks basic usage', function () {
		Config::$modSettings['optimus_forum_index'] = 'bar';

		$this->handler->changeTitle();

		expect(Lang::getTxt('forum_index'))
			->toBe('bar');
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_forum_index'] = '';

		Lang::setTxt('forum_index', '');

		$this->handler->changeTitle();

		expect(Lang::getTxt('forum_index'))
			->toBeEmpty();
	});
});

describe('addDescription method', function () {
	it('checks basic usage', function () {
		$this->request->server->remove('QUERY_STRING');
		$this->request->server->remove('argv');
		$this->request->overrideGlobals();

		Config::$modSettings['optimus_description'] = 'bar';

		Utils::$context['meta_description'] = Utils::$context['current_action'] = '';

		$this->handler->addDescription();

		expect(Utils::$context['meta_description'])
			->toBe(Input::xss(Config::$modSettings['optimus_description']));
	});

	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_description'] = false;

		Utils::$context['meta_description'] = '';

		$this->handler->addDescription();

		expect(Utils::$context['meta_description'])
			->toBeEmpty();
	});
});
