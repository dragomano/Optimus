<?php

namespace Bugo\Optimus;

/**
 * Subs.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7.4
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Special class with helpers functions
 */
class Subs
{
	/**
	 * Adding counters codes into the body pages
	 *
	 * @return void
	 */
	public static function addCounters()
	{
		global $modSettings, $context;

		$ignored_actions = !empty($modSettings['optimus_ignored_actions']) ? explode(",", $modSettings['optimus_ignored_actions']) : array();

		if (!in_array($context['current_action'], $ignored_actions)) {
			// Invisible counters like as Google Analytics
			if (!empty($modSettings['optimus_head_code'])) {
				$head = explode(PHP_EOL, trim($modSettings['optimus_head_code']));
				foreach ($head as $part)
					$context['html_headers'] .= "\n\t" . $part;
			}

			// Other invisible counters
			if (!empty($modSettings['optimus_stat_code'])) {
				$stat = explode(PHP_EOL, trim($modSettings['optimus_stat_code']));
				foreach ($stat as $part)
					$context['insert_after_template'] .= "\n\t" . $part;
			}

			// Visible (normal) counters
			if (!empty($modSettings['optimus_count_code'])) {
				loadTemplate('Optimus');
				$context['template_layers'][] = 'footer_counters';
			}

			// Styles for visible counters
			if (!empty($modSettings['optimus_count_code']) && !empty($modSettings['optimus_counters_css']))
				addInlineCss($modSettings['optimus_counters_css']);
		}
	}

	/**
	 * Favicon => head
	 *
	 * @return void
	 */
	public static function addFavicon()
	{
		global $modSettings, $context;

		if (!empty($modSettings['optimus_favicon_text'])) {
			$favicon = explode(PHP_EOL, trim($modSettings['optimus_favicon_text']));
			foreach ($favicon as $fav_line)
				$context['html_headers'] .= "\n\t" . $fav_line;
		}
	}

	/**
	 * Front page description
	 *
	 * @return void
	 */
	public static function addFrontPageDescription()
	{
		global $context, $modSettings, $smcFunc;

		if (empty($context['current_action']) || in_array($context['current_action'], array('forum', 'community'))) {
			if (empty($_SERVER['QUERY_STRING']) && empty($_SERVER['argv']) && !empty($modSettings['optimus_description']))
				$context['meta_description'] = $smcFunc['htmlspecialchars']($modSettings['optimus_description']);
		}
	}

	/**
	 * Supplement the headings of boards and topics
	 *
	 * @return void
	 */
	public static function makeExtendTitles()
	{
		global $board_info, $modSettings, $context;

		if (SMF == 'SSI')
			return;

		// Boards
		if (!empty($board_info['total_topics']) && !empty($modSettings['optimus_board_extend_title'])) {
			if ($modSettings['optimus_board_extend_title'] == 1)
				$context['page_title_html_safe'] = $context['forum_name'] . ' - ' . $context['page_title_html_safe'];
			else
				$context['page_title_html_safe'] = $context['page_title_html_safe'] . ' - ' . $context['forum_name'];
		}

		// Topics
		if (!empty($context['first_message']) && !empty($modSettings['optimus_topic_extend_title'])) {
			if ($modSettings['optimus_topic_extend_title'] == 1)
				$context['page_title_html_safe'] = $context['forum_name'] . ' - ' . $board_info['name'] . ' - ' . $context['page_title_html_safe'];
			else
				$context['page_title_html_safe'] = $context['page_title_html_safe'] . ' - ' . $board_info['name'] . ' - ' . $context['forum_name'];
		}
	}

