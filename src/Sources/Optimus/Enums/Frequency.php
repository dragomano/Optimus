<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC2
 */

namespace Bugo\Optimus\Enums;

enum Frequency: string
{
	case Always = 'always';
	case Hourly = 'hourly';
	case Daily = 'daily';
	case Weekly = 'weekly';
	case Monthly = 'monthly';
	case Yearly = 'yearly';

	public static function fromTimestamp(int $timestamp): self
	{
		$frequency = time() - $timestamp;

		return match (true) {
			$frequency < 86400 => self::Hourly,  // 24 * 60 * 60
			$frequency < 604800 => self::Daily,  // 7 * 24 * 60 * 60
			$frequency < 2628000 => self::Weekly, // (52 / 12) * 7 * 24 * 60 * 60
			$frequency < 31536000 => self::Monthly, // 365 * 24 * 60 * 60
			default => self::Yearly,
		};
	}
}
