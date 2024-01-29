<?php declare(strict_types=1);

/**
 * SearchTermHandler.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...');

final class SearchTermHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_load_permissions', self::class . '::loadPermissions#', false, __FILE__);
		add_integration_function('integrate_menu_buttons', self::class . '::prepareSearchTerms#', false, __FILE__);
		add_integration_function('integrate_search_params', self::class . '::searchParams#', false, __FILE__);
	}

	public function loadPermissions(array $permissionGroups, array &$permissionList): void
	{
		global $modSettings;

		if (empty($modSettings['optimus_log_search']))
			return;

		$permissionList['membergroup']['optimus_view_search_terms'] = [false, 'general', 'view_basic_info'];
	}

	public function prepareSearchTerms(): void
	{
		global $modSettings, $context, $smcFunc;

		if (empty($modSettings['optimus_log_search']))
			return;

		if ($context['current_action'] !== 'search' && $context['current_action'] !== 'search2')
			return;

		if (($context['search_terms'] = cache_get_data('optimus_search_terms', 3600)) === null) {
			$request = $smcFunc['db_query']('', /** @lang text */ '
				SELECT phrase, hit
				FROM {db_prefix}optimus_search_terms
				ORDER BY hit DESC
				LIMIT 30'
			);

			$scale = 1;
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				if ($scale < $row['hit'])
					$scale = $row['hit'];

				$context['search_terms'][] = [
					'text'  => $row['phrase'],
					'scale' => round(($row['hit'] * 100) / $scale),
					'hit'   => $row['hit']
				];
			}

			$smcFunc['db_free_result']($request);

			cache_put_data('optimus_search_terms', $context['search_terms'], 3600);
		}

		$this->showChart();
	}

	public function searchParams(): bool
	{
		global $modSettings, $smcFunc;

		if (empty($modSettings['optimus_log_search']) || ! Input::request('search'))
			return false;

		$searchString = un_htmlspecialchars(Input::request('search'));

		if (empty($searchString))
			return false;

		$request = $smcFunc['db_query']('', '
			SELECT id_term
			FROM {db_prefix}optimus_search_terms
			WHERE phrase = {string:phrase}
			LIMIT 1',
			[
				'phrase' => $searchString
			]
		);

		[$id] = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		if (empty($id)) {
			$smcFunc['db_insert']('insert',
				'{db_prefix}optimus_search_terms',
				[
					'phrase' => 'string-255',
					'hit'    => 'int'
				],
				[$searchString, 1],
				['id_term']
			);
		} else {
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}optimus_search_terms
				SET hit = hit + 1
				WHERE id_term = {int:id_term}',
				[
					'id_term' => $id
				]
			);
		}

		return true;
	}

	private function showChart(): void
	{
		global $context;

		if (empty($context['search_terms']) || ! $this->canView())
			return;

		loadTemplate('Optimus');

		$context['template_layers'][] = 'search_terms';
	}

	private function canView(): bool
	{
		global $modSettings;

		if (empty($modSettings['optimus_log_search']))
			return false;

		return allowedTo('optimus_view_search_terms');
	}
}