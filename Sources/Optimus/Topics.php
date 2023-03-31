<?php

declare(strict_types=1);

namespace Bugo\Optimus;

/**
 * Topics.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 2.10
 */

if (! defined('SMF'))
	die('No direct access...');

final class Topics
{
	public function hooks()
	{
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::prepareOgImage', false, __FILE__, true);
		add_integration_function('integrate_menu_buttons', __CLASS__ . '::menuButtons', false, __FILE__, true);
		add_integration_function('integrate_load_permissions', __CLASS__ . '::loadPermissions', false, __FILE__, true);
		add_integration_function('integrate_display_topic', __CLASS__ . '::displayTopic', false, __FILE__, true);
		add_integration_function('integrate_before_create_topic', __CLASS__ . '::beforeCreateTopic', false, __FILE__, true);
		add_integration_function('integrate_modify_post', __CLASS__ . '::modifyPost', false, __FILE__, true);
		add_integration_function('integrate_post_end', __CLASS__ . '::postEnd', false, __FILE__, true);

		(new Keywords)->hooks();
	}

	public function prepareOgImage()
	{
		global $context, $scripturl, $settings;

		if (is_off('optimus_og_image') || empty($context['topicinfo']['id_first_msg']))
			return;

		$first_message_id = $context['topicinfo']['id_first_msg'];

		// Looking for an image in attachments of the topic first message
		if (! empty($context['loaded_attachments']) && isset($context['loaded_attachments'][$first_message_id])) {
			$attachments = $context['loaded_attachments'][$first_message_id];
			$settings['og_image'] = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . ';attach=' . ($key = array_key_first($attachments)) . ';image';

			$context['optimus_og_image'] = array(
				'url'    => $settings['og_image'],
				'width'  => $attachments[$key]['width'],
				'height' => $attachments[$key]['height'],
				'mime'   => $attachments[$key]['mime_type']
			);
		}

		// Looking for an image in the text of the topic first message
		if (empty($context['optimus_og_image']) && ! empty($context['topicinfo']['topic_first_message'])) {
			$image = preg_match('/\[img.*]([^]\[]+)\[\/img]/U', $context['topicinfo']['topic_first_message'], $value);
			$settings['og_image'] = $image ? array_pop($value) : null;
		}
	}

	public function loadPermissions(array &$permissionGroups, array &$permissionList)
	{
		if (is_on('optimus_allow_change_topic_desc'))
			$permissionList['membergroup']['optimus_add_descriptions'] = array(true, 'general', 'view_basic_info');
	}

	public function displayTopic(array &$topic_selects)
	{
		if (! in_array('ms.modified_time AS topic_modified_time', $topic_selects))
			$topic_selects[] = 'ms.modified_time AS topic_modified_time';

		if ((is_on('optimus_topic_description') || is_on('optimus_og_image')) && ! in_array('ms.body AS topic_first_message', $topic_selects))
			$topic_selects[] = 'ms.body AS topic_first_message';

		if (is_on('optimus_allow_change_topic_desc'))
			$topic_selects[] = 't.optimus_description';
	}

	/**
	 * Prepare a description and additional Open Graph data
	 */
	public function menuButtons()
	{
		global $context, $board_info;

		if (empty($context['first_message']))
			return;

		// Generated description from the text of the first post of the topic
		if (is_on('optimus_topic_description'))
			$this->makeDescriptionFromFirstMessage();

		// Use own description of topic
		if (! empty($context['topicinfo']['optimus_description']))
			$context['meta_description'] = $context['topicinfo']['optimus_description'];

		// Additional data
		$context['optimus_og_type']['article'] = array(
			'published_time' => date('Y-m-d\TH:i:s', (int) $context['topicinfo']['topic_started_time']),
			'modified_time'  => empty($context['topicinfo']['topic_modified_time']) ? null : date('Y-m-d\TH:i:s', (int)
			$context['topicinfo']['topic_modified_time']),
			'author'         => empty($context['topicinfo']['topic_started_name']) ? null : $context['topicinfo']['topic_started_name'],
			'section'        => $board_info['name'],
			'tag'            => $context['optimus_keywords'] ?? null
		);
	}

	public function beforeCreateTopic(array &$msgOptions, array &$topicOptions, array &$posterOptions, array &$topic_columns, array &$topic_parameters)
	{
		if (! $this->canChangeDescription())
			return;

		$topic_columns['optimus_description'] = 'string-255';
		$topic_parameters[] = op_xss(op_request('optimus_description', ''));
	}

	public function modifyPost(array &$messages_columns, array &$update_parameters, array &$msgOptions, array &$topicOptions)
	{
		if (empty($topicOptions['first_msg']) || $topicOptions['first_msg'] != $msgOptions['id'])
			return;

		$this->modifyDescription($topicOptions['id']);
	}

	public function postEnd()
	{
		global $context, $smcFunc, $txt;

		if ($context['is_new_topic']) {
			$context['optimus']['description'] = op_xss(op_request('optimus_description', ''));
		} else {
			$request = $smcFunc['db_query']('', '
				SELECT optimus_description, id_member_started
				FROM {db_prefix}topics
				WHERE id_topic = {int:id_topic}
				LIMIT 1',
				array(
					'id_topic' => $context['current_topic']
				)
			);

			[$context['optimus']['description'], $topic_author] = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);

			$context['user']['started'] = $context['user']['id'] == $topic_author && !$context['user']['is_guest'];
		}

		if (! $this->canChangeDescription() || empty($context['is_first_post']))
			return;

		$context['posting_fields']['optimus_description']['label']['text'] = $txt['optimus_seo_description'];
		$context['posting_fields']['optimus_description']['input'] = array(
			'type' => 'textarea',
			'attributes' => array(
				'id'        => 'optimus_description',
				'maxlength' => 255,
				'value'     => $context['optimus']['description']
			)
		);
	}

	private function makeDescriptionFromFirstMessage()
	{
		global $context;

		if (empty($context['topicinfo']['topic_first_message']) || ! empty($context['topicinfo']['optimus_description']))
			return;

		$body = $context['topicinfo']['topic_first_message'];

		censorText($body);

		$context['meta_description'] = op_teaser($body);
	}

	private function modifyDescription(int $topic)
	{
		global $smcFunc;

		if (! $this->canChangeDescription())
			return;

		$description = op_xss(op_request('optimus_description', ''));

		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET optimus_description = {string:description}
			WHERE id_topic = {int:current_topic}',
			array(
				'description'   => shorten_subject(strip_tags(parse_bbc($description)), 200),
				'current_topic' => $topic
			)
		);
	}

	private function canChangeDescription(): bool
	{
		global $context, $topic;

		if (! isset($context['user']['started']))
			$context['user']['started'] = empty($topic);

		return is_on('optimus_allow_change_topic_desc') && (
			allowedTo('optimus_add_descriptions_any') || (! empty($context['user']['started']) && allowedTo('optimus_add_descriptions_own'))
		);
	}
}
