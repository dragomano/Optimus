<?php declare(strict_types=1);

/**
 * HandlerLoader.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Handlers;

if (! defined('SMF'))
	die('No direct access...');

final class HandlerLoader
{
	private array $handlers = [
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
		ErrorHandler::class,
		SitemapLinkHandler::class,
		RedirectHandler::class,
		AddonHandler::class,
	];

	public function __construct()
	{
		array_map(static fn($handler) => (new $handler())(), $this->handlers);
	}
}
