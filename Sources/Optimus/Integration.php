<?php

namespace Bugo\Optimus;

/**
 * Integration.php
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
 * Main class of the Optimus mod
 */
class Integration
{
	/**
	 * Used hooks
	 *
	 * @return void
	 */
	public static function hooks()
	{
		add_integration_function('integrate_autoload', __CLASS__ . '::autoload', false, __FILE__);
		add_integration_function('integrate_load_session', __CLASS__ . '::loadSession', false, __FILE__);
		add_integration_function('integrate_buffer', __CLASS__ . '::buffer', false, __FILE__);
		add_integration_function('integrate_pre_load_theme', __CLASS__ . '::preLoadTheme', false, __FILE__);
		add_integration_function('integrate_load_theme', __CLASS__ . '::loadTheme', false, __FILE__);
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::menuButtons', false, __FILE__);
		add_integration_function('integrate_actions', __CLASS__ . '::actions', false, __FILE__);
		add_integration_function('integrate_simple_actions', __CLASS__ . '::simpleActions', false, __FILE__);
		add_integration_function('integrate_theme_context', __CLASS__ . '::themeContext', false, __FILE__);
		add_integration_function('integrate_display_topic', __NAMESPACE__ . '\TopicHooks::displayTopic', false, '$sourcedir/Optimus/TopicHooks.php');
		add_integration_function('integrate_prepare_display_context', __NAMESPACE__ . '\TopicHooks::prepareDisplayContext', false, '$sourcedir/Optimus/TopicHooks.php');
		add_integration_function('integrate_post_end', __NAMESPACE__ . '\TopicHooks::postEnd', false, '$sourcedir/Optimus/TopicHooks.php');
		add_integration_function('integrate_before_create_topic', __NAMESPACE__ . '\TopicHooks::beforeCreateTopic', false, '$sourcedir/Optimus/TopicHooks.php');
		add_integration_function('integrate_create_topic', __NAMESPACE__ . '\TopicHooks::createTopic', false, '$sourcedir/Optimus/TopicHooks.php');
		add_integration_function('integrate_modify_post', __NAMESPACE__ . '\TopicHooks::modifyPost', false, '$sourcedir/Optimus/TopicHooks.php');
		add_integration_function('integrate_remove_topics', __NAMESPACE__ . '\TopicHooks::removeTopics', false, '$sourcedir/Optimus/TopicHooks.php');
		add_integration_function('integrate_load_board', __NAMESPACE__ . '\BoardHooks::loadBoard', false, '$sourcedir/Optimus/BoardHooks.php');
		add_integration_function('integrate_board_info', __NAMESPACE__ . '\BoardHooks::boardInfo', false, '$sourcedir/Optimus/BoardHooks.php');
		add_integration_function('integrate_pre_boardtree', __NAMESPACE__ . '\BoardHooks::preBoardtree', false, '$sourcedir/Optimus/BoardHooks.php');
		add_integration_function('integrate_boardtree_board', __NAMESPACE__ . '\BoardHooks::boardtreeBoard', false, '$sourcedir/Optimus/BoardHooks.php');
		add_integration_function('integrate_edit_board', __NAMESPACE__ . '\BoardHooks::editBoard', false, '$sourcedir/Optimus/BoardHooks.php');
		add_integration_function('integrate_modify_board', __NAMESPACE__ . '\BoardHooks::modifyBoard', false, '$sourcedir/Optimus/BoardHooks.php');
		add_integration_function('integrate_modify_basic_settings', __NAMESPACE__ . '\Settings::modifyBasicSettings', false, '$sourcedir/Optimus/Settings.php');
		add_integration_function('integrate_admin_areas', __NAMESPACE__ . '\Settings::adminAreas', false, '$sourcedir/Optimus/Settings.php');
		add_integration_function('integrate_admin_search', __NAMESPACE__ . '\Settings::adminSearch', false, '$sourcedir/Optimus/Settings.php');
		add_integration_function('integrate_credits', __CLASS__ . '::credits', false, __FILE__);
	}

	/**
	 * Autoloading of used classes
	 *
	 * @param array $classMap
	 * @return void
	 */
	public static function autoload(&$classMap)
	{
		$classMap['Bugo\\Optimus\\'] = 'Optimus/';
		$classMap['Bugo\\Optimus\\Addons\\'] = 'Optimus/addons/';
	}

