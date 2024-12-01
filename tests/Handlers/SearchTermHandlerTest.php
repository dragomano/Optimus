<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\SearchTermHandler;
use Bugo\Optimus\Utils\Input;

beforeEach(function () {
	$this->handler = new SearchTermHandler();

	Utils::$context['page_title'] = '';
    Utils::$context['canonical_url'] = '';
	Config::$modSettings['optimus_search_page_title'] = '';
	Config::$scripturl = 'https://example.com';
});

describe('__invoke method', function () {
    it('does nothing when search_params is not set', function () {
        Utils::$context['search_params'] = null;

        $this->handler->__invoke();

        expect(Utils::$context['page_title'])->toBeEmpty();
    });

    it('does nothing when search term is empty', function () {
        Utils::$context['search_params'] = ['search' => ''];

        $this->handler->__invoke();

        expect(Utils::$context['page_title'])->toBeEmpty();
    });

    it('preserves existing page title when template is empty', function () {
        Utils::$context['search_params'] = ['search' => 'test query'];
        Utils::$context['page_title'] = 'Existing Title';
        Config::$modSettings['optimus_search_page_title'] = '';

        $this->handler->__invoke();

        expect(Utils::$context['page_title'])
            ->toBe('Existing Title');
    });
});

describe('loadPermissions method', function () {
	it('checks case with disabled setting', function () {
		$permissionList = [];

		Config::$modSettings['optimus_log_search'] = false;

		$this->handler->loadPermissions([], $permissionList);

		expect($permissionList)->toBeEmpty();
	});

	it('checks case with enabled setting', function () {
		$permissionList = [];

		Config::$modSettings['optimus_log_search'] = true;

		$this->handler->loadPermissions([], $permissionList);

		expect($permissionList['membergroup'])
			->toHaveKey('optimus_view_search_terms');
	});
});

describe('prepareSearchTerms method', function () {
	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_log_search'] = false;

		$this->handler->prepareSearchTerms();

		$this->assertArrayNotHasKey('search_terms', Utils::$context);
	});

	it('checks case with enabled setting', function () {
		Config::$modSettings['optimus_log_search'] = true;

		Utils::$context['current_action'] = 'search';

		$this->handler->prepareSearchTerms();

		expect(Utils::$context['search_terms'])->toBeArray();
	});
});

describe('searchParams method', function () {
	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_log_search'] = false;

		expect($this->handler->searchParams())->toBeFalse();
	});

	it('checks case with enabled setting', function () {
		Config::$modSettings['optimus_log_search'] = true;

		Input::request(['search' => 'bar']);

		expect($this->handler->searchParams())->toBeTrue();
	});
});