	/**
	 * Change current metatags and add the new one if it is needed
	 *
	 * @return void
	 */
	public static function prepareMetaTags()
	{
		global $context, $settings, $modSettings;

		if (!empty($context['robot_no_index']))
			return;

		$meta = [
			'og:site_name',
			'og:title',
			'og:url',
			'og:image',
			'og:description'
		];

		foreach ($context['meta_tags'] as $key => $value) {
			foreach ($value as $k => $v) {
				if ($k === 'property' && in_array($v, $meta))
					$context['meta_tags'][$key] = array_merge(array('prefix' => 'og: http://ogp.me/ns#'), $value);
			}
		}

		// Various types
		if (!empty($context['optimus_og_type'])) {
			$type = key($context['optimus_og_type']);
			$context['meta_tags'][] = array('prefix' => 'og: http://ogp.me/ns#', 'property' => 'og:type', 'content' => $type);

			$og_type = $context['optimus_og_type'][$type];
			foreach ($og_type as $property => $content) {
				if (!empty($content))
					$context['meta_tags'][] = array('prefix' => $type . ': http://ogp.me/ns/' . $type . '#', 'property' => $type . ':' . $property, 'content' => $content);
			}
		} elseif ($context['current_action'] == 'profile' && isset($_REQUEST['u'])) {
			$context['meta_tags'][] = array('prefix' => 'og: http://ogp.me/ns#', 'property' => 'og:type', 'content' => 'profile');
		} else {
			$context['meta_tags'][] = array('prefix' => 'og: http://ogp.me/ns#', 'property' => 'og:type', 'content' => 'website');
		}

		// Twitter cards
		if (!empty($modSettings['optimus_tw_cards']) && isset($context['canonical_url'])) {
			$context['meta_tags'][] = array('property' => 'twitter:card', 'content' => 'summary');
			$context['meta_tags'][] = array('property' => 'twitter:site', 'content' => '@' . $modSettings['optimus_tw_cards']);

			if (!empty($settings['og_image']))
				$context['meta_tags'][] = array('property' => 'twitter:image', 'content' => $settings['og_image']);
		}

		// Facebook
		if (!empty($modSettings['optimus_fb_appid']))
			$context['meta_tags'][] = array('prefix' => 'fb: http://ogp.me/ns/fb#', 'property' => 'fb:app_id', 'content' => $modSettings['optimus_fb_appid']);

		// Metatags
		if (!empty($modSettings['optimus_meta'])) {
			$tags = unserialize($modSettings['optimus_meta']);

			foreach ($tags as $name => $value) {
				if (!empty($value))
					$context['meta_tags'][] = array('name' => $name, 'content' => $value);
			}
		}
	}

