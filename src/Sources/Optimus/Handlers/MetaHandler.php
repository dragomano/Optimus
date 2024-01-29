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

if (! defined('SMF'))
	die('No direct access...');

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

		$tags = $context['meta_tags'];

		foreach ($tags as $key => $value) {
			foreach ($value as $k => $v) {
				if ($k === 'property' && in_array($v, $meta)) {
					$tags[$key] = array_merge(
						['prefix' => 'og: https://ogp.me/ns#'], $value
					);
				}

				if ($v === 'og:image') {
					$ogImageKey = $key;

					if (! empty($context['optimus_og_image'])) {
						$imageData[0] = $context['optimus_og_image']['width'];
						$imageData[1] = $context['optimus_og_image']['height'];
						$imageData['mime'] = $context['optimus_og_image']['mime'];
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
		if (! empty($context['optimus_og_type'])) {
			$type = key($context['optimus_og_type']);
			$tags[] = [
				'prefix'   => 'og: https://ogp.me/ns#',
				'property' => 'og:type',
				'content'  => $type,
			];

			$customTypes = array_filter($context['optimus_og_type'][$type]);

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

		if ($context['current_action'] == 'profile' && Input::isRequest('u')) {
			$tags[] = [
				'prefix'   => 'og: https://ogp.me/ns#',
				'property' => 'og:type',
				'content'  => 'profile',
			];
		}

		// Twitter cards
		if (! empty($modSettings['optimus_tw_cards']) && isset($context['canonical_url'])) {
			$tags[] = ['property' => 'twitter:card', 'content' => 'summary'];
			$tags[] = ['property' => 'twitter:site', 'content' => '@' . $modSettings['optimus_tw_cards']];

			if (! empty($settings['og_image']))
				$tags[] = ['property' => 'twitter:image', 'content' => $settings['og_image']];
		}

		// Facebook
		if (! empty($modSettings['optimus_fb_appid'])) {
			$tags[] = [
				'prefix'   => 'fb: https://ogp.me/ns/fb#',
				'property' => 'fb:app_id',
				'content'  => $modSettings['optimus_fb_appid'],
			];
		}

		// Meta-tags
		if (! empty($modSettings['optimus_meta'])) {
			$customTags = array_filter(unserialize($modSettings['optimus_meta']));

			foreach ($customTags as $name => $value) {
				$tags[] = ['name' => $name, 'content' => $value];
			}
		}

		$context['meta_tags'] = $tags;
	}
}