	/**
	 * Change some PHP settings
	 *
	 * @return void
	 */
	public static function loadSession()
	{
		global $modSettings;

		@ini_set('session.use_only_cookies', !empty($modSettings['optimus_use_only_cookies']));
	}

	/**
	 * Remove index.php from $scripturl
	 *
	 * @param string $buffer
	 * @return void
	 */
	public static function buffer($buffer)
	{
		global $modSettings, $boardurl, $scripturl, $mbname, $context;

		if (isset($_REQUEST['xml']) || (empty($modSettings['optimus_remove_index_php']) && empty($modSettings['optimus_extend_h1'])))
			return $buffer;

		$replacements = [];
		if (!empty($modSettings['optimus_remove_index_php']))
			$replacements[$boardurl . '/index.php'] = $boardurl . '/';

		if (!empty($modSettings['optimus_extend_h1'])) {
			if (!empty($context['current_action']) || !empty($_GET))
				$new_h1 = '<a id="top" href="' . $scripturl . '">' . $mbname . ' - ' . str_replace($mbname . ' - ', '', $context['page_title']) . '</a>';
			else
				$new_h1 = $mbname;

			$replacements['<a id="top" href="' . $scripturl . '">' . $context['forum_name_html_safe'] . '</a>'] = $new_h1;
		}

		return str_replace(array_keys($replacements), array_values($replacements), $buffer);
	}

	/**
	 * Remove index.php from $scripturl
	 *
	 * @return void
	 */
	public static function preLoadTheme()
	{
		global $modSettings, $scripturl, $boardurl;

		if (!empty($modSettings['optimus_remove_index_php']))
			$scripturl = $boardurl . '/';
	}

	/**
	 * Language files and various operations
	 *
	 * @return void
	 */
	public static function loadTheme()
	{
		global $sourcedir;

		loadLanguage('Optimus/');

		require_once($sourcedir . "/Optimus/Subs.php");
		require_once($sourcedir . "/Optimus/Keywords.php");

		Subs::changeFrontPageTitle();
		Subs::addCounters();
	}

	/**
	 * Various scripts and variables
	 *
	 * @return void
	 */
	public static function menuButtons()
	{
		Subs::addFavicon();
		Subs::addFrontPageDescription();
		Subs::makeErrorCodes();
		Subs::makeTopicDescription();
		Subs::getOgImage();
		Subs::addSitemapLink();
		Subs::runAddons();
	}

	/**
	 * Add "keywords" action
	 *
	 * @param array $actions
	 * @return void
	 */
	public static function actions(&$actions)
	{
		global $modSettings;

		if (!empty($modSettings['optimus_allow_change_topic_keywords']) || !empty($modSettings['optimus_show_keywords_block']))
			$actions['keywords'] = array('Optimus/Keywords.php', array(__NAMESPACE__ . '\Keywords', 'showTableWithTheSameKeyword'));

		if (!empty($modSettings['optimus_sitemap_enable']))
			$actions['sitemap'] = array('Optimus/Sitemap.php', array(__NAMESPACE__ . '\Sitemap', 'main'));
	}

	/**
	 * Add simple action
	 *
	 * @param array $simpleActions
	 * @param array $simpleAreas
	 * @param array $simpleSubActions
	 * @param array $extraParams
	 * @param array $xmlActions
	 * @return void
	 */
	public static function simpleActions(&$simpleActions, &$simpleAreas, &$simpleSubActions, &$extraParams, &$xmlActions)
	{
		global $modSettings;

		if (!empty($modSettings['optimus_sitemap_enable']))
			$xmlActions[] = 'sitemap';
	}

	/**
	 * Change various metatags
	 *
	 * @return void
	 */
	public static function themeContext()
	{
		Subs::makeExtendTitles();
		Subs::prepareMetaTags();
	}

	/**
	 * The mod credits for action=credits
	 *
	 * @return void
	 */
	public static function credits()
	{
		global $context;

		$context['credits_modifications'][] = Subs::getOptimusLink() . ' &copy; 2010&ndash;2020, Bugo';
	}
}
