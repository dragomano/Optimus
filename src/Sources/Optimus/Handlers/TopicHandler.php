<?php declare(strict_types=1);

/**
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2024 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 3.0 Beta
 */

namespace Bugo\Optimus\Handlers;

use Bugo\Compat\{Board, Config, IntegrationHook};
use Bugo\Compat\{BBCodeParser, Lang, Theme};
use Bugo\Compat\{Db, Topic, User, Utils};
use Bugo\Optimus\Utils\{Input, Str};

if (! defined('SMF'))
	die('No direct access...');

final class TopicHandler
{
	public function __invoke(): void
	{
		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::prepareOgImage#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_menu_buttons', self::class . '::menuButtons#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_load_permissions', self::class . '::loadPermissions#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_optimus_basic_settings', self::class . '::basicSettings#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_display_topic', self::class . '::displayTopic#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_before_create_topic', self::class . '::beforeCreateTopic#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_modify_post', self::class . '::modifyPost#', false, __FILE__
		);

		IntegrationHook::add(
			'integrate_post_end', self::class . '::postEnd#', false, __FILE__
		);
	}

	public function prepareOgImage(): void
	{
		if (empty(Config::$modSettings['optimus_og_image']) || empty(Utils::$context['topicinfo']['id_first_msg']))
			return;

		$firstMessageId = Utils::$context['topicinfo']['id_first_msg'];

		// Looking for an image in attachments of the topic first message
		if (
			! empty(Utils::$context['loaded_attachments'])
			&& isset(Utils::$context['loaded_attachments'][$firstMessageId])
		) {
			$attachments = Utils::$context['loaded_attachments'][$firstMessageId];
			$attach = ';attach=' . ($key = array_key_first($attachments)) . ';image';
			Theme::$current->settings['og_image'] = Config::$scripturl . '?action=dlattach;topic='
				. Utils::$context['current_topic'] . $attach;

			Utils::$context['optimus_og_image'] = [
				'url'    => Theme::$current->settings['og_image'],
				'width'  => $attachments[$key]['width'],
				'height' => $attachments[$key]['height'],
				'mime'   => $attachments[$key]['mime_type'],
			];
		}

		// Looking for an image in the text of the topic first message
		if (empty(Utils::$context['topicinfo']['topic_first_message']))
			return;

		if (empty(Utils::$context['optimus_og_image'])) {
			$image = preg_match(
				'/\[img.*]([^]\[]+)\[\/img]/U',
				Utils::$context['topicinfo']['topic_first_message'],
				$value
			);

			Theme::$current->settings['og_image'] = $image
				? array_pop($value)
				: (Theme::$current->settings['og_image'] ?? '');
		}
	}

	public function loadPermissions(array $permissionGroups, array &$permissionList): void
	{
		if (empty(Config::$modSettings['optimus_allow_change_topic_desc']))
			return;

		$permissionList['membergroup']['optimus_add_descriptions'] = [true, 'general', 'view_basic_info'];
	}

	public function basicSettings(array &$config_vars): void
	{
		$counter = 0;
		foreach ($config_vars as $key => $dump) {
			if (isset($dump[1]) && $dump[1] === 'optimus_topic_extend_title') {
				$counter = $key + 1;
				break;
			}
		}

		$config_vars = array_merge(
			array_slice($config_vars, 0, $counter, true),
			[
				'',
				['check', 'optimus_topic_description'],
				[
					'check',
					'optimus_allow_change_topic_desc',
					'subtext' => Lang::$txt['optimus_allow_change_topic_desc_subtext']
				],
			],
			array_slice($config_vars, $counter, null, true)
		);
	}

	public function displayTopic(array &$columns): void
	{
		if (! empty(Config::$modSettings['optimus_allow_change_topic_desc']))
			$columns[] = 't.optimus_description';

		if (! in_array('ms.modified_time AS topic_modified_time', $columns))
			$columns[] = 'ms.modified_time AS topic_modified_time';

		if (
			empty(Config::$modSettings['optimus_topic_description'])
			&& empty(Config::$modSettings['optimus_og_image'])
		) {
			return;
		}

		if (! in_array('ms.body AS topic_first_message', $columns))
			$columns[] = 'ms.body AS topic_first_message';
	}

	/**
	 * Prepare a description and additional Open Graph data
	 */
	public function menuButtons(): void
	{
		if (empty(Utils::$context['first_message']))
			return;

		$this->makeDescriptionFromFirstMessage();
		$this->makeDescriptionByOptimus();

		// Additional data
		$startedName = Utils::$context['topicinfo']['topic_started_name'] ?? '';
		$modifiedTime = Utils::$context['topicinfo']['topic_modified_time'] ?? 0;
		Utils::$context['optimus_og_type']['article'] = [
			'published_time' => date('Y-m-d\TH:i:s', (int) Utils::$context['topicinfo']['topic_started_time']),
			'modified_time'  => empty($modifiedTime) ? null : date('Y-m-d\TH:i:s', (int) $modifiedTime),
			'author'         => empty($startedName) ? null : $startedName,
			'section'        => Board::$info['name'],
			'tag'            => Utils::$context['optimus_keywords'] ?? null,
		];
	}

	public function beforeCreateTopic(
		array $msgOptions,
		array $topicOptions,
		array $posterOptions,
		array &$topicColumns,
		array &$topicParameters
	): void
	{
		if (! $this->canChangeDescription())
			return;

		$topicColumns['optimus_description'] = 'string-255';
		$topicParameters[] = Input::xss(Input::request('optimus_description', ''));
	}

	public function modifyPost(
		array $messagesColumns,
		array $updateParameters,
		array $msgOptions,
		array $topicOptions
	): void
	{
		if (empty($topicOptions['first_msg']) || $topicOptions['first_msg'] != $msgOptions['id'])
			return;

		$this->modifyDescription($topicOptions['id']);
	}

	public function postEnd(): void
	{
		if (Utils::$context['is_new_topic']) {
			Utils::$context['optimus']['description'] = Input::xss(
				Input::request('optimus_description', '')
			);
		} else {
			$result = Db::$db->query('', '
				SELECT optimus_description, id_member_started
				FROM {db_prefix}topics
				WHERE id_topic = {int:id_topic}
				LIMIT 1',
				[
					'id_topic' => Utils::$context['current_topic'],
				]
			);

			[Utils::$context['optimus']['description'], $topicAuthor] = Db::$db->fetch_row($result);

			Db::$db->free_result($result);

			Utils::$context['user']['started'] = Utils::$context['user']['id'] == $topicAuthor
				&& ! Utils::$context['user']['is_guest'];
		}

		$this->addFields();
	}

	private function addFields(): void
	{
		if (! $this->canChangeDescription() || empty(Utils::$context['is_first_post']))
			return;

		Utils::$context['posting_fields']['optimus_description']['label']['text'] = Lang::$txt['optimus_seo_description'];
		Utils::$context['posting_fields']['optimus_description']['input'] = [
			'type' => 'textarea',
			'attributes' => [
				'id'          => 'optimus_description',
				'maxlength'   => 255,
				'value'       => Utils::$context['optimus']['description'],
				'placeholder' => Lang::$txt['optimus_enter_description'],
			],
		];
	}

	private function makeDescriptionFromFirstMessage(): void
	{
		if (
			empty(Config::$modSettings['optimus_topic_description'])
			|| empty(Utils::$context['topicinfo']['topic_first_message'])
		) {
			return;
		}

		Utils::$context['meta_description'] = Str::teaser(Utils::$context['topicinfo']['topic_first_message']);
	}

	private function makeDescriptionByOptimus(): void
	{
		if (empty(Utils::$context['topicinfo']['optimus_description']))
			return;

		Utils::$context['meta_description'] = Utils::$context['topicinfo']['optimus_description'];
	}

	private function modifyDescription(int $topic): void
	{
		if (! $this->canChangeDescription())
			return;

		$description = Input::xss(Input::request('optimus_description', ''));

		Db::$db->query('', '
			UPDATE {db_prefix}topics
			SET optimus_description = {string:description}
			WHERE id_topic = {int:current_topic}',
			[
				'description'   => Utils::shorten(strip_tags(BBCodeParser::load()->parse($description)), 200),
				'current_topic' => $topic,
			]
		);
	}

	private function canChangeDescription(): bool
	{
		if (! isset(Utils::$context['user']['started']))
			Utils::$context['user']['started'] = empty(Topic::$id);

		if (empty(Config::$modSettings['optimus_allow_change_topic_desc']))
			return false;

		return User::hasPermission('optimus_add_descriptions_any')
			|| (
				User::hasPermission('optimus_add_descriptions_own')
				&& ! empty(Utils::$context['user']['started'])
			);
	}
}
