<?php declare(strict_types=1);

/**
 * AddonInterface.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Events\AddonEvent;

interface AddonInterface
{
	public const HOOK_EVENT = 'optimus_hook_event';

	public const ROBOTS_RULES = 'optimus_robots_rules';

	public const SITEMAP_LINKS = 'optimus_sitemap_links';

	public const SITEMAP_CONTENT = 'optimus_sitemap_content';

	public const CREATE_SEF_URLS = 'optimus_create_sef_urls';

	public function __invoke(AddonEvent $event): void;
}