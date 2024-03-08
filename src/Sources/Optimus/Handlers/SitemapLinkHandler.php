<?php declare(strict_types=1);

/**
 * Sitemap.php
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

use Bugo\Compat\{Config, IntegrationHook};
use Bugo\Compat\{Lang, Theme, Utils};

if (! defined('SMF'))
	die('No direct access...');

final class SitemapLinkHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_actions', self::class . '::actions#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_pre_log_stats', self::class . '::preLogStats#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::addLink#', false, __FILE__
		);
	}

	public function actions(array &$actions): void
	{
		$actions['sitemap_xsl'] = [false, [$this, 'xsl']];
	}

	public function xsl(): void
	{
		ob_end_clean();

		empty(Config::$modSettings['enableCompressedOutput']) ? ob_start() : @ob_start('ob_gzhandler');

		header('content-type: text/xsl; charset=UTF-8');

		$content = file_get_contents(Theme::$current->settings['default_theme_dir'] . '/css/optimus/sitemap.xsl');

		$content = strtr($content, [
			'{link}'          => Theme::$current->settings['theme_url'] . '/css/index.css',
			'{sitemap}'       => Lang::$txt['optimus_sitemap_title'],
			'{mobile}'        => Lang::$txt['optimus_mobile'],
			'{images}'        => Lang::$txt['optimus_images'],
			'{news}'          => Lang::$txt['optimus_news'],
			'{video}'         => Lang::$txt['optimus_video'],
			'{index}'         => Lang::$txt['optimus_index'],
			'{total_files}'   => Lang::$txt['optimus_total_files'],
			'{total_urls}'    => Lang::$txt['optimus_total_urls'],
			'{url}'           => Lang::$txt['url'],
			'{last_modified}' => Lang::$txt['optimus_last_modified'],
			'{frequency}'     => Lang::$txt['optimus_frequency'],
			'{priority}'      => Lang::$txt['optimus_priority'],
			'{direct_link}'   => Lang::$txt['optimus_direct_link'],
			'{caption}'       => Lang::$txt['optimus_caption'],
			'{thumbnail}'     => Lang::$txt['optimus_thumbnail'],
			'{optimus}'       => OP_NAME,
		]);

		echo $content;

		Utils::obExit(false);
	}

	public function preLogStats(array &$no_stat_actions): void
	{
		$no_stat_actions['sitemap_xsl'] = true;
	}

	public function addLink(): void
	{
		if (empty(Config::$modSettings['optimus_sitemap_link']) || empty(Lang::$txt['optimus_sitemap_title']))
			return;

		Lang::$forum_copyright .= ' | <a href="' . Config::$boardurl . '/sitemap.xml">'
			. Lang::$txt['optimus_sitemap_title'] . '</a>';

		Utils::$context['html_headers'] .= "\n\t" . '<link rel="sitemap" type="application/xml" title="Sitemap" href="'
			. Config::$boardurl . '/sitemap.xml">';
	}
}
