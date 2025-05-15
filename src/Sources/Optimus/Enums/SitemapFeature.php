<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC4
 */

namespace Bugo\Optimus\Enums;

use Bugo\Compat\Config;

enum SitemapFeature: string
{
	case Mobile = 'mobile';
	case Images = 'images';
	case Videos = 'videos';

	private const SETTINGS_MAP = [
		'optimus_sitemap_mobile',
		'optimus_sitemap_add_found_images',
		'optimus_sitemap_add_found_videos',
	];

	public function isEnabled(): bool
	{
		$map = array_combine(array_map(fn($case) => $case->value, self::cases()), self::SETTINGS_MAP);

		return ! empty(Config::$modSettings[$map[$this->value]]);
	}

	public static function getOptions(): array
	{
		return array_reduce(self::cases(), function ($options, $case) {
			$options[$case->value] = $case->isEnabled();
			return $options;
		}, []);
	}
}
