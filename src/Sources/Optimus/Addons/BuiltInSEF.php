<?php declare(strict_types=1);

/**
 * @package BuiltInSEF (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 26.05.25
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\Config;
use Bugo\Compat\QueryString;
use Bugo\Optimus\Enums\Entity;
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Services\RobotsGenerator;
use Bugo\Optimus\Services\SitemapGenerator;
use League\Event\ListenerPriority;

class BuiltInSEF extends AbstractAddon
{
	public const PACKAGE_ID = 'Optimus:BuiltInSEF';

	public const PRIORITY = ListenerPriority::HIGH;

	public static array $events = [
		self::ROBOTS_RULES,
		self::SITEMAP_CONTENT,
	];

	public function __invoke(AddonEvent $event): void
	{
		if (str_starts_with(SMF_VERSION, '3.0') === false || empty(Config::$modSettings['queryless_urls']))
			return;

		match ($event->eventName()) {
			self::ROBOTS_RULES    => $this->changeRobots($event->getTarget()),
			self::SITEMAP_CONTENT => $this->changeSitemapContent($event->getTarget()),
		};
	}

	public function changeRobots(RobotsGenerator $generator): void
	{
		$generator->useSef = true;

		$generator->customRules['*'][$generator::RULE_ALLOW][] = $generator->urlPath . Entity::BOARD->buildPattern();
		$generator->customRules['*'][$generator::RULE_ALLOW][] = $generator->urlPath . Entity::TOPIC->buildPattern();
		$generator->customRules['*'][$generator::RULE_ALLOW][] = $generator->urlPath . Entity::MSG->buildPattern();
	}

	public function changeSitemapContent(SitemapGenerator $generator): void
	{
		$generator->content = QueryString::rewriteAsQueryless($generator->content);
	}
}
