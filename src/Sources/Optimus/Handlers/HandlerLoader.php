<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 RC1
 */

namespace Bugo\Optimus\Handlers;

if (! defined('SMF'))
	die('No direct access...');

final class HandlerLoader
{
	private array $handlers = [
		CoreHandler::class,
		SettingHandler::class,
		BoardHandler::class,
		TagHandler::class,
		TopicHandler::class,
		FrontPageHandler::class,
		AssetHandler::class,
		TitleHandler::class,
		MetaHandler::class,
		FaviconHandler::class,
		SearchTermHandler::class,
		ErrorPageHandler::class,
		SitemapLinkHandler::class,
		RedirectHandler::class,
		AddonHandler::class,
	];

	public function __construct()
	{
		array_map(static fn($handler) => (new $handler())(), $this->handlers);
	}
}
