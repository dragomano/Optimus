<?php

/**
 * Class-Optimus.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2018 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 0.1 alpha
 */

if (!defined('WEDGE'))
	die('Hacking attempt...');

class Optimus
{
	public static function loadTheme()
	{
		global $context, $settings, $txt;

		loadPluginLanguage('Bugo:Optimus', 'lang/');

		// Описание (девиз сайта) для главной страницы
		if (empty($context['action']) && !empty($settings['optimus_description']) && empty($context['current_topic']) && empty($context['current_board']))
			$context['meta_description'] = strip_tags($settings['optimus_description']);

		// Изображение по умолчанию для мета-тега og:image
		if (empty($context['optimus_og_image']) && !empty($settings['optimus_default_og_image']))
			$context['optimus_og_image'] = strip_tags($settings['optimus_default_og_image']);

		// Metatags
		if (!empty($settings['optimus_meta'])) {
			$tags = unserialize($settings['optimus_meta']);

			foreach ($tags as $name => $value) {
				if (!empty($value))
					$context['header'] .= "\n\t" . '<meta name="' . $name . '" content="' . $value . '">';
			}
		}

		// Counters
		$ignored_actions = !empty($settings['optimus_ignored_actions']) ? explode(",", $settings['optimus_ignored_actions']) : array();

		if (!in_array($context['action'], $ignored_actions)) {
			// Invisible counters like as Google Analytics
			if (!empty($settings['optimus_head_code'])) {
				$head = explode("\n", trim($settings['optimus_head_code']));
				foreach ($head as $part)
					$context['header'] .= "\n\t" . $part;
			}

			// Other invisible counters
			if (!empty($settings['optimus_stat_code'])) {
				$stat = explode("\n", trim($settings['optimus_stat_code']));
				foreach ($stat as $part)
					$context['footer'] .= "\n\t" . $part;
			}

			if (!empty($settings['optimus_count_code'])) {
				loadPluginTemplate('Bugo:Optimus', 'Optimus');
				wetem::before('footer','count_code');
			}

			// Styles for visible counters
			if (!empty($settings['optimus_count_code']) && !empty($settings['optimus_counters_css']))
				add_css($settings['optimus_counters_css']);
		}

		// JSON-LD
		if (!empty($settings['optimus_json_ld']) && empty($context['robot_no_index'])) {
			loadPluginTemplate('Bugo:Optimus', 'Optimus');
			wetem::after('footer','json_ld');
		}

		// XML sitemap link
		if (!empty($settings['optimus_sitemap_link']) && file_exists(ROOT_DIR . '/sitemap.xml'))
			$txt['copyright'] .= ' | <a href="' . SCRIPT . 'sitemap.xml" target="_blank">' . $txt['optimus_sitemap_xml_link'] . '</a>';
	}

	public static function displayPostDone(&$counter, &$output)
	{
		global $context, $settings, $board_info;

		if (empty($context['first_message']))
			return;

		if (!empty($settings['optimus_og_image'])) {
			if (($context['optimus_og_image'] = cache_get_data('og_image_' . $context['first_message'], 360)) == null) {
				if (!empty($settings['optimus_topic_body_og_image'])) {
					$first_post_image = preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $output['body'], $value);
					$context['optimus_og_image'] = $first_post_image ? array_pop($value) : null;
				}

				if (empty($context['optimus_og_image']) && !empty($settings['optimus_topic_attach_og_image']) && !empty($output['attachment'])) {
					foreach ($output['attachment'] as $attach) {
						if ($attach['is_image']) {
							$context['optimus_og_image'] = $attach['href'] . ';image';
							break;
						}
					}
				}

				cache_put_data('og_image_' . $context['first_message'], $context['optimus_og_image'], 360);
			}
		}

