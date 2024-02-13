<?php declare(strict_types=1);

/**
 * MetaHandler.php
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
use Bugo\Compat\{Theme, Utils};
use Bugo\Optimus\Utils\Input;

if (! defined('SMF'))
	die('No direct access...');

final class MetaHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_theme_context', self::class . '::handle#', false, __FILE__
		);
	}

	public function handle(): void
	{
		if (! empty(Config::$modSettings['optimus_forum_index']))
			Utils::$context['page_title_html_safe'] = Utils::htmlspecialcharsDecode(Utils::$context['page_title_html_safe']);

		if (! empty(Utils::$context['robot_no_index']))
			return;

		$meta = [
			'og:site_name',
			'og:title',
			'og:url',
			'og:image',
			'og:description'
		];

		$ogImageKey = 0;

		$tags = Utils::$context['meta_tags'];

		foreach ($tags as $key => $value) {
			foreach ($value as $k => $v) {
				if ($k === 'property' && in_array($v, $meta)) {
					$tags[$key] = array_merge(
						['prefix' => 'og: https://ogp.me/ns#'], $value
					);
				}

				if ($v === 'og:image') {
					$ogImageKey = $key;

					if (! empty(Utils::$context['optimus_og_image'])) {
						$imageData[0] = Utils::$context['optimus_og_image']['width'];
						$imageData[1] = Utils::$context['optimus_og_image']['height'];
						$imageData['mime'] = Utils::$context['optimus_og_image']['mime'];
					}
				}
			}
		}

		if (! empty($imageData)) {
			$tags = array_merge(
				array_slice($tags, 0, $ogImageKey + 1, true),
				[
					[
						'prefix'   => 'og: https://ogp.me/ns#',
						'property' => 'og:image:type',
						'content'  => $imageData['mime'],
					]
				],
				[
					[
						'prefix'   => 'og: https://ogp.me/ns#',
						'property' => 'og:image:width',
						'content'  => $imageData[0],
					]
				],
				[
					[
						'prefix'   => 'og: https://ogp.me/ns#',
						'property' => 'og:image:height',
						'content'  => $imageData[1],
					]
				],
				array_slice($tags, $ogImageKey + 1, null, true)
			);
		}

		// Various types
		if (! empty(Utils::$context['optimus_og_type'])) {
			$type = key(Utils::$context['optimus_og_type']);
			$tags[] = [
				'prefix'   => 'og: https://ogp.me/ns#',
				'property' => 'og:type',
				'content'  => $type,
			];

			$customTypes = array_filter(Utils::$context['optimus_og_type'][$type]);

			foreach ($customTypes as $property => $content) {
				if (is_array($content)) {
					foreach ($content as $value) {
						$tags[] = [
							'prefix'   => $type . ': https://ogp.me/ns/' . $type . '#',
							'property' => $type . ':' . $property, 'content' => $value
						];
					}
				} else {
					$tags[] = [
						'prefix'   => $type . ': https://ogp.me/ns/' . $type . '#',
						'property' => $type . ':' . $property, 'content' => $content
					];
				}
			}
		}

		if (Utils::$context['current_action'] == 'profile' && Input::isRequest('u')) {
			$tags[] = [
				'prefix'   => 'og: https://ogp.me/ns#',
				'property' => 'og:type',
				'content'  => 'profile',
			];
		}

		// Twitter cards
		if (! empty(Config::$modSettings['optimus_tw_cards']) && isset(Utils::$context['canonical_url'])) {
			$tags[] = ['property' => 'twitter:card', 'content' => 'summary'];
			$tags[] = ['property' => 'twitter:site', 'content' => '@' . Config::$modSettings['optimus_tw_cards']];

			if (! empty(Theme::$current->settings['og_image']))
				$tags[] = ['property' => 'twitter:image', 'content' => Theme::$current->settings['og_image']];
		}

		// Facebook
		if (! empty(Config::$modSettings['optimus_fb_appid'])) {
			$tags[] = [
				'prefix'   => 'fb: https://ogp.me/ns/fb#',
				'property' => 'fb:app_id',
				'content'  => Config::$modSettings['optimus_fb_appid'],
			];
		}

		// Meta-tags
		if (! empty(Config::$modSettings['optimus_meta'])) {
			$customTags = array_filter(unserialize(Config::$modSettings['optimus_meta']));

			foreach ($customTags as $name => $value) {
				$tags[] = ['name' => $name, 'content' => $value];
			}
		}

		Utils::$context['meta_tags'] = $tags;
	}
}
