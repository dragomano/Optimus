<?php declare(strict_types=1);

namespace Tests\Utils;

use Tests\AbstractBase;
use Bugo\Optimus\Utils\Input;

/**
 * @requires PHP 8.0
 */
class InputTest extends AbstractBase
{
	/**
	 * @covers Input::request
	 */
	public function testRequest()
	{
		$this->request->request->set('foo', 'bar');
		$this->request->overrideGlobals();

		$this->assertSame('bar', Input::request('foo'));
	}

	/**
	 * @covers Input::request
	 */
	public function testRequestWithoutData()
	{
		$this->request->request->remove('foo');
		$this->request->overrideGlobals();

		// Default value is set
		$this->assertSame('bar', Input::request('foo', 'bar'));

		// Default value is not set
		$this->assertFalse(Input::request('foo'));
	}

	/**
	 * @covers Input::request
	 */
	public function testRequestWithArray()
	{
		Input::request([
			'foo' => 'bar',
			'bar' => 'foo',
		]);

		$this->assertSame('bar', Input::request('foo'));
		$this->assertSame('foo', Input::request('bar'));
	}

	/**
	 * @covers Input::post
	 */
	public function testPost()
	{
		$this->request->request->set('foo', 'bar');
		$this->request->overrideGlobals();

		$this->assertSame('bar', Input::post('foo'));
	}

	/**
	 * @covers Input::post
	 */
	public function testPostWithoutData()
	{
		$this->request->request->remove('foo');
		$this->request->overrideGlobals();

		// Default value is set
		$this->assertSame('bar', Input::post('foo', 'bar'));

		// Default value is not set
		$this->assertFalse(Input::post('foo'));
	}

	/**
	 * @covers Input::post
	 */
	public function testPostWithArray()
	{
		Input::post([
			'foo' => 'bar',
			'bar' => 'foo',
		]);

		$this->assertSame('bar', Input::post('foo'));
		$this->assertSame('foo', Input::post('bar'));
	}

	/**
	 * @covers Input::server
	 */
	public function testServer()
	{
		$this->request->server->set('HOST_NAME', 'localhost');
		$this->request->overrideGlobals();

		$this->assertSame($this->request->server->get('HOST_NAME'), Input::server('host_name'));

		$this->request->server->remove('HOST_NAME');
	}

	/**
	 * @covers Input::server
	 */
	public function testServerWithEmptyKey()
	{
		$this->request->server->set('HOST_NAME', 'localhost');
		$this->request->overrideGlobals();

		$this->assertSame($this->request->server->all(), Input::server());

		$this->request->server->remove('HOST_NAME');
	}

	/**
	 * @covers Input::session
	 */
	public function testSession()
	{
		$_SESSION['foo'] = 'bar';

		$this->assertSame('bar', Input::session('foo'));
	}

	/**
	 * @covers Input::session
	 */
	public function testSessionWithoutData()
	{
		unset($_SESSION['foo']);

		$this->assertNull(Input::session('foo'));
	}

	/**
	 * @covers Input::session
	 */
	public function testSessionWithArray()
	{
		Input::session([
			'foo' => 'bar',
			'bar' => 'foo',
		]);

		$this->assertSame('bar', Input::session('foo'));
		$this->assertSame('foo', Input::session('bar'));
	}

	/**
	 * @covers Input::isRequest
	 */
	public function testIsRequest()
	{
		$this->request->request->set('foo', 'bar');
		$this->request->overrideGlobals();

		$this->assertTrue(Input::isRequest('foo'));
		$this->assertFalse(Input::isRequest('bar'));

		$this->request->request->remove('foo');
	}

	/**
	 * @covers Input::isPost
	 */
	public function testIsPost()
	{
		$this->request->request->set('foo', 'bar');
		$this->request->overrideGlobals();

		$this->assertTrue(Input::isPost('foo'));
		$this->assertFalse(Input::isPost('bar'));

		$this->request->request->remove('foo');
	}

	/**
	 * @covers Input::isGet
	 */
	public function testIsGet()
	{
		$this->request->query->set('foo', 'bar');
		$this->request->overrideGlobals();

		$this->assertTrue(Input::isGet('foo'));
		$this->assertFalse(Input::isGet('bar'));

		$this->request->query->remove('foo');
	}

	/**
	 * @covers Input::xss
	 */
	public function testXss()
	{
		$source = /** @lang text */
			'<a href="foo">bar</a>';
		$result = '&lt;a href=&quot;foo&quot;&gt;bar&lt;/a&gt;';

		$this->assertSame($result, Input::xss($source));
	}

	/**
	 * @covers Input::xss
	 */
	public function testXssWithArray()
	{
		$source = /** @lang text */
			'<a href="foo">bar</a>';
		$result = '&lt;a href=&quot;foo&quot;&gt;bar&lt;/a&gt;';

		$source = [$source,	$source];
		$result = [$result, $result];

		$this->assertSame($result, Input::xss($source));
	}

	/**
	 * @covers Input::filter
	 */
	public function testFilter()
	{
		$this->assertNull(Input::filter('foo'));
	}

	/**
	 * @covers Input::filter
	 */
	public function testFilterWithWrongType()
	{
		$this->assertNull(Input::filter('foo', 'wrong'));
	}
}