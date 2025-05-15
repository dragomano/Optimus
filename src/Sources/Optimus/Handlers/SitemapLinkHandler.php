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

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\{Config, IntegrationHook};
use Bugo\Compat\{Lang, Theme, Utils};
use Bugo\Optimus\Enums\Action;
use Bugo\Optimus\Utils\Str;

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
		$actions[Action::XSL->value] = [false, $this->xsl(...)];
	}

	public function xsl(): void
	{
		ob_end_clean();

		empty(Config::$modSettings['enableCompressedOutput']) ? ob_start() : @ob_start('ob_gzhandler');

		header('content-type: text/xsl; charset=UTF-8');

		$content = file_get_contents(Theme::$current->settings['default_theme_dir'] . '/css/optimus/sitemap.xsl');

		$content = strtr($content, [
			'{link}'          => Theme::$current->settings['theme_url'] . '/css/index.css',
			'{sitemap}'       => Lang::getTxt('optimus_sitemap_title', file: 'Optimus/Optimus'),
			'{mobile}'        => Lang::getTxt('optimus_mobile'),
			'{images}'        => Lang::getTxt('optimus_images'),
			'{news}'          => Lang::getTxt('optimus_news'),
			'{video}'         => Lang::getTxt('optimus_video'),
			'{index}'         => Lang::getTxt('optimus_index'),
			'{total_files}'   => Lang::getTxt('optimus_total_files'),
			'{total_urls}'    => Lang::getTxt('optimus_total_urls'),
			'{url}'           => Lang::getTxt('url'),
			'{last_modified}' => Lang::getTxt('optimus_last_modified'),
			'{frequency}'     => Lang::getTxt('optimus_frequency'),
			'{priority}'      => Lang::getTxt('optimus_priority'),
			'{direct_link}'   => Lang::getTxt('optimus_direct_link'),
			'{caption}'       => Lang::getTxt('optimus_caption'),
			'{thumbnail}'     => Lang::getTxt('optimus_thumbnail'),
			'{optimus}'       => OP_NAME,
		]);

		echo $content;

		Utils::obExit(false);
	}

	public function preLogStats(array &$no_stat_actions): void
	{
		$no_stat_actions[Action::XSL->value] = true;
	}

	public function addLink(): void
	{
		if (isset(Utils::$context['uninstalling']) || empty(Config::$modSettings['optimus_sitemap_link']))
			return;

		if (empty(Lang::getTxt('optimus_sitemap_title', file: 'Optimus/Optimus')))
			return;

		Lang::$forum_copyright .= ' | ' . Str::html('a', Lang::getTxt('optimus_sitemap_title'))
			->href(Config::$boardurl . '/sitemap.xml');

		Utils::$context['html_headers'] .= "\n\t" . Str::html('link')
			->rel('sitemap')
			->type('application/xml')
			->title('Sitemap')
			->href(Config::$boardurl . '/sitemap.xml');
	}
}
