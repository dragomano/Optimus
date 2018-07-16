<?php

/**
 * Optimus.template.php
 *
 * @package Optimus
 * @link https://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2018 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 0.1 alpha
 */

function template_favicon()
{
	global $txt, $settings, $context, $boardurl;

	echo '
	<we:cat>', $txt['optimus_favicon_title'], '</we:cat>';

	if (!empty($settings['optimus_favicon_api_key']))
		echo '
	<form id="favicon_form" method="post" action="https://realfavicongenerator.net/api/favicon_generator" id="favicon_form" target="_blank">
		<input type="hidden" name="json_params" id="json_params">
	</form>';

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="UTF-8">
		<div class="windowbg2 wrc">
			<dl class="settings">
				<dt>
					<span><label for="optimus_favicon_api_key">', $txt['optimus_favicon_api_key'], '</label></span>
				</dt>
				<dd>
					<input name="optimus_favicon_api_key" id="optimus_favicon_api_key" value="', !empty($settings['optimus_favicon_api_key']) ? $settings['optimus_favicon_api_key'] : '', '" type="text" size="50">';

	if (!empty($settings['optimus_favicon_api_key']))
		echo '
					<input type="submit" form="favicon_form" id="form_button" value="', $txt['optimus_favicon_create'], '">';

	echo '
				</dd>
				<dt>
					<span>
						<label for="optimus_favicon_text">', $txt['optimus_favicon_text'], '</label><br>
						<span class="smalltext">', $txt['optimus_favicon_help'], '</span>
					</span>
				</dt>
				<dd>
					<textarea rows="5" style="width:90%" name="optimus_favicon_text" id="optimus_favicon_text">', !empty($settings['optimus_favicon_text']) ? $settings['optimus_favicon_text'] : '', '</textarea>
				</dd>
			</dl>
		</div>
		<div class="right padding"><input type="submit" class="submit" value="', $txt['save'], '"></div>
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
	</form>';

	// https://realfavicongenerator.net/api/interactive_api
	if (!empty($settings['optimus_favicon_api_key']))
		echo '
	<script type="text/javascript">
		function computeJson() {
			var params = { favicon_generation: {
				callback: {},
				master_picture: {},
				files_location: {},
				api_key: $("#optimus_favicon_api_key").val()
			}};
			params.favicon_generation.master_picture.type = "no_picture";
			params.favicon_generation.files_location.type = "path";
			params.favicon_generation.files_location.path = "' . parse_url($boardurl, PHP_URL_PATH) . '/";
			params.favicon_generation.callback.type = "none";
			return params;
		}
		jQuery(document).ready(function($) {
			$("#favicon_form").submit(function(e) {
				$("#json_params").val(JSON.stringify(computeJson()));
			});
		});
	</script>';
}

