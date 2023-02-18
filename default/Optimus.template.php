<?php

/**
 * Optimus.template.php
 *
 * @package Optimus
 * @link https://addons.elkarte.net/feature/Optimus.html
 * @author Bugo https://dragomano.ru/mods/optimus
 * @copyright 2010-2023 Bugo
 * @license https://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 0.5
 */

function template_favicon()
{
	global $txt, $modSettings, $context, $boardurl;

	echo '
	<div id="admin_form_wrapper">
		<h3 class="category_header">', $txt['optimus_favicon_title'], '</h3>';

	if (!empty($modSettings['optimus_favicon_api_key']))
		echo '
		<form id="favicon_form" method="post" action="https://realfavicongenerator.net/api/favicon_generator" id="favicon_form" target="_blank">
			<input type="hidden" name="json_params" id="json_params">
		</form>';

	echo '
		<div class="content">
			<form action="', $context['post_url'], '" method="post" accept-charset="UTF-8">
				<dl class="settings">
					<dt>
						<span><label for="optimus_favicon_api_key">', $txt['optimus_favicon_api_key'], '</label></span>
					</dt>
					<dd>
						<input name="optimus_favicon_api_key" id="optimus_favicon_api_key" value="', !empty($modSettings['optimus_favicon_api_key']) ? $modSettings['optimus_favicon_api_key'] : '', '" class="input_text" type="text" size="50">';

	if (!empty($modSettings['optimus_favicon_api_key']))
		echo '
						<button type="submit" form="favicon_form" id="form_button" class="button" style="float:none">', $txt['optimus_favicon_create'], '</button>';

	echo '
					</dd>
					<dt>
						<span>
							<label for="optimus_favicon_text">', $txt['optimus_favicon_text'], '</label><br>
							<span class="smalltext">', $txt['optimus_favicon_help'], '</span>
						</span>
					</dt>
					<dd>
						<textarea rows="5" style="width:90%" name="optimus_favicon_text" id="optimus_favicon_text">', !empty($modSettings['optimus_favicon_text']) ? $modSettings['optimus_favicon_text'] : '', '</textarea>
					</dd>
				</dl>
				<hr class="hrcolor clear">';

	if (isset($context['admin-ssc_token']))
		echo '
				<input type="hidden" name="', $context['admin-ssc_token_var'], '" value="', $context['admin-ssc_token'], '">';

	echo '
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<div class="righttext"><input type="submit" class="button" value="', $txt['save'], '"></div>
			</form>
		</div>
	</div>';

	// https://realfavicongenerator.net/api/interactive_api
	if (!empty($modSettings['optimus_favicon_api_key']))
		echo '
	<script>
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

	echo '
	<br class="clear">';
}

