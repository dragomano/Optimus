<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC5
 */

namespace Bugo\Optimus\Enums;

enum Priority: string
{
	case Supreme = '1.0';    // Highest priority, absolute SEO focus
	case Prime = '0.8';      // High priority, regularly refreshed
	case Elevated = '0.6';   // Important, but not top-level
	case Base = '0.4';       // Standard content, normal indexing
	case Minimal = '0.2';    // Low relevance, rarely updated

	public static function fromTimestamp(int $timestamp): self
	{
		$daysDiff = floor((time() - $timestamp) / 86400);

		return match (true) {
			$daysDiff <= 30 => self::Prime,
			$daysDiff <= 60 => self::Elevated,
			$daysDiff <= 90 => self::Base,
			default => self::Minimal,
		};
	}
}
