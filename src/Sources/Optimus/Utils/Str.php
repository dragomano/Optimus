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

namespace Bugo\Optimus\Utils;

use Bugo\Compat\{IntegrationHook, Utils};
use Bugo\Compat\Parsers\BBCodeParser;
use Nette\Utils\Html;

if (! defined('SMF'))
	die('No direct access...'); // @codeCoverageIgnore

final class Str
{
	public static function teaser(string $text, int $sentencesCount = 2, int $length = 252): string
	{
		$text = BBCodeParser::load()->parse($text);

		// Replace all <br>
		$text = str_replace('<br>', ' ', $text);
		$text = strip_tags($text);

		// Remove all urls
		$text = preg_replace('~http(s)?://\S+~', '', $text);

		// Replace duplicate spaces
		$text = preg_replace('~\s+~', ' ', $text);

		// Additional replacements
		$replacements = ['&nbsp;' => ' ', '&amp;nbsp;' => ' ', '&quot;' => ''];

		// External integrations
		IntegrationHook::call('integrate_optimus_teaser', [&$replacements]);

		$text = strtr($text, $replacements);

		$sentences = preg_split('/([.?!])(\s)/', $text);

		// Limit given text
		$text = Utils::shorten($text, $length);

		if (count($sentences) <= $sentencesCount) {
			return trim($text);
		}

		$stopAt = 0;
		foreach ($sentences as $i => $sentence) {
			$stopAt += Utils::$smcFunc['strlen']($sentence);
			if ($i >= $sentencesCount - 1)
				break;
		}

		$stopAt += ($sentencesCount * 2);

		return trim(Utils::$smcFunc['substr']($text, 0, $stopAt));
	}

	public static function html(?string $name = null, array|string|null $params = null): Html
	{
		return Html::el($name, $params);
	}
}
