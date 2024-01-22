<?php declare(strict_types=1);

/**
 * TopicHandler.php
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
use Bugo\Optimus\Utils\Str;

if (! defined('SMF'))
	die('No direct access...');

final class TopicHandler
{
	public function __construct()
	{
		(new TagHandler())();
	}

	public function __invoke(): void
	{
		add_integration_function('integrate_menu_buttons', self::class . '::prepareOgImage#', false, __FILE__);
		add_integration_function('integrate_menu_buttons', self::class . '::menuButtons#', false, __FILE__);
		add_integration_function('integrate_load_permissions', self::class . '::loadPermissions#', false, __FILE__);
		add_integration_function('integrate_display_topic', self::class . '::displayTopic#', false, __FILE__);
		add_integration_function('integrate_before_create_topic', self::class . '::beforeCreateTopic#', false, __FILE__);
		add_integration_function('integrate_modify_post', self::class . '::modifyPost#', false, __FILE__);
		add_integration_function('integrate_post_end', self::class . '::postEnd#', false, __FILE__);
	}

	public function prepareOgImage(): void
	{
		global $modSettings, $context, $scripturl, $settings;

		if (empty($modSettings['optimus_og_image']) || empty($context['topicinfo']['id_first_msg']))
			return;

		$firstMessageId = $context['topicinfo']['id_first_msg'];

		// Looking for an image in attachments of the topic first message
		if (! empty($context['loaded_attachments']) && isset($context['loaded_attachments'][$firstMessageId])) {
			$attachments = $context['loaded_attachments'][$firstMessageId];
			$settings['og_image'] = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . ';attach=' . ($key = array_key_first($attachments)) . ';image';

			$context['optimus_og_image'] = [
				'url'    => $settings['og_image'],
				'width'  => $attachments[$key]['width'],
				'height' => $attachments[$key]['height'],
				'mime'   => $attachments[$key]['mime_type'],
			];
		}

		// Looking for an image in the text of the topic first message
		if (empty($context['optimus_og_image']) && ! empty($context['topicinfo']['topic_first_message'])) {
			$image = preg_match('/\[img.*]([^]\[]+)\[\/img]/U', $context['topicinfo']['topic_first_message'], $value);
			$settings['og_image'] = $image ? array_pop($value) : $settings['og_image'];
		}
	}

	public function loadPermissions(array $permissionGroups, array &$permissionList): void
	{
		global $modSettings;

		if (! empty($modSettings['optimus_allow_change_topic_desc'])) {
			$permissionList['membergroup']['optimus_add_descriptions'] = [true, 'general', 'view_basic_info'];
		}
	}

	public function displayTopic(array &$topic_selects): void
	{
		global $modSettings;

		if (! in_array('ms.modified_time AS topic_modified_time', $topic_selects))
			$topic_selects[] = 'ms.modified_time AS topic_modified_time';

		if (
			(! empty($modSettings['optimus_topic_description']) || ! empty($modSettings['optimus_og_image']))
			&& ! in_array('ms.body AS topic_first_message', $topic_selects)
		) {
			$topic_selects[] = 'ms.body AS topic_first_message';
		}

		if (! empty($modSettings['optimus_allow_change_topic_desc']))
			$topic_selects[] = 't.optimus_description';
	}

	/**
	 * Prepare a description and additional Open Graph data
	 */
	public function menuButtons(): void
	{
		global $context, $modSettings, $board_info;

		if (empty($context['first_message']))
			return;

		// Generated description from the text of the first post of the topic
		if (! empty($modSettings['optimus_topic_description']))
			$this->makeDescriptionFromFirstMessage();

		// Use own description of topic
		if (! empty($context['topicinfo']['optimus_description'])) {
			$context['meta_description'] = $context['topicinfo']['optimus_description'];
		}

		// Additional data
		$context['optimus_og_type']['article'] = [
			'published_time' => date('Y-m-d\TH:i:s', (int) $context['topicinfo']['topic_started_time']),
			'modified_time'  => empty($context['topicinfo']['topic_modified_time']) ? null : date('Y-m-d\TH:i:s', (int) $context['topicinfo']['topic_modified_time']),
			'author'         => empty($context['topicinfo']['topic_started_name']) ? null : $context['topicinfo']['topic_started_name'],
			'section'        => $board_info['name'],
			'tag'            => $context['optimus_keywords'] ?? null,
		];
	}

	public function beforeCreateTopic(array $msgOptions, array $topicOptions, array $posterOptions, array &$topic_columns, array &$topic_parameters): void
	{
		if (! $this->canChangeDescription())
			return;

		$topic_columns['optimus_description'] = 'string-255';
		$topic_parameters[] = Input::xss(Input::request('optimus_description', ''));
	}

	public function modifyPost(array $messages_columns, array $update_parameters, array $msgOptions, array $topicOptions): void
	{
		if (empty($topicOptions['first_msg']) || $topicOptions['first_msg'] != $msgOptions['id'])
			return;

		$this->modifyDescription($topicOptions['id']);
	}

	public function postEnd(): void
	{
		global $context, $smcFunc, $txt;

		if ($context['is_new_topic']) {
			$context['optimus']['description'] = Input::xss(Input::request('optimus_description', ''));
		} else {
			$request = $smcFunc['db_query']('', '
				SELECT optimus_description, id_member_started
				FROM {db_prefix}topics
				WHERE id_topic = {int:id_topic}
				LIMIT 1',
				[
					'id_topic' => $context['current_topic'],
				]
			);

			[$context['optimus']['description'], $topic_author] = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);

			$context['user']['started'] = $context['user']['id'] == $topic_author && !$context['user']['is_guest'];
		}

		if (! $this->canChangeDescription() || empty($context['is_first_post']))
			return;

		$context['posting_fields']['optimus_description']['label']['text'] = $txt['optimus_seo_description'];
		$context['posting_fields']['optimus_description']['input'] = [
			'type' => 'textarea',
			'attributes' => [
				'id'        => 'optimus_description',
				'maxlength' => 255,
				'value'     => $context['optimus']['description'],
			],
		];
	}

	private function makeDescriptionFromFirstMessage(): void
	{
		global $context;

		if (
			empty($context['topicinfo']['topic_first_message'])
			|| ! empty($context['topicinfo']['optimus_description'])
		) return;

		$body = $context['topicinfo']['topic_first_message'];

		censorText($body);

		$context['meta_description'] = Str::teaser($body);
	}

	private function modifyDescription(int $topic): void
	{
		global $smcFunc;

		if (! $this->canChangeDescription())
			return;

		$description = Input::xss(Input::request('optimus_description', ''));

		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET optimus_description = {string:description}
			WHERE id_topic = {int:current_topic}',
			[
				'description'   => shorten_subject(strip_tags(parse_bbc($description)), 200),
				'current_topic' => $topic,
			]
		);
	}

	private function canChangeDescription(): bool
	{
		global $context, $topic, $modSettings;

		if (! isset($context['user']['started'])) {
			$context['user']['started'] = empty($topic);
		}

		return ! empty($modSettings['optimus_allow_change_topic_desc']) && (
			allowedTo('optimus_add_descriptions_any')
			|| (! empty($context['user']['started']) && allowedTo('optimus_add_descriptions_own'))
		);
	}
}
