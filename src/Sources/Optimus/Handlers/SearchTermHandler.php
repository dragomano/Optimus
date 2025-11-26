<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\Cache\CacheApi;
use Bugo\Compat\{Config, Db, IntegrationHook};
use Bugo\Compat\{Theme, User, Utils};
use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...');

final class SearchTermHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_load_permissions', self::class . '::loadPermissions#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_permissions_list', self::class . '::permissionsList#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::prepareSearchTerms#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_search_params', self::class . '::searchParams#', false, __FILE__
		);
	}

	public function loadPermissions(array $permissionGroups, array &$permissionList): void
	{
		if (empty(Config::$modSettings['optimus_log_search']))
			return;

		$permissionList['membergroup']['optimus_view_search_terms'] = [false, 'general', 'view_basic_info'];
	}

	public function permissionsList(array &$permissions): void
	{
		if (empty(Config::$modSettings['optimus_log_search']))
			return;

		$permissions['optimus_view_search_terms'] = [
			'view_group'   => 'general',
			'scope'        => 'global',
			'never_guests' => true,
		];
	}

	public function prepareSearchTerms(): void
	{
		if (empty(Config::$modSettings['optimus_log_search']))
			return;

		if (Utils::$context['current_action'] !== 'search' && Utils::$context['current_action'] !== 'search2')
			return;

		if ((Utils::$context['search_terms'] = CacheApi::get('optimus_search_terms', 3600)) === null) {
			$result = Db::$db->query(/** @lang text */ '
				SELECT phrase, hit
				FROM {db_prefix}optimus_search_terms
				ORDER BY hit DESC
				LIMIT 30',
			);

			$scale = 1;
			while ($row = Db::$db->fetch_assoc($result)) {
				$scale < $row['hit'] && $scale = $row['hit'];

				Utils::$context['search_terms'][] = [
					'text'  => $row['phrase'],
					'scale' => round(($row['hit'] * 100) / $scale),
					'hit'   => $row['hit'],
				];
			}

			Db::$db->free_result($result);

			CacheApi::put('optimus_search_terms', Utils::$context['search_terms'], 3600);
		}

		$this->showChart();
	}

	public function searchParams(): bool
	{
		if (empty(Config::$modSettings['optimus_log_search']) || ! Input::request('search')) {
			return false;
		}

		$searchString = Utils::htmlspecialcharsDecode(Input::request('search'));

		if (empty($searchString)) {
			return false;
		}

		$result = Db::$db->query('
			SELECT id_term
			FROM {db_prefix}optimus_search_terms
			WHERE phrase = {string:phrase}
			LIMIT 1',
			[
				'phrase' => $searchString,
			]
		);

		[$id] = Db::$db->fetch_row($result);
		Db::$db->free_result($result);

		if (empty($id)) {
			Db::$db->insert('insert',
				'{db_prefix}optimus_search_terms',
				[
					'phrase' => 'string-255',
					'hit'    => 'int',
				],
				[$searchString, 1],
				['id_term'],
			);
		} else {
			Db::$db->query('
				UPDATE {db_prefix}optimus_search_terms
				SET hit = hit + 1
				WHERE id_term = {int:id_term}',
				[
					'id_term' => $id,
				]
			);
		}

		return true;
	}

	private function showChart(): void
	{
		if (empty(Utils::$context['search_terms']) || ! $this->canView())
			return;

		Theme::loadTemplate('Optimus');

		Utils::$context['template_layers'][] = 'search_terms';
	}

	private function canView(): bool
	{
		if (empty(Config::$modSettings['optimus_log_search'])) {
			return false;
		}

		return User::$me->allowedTo('optimus_view_search_terms');
	}
}