function template_metatags()
{
	global $context, $txt, $settings;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="UTF-8">
		<we:cat>', $txt['optimus_meta_title'], '</we:cat>
		<p class="description">', $txt['optimus_meta_info'], '</p>
		<div class="windowbg2 wrc center">
			<table class="table_grid w100 cs0">
				<tr class="catbg">
					<th>', $txt['optimus_meta_name'], '</th>
					<th>', $txt['optimus_meta_content'], '</th>
				</tr>';

	$metatags = !empty($settings['optimus_meta']) ? unserialize($settings['optimus_meta']) : '';
	$engines  = array();

	$i = 0;
	foreach ($txt['optimus_search_engines'] as $tag) {
		$engines[] = $tag;

		echo '
				<tr class="windowbg', $i % 2 == 0 ? '' : '2', '">
					<td>
						<input type="text" name="custom_tag_name[]" size="24" value="', $tag, '">
					</td>
					<td>
						<input type="text" name="custom_tag_value[]" size="40" value="', isset($metatags[$tag]) ? $metatags[$tag] : '', '">
					</td>
				</tr>';

		$i++;
	}

	if (!empty($metatags)) {
		foreach ($metatags as $name => $value) {
			if (!in_array($name, $engines)) {
				echo '
				<tr class="windowbg', $i % 2 == 0 ? '' : '2', '">
					<td>
						<input type="text" name="custom_tag_name[]" size="24" value="', $name, '">
					</td>
					<td>
						<input type="text" name="custom_tag_value[]" size="40" value="', $value, '">
					</td>
				</tr>';
			}

			$i++;
		}
	}

	echo '
			</table>
			<hr>
			<div id="moreTags"></div>
			<div style="margin-top: 1ex; display: none" id="newtag_link">
				<a href="#" onclick="addNewTag(); return false;">', $txt['optimus_meta_addtag'], '</a>
			</div>';

	add_js('
	$("#newtag_link").show();
	function addNewTag()
	{
		$("#moreTags").append(\'<div style="margin-top: 1ex"><input name="custom_tag_name[]" size="24"> =&gt; <input name="custom_tag_value[]" size="40"><\' + \'/div>\');
	}');

	echo '
		</div>
		<div class="right padding"><input type="submit" class="submit" value="', $txt['save'], '"></div>
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
	</form>';
}

function template_counters()
{
	global $context, $txt, $settings;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="UTF-8">
		<we:cat>', $txt['optimus_counters'], '</we:cat>
		<div class="windowbg2 wrc">
			<label for="optimus_head_code">', $txt['optimus_head_code'], '</label><br>
			<textarea id="optimus_head_code" name="optimus_head_code" cols="60" rows="4" style="width: 99%">', !empty($settings['optimus_head_code']) ? $settings['optimus_head_code'] : '', '</textarea>
			<br><br>
			<label for="optimus_stat_code">', $txt['optimus_stat_code'], '</label><br>
			<textarea id="optimus_stat_code" name="optimus_stat_code" cols="60" rows="4" style="width: 99%">', !empty($settings['optimus_stat_code']) ? $settings['optimus_stat_code'] : '', '</textarea>
			<br><br>
			<label for="optimus_count_code">', $txt['optimus_count_code'], '</label><br>
			<textarea id="optimus_count_code" name="optimus_count_code" cols="60" rows="4" style="width: 99%">', !empty($settings['optimus_count_code']) ? $settings['optimus_count_code'] : '', '</textarea>
			<br><br>
			<label for="optimus_counters_css">', $txt['optimus_counters_css'], '</label><br>
			<textarea id="optimus_counters_css" name="optimus_counters_css" cols="60" rows="4" style="width: 99%">', !empty($settings['optimus_counters_css']) ? $settings['optimus_counters_css'] : '', '</textarea>
			<br><br>
			<label for="optimus_ignored_actions">', $txt['optimus_ignored_actions'], '</label><br>
			<input type="text" value="', !empty($settings['optimus_ignored_actions']) ? $settings['optimus_ignored_actions'] : '', '" id="optimus_ignored_actions" name="optimus_ignored_actions" style="width: 99%">
		</div>
		<div class="right padding"><input type="submit" class="submit" value="', $txt['save'], '"></div>
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
	</form>';
}

function template_robots()
{
	global $context, $txt, $boardurl;

	echo '
	<form action="', $context['post_url'], '" method="post">
		<we:cat>', $txt['optimus_manage'], '</we:cat>
		<div class="windowbg2 wrc">
			<h4>', $txt['optimus_rules'], '</h4>
			<span class="smalltext">', $txt['optimus_rules_hint'], '</span>
			', $context['new_robots_content'], '
			<span class="smalltext">', $txt['optimus_useful'], '</span>
		</div>
		<div class="windowbg2 wrc">
			<h4>', $context['robots_txt_exists'] ? '<a href="' . $boardurl . '/robots.txt" target="_blank">robots.txt</a>' : 'robots.txt', '</h4>
			<textarea rows="12" name="robots" style="width: 100%">', $context['robots_content'], '</textarea>
		</div>
		<div class="windowbg2 wrc">
			<h4>', $txt['optimus_links_title'], '</h4>
			<ul class="smalltext">';

	foreach ($txt['optimus_links'] as $ankor => $url) {
		echo '
				<li><a href="', $url, '" target="_blank">', $ankor, '</a></li>';
	}

	echo '
			</ul>
		</div>
		<div class="right padding"><input type="submit" class="submit" value="', $txt['save'], '"></div>
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
	</form>';
}

function template_donate()
{
	global $txt, $scripturl;

	echo '
	<we:cat>', $txt['optimus_donate_title'], '</we:cat>
	<div class="windowbg2 wrc center">';

	if (in_array($txt['lang_dictionary'], array('ru', 'uk')))
		echo '
		<iframe src="https://money.yandex.ru/embed/donate.xml?account=410011113366680&quickpay=donate&payment-type-choice=on&mobile-payment-type-choice=on&default-sum=100&targets=%D0%9D%D0%B0+%D1%80%D0%B0%D0%B7%D0%B2%D0%B8%D1%82%D0%B8%D0%B5+%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B0&target-visibility=on&project-name=%D0%9B%D0%BE%D0%B3%D0%BE%D0%B2%D0%BE+%D0%BC%D0%B5%D0%B4%D0%B2%D0%B5%D0%B4%D1%8F&project-site=https%3A%2F%2Fdragomano.ru&button-text=05&successURL=" width="508" height="117" style="border:none;"></iframe>';
	else
		echo '
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="K2AVLACFRVJN6">
			<input type="hidden" name="return" value="', $scripturl, '?action=admin;area=optimus;sa=donate">
			<input type="hidden" name="cancel_return" value="', $scripturl, '">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" style="height: auto">
		</form>';

	echo '
	</div>';
}

function template_count_code()
{
	global $settings;

	echo '
	<div class="counters">' . $settings['optimus_count_code'] . '</div>';
}

function template_json_ld()
{
	global $settings, $context;

	// JSON-LD
	echo '
	<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"@type": "BreadcrumbList",
		"itemListElement": [';

		$i = 1;
		foreach ($context['linktree'] as $id => $data)
			$list_item[$id] = '{
			"@type": "ListItem",
			"position": ' . $i++ . ',
			"item": {
				"@id": "' . (isset($data['url']) ? $data['url'] : '') . '",
				"name": "' . $data['name'] . '"
			}
		}';

		if (!empty($list_item))
			echo implode($list_item, ',');

		echo ']
	}
	</script>';
}
