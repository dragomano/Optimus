<?php

function template_base()
{
	global $context, $txt, $modSettings;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_main_page'], '</h3>
		</div>
		<p class="description centertext">', $txt['optimus_base_info'], '</p>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<div class="content">
				<dl class="settings">
					<dt>
						<span>
							<label for="optimus_forum_index">', $txt['optimus_forum_index'], '</label>
						</span>
					</dt>
					<dd>
						<em>', $context['forum_name'], '</em> - <input type="text" class="input_text" value="', !empty($modSettings['optimus_forum_index']) ? $modSettings['optimus_forum_index'] : '', '" id="optimus_forum_index" name="optimus_forum_index" style="width: 59%" />
					</dd>
					<dt>
						<span>
							<label for="optimus_description">', $txt['optimus_description'], '</label>
						</span>
					</dt>
					<dd>
						<textarea id="optimus_description" name="optimus_description" rows="4" style="width: 99%">', !empty($modSettings['optimus_description']) ? $modSettings['optimus_description'] : '', '</textarea>
					</dd>
				</dl>
			</div>
			<span class="botslice"><span></span></span>
		</div>
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_all_pages'], '</h3>
		</div>
		<p class="description centertext">', $txt['optimus_tpl_info'], '</p>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<div class="content">
				<dl class="settings">';

	$templates = !empty($modSettings['optimus_templates']) ? unserialize($modSettings['optimus_templates']) : '';
	foreach ($txt['optimus_templates'] as $name => $template) {
		echo '
					<dt>
						<span><label>', $txt['optimus_' . $name . '_tpl'], '</label></span>
					</dt>
					<dd>
						<input type="text" class="input_text" value="', isset($templates[$name]['name']) ? $templates[$name]['name'] : $template[0], '" name="', $name, '_name" />
						<input type="text" class="input_text" value="', isset($templates[$name]['page']) ? $templates[$name]['page'] : $template[1], '" name="', $name, '_page" />
						<input type="text" class="input_text" value="', isset($templates[$name]['site']) ? $templates[$name]['site'] : $template[2], '" name="', $name, '_site" />
					</dd>';
	}

	echo '
				</dl>
				<dl class="settings">
					<dt>
						<span>
							<label for="optimus_no_first_number">', $txt['optimus_no_first_number'], '</label>
						</span>
					</dt>
					<dd>
						<input type="checkbox" name="optimus_no_first_number" id="optimus_no_first_number"', !empty($modSettings['optimus_no_first_number']) ? ' checked="checked"' : '', ' />
					</dd>
					<dt>
						<span>
							<label for="optimus_board_description">', $txt['optimus_board_description'], '</label>
						</span>
					</dt>
					<dd>
						<input type="checkbox" name="optimus_board_description" id="optimus_board_description"', !empty($modSettings['optimus_board_description']) ? ' checked="checked"' : '', ' />
					</dd>
					<dt>
						<span>
							<label for="optimus_topic_description">', $txt['optimus_topic_description'], '</label>
						</span>
					</dt>
					<dd>
						<input type="checkbox" name="optimus_topic_description" id="optimus_topic_description"', !empty($modSettings['optimus_topic_description']) ? ' checked="checked"' : '', ' />
					</dd>
					<dt>
						<span>
							<label for="optimus_404_status">', $txt['optimus_404_status'], '</label>
						</span>
					</dt>
					<dd>
						<input type="checkbox" name="optimus_404_status" id="optimus_404_status"', !empty($modSettings['optimus_404_status']) ? ' checked="checked"' : '', ' />
					</dd>
				</dl>
				<hr class="hrcolor clear" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
			</div>
			<span class="botslice"><span></span></span>
		</div>
	</form>
	<br class="clear" />';
}

