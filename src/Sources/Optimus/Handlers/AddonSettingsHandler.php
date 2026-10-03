<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2026 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.1
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\Actions\Admin\ACP;
use Bugo\Compat\{Config, Lang, User, Utils};
use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...'); // @codeCoverageIgnore

/**
 * The admin page with a list of all addons of the modification.
 * Allows to enable/disable any addon and edit its own settings, if any.
 */
final class AddonSettingsHandler
{
	public function handle(): void
	{
		Utils::$context['page_title'] .= ' - ' . Lang::getTxt('optimus_addons_title');

		$packageId = (string) Input::request('addon');

		if ($packageId !== '') {
			$this->saveAddonSettings($packageId);
			return;
		}

		if (Input::isGet('toggle')) {
			$this->toggleAddon((string) Input::request('toggle'));
		}

		$this->renderAddonList();
	}

	private function renderAddonList(): void
	{
		Utils::$context['sub_template'] = 'addons';

		$addons = (new AddonHandler())->getAddonData();

		$formTokens = [];

		foreach ($addons as $i => $row) {
			if ($row['is_active'] && $row['has_settings']) {
				$addons[$i]['settings_html'] = $this->getSettingsHtml($row);
				$formTokens[$i] = [
					Utils::$context['admin-dbsc_token_var'] ?? '',
					Utils::$context['admin-dbsc_token'] ?? '',
				];
			}
		}

		// SMF keeps only the last created admin-dbsc token in the session,
		// and its input name is random, so every captured form must be
		// aligned with it, otherwise token verification will fail
		// when saving from any form except the last one
		if ($formTokens !== []) {
			[$actualVar, $actualToken] = $formTokens[array_key_last($formTokens)];

			foreach ($formTokens as $i => [$tokenVar, $token]) {
				if ($tokenVar === $actualVar) {
					continue;
				}

				$addons[$i]['settings_html'] = str_replace(
					'<input type="hidden" name="' . $tokenVar . '" value="' . $token . '">',
					'<input type="hidden" name="' . $actualVar . '" value="' . $actualToken . '">',
					$addons[$i]['settings_html'],
				);
			}
		}

		Utils::$context['optimus_addons'] = $addons;
	}

	/**
	 * Render the settings form of a single addon into a string
	 * to expand it right on the addon list page.
	 */
	private function getSettingsHtml(array $row): string
	{
		$instance = $this->getInstance($row);

		Utils::$context['settings_title'] = sprintf(
			Lang::getTxt('optimus_addons_settings_title'), $row['name']
		);

		Utils::$context['post_url'] = Config::$scripturl
			. '?action=admin;area=optimus;sa=addons;addon=' . urlencode($row['package_id']) . ';save';

		$config_vars = $instance->getSettings();

		Utils::$context['settings'] = [];

		ACP::prepareDBSettingContext($config_vars);

		ob_start();
		template_show_settings();
		return ob_get_clean();
	}

	private function saveAddonSettings(string $packageId): void
	{
		if (! Input::isGet('save')) {
			Utils::redirectexit('action=admin;area=optimus;sa=addons');
			return;
		}

		$addon = $this->findAddon($packageId);

		if ($addon === false || ! $addon['is_active'] || ! $addon['has_settings']) {
			Utils::redirectexit('action=admin;area=optimus;sa=addons');
			return;
		}

		$instance = $this->getInstance($addon);

		User::$me->checkSession();

		$instance->saveSettings();

		$config_vars = $instance->getSettings();

		ACP::saveDBSettings($config_vars);

		Utils::redirectexit('action=admin;area=optimus;sa=addons');
	}

	private function toggleAddon(string $packageId): void
	{
		User::$me->checkSession('get');

		(new AddonHandler())->toggle($packageId);

		Utils::redirectexit('action=admin;area=optimus;sa=addons');
	}

	private function findAddon(string $packageId): array|false
	{
		return current(array_filter(
			(new AddonHandler())->getAddonData(),
			static fn(array $row): bool => $row['package_id'] === $packageId,
		));
	}

	private function getInstance(array $row): object
	{
		$class = $row['class'];

		return new $class();
	}
}
