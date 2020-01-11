<?php

function template_favicon()
{
	global $txt, $modSettings, $context, $boardurl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">', $txt['optimus_favicon_title'], '</h3>
	</div>';

	if (!empty($modSettings['optimus_favicon_api_key']))
		echo '
	<form id="favicon_form" method="post" action="https://realfavicongenerator.net/api/favicon_generator" id="favicon_form" target="_blank">
		<input type="hidden" name="json_params" id="json_params">
	</form>';

	echo '
	<div class="windowbg noup">
		<div class="content">
			<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
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
				<hr class="hrcolor clear">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
				<div class="righttext"><input type="submit" class="button" value="', $txt['save'], '"></div>
			</form>
		</div>
	</div>';

	// https://realfavicongenerator.net/api/interactive_api
	if (!empty($modSettings['optimus_favicon_api_key']))
		echo '
	<script type="text/javascript">
		function computeJson() {
			let params = { favicon_generation: {
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
	<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_meta_title'], '</h3>
		</div>
		<p class="information centertext">', $txt['optimus_meta_info'], '</p>
		<div class="windowbg">
			<div class="content centertext">
				<table class="table_grid">
					<tr class="title_bar">
						<th>', $txt['optimus_meta_tools'], '</th>
						<th>', $txt['optimus_meta_name'], '</th>
						<th>', $txt['optimus_meta_content'], '</th>
					</tr>';

	$metatags = !empty($modSettings['optimus_meta']) ? unserialize($modSettings['optimus_meta']) : '';
	$engines  = array();

	foreach ($txt['optimus_search_engines'] as $engine => $data) {
		$engines[] = $data[0];

		echo '
					<tr class="windowbg">
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
					<tr class="windowbg">
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
					<a href="#" onclick="addNewTag(); return false;" class="bbc_link">', $txt['optimus_meta_addtag'], '</a>
				</div>
				<script type="text/javascript"><!-- // --><![CDATA[
					document.getElementById("newtag_link").style.display = "";
					function addNewTag() {
						setOuterHTML(document.getElementById("moreTags"), \'<div style="margin-top: 1ex;"><input type="text" name="custom_tag_name[]" size="24" class="input_text"> => <input type="text" name="custom_tag_value[]" size="40" class="input_text"><\' + \'/div><div id="moreTags"><\' + \'/div>\');
					}
				// ]]></script>
				<hr class="hrcolor clear">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
				<div class="righttext"><input type="submit" class="button" value="', $txt['save'], '"></div>
			</div>
		</div>
	</form>
	<br class="clear">';
}

function template_counters()
{
	global $context, $txt, $modSettings;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_counters'], '</h3>
		</div>
		<div class="windowbg noup">
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
				<hr class="hrcolor clear">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
				<div class="righttext"><input type="submit" class="button" value="', $txt['save'], '"></div>
			</div>
		</div>
	</form>
	<br class="clear">';
}

function template_robots()
{
	global $context, $txt, $boardurl;

	echo '
	<form action="', $context['post_url'], '" method="post">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_manage'], '</h3>
		</div>
		<div class="windowbg noup">
			<div class="content">
				<div class="half_content">
					<div class="content">
						<h4>', $txt['optimus_rules'], '</h4>
						<span class="smalltext">', $txt['optimus_rules_hint'], '</span>
						', $context['new_robots_content'], '
						<span class="smalltext">', $txt['optimus_useful'], '</span>
					</div>
				</div>
				<div class="half_content">
					<div class="content">
						<h4>', $context['robots_txt_exists'] ? '<a href="' . $boardurl . '/robots.txt" target="_blank">robots.txt</a>' : 'robots.txt', '</h4>
						<textarea rows="22" name="robots">', $context['robots_content'], '</textarea>
					</div>
				</div>
				<hr class="hrcolor clear">
				<div class="half_content">
					<div class="floatleft">
						<h4>', $txt['optimus_links_title'], '</h4>
						<ul class="smalltext">';

	foreach ($txt['optimus_links'] as $ankor => $url) {
		echo '
							<li><a href="', $url, '" target="_blank">', $ankor, '</a></li>';
	}

	echo '
						</ul>
					</div>
				</div>
				<hr class="hrcolor clear">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<div class="righttext"><input type="submit" class="button" value="', $txt['save'], '"></div>
			</div>
		</div>
	</form>
	<br class="clear">';
}

function template_404()
{
	global $txt;

	echo '
	<div class="centertext" style="width: 60%">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_404_h2'], '</h3>
		</div>
		<div class="windowbg">
			<div class="content">', $txt['optimus_404_h3'], '</div>
		</div>
	</div>
	<div class="centertext"><a href="javascript:history.go(-1)">', $txt['back'], '</a></div>';
}

function template_403()
{
	global $txt;

	echo '
	<div class="centertext" style="width: 60%">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_403_h2'], '</h3>
		</div>
		<div class="windowbg">
			<div class="content">', $txt['optimus_403_h3'], '</div>
		</div>
	</div>
	<div class="centertext"><a href="javascript:history.go(-1)">', $txt['back'], '</a></div>';
}

function template_footer_counters_above()
{
}

function template_footer_counters_below()
{
	global $modSettings;

	if (!empty($modSettings['optimus_count_code']))
		echo '
	<div class="counters">', $modSettings['optimus_count_code'], '</div>';
}
