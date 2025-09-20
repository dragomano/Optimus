<?php declare(strict_types=1);

use Bugo\Optimus\Utils\Input;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
	$this->request = Request::createFromGlobals();
});

describe('request', function () {
	it('sets $_REQUEST foo with bar', function () {
		$this->request->request->set('foo', 'bar');
		$this->request->overrideGlobals();

		expect(Input::request('foo'))->toBe('bar');
	});

	it('sets $_REQUEST foo with default value', function () {
		expect(Input::request('foo', 'bar'))->toBe('bar');
	});

	it('sets $_REQUEST foo without default value', function () {
		expect(Input::request('foo'))->toBeFalse();
	});

	it('sets $_REQUEST with array dataset', function () {
		Input::request([
			'foo' => 'bar',
			'bar' => 'foo',
		]);

		expect(Input::request('foo'))->toBe('bar')
			->and(Input::request('bar'))->toBe('foo');

	});

	afterEach(function () {
		$this->request->request->remove('foo');
		$this->request->overrideGlobals();
	});
});

describe('post', function () {
	it('sets $_POST foo with bar', function () {
		$this->request->request->set('foo', 'bar');
		$this->request->overrideGlobals();

		expect(Input::post('foo'))->toBe('bar');
	});

	it('sets $_POST foo with default value', function () {
		expect(Input::post('foo', 'bar'))->toBe('bar');
	});

	it('sets $_POST foo without default value', function () {
		expect(Input::post('foo'))->toBeFalse();
	});

	it('sets $_POST with array dataset', function () {
		Input::post([
			'foo' => 'bar',
			'bar' => 'foo',
		]);

		expect(Input::post('foo'))->toBe('bar')
			->and(Input::post('bar'))->toBe('foo');
	});

	afterEach(function () {
		$this->request->request->remove('foo');
		$this->request->overrideGlobals();
	});
});

describe('server', function () {
	it('gets $_SERVER[QUERY_STRING]', function () {
		expect(Input::server('query_string'))
			->toBe($this->request->server->get('QUERY_STRING'));
	});

	it('gets $_SERVER (whole array)', function () {
		expect(Input::server())->toBe($this->request->server->all());
	});

	it('gets $_SERVER[argv] without strtoupper', function () {
		$this->request->server->set('argv', ['script', 'arg1']);
		$this->request->overrideGlobals();

		expect(Input::server('argv'))->toBe(['script', 'arg1']);
	});

	it('gets $_SERVER with getenv fallback', function () {
		putenv('TEST_VAR=test_value');

		expect(Input::server('test_var'))->toBe('test_value');
	});
});

describe('session', function () {
	it('sets $_SESSION foo with bar', function () {
		$_SESSION['foo'] = 'bar';

		expect(Input::session('foo'))->toBe('bar');
	});

	it('gets $_SESSION foo when it is unset', function () {
		unset($_SESSION['foo']);

		expect(Input::session('foo'))->toBeNull();
	});

	it('sets $_SESSION with array dataset', function () {
		Input::session([
			'foo' => 'bar',
			'bar' => 'foo',
		]);

		expect(Input::session('foo'))->toBe('bar')
			->and(Input::session('bar'))->toBe('foo');

	});

	it('gets $_SESSION with empty string name', function () {
		$_SESSION[''] = 'empty_key';

		expect(Input::session(''))->toBe('empty_key');
	});
});

describe('isRequest, isPost, isGet', function () {
	beforeEach(function () {
		$this->request->request->set('foo', 'bar');
		$this->request->query->set('foo', 'bar');
		$this->request->overrideGlobals();
	});

	it('checks isRequest()', function () {
		expect(Input::isRequest('foo'))->toBeTrue()
			->and(Input::isRequest('bar'))->toBeFalse();

	});

	it('checks isPost()', function () {
		expect(Input::isPost('foo'))->toBeTrue()
			->and(Input::isPost('bar'))->toBeFalse();

	});

	it('checks isGet()', function () {
		expect(Input::isGet('foo'))->toBeTrue()
			->and(Input::isGet('bar'))->toBeFalse();

	});

	afterEach(function () {
		$this->request->request->remove('foo');
		$this->request->query->remove('foo');
		$this->request->overrideGlobals();
	});
});

describe('xss', function () {
	beforeEach(function () {
		$this->source = /** @lang text */ '<a href="foo">bar</a>';
		$this->result = htmlspecialchars($this->source, ENT_QUOTES, 'UTF-8');
	});

	it('checks xss (basic usage)', function () {
		expect(Input::xss($this->source))->toBe($this->result);
	});

	it('checks xss with array param', function () {
		$source = [$this->source, $this->source];
		$result = [$this->result, $this->result];

		expect(Input::xss($source))->toBe($result);
	});
});

describe('filter', function () {
	it('checks with unknown variable', function () {
		expect(Input::filter('foo'))->toBeNull();
	});

	it('checks with unknown type', function () {
		$this->request->request->set('foo', '<script>console.log("bar")</script>');
		$this->request->overrideGlobals();

		expect(Input::filter('foo', 'unknown'))->toBe(Input::filter('foo'))
			->and(Input::filter('foo', 'unknown'))->toBe(Input::xss($this->request->get('foo')));
	});

	it('checks with url type and valid URL', function () {
		$this->request->request->set('url', 'https://example.com');
		$this->request->overrideGlobals();

		expect(Input::filter('url', 'url'))->toBe(Input::xss('https://example.com'));
	});

	it('checks with url type and invalid URL', function () {
		$this->request->request->set('url', 'not-a-valid-url');
		$this->request->overrideGlobals();

		expect(Input::filter('url', 'url'))->toBeFalse();
	});
});
