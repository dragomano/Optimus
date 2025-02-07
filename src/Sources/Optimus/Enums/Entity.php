<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2025 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC3
 */

namespace Bugo\Optimus\Enums;

use Bugo\Compat\Config;

enum Entity: string
{
	case TOPIC = 'topic';
	case BOARD = 'board';

	public function buildUrl(string $id): string
	{
		return Config::$scripturl . match (true) {
			empty(Config::$modSettings['queryless_urls']) => "?$this->value=$id",
			str_starts_with(SMF_VERSION, '3.0') => "?$this->value=$id",
			default => "/$this->value,$id.html",
		};
	}

	public function buildPattern(): string
	{
		return match (true) {
			empty(Config::$modSettings['queryless_urls']) => "/*$this->value=*.0$",
			str_starts_with(SMF_VERSION, '3.0') => "/{$this->value}s/*$",
			default => "/*$this->value,*.0.html$",
		};
	}
}
