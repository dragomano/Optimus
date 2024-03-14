<?php

namespace Bugo\Optimus;

/**
 * Integration.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.7.6
 */

if (!defined('SMF'))
	die('Hacking attempt...');

class Integration
{
	public static function hooks()
	{
		add_integration_function('integrate_load_theme', __CLASS__ . '::loadTheme', false);
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::menuButtons', false);
		add_integration_function('integrate_buffer', __CLASS__ . '::buffer', false);
		add_integration_function('integrate_admin_include', '$sourcedir/Optimus/Settings.php', false);
		add_integration_function('integrate_admin_areas', __NAMESPACE__ . '\Settings::adminAreas', false);
	}

	/**
	 * @hook integrate_load_theme
	 */
	public static function loadTheme()
	{
		loadLanguage('Optimus/');

		Subs::changeForumTitle();
		Subs::addFavicon();
		Subs::addCounters();
	}

	/**
     * @hook integrate_menu_buttons
     */
	public static function menuButtons()
	{
		Subs::addMainPageDescription();
		Subs::processPageTemplates();
		Subs::processErrorCodes();
		Subs::runAddons();
		Subs::addSitemapLink();
		Subs::addCredits();
	}

	/**
     * @hook integrate_buffer
     */
	public static function buffer(string $buffer): string
	{
		global $context, $modSettings, $txt;

		if (isset($_REQUEST['xml']) || !empty($context['robot_no_index']))
			return $buffer;

		$replacements = array();

		// Description
		if (!empty($context['optimus_description'])) {
			$desc_old = '<meta name="description" content="' . $context['page_title_html_safe'] . '" />';
			$desc_new = '<meta name="description" content="' . $context['optimus_description'] . '" />';
			$replacements[$desc_old] = $desc_new;
		}

		// Meta tags
		if (!empty($modSettings['optimus_meta'])) {
			$meta = '';
			$test = unserialize($modSettings['optimus_meta']);

			foreach ($test as $n => $val) {
				if (!empty($val))
					$meta .= "\n\t" . '<meta name="' . $n . '" content="' . $val . '" />';
			}

			$charset_meta = '<meta http-equiv="Content-Type" content="text/html; charset=' . $context['character_set'] . '" />';
			$check_meta = $charset_meta . $meta;
			$replacements[$charset_meta] = $check_meta;
		}

		// Open Graph
		if (!empty($modSettings['optimus_open_graph'])) {
			$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			$new_doctype = '<!DOCTYPE html>';
			$replacements[$doctype] = $new_doctype;

			$type = !empty($context['optimus_og_type']) ? key($context['optimus_og_type']) : 'website';
			$xmlns = 'html xmlns="http://www.w3.org/1999/xhtml"';
			$new_xmlns = 'html prefix="og: https://ogp.me/ns#' . ($type == 'article' ? ' article: https://ogp.me/ns/article#' : '') . (!empty($modSettings['optimus_fb_appid']) ? ' fb: https://ogp.me/ns/fb#' : '') . '" lang="' . $txt['lang_dictionary'] . '"';
			$replacements[$xmlns] = $new_xmlns;

			$xmlns1 = '<html lang';
			$new_xmlns1 = '<html prefix="og: https://ogp.me/ns#' . ($type == 'article' ? ' article: https://ogp.me/ns/article#' : '') . (!empty($modSettings['optimus_fb_appid']) ? ' fb: https://ogp.me/ns/fb#' : '') . '" lang';
			$replacements[$xmlns1] = $new_xmlns1;

			$open_graph = '<meta property="og:title" content="' . (!empty($context['subject']) ? $context['subject'] : $context['page_title_html_safe']) . '" />';

			$open_graph .= '
	<meta property="og:type" content="' . $type . '" />';

			if (!empty($context['optimus_og_type'])) {
				$og_type = $context['optimus_og_type'][$type];
				foreach ($og_type as $t_key => $t_value) {
					$open_graph .= '
	<meta property="' . $type . ':' . $t_key . '" content="' . $t_value . '" />';
				}
			}

			if (!empty($context['canonical_url'])) {
				$open_graph .= '
	<meta property="og:url" content="' . $context['canonical_url'] . '" />';
			}

			if (!empty($context['optimus_og_image']) || !empty($modSettings['optimus_og_image'])) {
				$img_link = !empty($context['optimus_og_image']) ? $context['optimus_og_image'] : $modSettings['optimus_og_image'];
				$open_graph .= '
	<meta property="og:image" content="' . $img_link . '" />';
			}

			$open_graph .= '
	<meta property="og:description" content="' . (!empty($context['optimus_description']) ? $context['optimus_description'] : $context['page_title_html_safe']) . '" />
	<meta property="og:site_name" content="' . $context['forum_name_html_safe'] . '" />';

			if (!empty($modSettings['optimus_fb_appid'])) {
				$open_graph .= '
	<meta property="fb:app_id" name="app_id" content="' . $modSettings['optimus_fb_appid'] . '" />';
			}

			$head_op = '<title>' . $context['page_title_html_safe'] . '</title>';
			$op_head = $open_graph . "\n\t" . $head_op;
			$replacements[$head_op] = $op_head;
		}

		if (!empty($modSettings['optimus_tw_cards']) && isset($context['canonical_url'])) {
			$twitter = '<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@' . $modSettings['optimus_tw_cards'] . '" />';

			if (empty($modSettings['optimus_open_graph']))
				$twitter .= '
	<meta name="twitter:title" content="' . (!empty($context['subject']) ? $context['subject'] : $context['page_title_html_safe']) . '" />
	<meta name="twitter:description" content="' . (!empty($context['optimus_description']) ? $context['optimus_description'] : $context['page_title_html_safe']) . '" />';

			if (!empty($context['optimus_og_image']) || !empty($modSettings['optimus_og_image']))
				$twitter .= '
	<meta name="twitter:image" content="' . (!empty($context['optimus_og_image']) ? $context['optimus_og_image'] : $modSettings['optimus_og_image']) . '" />';

			$head_tw = '<title>';
			$tw_head = $twitter . "\n\t" . $head_tw;
			$replacements[$head_tw] = $tw_head;
		}

		return str_replace(array_keys($replacements), array_values($replacements), $buffer);
	}
}
