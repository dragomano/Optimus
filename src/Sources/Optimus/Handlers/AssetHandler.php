<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC2
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\{Config, IntegrationHook};
use Bugo\Compat\{Theme, Utils};
use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...');

final class AssetHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_load_theme', self::class . '::handle#', false, __FILE__
		);
	}

	public function handle(): void
	{
		if (Input::isRequest('xml'))
			return;

		if (stripos((string) Input::server('http_user_agent'), 'Lighthouse') !== false)
			return;

		if (in_array(
			Utils::$context['current_action'],
			explode(',', Config::$modSettings['optimus_ignored_actions'] ?? '')
		)) {
			return;
		}

		$this->prepareHeadCode();
		$this->prepareStatCode();
		$this->prepareCountCode();
	}

	private function prepareHeadCode(): void
	{
		if (empty(Config::$modSettings['optimus_head_code']))
			return;

		$head = explode(PHP_EOL, trim(Config::$modSettings['optimus_head_code']));

		foreach ($head as $part) {
			Utils::$context['html_headers'] .= "\n\t" . $part;
		}
	}

	private function prepareStatCode(): void
	{
		if (empty(Config::$modSettings['optimus_stat_code']))
			return;

		$stat = explode(PHP_EOL, trim(Config::$modSettings['optimus_stat_code']));

		foreach ($stat as $part) {
			Utils::$context['insert_after_template'] .= "\n\t" . $part;
		}
	}

	private function prepareCountCode(): void
	{
		if (empty(Config::$modSettings['optimus_count_code']))
			return;

		Theme::loadTemplate('Optimus');
		Utils::$context['template_layers'][] = 'footer_counters';

		if (empty(Config::$modSettings['optimus_counters_css']))
			return;

		Theme::addInlineCss(Config::$modSettings['optimus_counters_css']);
	}
}
