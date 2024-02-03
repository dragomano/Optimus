<?php declare(strict_types=1);

/**
 * PrettyUrls.php
 *
 * @package PrettyUrls (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 23.01.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\{Config, Utils};
use Bugo\Optimus\Events\AddonEvent;
use Bugo\Optimus\Robots\Generator;
use Bugo\Optimus\Tasks\Sitemap;
use function pretty_rewrite_buffer;

if (! defined('SMF'))
	die('No direct access...');

final class PrettyUrls extends AbstractAddon
{
	public const PACKAGE_ID = 'el:prettyurls';

	public static array $events = [
		self::HOOK_EVENT,
		self::ROBOTS_RULES,
		self::SITEMAP_CONTENT,
	];

	public function __invoke(AddonEvent $event): void
	{
		match ($event->eventName()) {
			self::HOOK_EVENT => $this->addSupportKeywordsAction(),
			self::ROBOTS_RULES  => $this->changeRobots($event->getTarget()),
			self::SITEMAP_CONTENT => $this->changeSitemapContent($event->getTarget()),
		};
	}

	public function addSupportKeywordsAction(): void
	{
		if (isset(Utils::$context['pretty']['action_array']))
			Utils::$context['pretty']['action_array'][] = 'keywords';
	}

	public function changeRobots(object $generator): void
	{
		/* @var Generator $generator */
		$generator->useSef = ! empty(Config::$modSettings['pretty_enable_filters'])
			&& is_file(dirname(__DIR__, 2) . '/PrettyUrls-Filters.php');
	}

	public function changeSitemapContent(object $sitemap): void
	{
		$pretty = Config::$sourcedir . '/PrettyUrls-Filters.php';
		if (! file_exists($pretty) || empty(Config::$modSettings['pretty_enable_filters']))
			return;

		if (! function_exists('pretty_rewrite_buffer'))
			require_once($pretty);

		if (! isset(Utils::$context['session_var'])) {
			Utils::$context['session_var'] = substr(
				md5(Utils::$smcFunc['random_int']() . session_id() . Utils::$smcFunc['random_int']()),
				0,
				Utils::$smcFunc['random_int'](7, 12)
			);
		}

		Utils::$context['pretty']['search_patterns']  = ['~(<loc>)([^#<]+)~'];
		Utils::$context['pretty']['replace_patterns'] = ['~(<loc>)([^<]+)~'];

		/* @var Sitemap $sitemap */
		if (function_exists('pretty_rewrite_buffer'))
			pretty_rewrite_buffer($sitemap->content);
	}
}