		$context['optimus_og_type']['article'] = array(
			'published_time' => date('Y-m-d\TH:i:s', $output['timestamp']),
			'modified_time'  => !empty($output['modified']['timestamp']) ? date('Y-m-d\TH:i:s', $output['modified']['timestamp']) : null,
			'section'        => $board_info['name']
		);
	}

	public static function buffer($buffer)
	{
		global $context, $txt, $settings, $board_info;

		$replacements = array();

		// Open Graph
		$html = '<html' . $context['right_to_left'] ? ' dir="rtl"' : '' . !empty($txt['lang_dictionary']) ? ' lang="' . $txt['lang_dictionary'] . '"' : '' . '>';
		$new_html = ($context['right_to_left'] ? ' dir="rtl"' : '') . ' prefix="og: http://ogp.me/ns#' . (!empty($context['optimus_og_type']['article']) ? ' article: http://ogp.me/ns/article#' : '') . (!empty($settings['optimus_fb_appid']) ? ' fb: http://ogp.me/ns/fb#' : '') . '"' . (!empty($txt['lang_dictionary']) ? ' lang="' . $txt['lang_dictionary'] . '"' : '');
		$replacements[$html] = $new_html;

		$start_title = '<title>';
		$end_title = '</title>';
		if (!empty($settings['optimus_board_extend_title']) && !empty($context['current_board'])) {
			if ($settings['optimus_board_extend_title'] == 1) {
				$new_title = $start_title . $context['forum_name'] . ' - ';
				$replacements[$start_title] = $new_title;
			} else {
				$new_title = ' - ' . $context['forum_name'] . $end_title;
				$replacements[$end_title] = $new_title;
			}
		}

		if (!empty($settings['optimus_topic_extend_title']) && !empty($context['current_topic'])) {
			if ($settings['optimus_topic_extend_title'] == 1) {
				$new_title = $start_title . $context['forum_name'] . ' - ' . $board_info['name'] . ' - ';
				$replacements[$start_title] = $new_title;
			} else {
				$new_title = ' - ' . $board_info['name'] . ' - ' . $context['forum_name'] . $end_title;
				$replacements[$end_title] = $new_title;
			}
		}

		$end_head = '</head>';

		if (empty($context['action']) && !empty($settings['optimus_forum_index']) && empty($context['current_topic']) && empty($context['current_board'])) {
			$page_title = strip_tags($settings['optimus_forum_index']);
			$old_title = '<title><PAGE_TITLE></title>';
			$new_title = '<title>' . $page_title . '</title>';
			$replacements[$old_title] = $new_title;
		} else
			$page_title = $context['page_title'];

		$meta = "\t" . '<meta property="og:title" content="' . $page_title . (!empty($context['page_indicator']) ? $context['page_indicator'] : '') . '">';

		if (isset($context['meta_description'], $context['meta_description_repl']))
			$meta .= "\n\t" . '<meta property="og:description" content="' . rtrim($context['meta_description_repl']) . '">';
		elseif (!empty($context['meta_description']))
			$meta .= "\n\t" . '<meta property="og:description" content="' . $context['meta_description'] . '">';

		if (!empty($context['canonical_url']))
			$meta .= "\n\t" . '<meta property="og:url" content="' . $context['canonical_url'] . '">';

		if (!empty($context['optimus_og_image']))
			$meta .= "\n\t" . '<meta property="og:image" content="' . $context['optimus_og_image'] . '">';

		$meta .= "\n\t" . '<meta property="og:site_name" content="' . $context['forum_name'] . '">';

		// Various types
		if (!empty($context['optimus_og_type'])) {
			$type = key($context['optimus_og_type']);
			$meta .= "\n\t" . '<meta property="og:type" content="' . $type . '">';

			$og_type = $context['optimus_og_type'][$type];
			foreach ($og_type as $property => $content) {
				if (!empty($content))
					$meta .= "\n\t" . '<meta property="' . $type . ':' . $property . '" content="' . $content . '">';
			}
		}

		// Twitter cards
		if (!empty($settings['optimus_tw_cards'])) {
			$meta .= "\n\t" . '<meta property="twitter:card" content="summary">';
			$meta .= "\n\t" . '<meta property="twitter:site" content="@' . $settings['optimus_tw_cards'] . '">';;

			if (!empty($context['optimus_og_image']))
				$meta .= "\n\t" . '<meta property="twitter:image" content="' . $context['optimus_og_image'] . '">';
		}

		// Facebook
		if (!empty($settings['optimus_fb_appid']))
			$meta .= "\n\t" . '<meta property="fb:app_id" content="' . $settings['optimus_fb_appid'] . '">';

		$replacements[$end_head] = $meta . "\n" . $end_head;

		return str_replace(array_keys($replacements), array_values($replacements), $buffer);
	}

	public static function credits()
	{
		global $context;

		$context['plugin_credits'][] = '<a href="https://dragomano.ru/mods/optimus" target="_blank">Optimus</a> &copy; 2010&ndash;2018, Bugo';
	}

	public static function scheduledTask()
	{
		loadPluginSource('Bugo:Optimus', 'Class-OptimusSitemap');

		$sitemap = new OptimusSitemap();
		return $sitemap->create();
	}
}
