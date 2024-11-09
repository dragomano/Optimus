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
		$this->prepareForumIndex();

		if (! empty(Utils::$context['robot_no_index']))
			return;

		$meta = [
			'og:site_name',
			'og:title',
			'og:url',
			'og:image',
			'og:description',
		];

		$tags = Utils::$context['meta_tags'];

		foreach ($tags as $key => $value) {
			foreach ($value as $k => $v) {
				if ($k === 'property' && in_array($v, $meta)) {
					$tags[$key] = array_merge(
						['prefix' => 'og: https://ogp.me/ns#'], $value
					);
				}
			}
		}

		$this->prepareOgImageTags($tags);

		$this->prepareOgCustomType($tags);

		$this->prepareOgProfile($tags);

		$this->prepareOgTwitter($tags);

		$this->prepareOgFacebook($tags);

		$this->prepareMetaTags($tags);

		Utils::$context['meta_tags'] = $tags;
	}

	private function prepareForumIndex(): void
	{
		if (empty(Config::$modSettings['optimus_forum_index']))
			return;

		Utils::$context['page_title_html_safe'] = Utils::htmlspecialcharsDecode(Utils::$context['page_title_html_safe']);
	}

	private function prepareOgImageTags(array &$tags): void
	{
		$imageKey = 0;

		foreach ($tags as $key => $value) {
			foreach ($value as $v) {
				if ($v === 'og:image') {
					$imageKey = $key;
				}
			}
		}

		if (empty(Utils::$context['optimus_og_image']))
			return;

		$tags = array_merge(
			array_slice($tags, 0, $imageKey + 1, true),
			[
				[
					'prefix'   => 'og: https://ogp.me/ns#',
					'property' => 'og:image:type',
					'content'  => Utils::$context['optimus_og_image']['mime'] ?? '',
				]
			],
			[
				[
					'prefix'   => 'og: https://ogp.me/ns#',
					'property' => 'og:image:width',
					'content'  => Utils::$context['optimus_og_image']['width'] ?? 0,
				]
			],
			[
				[
					'prefix'   => 'og: https://ogp.me/ns#',
					'property' => 'og:image:height',
					'content'  => Utils::$context['optimus_og_image']['height'] ?? 0,
				]
			],
			array_slice($tags, $imageKey + 1, null, true)
		);
	}

	private function prepareOgCustomType(array &$tags): void
	{
		if (empty(Utils::$context['optimus_og_type']))
			return;

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
						'property' => $type . ':' . $property, 'content' => $value,
					];
				}
			} else {
				$tags[] = [
					'prefix'   => $type . ': https://ogp.me/ns/' . $type . '#',
					'property' => $type . ':' . $property, 'content' => $content,
				];
			}
		}
	}

	private function prepareOgProfile(array &$tags): void
	{
		if (Utils::$context['current_action'] == 'profile' && Input::isRequest('u')) {
			$tags[] = [
				'prefix'   => 'og: https://ogp.me/ns#',
				'property' => 'og:type',
				'content'  => 'profile',
			];
		}
	}

	private function prepareOgTwitter(array &$tags): void
	{
		if (empty(Config::$modSettings['optimus_tw_cards']) || empty(Utils::$context['canonical_url']))
			return;

		$tags[] = ['property' => 'twitter:card', 'content' => 'summary'];
		$tags[] = ['property' => 'twitter:site', 'content' => '@' . Config::$modSettings['optimus_tw_cards']];

		if (empty(Theme::$current->settings['og_image']))
			return;

		$tags[] = ['property' => 'twitter:image', 'content' => Theme::$current->settings['og_image']];
	}

	private function prepareOgFacebook(array &$tags): void
	{
		if (empty(Config::$modSettings['optimus_fb_appid']))
			return;

		$tags[] = [
			'prefix'   => 'fb: https://ogp.me/ns/fb#',
			'property' => 'fb:app_id',
			'content'  => Config::$modSettings['optimus_fb_appid'],
		];
	}

	private function prepareMetaTags(array &$tags): void
	{
		if (empty(Config::$modSettings['optimus_meta']))
			return;

		$customTags = array_filter(unserialize(Config::$modSettings['optimus_meta']));

		foreach ($customTags as $name => $value) {
			$tags[] = ['name' => $name, 'content' => $value];
		}
	}
}