function template_favicon()
{
	global $txt, $modSettings, $context, $boardurl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">', $txt['optimus_favicon_title'], '</h3>
	</div>';

	if (!empty($modSettings['optimus_favicon_api_key']))
		echo '
	<div class="description centertext">
		<form id="favicon_form" method="post" action="https://realfavicongenerator.net/api/favicon_generator" id="favicon_form" target="_blank">
			<input type="hidden" name="json_params" id="json_params"/>
			<button type="submit" id="form_button" class="button_submit">', $txt['optimus_favicon_create'], '</button>
		</form>
	</div>';

	echo '
	<div class="windowbg2">
		<span class="topslice"><span></span></span>
		<div class="content">
			<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
				<dl class="settings">
					<dt>
						<span><label for="optimus_favicon_api_key">', $txt['optimus_favicon_api_key'], '</label></span>
					</dt>
					<dd>
						<input name="optimus_favicon_api_key" id="optimus_favicon_api_key" value="', !empty($modSettings['optimus_favicon_api_key']) ? $modSettings['optimus_favicon_api_key'] : '', '" class="input_text" type="text" size="50">
					</dd>
					<dt>
						<span>
							<label for="optimus_favicon_text">', $txt['optimus_favicon_text'], '</label><br />
							<span class="smalltext">', $txt['optimus_favicon_help'], '</span>
						</span>
					</dt>
					<dd>
						<textarea rows="5" style="width:90%" name="optimus_favicon_text" id="optimus_favicon_text">', !empty($modSettings['optimus_favicon_text']) ? $modSettings['optimus_favicon_text'] : '', '</textarea>
					</dd>
				</dl>
				<hr class="hrcolor clear" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
			</form>
		</div>
		<span class="botslice"><span></span></span>
	</div>';

	// https://realfavicongenerator.net/api/interactive_api
	if (!empty($modSettings['optimus_favicon_api_key']))
		echo '
	<script type="text/javascript">window.jQuery || document.write(unescape(\'%3Cscript src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"%3E%3C/script%3E\'))</script>
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

	echo '
	<br class="clear" />';
}

function template_metatags()
{
	global $context, $txt, $modSettings;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_meta_title'], '</h3>
		</div>
		<p class="description centertext">', $txt['optimus_meta_info'], '</p>
		<div class="flow_hidden">
			<table class="table_grid metatags centertext" style="width: 100%">
				<thead>
					<tr class="catbg">
						<th class="first_th" scope="col">', $txt['optimus_meta_tools'], '</th>
						<th scope="col">', $txt['optimus_meta_name'], '</th>
						<th class="last_th" scope="col">', $txt['optimus_meta_content'], '</th>
					</tr>
				</thead>
				<tbody>';

	$meta = !empty($modSettings['optimus_meta']) ? unserialize($modSettings['optimus_meta']) : '';
	$engines = array();

	foreach ($txt['optimus_search_engines'] as $engine => $data) {
		$engines[] = $data[0];

		echo '
					<tr class="windowbg">
						<td>', $engine, ' (<strong>', $data[1], '</strong>)</td>
						<td>
							<input type="text" name="custom_tag_name[]" size="24" value="', $data[0], '" />
						</td>
						<td>
							<input type="text" name="custom_tag_value[]" size="40" value="', isset($meta[$data[0]]) ? $meta[$data[0]] : '', '" />
						</td>
					</tr>';
	}

	if (!empty($meta)) {
		foreach ($meta as $name => $value) {
			if (!in_array($name, $engines)) {
				echo '
					<tr class="windowbg">
						<td>', $txt['optimus_meta_customtag'], '</td>
						<td>
							<input type="text" name="custom_tag_name[]" size="24" value="', $name, '" />
						</td>
						<td>
							<input type="text" name="custom_tag_value[]" size="40" value="', $value, '" />
						</td>
					</tr>';
			}
		}
	}

	echo '
				</tbody>
			</table>
			<span class="upperframe"><span></span></span>
			<div class="roundframe">
				<noscript>
					<div><input type="text" name="custom_tag_name[]" size="24" class="input_text" /> => <input type="text" name="custom_tag_value[]" size="40" class="input_text" /></div>
				</noscript>
				<div id="moreTags"></div>
				<div class="centertext" style="display: none;" id="newtag_link">
					<a href="#" onclick="addNewTag(); return false;">', $txt['optimus_meta_addtag'], '</a>
				</div>
				<script type="text/javascript"><!-- // --><![CDATA[
					document.getElementById("newtag_link").style.display = "";
					function addNewTag() {
						setOuterHTML(document.getElementById("moreTags"), \'<div class="centertext" style="margin-bottom: 1px"><input type="text" name="custom_tag_name[]" size="24" class="input_text" /> => <input type="text" name="custom_tag_value[]" size="40" class="input_text" /><\' + \'/div><div id="moreTags"><\' + \'/div>\');
					}
				// ]]></script>
				<hr class="hrcolor clear" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
			</div>
			<span class="lowerframe"><span></span></span>
		</div>
	</form>
	<br class="clear" />';
}

function template_counters()
{
	global $context, $txt, $modSettings;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_counters'], '</h3>
		</div>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<div class="content">
				<label for="optimus_head_code">', $txt['optimus_head_code'], '</label><br />
				<textarea id="optimus_head_code" name="optimus_head_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_head_code']) ? $modSettings['optimus_head_code'] : '', '</textarea>
				<br /><br />
				<label for="optimus_stat_code">', $txt['optimus_stat_code'], '</label><br />
				<textarea id="optimus_stat_code" name="optimus_stat_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_stat_code']) ? $modSettings['optimus_stat_code'] : '', '</textarea>
				<br /><br />
				<label for="optimus_count_code">', $txt['optimus_count_code'], '</label><br />
				<textarea id="optimus_count_code" name="optimus_count_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_count_code']) ? $modSettings['optimus_count_code'] : '', '</textarea>
				<br /><br />
				<label for="optimus_counters_css">', $txt['optimus_counters_css'], '</label><br />
				<textarea id="optimus_counters_css" name="optimus_counters_css" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_counters_css']) ? $modSettings['optimus_counters_css'] : '', '</textarea>
				<br /><br />
				<label for="optimus_ignored_actions">', $txt['optimus_ignored_actions'], '</label><br/>
				<input type="text" class="input_text" value="', !empty($modSettings['optimus_ignored_actions']) ? $modSettings['optimus_ignored_actions'] : '', '" id="optimus_ignored_actions" name="optimus_ignored_actions" style="width: 99%" />
				<hr class="hrcolor clear" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
			</div>
			<span class="botslice"><span></span></span>
		</div>
	</form>
	<br class="clear" />';
}

function template_robots()
{
	global $context, $txt, $modSettings;

	echo '
	<form id="robots_area_form" action="', $context['post_url'], '" method="post">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_manage'], '</h3>
		</div>
		<div class="windowbg">
			<span class="topslice"><span></span></span>
			<div class="content">
				<dl class="settings">
					<dt>
						<span><label for="optimus_root_path">', $txt['optimus_root_path'], '</label></span>
					</dt>
					<dd>
						<input name="optimus_root_path" id="optimus_root_path" value="', !empty($modSettings['optimus_root_path']) ? $modSettings['optimus_root_path'] : '', '" class="input_text" type="text" size="60" />
					</dd>
				</dl>
			</div>
			<span class="botslice"><span></span></span>
		</div>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
			<div class="content">
				<div class="modblock_left">
					<h4>', $txt['optimus_rules'], '</h4>
					<span class="smalltext">', $txt['optimus_rules_hint'], '</span>
					', $context['new_robots_content'], '
					<span class="smalltext">', $txt['optimus_useful'], '</span>
				</div>
				<div class="modblock_right">
					<h4><a href="/robots.txt">robots.txt</a></h4>
					<textarea cols="70" rows="22" name="robots">', $context['robots_content'], '</textarea>
				</div>
				<hr class="hrcolor clear" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
			</div>
			<span class="botslice"><span></span></span>
		</div>
	</form>
	<br class="clear" />';
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
			<span class="topslice"><span></span></span>
			<div class="content">', $txt['optimus_404_h3'], '</div>
			<span class="botslice"><span></span></span>
		</div>
	</div>';
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
			<span class="topslice"><span></span></span>
			<div class="content">', $txt['optimus_403_h3'], '</div>
			<span class="botslice"><span></span></span>
		</div>
	</div>';
}

function template_sitemap_xml()
{
	global $boardurl, $context;

	echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="', $boardurl, '/Themes/default/css/optimus/sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	foreach ($context['sitemap']['items'] as $item)
		echo '
	<url>
		<loc>', $item['loc'], '</loc>
		<lastmod>', $item['lastmod'], '</lastmod>
		<changefreq>', $item['changefreq'], '</changefreq>
		<priority>', $item['priority'], '</priority>
	</url>';

	echo '
</urlset>';
}

function template_sitemapindex_xml()
{
	global $boardurl, $context;

	echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="', $boardurl, '/Themes/default/css/optimus/sitemap.xsl"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	foreach ($context['sitemap']['items'] as $item)
		echo '
	<sitemap>
		<loc>', $item['loc'], '</loc>
	</sitemap>';

	echo '
</sitemapindex>';
}
