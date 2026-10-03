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

interface HasSettingsInterface
{
	/**
	 * Return the config vars of the addon (in the standard SMF format)
	 * to display them in a separate block on the addon list page.
	 */
	public function getSettings(): array;
}
