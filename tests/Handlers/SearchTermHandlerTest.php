<?php declare(strict_types=1);

namespace Tests\Handlers;

use Bugo\Optimus\Handlers\SearchTermHandler;
use Bugo\Optimus\Utils\Input;
use Tests\AbstractBase;

/**
 * @requires PHP 8.0
 */
class SearchTermHandlerTest extends AbstractBase
{
	protected function setUp(): void
	{
		parent::setUp();

		$this->handler = new SearchTermHandler();
	}

	/**
	 * @covers SearchTermHandler::loadPermissions
	 */
	public function testLoadPermissions()
	{
		global $modSettings;

		$permissionList = [];

		$modSettings['optimus_log_search'] = false;

		$this->handler->loadPermissions([], $permissionList);

		$this->assertEmpty($permissionList);
	}

	/**
	 * @covers SearchTermHandler::loadPermissions
	 */
	public function testLoadPermissionsEnabled()
	{
		global $modSettings;

		$permissionList = [];

		$modSettings['optimus_log_search'] = true;

		$this->handler->loadPermissions([], $permissionList);

		$this->assertArrayHasKey(
			'optimus_view_search_terms',
			$permissionList['membergroup']
		);
	}

	/**
	 * @covers SearchTermHandler::prepareSearchTerms
	 */
	public function testPrepareSearchTerms()
	{
		global $modSettings, $context;

		$modSettings['optimus_log_search'] = false;

		$this->handler->prepareSearchTerms();

		$this->assertArrayNotHasKey('search_terms', $context);
	}

	/**
	 * @covers SearchTermHandler::prepareSearchTerms
	 */
	public function testPrepareSearchTermsEnabled()
	{
		global $modSettings, $context;

		$modSettings['optimus_log_search'] = true;

		$context['current_action'] = 'search';

		$this->handler->prepareSearchTerms();

		$this->assertSame([], $context['search_terms']);
	}

	/**
	 * @covers SearchTermHandler::searchParams
	 */
	public function testSearchParams()
	{
		global $modSettings;

		$modSettings['optimus_log_search'] = false;

		$this->assertFalse($this->handler->searchParams());
	}

	/**
	 * @covers SearchTermHandler::searchParams
	 */
	public function testSearchParamsEnabled()
	{
		global $modSettings;

		$modSettings['optimus_log_search'] = true;

		Input::request(['search' => 'bar']);

		$this->assertTrue($this->handler->searchParams());
	}
}