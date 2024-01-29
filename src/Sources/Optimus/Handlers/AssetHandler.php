<?php declare(strict_types=1);

/**
 * CounterHandler.php
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

final class AssetHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_load_theme', self::class . '::handle#', false, __FILE__);
	}

	public function handle(): void
	{
		global $context, $modSettings;

		if (Input::isRequest('xml'))
			return;

		if (stripos((string) Input::server('http_user_agent'), 'Lighthouse') !== false)
			return;

		if (in_array($context['current_action'], explode(',', $modSettings['optimus_ignored_actions'] ?? '')))
			return;

		if (! empty($modSettings['optimus_head_code'])) {
			$head = explode(PHP_EOL, trim($modSettings['optimus_head_code']));

			foreach ($head as $part)
				$context['html_headers'] .= "\n\t" . $part;
		}

		if (! empty($modSettings['optimus_stat_code'])) {
			$stat = explode(PHP_EOL, trim($modSettings['optimus_stat_code']));

			foreach ($stat as $part)
				$context['insert_after_template'] .= "\n\t" . $part;
		}

		if (! empty($modSettings['optimus_count_code'])) {
			loadTemplate('Optimus');
			$context['template_layers'][] = 'footer_counters';

			if (! empty($modSettings['optimus_counters_css']))
				addInlineCss($modSettings['optimus_counters_css']);
		}
	}
}