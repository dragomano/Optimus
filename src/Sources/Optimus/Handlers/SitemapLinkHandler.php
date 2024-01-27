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

final class SitemapLinkHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_actions', self::class . '::actions#', false, __FILE__);
		add_integration_function('integrate_pre_log_stats', self::class . '::preLogStats#', false, __FILE__);
		add_integration_function('integrate_menu_buttons', self::class . '::addLink#', false, __FILE__);

	}

	public function actions(array &$actions): void
	{
		$actions['sitemap_xsl'] = [false, [$this, 'xsl']];
	}

	public function xsl(): void
	{
		global $modSettings, $settings, $txt;

		ob_end_clean();

		empty($modSettings['enableCompressedOutput']) ? ob_start() : @ob_start('ob_gzhandler');

		header('content-type: text/xsl; charset=UTF-8');

		$content = file_get_contents($settings['default_theme_dir'] . '/css/optimus/sitemap.xsl');

		$content = strtr($content, [
			'{link}'          => $settings['theme_url'] . '/css/index.css',
			'{sitemap}'       => $txt['optimus_sitemap_title'],
			'{mobile}'        => $txt['optimus_mobile'],
			'{images}'        => $txt['optimus_images'],
			'{news}'          => $txt['optimus_news'],
			'{video}'         => $txt['optimus_video'],
			'{index}'         => $txt['optimus_index'],
			'{total_files}'   => $txt['optimus_total_files'],
			'{total_urls}'    => $txt['optimus_total_urls'],
			'{url}'           => $txt['url'],
			'{last_modified}' => $txt['optimus_last_modified'],
			'{frequency}'     => $txt['optimus_frequency'],
			'{priority}'      => $txt['optimus_priority'],
			'{direct_link}'   => $txt['optimus_direct_link'],
			'{caption}'       => $txt['optimus_caption'],
			'{thumbnail}'     => $txt['optimus_thumbnail'],
			'{optimus}'       => OP_NAME
		]);

		echo $content;

		obExit(false);
	}

	public function preLogStats(array &$no_stat_actions): void
	{
		$no_stat_actions['sitemap_xsl'] = true;
	}

	public function addLink(): void
	{
		global $modSettings, $txt, $forum_copyright, $boardurl, $context;

		if (empty($modSettings['optimus_sitemap_link']) || empty($txt['optimus_sitemap_title']))
			return;

		$forum_copyright .= ' | <a href="' . $boardurl . '/sitemap.xml">' . $txt['optimus_sitemap_title'] . '</a>';

		$context['html_headers'] .= "\n\t" . '<link rel="sitemap" type="application/xml" title="Sitemap" href="' . $boardurl . '/sitemap.xml">';
	}
}