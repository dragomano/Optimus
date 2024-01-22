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

use Bugo\Optimus\Utils\Input;

final class MetaHandler
{
	public function __invoke(): void
	{
		add_integration_function('integrate_theme_context', self::class . '::handle#', false, __FILE__);
	}

	public function handle(): void
	{
		global $modSettings, $context, $settings;

		if (! empty($modSettings['optimus_forum_index']))
			$context['page_title_html_safe'] = un_htmlspecialchars($context['page_title_html_safe']);

		if (! empty($context['robot_no_index']))
			return;

		$meta = [
			'og:site_name',
			'og:title',
			'og:url',
			'og:image',
			'og:description'
		];

		$ogImageKey = 0;
		foreach ($context['meta_tags'] as $key => $value) {
			foreach ($value as $k => $v) {
				if ($k === 'property' && in_array($v, $meta))
					$context['meta_tags'][$key] = array_merge(['prefix' => 'og: https://ogp.me/ns#'], $value);

				if ($v === 'og:image') {
					$ogImageKey = $key;

					if (! empty($context['optimus_og_image'])) {
						$image_data[0] = $context['optimus_og_image']['width'];
						$image_data[1] = $context['optimus_og_image']['height'];
						$image_data['mime'] = $context['optimus_og_image']['mime'];
					}
				}
			}
		}

		if (! empty($image_data)) {
			$context['meta_tags'] = array_merge(
				array_slice($context['meta_tags'], 0, $ogImageKey + 1, true),
				[
					['prefix' => 'og: https://ogp.me/ns#', 'property' => 'og:image:type', 'content' => $image_data['mime']]
				],
				[
					['prefix' => 'og: https://ogp.me/ns#', 'property' => 'og:image:width', 'content' => $image_data[0]]
				],
				[
					['prefix' => 'og: https://ogp.me/ns#', 'property' => 'og:image:height', 'content' => $image_data[1]]
				],
				array_slice($context['meta_tags'], $ogImageKey + 1, null, true)
			);
		}

		// Various types
		if (! empty($context['optimus_og_type'])) {
			$type = key($context['optimus_og_type']);
			$context['meta_tags'][] = ['prefix' => 'og: https://ogp.me/ns#', 'property' => 'og:type', 'content' => $type];
			$optimus_custom_types = array_filter($context['optimus_og_type'][$type]);

			foreach ($optimus_custom_types as $property => $content) {
				if (is_array($content)) {
					foreach ($content as $value) {
						$context['meta_tags'][] = ['prefix' => $type . ': https://ogp.me/ns/' . $type . '#', 'property' => $type . ':' . $property, 'content' => $value];
					}
				} else {
					$context['meta_tags'][] = ['prefix' => $type . ': https://ogp.me/ns/' . $type . '#', 'property' => $type . ':' . $property, 'content' => $content];
				}
			}
		}

		if ($context['current_action'] == 'profile' && Input::isRequest('u')) {
			$context['meta_tags'][] = ['prefix' => 'og: https://ogp.me/ns#', 'property' => 'og:type', 'content' => 'profile'];
		}

		// Twitter cards
		if (! empty($modSettings['optimus_tw_cards']) && isset($context['canonical_url'])) {
			$context['meta_tags'][] = ['property' => 'twitter:card', 'content' => 'summary'];
			$context['meta_tags'][] = ['property' => 'twitter:site', 'content' => '@' . $modSettings['optimus_tw_cards']];

			if (! empty($settings['og_image']))
				$context['meta_tags'][] = ['property' => 'twitter:image', 'content' => $settings['og_image']];
		}

		// Facebook
		if (! empty($modSettings['optimus_fb_appid']))
			$context['meta_tags'][] = ['prefix' => 'fb: https://ogp.me/ns/fb#', 'property' => 'fb:app_id', 'content' => $modSettings['optimus_fb_appid']];

		// Metatags
		if (! empty($modSettings['optimus_meta'])) {
			$tags = array_filter(unserialize($modSettings['optimus_meta']));

			foreach ($tags as $name => $value) {
				$context['meta_tags'][] = ['name' => $name, 'content' => $value];
			}
		}
	}
}