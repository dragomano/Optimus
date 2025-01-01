<?php declare(strict_types=1);

/**
 * @package SafeLinks (Optimus)
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @category addon
 * @version 09.12.24
 */

namespace Bugo\Optimus\Addons;

use Bugo\Compat\IntegrationHook;
use Bugo\Optimus\Events\AddonEvent;

if (! defined('SMF'))
	die('No direct access...');

final class SafeLinks extends AbstractAddon
{
	public const PACKAGE_ID = 'Optimus:SafeLinks';

	public static array $events = [
		self::HOOK_EVENT,
	];

	public function __invoke(AddonEvent $event): void
	{
		if ($event->eventName() !== self::HOOK_EVENT)
			return;

		IntegrationHook::add(
			'integrate_bbc_codes', self::class . '::changeAttributesForLinks#', false, __FILE__
		);
	}

	public function changeAttributesForLinks(array &$codes): void
	{
		foreach ($codes as &$code) {
			if ($code['tag'] !== 'url') continue;

			if ($code['type'] === 'unparsed_content') {
				$code['content'] = $this->replaceRel($code['content']);
			}

			if ($code['type'] === 'unparsed_equals') {
				$code['before'] = $this->replaceRel($code['before']);
			}
		}

		unset($code);
	}

	private function replaceRel(string $link): string
	{
		return str_replace('rel="noopener"', 'rel="noopener noreferrer nofollow"', $link);
	}
}