	/**
	 * Change the returned statuses of pages, boards and topics
	 *
	 * @return void
	 */
	public static function makeErrorCodes()
	{
		global $modSettings, $board_info, $context, $txt;

		if (empty($modSettings['optimus_correct_http_status']) || empty($board_info['error']))
			return;

		// Does not page exist?
		if ($board_info['error'] == 'exist') {
			send_http_status(404);
			$context['page_title']    = $txt['optimus_404_page_title'];
			$context['error_title']   = $txt['optimus_404_h2'];
			$context['error_message'] = $txt['optimus_404_h3'];
		}

		// No access?
		if ($board_info['error'] == 'access') {
			send_http_status(403);
			$context['page_title']    = $txt['optimus_403_page_title'];
			$context['error_title']   = $txt['optimus_403_h2'];
			$context['error_message'] = $txt['optimus_403_h3'];
		}

		if ($board_info['error'] == 'exist' || $board_info['error'] == 'access') {
			addInlineJavaScript('
			jQuery(document).ready(function ($) {
				$(\'#fatal_error + .centertext > a.button\').attr("href", "javascript:history.go(-1)");
			});', true);
		}
	}

	/**
	 * Create a page description from the first message
	 *
	 * @return void
	 */
	public static function getDescriptionFromFirstMessage()
	{
		global $context, $settings, $txt;

		if (empty($context['topicinfo']['topic_first_message']) || !empty($context['topicinfo']['optimus_description']))
			return;

		$body = $context['topicinfo']['topic_first_message'];

		censorText($body);

		$body = parse_bbc($body, false);

		// Looking for an image in the message text
		$first_post_image = preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $body, $value);
		$settings['og_image'] = $first_post_image ? array_pop($value) : null;

		$body = self::getTeaser($body);
		$body = strtr($body, array($txt['quote'] => '', '&nbsp;' => ''));
		$body = shorten_subject($body, 130);

		$context['meta_description'] = $body;
	}

	/**
	 * Get an excerpt of the text to create a description of the page
	 *
	 * @param string $text
	 * @param integer $num_sentences
	 * @return string
	 */
	public static function getTeaser($text, $num_sentences = 2)
	{
		$body = preg_replace('/\s+/', ' ', strip_tags($text));
		$sentences = preg_split('/(\.|\?|\!)(\s)/', $body);

		if (count($sentences) <= $num_sentences)
			return $body;

		$stop_at = 0;
		foreach ($sentences as $i => $sentence) {
			$stop_at += strlen($sentence);
			if ($i >= $num_sentences - 1)
				break;
		}

		$stop_at += ($num_sentences * 2);

		return trim(substr($body, 0, $stop_at));
	}

	/**
	 * Make topic description
	 *
	 * @return void
	 */
	public static function makeTopicDescription()
	{
		global $context, $modSettings, $board_info;

		if (empty($context['first_message']))
			return;

		// Generated description from the text of the first post of the topic
		if (!empty($modSettings['optimus_topic_description']))
			self::getDescriptionFromFirstMessage();

		// Use own description of topic
		if (!empty($context['topicinfo']['optimus_description']))
			$context['meta_description'] = $context['topicinfo']['optimus_description'];

		// Additional data
		$context['optimus_og_type']['article'] = array(
			'published_time' => date('Y-m-d\TH:i:s', $context['topicinfo']['topic_started_time']),
			'modified_time'  => !empty($context['topicinfo']['topic_modified_time']) ? date('Y-m-d\TH:i:s', $context['topicinfo']['topic_modified_time']) : null,
			'author'         => !empty($context['topicinfo']['topic_started_name']) ? $context['topicinfo']['topic_started_name'] : null,
			'section'        => $board_info['name']
		);
	}

	/**
	 * We get the url of the attachment from the first message of the topic
	 *
	 * @return void
	 */
	public static function getOgImage()
	{
		global $board_info, $settings, $context, $scripturl;

		if (!empty($board_info['og_image']))
			$settings['og_image'] = $board_info['og_image'];

		if (!empty($context['first_message']) && !empty($context['topicinfo']['og_image_attach_id']))
			$settings['og_image'] = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . ';attach=' . $context['topicinfo']['og_image_attach_id'] . ';image';
	}

	/**
	 * Get nested dirs recursively
	 *
	 * @param string $path
	 * @param array $ret
	 * @return array
	 */
	public static function getNestedDirs($path, $nested_dirs = [])
	{
		$dirs = glob(rtrim($path, "/") . "/*", GLOB_ONLYDIR) or array();

		foreach ($dirs as $path) {
			$nested_dirs[] = $path;
			$nested_dirs = self::getNestedDirs($path, $nested_dirs);
		}

		return $nested_dirs;
	}

	/**
	 * Connecting add-ons
	 *
	 * @param string $type
	 * @param array $vars (extra variables for changing)
	 * @return void
	 */
	public static function runAddons($type = 'meta', $vars = [])
	{
		global $sourcedir;

		$addon_dir = $sourcedir . '/Optimus/addons';

		if (($optimus_addons = cache_get_data('optimus_addons', 3600)) == null) {
			foreach (glob($addon_dir . '/*.php') as $filename) {
				$filename = basename($filename);
				if ($filename !== 'index.php')
					$optimus_addons[] = str_replace('.php', '', $filename);
			}

			$dirs = self::getNestedDirs($addon_dir);
			foreach ($dirs as $dir)
				$optimus_addons[] = basename($dir) . '|' . basename($dir);

			cache_put_data('optimus_addons', $optimus_addons, 3600);
		}

		if (empty($optimus_addons))
			return;

		foreach ($optimus_addons as $addon) {
			$class = __NAMESPACE__ . '\Addons\\' . str_replace('|', '\\', $addon);

			if (method_exists($class, $type))
				call_user_func_array(array($class, $type), $vars);
		}
	}

	/**
	 * Defending against XSS
	 *
	 * @param array|string $data
	 * @return array|string
	 */
	public static function xss($data)
	{
		global $smcFunc;

		if (is_array($data))
			return array_map('self::xss', $data);

		return $smcFunc['htmlspecialchars']($data, ENT_QUOTES);
	}

	/**
	 * Change front page title
	 *
	 * @return void
	 */
	public static function changeFrontPageTitle()
	{
		global $modSettings, $txt;

		if (!empty($modSettings['optimus_forum_index']))
			$txt['forum_index'] = $modSettings['optimus_forum_index'];
	}

	/**
	 * Topic description field
	 *
	 * @return void
	 */
	public static function topicDescriptionField()
	{
		global $modSettings, $context, $smcFunc, $txt;

		if (!empty($modSettings['optimus_allow_change_topic_desc'])) {
			if ($context['is_new_topic']) {
				$context['optimus']['description'] = isset($_REQUEST['optimus_description']) ? self::xss($_REQUEST['optimus_description']) : '';
			} else {
				$request = $smcFunc['db_query']('', '
					SELECT optimus_description
					FROM {db_prefix}topics
					WHERE id_topic = {int:id_topic}
					LIMIT 1',
					array(
						'id_topic' => $context['current_topic']
					)
				);

				list ($context['optimus']['description']) = $smcFunc['db_fetch_row']($request);
				$smcFunc['db_free_result']($request);
			}

			if (!empty($context['is_first_post'])) {
				$context['posting_fields']['optimus_description']['label']['text'] = $txt['optimus_seo_description'];
				$context['posting_fields']['optimus_description']['input'] = array(
					'type' => 'textarea',
					'attributes' => array(
						'id' => 'optimus_description',
						'maxlength' => 255,
						'value' => $context['optimus']['description']
					)
				);
			}
		}
	}

	/**
	 * Topic keywords field
	 *
	 * @return void
	 */
	public static function topicKeywordsField()
	{
		global $modSettings, $context, $txt;

		if (empty($modSettings['optimus_allow_change_topic_keywords']))
			return;

		if ($context['is_new_topic']) {
			$context['optimus']['keywords'] = isset($_REQUEST['optimus_keywords']) ? self::xss($_REQUEST['optimus_keywords']) : '';
		} else {
			Keywords::getAll();
			$context['optimus']['keywords'] = array_values($context['optimus_keywords']);
		}

		if (!empty($context['is_first_post'])) {
			$context['posting_fields']['optimus_keywords'] = array(
				'label' => array(
					'text' => $txt['optimus_seo_keywords']
				),
				'input' => array(
					'type' => 'select',
					'attributes' => array(
						'id' => 'optimus_keywords',
						'name' => 'optimus_keywords[]',
						'multiple' => true
					),
					'options' => array()
				)
			);

			if (!empty($context['optimus']['keywords'])) {
				foreach ($context['optimus']['keywords'] as $key) {
					if (!defined('JQUERY_VERSION')) {
						$context['posting_fields']['optimus_keywords']['input']['options'][$key]['attributes'] = array(
							'value' => $key,
							'selected' => true
						);
					} else {
						$context['posting_fields']['optimus_keywords']['input']['options'][$key] = array(
							'value' => $key,
							'selected' => true
						);
					}
				}
			}

			// Select2 http://select2.github.io/select2/
			loadCSSFile('optimus/select2.min.css');
			loadJavaScriptFile('optimus/select2.full.min.js');
			loadJavaScriptFile('optimus/i18n/' . $txt['lang_dictionary'] . '.js');
			addInlineJavaScript('
			jQuery(document).ready(function ($) {
				$("#optimus_keywords").select2({
					language: "' . $txt['lang_dictionary'] . '",
					placeholder: "' . $txt['optimus_enter_keywords'] . '",
					minimumInputLength: 2,
					width: "100%",
					cache: true,
					tags: true,' . ($context['right_to_left'] ? '
					dir: "rtl",' : '') . '
					tokenSeparators: [",", " "],
					ajax: {
						url: smf_scripturl + "?action=keywords;sa=search",
						type: "POST",
						delay: 250,
						dataType: "json",
						data: function (params) {
							return {
								q: params.term
							}
						},
						processResults: function (data, params) {
							return {
								results: data
							}
						}
					}
				});
			});', true);
		}
	}

	/**
	 * Change topic description
	 *
	 * @param int $topic
	 * @return void
	 */
	public static function modifyTopicDescription($topic)
	{
		global $modSettings, $smcFunc;

		if (empty($modSettings['optimus_allow_change_topic_desc']))
			return;

		$description = isset($_REQUEST['optimus_description']) ? self::xss($_REQUEST['optimus_description']) : '';

		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET optimus_description = {string:description}
			WHERE id_topic = {int:current_topic}',
			array(
				'description'   => $description,
				'current_topic' => $topic
			)
		);
	}

	/**
	 * Change topic keywords
	 *
	 * @param int $topic
	 * @param int $user
	 * @return void
	 */
	public static function modifyTopicKeywords($topic, $user)
	{
		global $modSettings, $context;

		if (empty($modSettings['optimus_allow_change_topic_keywords']))
			return;

		$keywords = isset($_REQUEST['optimus_keywords']) ? self::xss($_REQUEST['optimus_keywords']) : [];

		// Check if the keywords have been changed
		Keywords::getAll();
		$current_keywords = array_values($context['optimus_keywords']);

		if ($keywords == $current_keywords)
			return;

		$new_keywords = array_diff($keywords, $current_keywords);
		Keywords::add($new_keywords, $topic, $user);

		$del_keywords = array_diff($current_keywords, $keywords);
		Keywords::remove($del_keywords, $topic);
	}

	/**
	 * Get the id of the topic first message
	 *
	 * @param int $topic
	 * @return int
	 */
	public static function getTopicFirstMessageId($topic)
	{
		global $smcFunc;

		$request = $smcFunc['db_query']('', '
			SELECT id_first_msg
			FROM {db_prefix}topics
			WHERE id_topic = {int:current_topic}
			LIMIT 1',
			array(
				'current_topic' => $topic
			)
		);

		list ($first_message_id) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return (int) $first_message_id;
	}

	/**
	 * XML sitemap link
	 *
	 * @return void
	 */
	public static function addSitemapLink()
	{
		global $modSettings, $txt, $forum_copyright, $scripturl;

		if (!empty($modSettings['optimus_sitemap_link']) && isset($txt['optimus_sitemap_title']))
			$forum_copyright .= ' | <a href="' . $scripturl . '?action=sitemap;xml">' . $txt['optimus_sitemap_title'] . '</a>';
	}

	/**
	 * Get HTML-link to the Optimus blog page
	 *
	 * @return string
	 */
	public static function getOptimusLink()
	{
		return '<a href="https://dragomano.ru/mods/optimus" target="_blank" rel="noopener">Optimus</a>';
	}
}
