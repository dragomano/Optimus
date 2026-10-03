<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2026 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{IntegrationHook, Lang};

if (! defined('SMF'))
	die('No direct access...'); // @codeCoverageIgnore

/**
 * Addons that are not included in the standard Optimus package.
 * An entry is displayed on the Addons page with a download button
 * only while the addon is physically absent on the forum.
 */
final class DownloadableAddons
{
	public const PREMIUM_URL = 'https://ko-fi.com/post/All-premium-addons-for-Optimus-U7U3VKQHJ';

	public static function all(): array
	{
		$addons = [
			[
				'name'        => 'ExampleAddon',
				'package_id'  => 'Optimus:ExampleAddon',
				'description' => Lang::getTxt('optimus_addon_exampleaddon_desc'),
				'url'         => 'https://github.com/dragomano/Optimus/blob/main/src/Sources/Optimus/Addons/ExampleAddon.php',
			],
			[
				'name'        => 'ExtraSettings',
				'package_id'  => 'Optimus:ExtraSettings',
				'description' => Lang::getTxt('optimus_addon_extrasettings_desc'),
				'url'         => self::PREMIUM_URL,
			],
			[
				'name'        => 'IndexNow',
				'package_id'  => 'Optimus:IndexNow',
				'description' => Lang::getTxt('optimus_addon_indexnow_desc'),
				'url'         => self::PREMIUM_URL,
			],
			[
				'name'        => 'SafeLinks',
				'package_id'  => 'Optimus:SafeLinks',
				'description' => Lang::getTxt('optimus_addon_safelinks_desc'),
				'url'         => 'https://github.com/dragomano/Optimus/blob/main/src/Sources/Optimus/Addons/SafeLinks.php',
			],
			[
				'name'        => 'StructuredData',
				'package_id'  => 'Optimus:StructuredData',
				'description' => Lang::getTxt('optimus_addon_structureddata_desc'),
				'url'         => self::PREMIUM_URL,
			],
		];

		// External integrations
		IntegrationHook::call('integrate_optimus_downloadable_addons', [&$addons]);

		return $addons;
	}
}
