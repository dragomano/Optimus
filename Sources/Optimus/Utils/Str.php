<?php declare(strict_types=1);

/**
 * Teaser.php
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

if (! defined('SMF'))
	die('No direct access...');

final class Str
{
	public static function teaser(string $text, int $num_sentences = 2, int $length = 252): string
	{
		global $smcFunc;

		$text = parse_bbc($text);

		// Replace all <br> and duplicate spaces
		$text = preg_replace('~\s+~', ' ', strip_tags(str_replace('<br>', ' ', $text)));

		// Remove all urls
		$text = preg_replace('~http(s)?://(.*)\s~U', '', $text);

		// Additional replacements
		$replacements = ['&nbsp;' => ' ', '&amp;nbsp;' => ' ', '&quot;' => ''];

		call_integration_hook('integrate_optimus_teaser', [&$replacements]);

		$text = strtr($text, $replacements);

		$sentences = preg_split('/([.?!])(\s)/', $text);

		// Limit given text
		$text = shorten_subject($text, $length);

		if (count($sentences) <= $num_sentences)
			return trim($text);

		$stop_at = 0;
		foreach ($sentences as $i => $sentence) {
			$stop_at += $smcFunc['strlen']($sentence);
			if ($i >= $num_sentences - 1)
				break;
		}

		$stop_at += ($num_sentences * 2);

		return trim($smcFunc['substr']($text, 0, $stop_at));
	}
}
