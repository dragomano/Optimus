<?php declare(strict_types=1);

/**
 * TinyPortal.php
 *
 * @package Optimus
 */

namespace Bugo\Optimus\Addons;

use Bugo\Optimus\Utils\Input;
use Bugo\Optimus\Utils\Str;

if (! defined('SMF'))
	die('No direct access...');

/**
 * TinyPortal addon for Optimus
 */
class TinyPortal extends AbstractAddon
{
	public function __construct()
	{
		parent::__construct();

		$this->prepareArticleMeta();

		$this->dispatcher->subscribeTo('robots.rules', [$this, 'changeRobots']);
		$this->dispatcher->subscribeTo('sitemap.links', [$this, 'changeSitemap']);
	}

	public function prepareArticleMeta(): void
	{
		global $context, $settings, $scripturl;

		if (! Input::isGet('page') || empty($context['TPortal']['article']))
			return;

		$pattern = $context['TPortal']['article']['rendertype'] == 'bbc' ? '/\[img.*]([^\]\[]+)\[\/img\]/U' : '/<img(.*)src(.*)=(.*)"(.*)"/U';
		$firstPostImage = preg_match($pattern, $context['TPortal']['article']['body'], $value);
		$settings['og_image'] = $firstPostImage ? array_pop($value) : null;

		$context['meta_description'] = Str::teaser(empty($context['TPortal']['article']['intro']) ? $context['TPortal']['article']['body'] : $context['TPortal']['article']['intro']);
		$context['optimus_og_type']['article']['published_time'] = date('Y-m-d\TH:i:s', $context['TPortal']['article']['date']);
		$context['optimus_og_type']['article']['section'] = $context['TPortal']['article']['category_name'] ?? '';
		$context['canonical_url'] = $scripturl . '?page=' . ($context['TPortal']['article']['shortname'] ?: $context['TPortal']['article']['id']);
	}

	public function changeRobots(object $object): void
	{
		if (! function_exists('TPortal'))
			return;

		$object->getTarget()->commonRules[] = "Allow: " . $object->getTarget()->urlPath . "/*page";
	}

	public function changeSitemap(object $object): void
	{
		global $modSettings, $smcFunc, $scripturl;

		if (! class_exists('\TinyPortal\Integrate'))
			return;

		$startYear = (int) ($modSettings['optimus_start_year'] ?? 0);

		$request = $smcFunc['db_query']('', '
			SELECT a.id, a.date, a.shortname
			FROM {db_prefix}tp_articles AS a
				INNER JOIN {db_prefix}tp_variables AS v ON (a.category = v.id)
			WHERE a.approved = {int:approved}
				AND a.off = {int:off_status}
				AND {int:guests} IN (v.value3)' . ($startYear ? '
				AND YEAR(FROM_UNIXTIME(a.date)) >= {int:start_year}' : '') . '
			ORDER BY a.id DESC',
			[
				'approved'   => 1, // The article must be approved
				'off_status' => 0, // The article must be active
				'guests'     => -1, // The article category must be available to guests
				'start_year' => $startYear
			]
		);

		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$url = $scripturl . '?page=' . ($row['shortname'] ?: $row['id']);

			$object->getTarget()->links[] = [
				'loc'     => $url,
				'lastmod' => $row['date']
			];
		}

		$smcFunc['db_free_result']($request);
	}
}
