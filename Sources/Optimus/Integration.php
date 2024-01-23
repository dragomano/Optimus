<?php declare(strict_types=1);

/**
 * Integration.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus;

use Bugo\Optimus\Addons\AddonInterface;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Events\DispatcherFactory;
use Bugo\Optimus\Handlers\HandlerLoader;
use Bugo\Optimus\Utils\Copyright;

if (! defined('SMF'))
	die('No direct access...');

final class Integration
{
	public function __construct()
	{
		new HandlerLoader();
	}

	public function __invoke(): void
	{
		add_integration_function('integrate_load_theme', self::class . '::loadTheme#', false, __FILE__);
		add_integration_function('integrate_load_permissions', self::class . '::loadPermissions#', false, __FILE__);
		add_integration_function('integrate_credits', self::class . '::credits#', false, __FILE__);

		(new DispatcherFactory())()->dispatch(new AddonEvent(AddonInterface::HOOK_EVENT, $this));
	}

	public function loadTheme(): void
	{
		loadLanguage('Optimus/Optimus');
	}

	public function loadPermissions(array $permissionGroups, array &$permissionList): void
	{
		global $modSettings;

		if (empty($modSettings['optimus_log_search']))
			return;

		$scope = isset($permissionList['global']) ? 'global' : 'membergroup';

		$permissionList[$scope]['optimus_view_search_terms'] = [false, 'general', 'view_basic_info'];
	}

	public function credits(): void
	{
		global $context;

		$context['credits_modifications'][] = Copyright::getLink() . ' &copy; 2010&ndash;' . date('Y') . ', Bugo';
	}
}
