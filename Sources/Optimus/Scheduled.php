<?php

/**
 * Scheduled.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2019 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.2
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Вызов генерации карты через Диспетчер задач
 *
 * @return void
 */
function scheduled_optimus_sitemap()
{
	global $sourcedir;

	// Additional links for Sitemap
	require_once($sourcedir . '/Optimus/Sitemap-Addons.php');
	$urls = (new \Bugo\Optimus\SitemapAddons())->getTPLinks();

	// All links
	require_once($sourcedir . '/Optimus/Sitemap.php');
	$sitemap = new \Bugo\Optimus\Sitemap(false, $urls);

	return $sitemap->create();
}
