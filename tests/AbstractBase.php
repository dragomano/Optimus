<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBase extends TestCase
{
	protected function setUp(): void
	{
		require_once __DIR__ . '/boostrap.php';

		require_once dirname(__DIR__) . '/src/Sources/Optimus/app.php';

		$this->request = Request::createFromGlobals();
	}

	protected function tearDown(): void
	{
		$this->request->overrideGlobals();
	}
}
