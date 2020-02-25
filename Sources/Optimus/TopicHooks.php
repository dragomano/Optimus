<?php

namespace Bugo\Optimus;

/**
 * TopicHooks.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2020 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.6.1
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Main class of the Optimus mod
 */
class TopicHooks
{
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

		if (!empty($modSettings['optimus_allow_change_topic_desc']))
			$topic_selects[] = 't.optimus_description';

		if (allowedTo('view_attachments') && !empty($modSettings['optimus_og_image'])) {
			$topic_selects[] = 'COALESCE(optimus_attachments.id_attach, 0) AS og_image_attach_id';
			$topic_tables[]  = 'LEFT JOIN {db_prefix}attachments AS optimus_attachments ON (optimus_attachments.id_msg = t.id_first_msg AND optimus_attachments.width > 0 AND optimus_attachments.height > 0)';
		}
	}

	/**
	 * Displaying keywords above the first message of the topic
	 *
	 * @param array $output
	 * @param array $message
	 * @param int $counter
	 *
	 * @return void
	 */
	public static function prepareDisplayContext(&$output, &$message, $counter)
	{
		global $context, $modSettings, $txt, $scripturl;

		if (empty($context['optimus_keywords']) || empty($modSettings['optimus_show_keywords_block']))
			return;

		if ($counter == 1) {
			$keywords = '<fieldset class="roundframe"><legend class="windowbg" style="padding: 0.2em 0.4em"> ' . $txt['optimus_seo_keywords'] . ' </legend>';

			foreach ($context['optimus_keywords'] as $id => $keyword)
				$keywords .= '<a class="button" href="' . $scripturl . '?action=keywords;id=' . $id . '">' . $keyword . '</a>';

			$keywords .= '</fieldset>';

			echo $keywords;
		}
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
     * @param array $topic_columns
     * @param array $topic_parameters
     *
     * @return void
     */
	public static function beforeCreateTopic(&$msgOptions, &$topicOptions, &$posterOptions, &$topic_columns, &$topic_parameters)
	{
		global $modSettings;

		if (empty($modSettings['optimus_allow_change_topic_desc']))
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

		if (empty($modSettings['optimus_allow_change_topic_keywords']))
			return;

		$keywords = isset($_REQUEST['optimus_keywords']) ? Subs::xss($_REQUEST['optimus_keywords']) : '';

		Keywords::add($keywords, $topicOptions['id'], $posterOptions['id']);
	}

	/**
	 * Edit the first post of the topic
	 *
	 * @param array $messages_columns
	 * @param array $update_parameters
	 * @param array $msgOptions
	 * @param array $topicOptions
	 * @param array $posterOptions
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
	 * Удаляем ключевые слова при удалении темы
	 *
	 * @param array $topics
	 * @return void
	 */
	public static function removeTopics($topics)
	{
		global $smcFunc;

		if (empty($topics))
			return;

		$request = $smcFunc['db_query']('', '
			DELETE FROM {db_prefix}optimus_log_keywords
			WHERE topic_id IN ({array_int:topics})',
			array(
				'topics' => $topics
			)
		);
	}
}
