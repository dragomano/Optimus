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
 * @version 2.5
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
		add_integration_function('integrate_autoload', __NAMESPACE__ . '\Integration::autoload', false, __FILE__);
		add_integration_function('integrate_load_session', __NAMESPACE__ . '\Integration::loadSession', false, __FILE__);
		add_integration_function('integrate_load_theme', __NAMESPACE__ . '\Integration::loadTheme', false, __FILE__);
		add_integration_function('integrate_menu_buttons', __NAMESPACE__ . '\Integration::menuButtons', false, __FILE__);
		add_integration_function('integrate_actions', __NAMESPACE__ . '\Integration::actions', false, __FILE__);
		add_integration_function('integrate_theme_context', __NAMESPACE__ . '\Integration::themeContext', false, __FILE__);
		add_integration_function('integrate_display_topic', __NAMESPACE__ . '\Integration::displayTopic', false, __FILE__);
		add_integration_function('integrate_prepare_display_context', __NAMESPACE__ . '\Integration::prepareDisplayContext', false, __FILE__);
		add_integration_function('integrate_post_end', __NAMESPACE__ . '\Integration::postEnd', false, __FILE__);
		add_integration_function('integrate_before_create_topic', __NAMESPACE__ . '\Integration::beforeCreateTopic', false, __FILE__);
		add_integration_function('integrate_create_topic', __NAMESPACE__ . '\Integration::createTopic', false, __FILE__);
		add_integration_function('integrate_modify_post', __NAMESPACE__ . '\Integration::modifyPost', false, __FILE__);
		add_integration_function('integrate_credits', __NAMESPACE__ . '\Integration::credits', false, __FILE__);
		add_integration_function('integrate_admin_areas', __NAMESPACE__ . '\Settings::adminAreas', false, '$sourcedir/Optimus/Settings.php');
		add_integration_function('integrate_admin_search', __NAMESPACE__ . '\Settings::adminSearch', false, '$sourcedir/Optimus/Settings.php');
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
	 * Language files and various operations
	 *
	 * @return void
	 */
	public static function loadTheme()
	{
		global $sourcedir;

		loadLanguage('Optimus/');

		require_once($sourcedir . '/Optimus/Subs.php');

		Subs::loadClass('Keywords');
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
		Subs::addJsonLd();
		Subs::addFrontPageDescription();
		Subs::makeErrorCodes();
		Subs::makeTopicDescription();
		Subs::getOgImage();
		Subs::addSitemapLink();
		Subs::runAddons();
	}

	/**
	 * Used actions
	 *
	 * @param array $actionArray - all forum actions
	 * @return void
	 */
	public static function actions(&$actionArray)
	{
		Keywords::makeAction($actionArray);
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
	 * Additional columns for $context['topicinfo'] array
	 *
	 * @param array $topic_selects
	 * @param array $topic_tables
	 * @return void
	 */
	public static function displayTopic(&$topic_selects, &$topic_tables)
	{
		global $modSettings;

		if (!empty($modSettings['optimus_show_keywords_block']))
			Keywords::getAll();

		if (!in_array('ms.modified_time AS topic_modified_time', $topic_selects))
			$topic_selects[] = 'ms.modified_time AS topic_modified_time';

		if (!empty($modSettings['optimus_topic_description']) && !in_array('ms.body AS topic_first_message', $topic_selects))
			$topic_selects[] = 'ms.body AS topic_first_message';

		if (!empty($modSettings['optimus_allow_change_desc']))
			$topic_selects[] = 't.optimus_description';

		if (allowedTo('view_attachments') && !empty($modSettings['optimus_og_image'])) {
			$topic_selects[] = 'COALESCE(a.id_attach, 0) AS og_image_attach_id';
			$topic_tables[]  = 'LEFT JOIN {db_prefix}attachments AS a ON (a.id_msg = t.id_first_msg AND a.width > 0 AND a.height > 0)';
		}
	}

	/**
	 * Make various changes on Display Context area
	 *
	 * @param array $output
	 * @param array $message
	 * @param int $counter - the message counter
	 *
	 * @return void
	 */
	public static function prepareDisplayContext(&$output, &$message, $counter)
	{
		Keywords::displayBlock($counter);
	}

	/**
	 * The output of the template creation/editing messages
	 *
	 * @return void
	 */
	public static function postEnd()
	{
		Subs::topicDescriptionField();
		Subs::topicKeywordsField();
	}

    /**
     * Add the necessary data before creating a topic
     *
     * @param array $msgOptions
     * @param array $topicOptions
     * @param array $posterOptions
     * @param array $topic_columns    — a set of columns to add to the smf_topics table
     * @param array $topic_parameters — data set for use in $topic_columns
     *
     * @return void
     */
	public static function beforeCreateTopic(&$msgOptions, &$topicOptions, &$posterOptions, &$topic_columns, &$topic_parameters)
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_desc']))
			return;

		$description = isset($_REQUEST['optimus_description']) ? Subs::xss($_REQUEST['optimus_description']) : '';

		$topic_columns['optimus_description'] = 'string-255';
		$topic_parameters[] = $description;
	}

    /**
     * Creating a topic
     *
     * @param array $msgOptions
     * @param array $topicOptions
     * @param array $posterOptions
     *
     * @return void
     */
	public static function createTopic(&$msgOptions, &$topicOptions, &$posterOptions)
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_keywords']))
			return;

		$keywords = isset($_REQUEST['optimus_keywords']) ? Subs::xss($_REQUEST['optimus_keywords']) : '';

		Keywords::add($keywords, $topicOptions['id'], $posterOptions['id']);
	}

	/**
	 * Edit the first post of the topic
	 *
	 * @param array $messages_columns — editable columns in the smf_topics table
	 * @param array $update_parameters — data to update the columns
	 * @param array $msgOptions — parameters of the message to be modified
	 * @param array $topicOptions — changeable topic options
	 * @param array $posterOptions — the parameters of the author of the changes
	 * @return void
	 */
	public static function modifyPost(&$messages_columns, &$update_parameters, &$msgOptions, &$topicOptions, &$posterOptions)
	{
		if (Subs::getTopicFirstMessageId($topicOptions['id']) != $msgOptions['id'])
			return;

		Subs::modifyTopicDescription($topicOptions['id']);
		Subs::modifyTopicKeywords($topicOptions['id'], $posterOptions['id']);
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

	/**
	 * Calling sitemap generation via task manager
	 *
	 * @return boolean
	 */
	public static function scheduledTask()
	{
		global $modSettings, $sourcedir;

		if (empty($modSettings['optimus_sitemap_enable']))
			return false;

		require_once($sourcedir . '/Optimus/Subs.php');
		Subs::loadClass('Sitemap');

		$links   = Subs::getLinks();
		$sitemap = new Sitemap($links, '', !empty($modSettings['optimus_sitemap_name']) ? $modSettings['optimus_sitemap_name'] : '');

		return $sitemap->generate();
	}
}
