<?php declare(strict_types=1);

use Bugo\Compat\Config;
use Bugo\Compat\Db;
use Bugo\Compat\Db\FuncMapper;
use Bugo\Compat\User;
use Bugo\Compat\Utils;
use Bugo\Optimus\Handlers\SearchTermHandler;
use Bugo\Optimus\Utils\Input;
use Tests\TestDbMapper;

beforeEach(function () {
	$this->handler = new SearchTermHandler();

	Utils::$context['page_title'] = '';
	Utils::$context['canonical_url'] = '';

	Config::$modSettings['optimus_search_page_title'] = '';

	Db::$db = new class extends TestDbMapper {
		private array $data = [
			['phrase' => 'foo', 'hit' => '10'],
			['phrase' => 'bar', 'hit' => '20'],
		];

		public function testQuery($query, $params = []): array
		{
			if (str_contains($query, 'SELECT phrase, hit')) {
				return array_slice($this->data, 0, 30);
			}

			if (str_contains($query, 'SELECT id_term')) {
				return $params['phrase'] === 'bar' ? ['1'] : [null];
			}

			return [];
		}

		public function insert(
			string $method,
			string $table,
			array $columns,
			array $data,
			array $keys,
			int $returnmode = 0
		): int|array|null
		{
			expect($method)->toBe('insert')
				->and($keys)->toBe(['id_term']);

			$this->data[] = array_combine($columns, $data);

			return match($returnmode) {
				1 => count($this->data) - 1,
				2 => [count($this->data) - 1],
				default => null,
			};
		}
	};
});

afterEach(function () {
	Db::$db = new FuncMapper();
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

		expect(Utils::$context['page_title'])->toBe('Existing Title');
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

describe('permissionsList method', function () {
	it('checks case with disabled setting', function () {
		$permissionList = [];

		Config::$modSettings['optimus_log_search'] = false;

		$this->handler->permissionsList($permissionList);

		expect($permissionList)->toBeEmpty();
	});

	it('checks case with enabled setting', function () {
		$permissionList = [];

		Config::$modSettings['optimus_log_search'] = true;

		$this->handler->permissionsList($permissionList);

		expect($permissionList)->toHaveKey('optimus_view_search_terms');
	});
});

describe('prepareSearchTerms method', function () {
	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_log_search'] = false;

		$this->handler->prepareSearchTerms();

		expect(isset(Utils::$context['search_terms']))->toBeFalse();
	});

	it('checks case with enabled setting', function () {
		Config::$modSettings['optimus_log_search'] = true;

		Utils::$context['current_action'] = 'search';

		$this->handler->prepareSearchTerms();

		expect(isset(Utils::$context['search_terms']))->toBeTrue()
			->and(Utils::$context['search_terms'])->toHaveCount(2);
	});

	it('checks case with current_action search2', function () {
		Config::$modSettings['optimus_log_search'] = true;

		Utils::$context['current_action'] = 'search2';

		$this->handler->prepareSearchTerms();

		expect(isset(Utils::$context['search_terms']))->toBeTrue()
			->and(Utils::$context['search_terms'])->toHaveCount(2);
	});

});

describe('searchParams method', function () {
	it('checks case with disabled setting', function () {
		Config::$modSettings['optimus_log_search'] = false;

		expect($this->handler->searchParams())->toBeFalse();
	});

	it('checks case with enabled setting', function () {
		Config::$modSettings['optimus_log_search'] = true;

		Input::request(['search' => 'unknown']);

		expect($this->handler->searchParams())->toBeTrue();

		Input::request(['search' => 'bar']);

		expect($this->handler->searchParams())->toBeTrue();
	});

	it('checks case with empty search string', function () {
		Config::$modSettings['optimus_log_search'] = true;

		Input::request(['search' => '']);

		expect($this->handler->searchParams())->toBeFalse();
	});
});

describe('showChart method', function () {
	it('checks case with disabled optimus_log_search', function () {
		Utils::$context['template_layers'] = [];

		Config::$modSettings['optimus_log_search'] = false;

		$showChart = new ReflectionMethod($this->handler, 'showChart');
		$showChart->invoke($this->handler);

		expect(Utils::$context['template_layers'])->toBeEmpty();
	});

	it('checks normal case', function () {
		Config::$modSettings['optimus_log_search'] = true;

		Utils::$context['search_terms'] = [['test']];

		$showChart = new ReflectionMethod($this->handler, 'showChart');
		$showChart->invoke($this->handler);

		expect(Utils::$context['template_layers'])->toContain('search_terms');
	});

	it('checks case with empty search_terms', function () {
		Config::$modSettings['optimus_log_search'] = true;

		Utils::$context['search_terms'] = [];
		Utils::$context['template_layers'] = [];

		$showChart = new ReflectionMethod($this->handler, 'showChart');
		$showChart->invoke($this->handler);

		expect(Utils::$context['template_layers'])->toBeEmpty();
	});
});

describe('canView method', function () {
	it('checks case with allowedTo false', function () {
		Config::$modSettings['optimus_log_search'] = true;

		$userMock = Mockery::mock('Bugo\Compat\User');
		$userMock->shouldReceive('allowedTo')->with('optimus_view_search_terms')->andReturn(false);
		User::$me = $userMock;

		$canView = new ReflectionMethod($this->handler, 'canView');
		$result = $canView->invoke($this->handler);

		expect($result)->toBeFalse();

		Mockery::close();
	});
});
