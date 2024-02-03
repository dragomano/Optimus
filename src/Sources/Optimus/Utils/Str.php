<?php declare(strict_types=1);

/**
 * Str.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Utils;

use Bugo\Compat\{BBCodeParser, IntegrationHook, Utils};

if (! defined('SMF'))
	die('No direct access...');

final class Str
{
	public static function teaser(string $text, int $num_sentences = 2, int $length = 252): string
	{
		$text = BBCodeParser::load()->parse($text);

		// Replace all <br> and duplicate spaces
		$text = preg_replace('~\s+~', ' ', strip_tags(str_replace('<br>', ' ', $text)));

		// Remove all urls
		$text = preg_replace('~http(s)?://(.*)\s~U', '', $text);

		// Additional replacements
		$replacements = ['&nbsp;' => ' ', '&amp;nbsp;' => ' ', '&quot;' => ''];

		// External integrations
		IntegrationHook::call('integrate_optimus_teaser', [&$replacements]);

		$text = strtr($text, $replacements);

		$sentences = preg_split('/([.?!])(\s)/', $text);

		// Limit given text
		$text = Utils::shorten($text, $length);

		if (count($sentences) <= $num_sentences)
			return trim($text);

		$stop_at = 0;
		foreach ($sentences as $i => $sentence) {
			$stop_at += Utils::$smcFunc['strlen']($sentence);
			if ($i >= $num_sentences - 1)
				break;
		}

		$stop_at += ($num_sentences * 2);

		return trim(Utils::$smcFunc['substr']($text, 0, $stop_at));
	}
}