function template_metatags()
{
	global $context, $txt, $modSettings;

	echo '
	<form id="admin_form_wrapper" action="', $context['post_url'], '" method="post" accept-charset="UTF-8">
		<h3 class="category_header">', $txt['optimus_meta_title'], '</h3>
		<p class="information centertext">', $txt['optimus_meta_info'], '</p>
		<div class="content centertext">
			<table>
				<tr>
					<th>', $txt['optimus_meta_tools'], '</th>
					<th>', $txt['optimus_meta_name'], '</th>
					<th>', $txt['optimus_meta_content'], '</th>
				</tr>';

	$metatags = !empty($modSettings['optimus_meta']) ? unserialize($modSettings['optimus_meta']) : '';
	$engines  = array();

	foreach ($txt['optimus_search_engines'] as $engine => $data) {
		$engines[] = $data[0];

		echo '
				<tr>
					<td>', $engine, ' (<strong>', $data[1], '</strong>)</td>
					<td>
						<input type="text" name="custom_tag_name[]" size="24" value="', $data[0], '">
					</td>
					<td>
						<input type="text" name="custom_tag_value[]" size="40" value="', isset($metatags[$data[0]]) ? $metatags[$data[0]] : '', '">
					</td>
				</tr>';
	}

	if (!empty($metatags)) {
		foreach ($metatags as $name => $value) {
			if (!in_array($name, $engines)) {
				echo '
				<tr>
					<td>', $txt['optimus_meta_customtag'], '</td>
					<td>
						<input type="text" name="custom_tag_name[]" size="24" value="', $name, '">
					</td>
					<td>
						<input type="text" name="custom_tag_value[]" size="40" value="', $value, '">
					</td>
				</tr>';
			}
		}
	}

	echo '
			</table>
			<noscript>
				<div style="margin-top: 1ex;"><input type="text" name="custom_tag_name[]" size="24" class="input_text"> => <input type="text" name="custom_tag_value[]" size="40" class="input_text"></div>
			</noscript>
			<div id="moreTags"></div>
			<div style="margin-top: 1ex; display: none;" id="newtag_link">
				<a href="#" onclick="addNewTag(); return false;">', $txt['optimus_meta_addtag'], '</a>
			</div>
			<script>
				document.getElementById("newtag_link").style.display = "";
				function addNewTag() {
					setOuterHTML(document.getElementById("moreTags"), \'<div style="margin-top: 1ex;"><input type="text" name="custom_tag_name[]" size="24" class="input_text"> => <input type="text" name="custom_tag_value[]" size="40" class="input_text"><\' + \'/div><div id="moreTags"><\' + \'/div>\');
				}
			</script>
			<hr class="hrcolor clear">';

	if (isset($context['admin-ssc_token']))
		echo '
			<input type="hidden" name="', $context['admin-ssc_token_var'], '" value="', $context['admin-ssc_token'], '">';

	echo '
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			<div class="righttext"><input type="submit" class="button" value="', $txt['save'], '"></div>
		</div>
	</form>
	<br class="clear">';
}

function template_counters()
{
	global $context, $txt, $modSettings;

	echo '
	<form id="admin_form_wrapper" action="', $context['post_url'], '" method="post" accept-charset="UTF-8">
		<h3 class="category_header">', $txt['optimus_counters'], '</h3>
		<div class="content">
			<label for="optimus_head_code">', $txt['optimus_head_code'], '</label><br>
			<textarea id="optimus_head_code" name="optimus_head_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_head_code']) ? $modSettings['optimus_head_code'] : '', '</textarea>
			<br><br>
			<label for="optimus_stat_code">', $txt['optimus_stat_code'], '</label><br>
			<textarea id="optimus_stat_code" name="optimus_stat_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_stat_code']) ? $modSettings['optimus_stat_code'] : '', '</textarea>
			<br><br>
			<label for="optimus_count_code">', $txt['optimus_count_code'], '</label><br>
			<textarea id="optimus_count_code" name="optimus_count_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_count_code']) ? $modSettings['optimus_count_code'] : '', '</textarea>
			<br><br>
			<label for="optimus_counters_css">', $txt['optimus_counters_css'], '</label><br>
			<textarea id="optimus_counters_css" name="optimus_counters_css" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_counters_css']) ? $modSettings['optimus_counters_css'] : '', '</textarea>
			<br><br>
			<label for="optimus_ignored_actions">', $txt['optimus_ignored_actions'], '</label><br>
			<input type="text" class="input_text" value="', !empty($modSettings['optimus_ignored_actions']) ? $modSettings['optimus_ignored_actions'] : '', '" id="optimus_ignored_actions" name="optimus_ignored_actions" style="width: 99%">
			<hr class="hrcolor clear">';

	if (isset($context['admin-ssc_token']))
		echo '
			<input type="hidden" name="', $context['admin-ssc_token_var'], '" value="', $context['admin-ssc_token'], '">';

	echo '
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			<div class="righttext"><input type="submit" class="button" value="', $txt['save'], '"></div>
		</div>
	</form>
	<br class="clear">';
}

function template_robots()
{
	global $context, $txt;

	echo '
	<form id="admin_form_wrapper" action="', $context['post_url'], '" method="post">
		<h3 class="category_header">', $txt['optimus_manage'], '</h3>
		<span class="topslice"><span></span></span>
		<div class="content">
			<div class="min">
				<div class="content">
					<h4>', $txt['optimus_rules'], '</h4>
					<span class="smalltext">', $txt['optimus_rules_hint'], '</span>
					', $context['new_robots_content'], '
				</div>
			</div>
			<div class="min">
				<div class="content">
					<h4>', $context['robots_txt_exists'] ? '<a href="/robots.txt">robots.txt</a>' : 'robots.txt', '</h4>
					<textarea rows="22" name="robots">', $context['robots_content'], '</textarea>
				</div>
			</div>
			<hr class="clear">
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			<div class="righttext"><input type="submit" class="button" value="', $txt['save'], '"></div>
		</div>
		<span class="botslice"><span></span></span>
	</form>
	<br class="clear">';
}
