<?php

/**
 * Optimus.template.php
 *
 * @package Optimus
 * @link http://custom.simplemachines.org/mods/index.php?mod=2659
 * @author Bugo http://dragomano.ru/mods/optimus
 * @copyright 2010-2016 Bugo
 * @license http://opensource.org/licenses/artistic-license-2.0 Artistic-2.0
 *
 * @version 1.9
 */

function template_common()
{
	global $context, $txt, $smcFunc, $modSettings;

	echo '
	<div id="optimus">
		<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">

			<div class="cat_bar">
				<h3 class="catbg">', $txt['optimus_main_page'], '</h3>
			</div>
			<p class="description">', $txt['optimus_common_info'], '</p>

			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl class="settings">
						<dt>
							<span>
								<label for="optimus_portal_compat">', $txt['optimus_portal_compat'], '</label>
							</span>
						</dt>
						<dd>
							<select name="optimus_portal_compat" id="optimus_portal_compat">';

	$modSettings['optimus_portal_compat'] = !empty($modSettings['optimus_portal_compat']) ? $modSettings['optimus_portal_compat'] : 0;
	foreach ($txt['optimus_portal_compat_set'] as $val => $portal) {
		echo '
								<option value="', $val, '"', $modSettings['optimus_portal_compat'] == $val ? ' selected="selected"' : '', '>', $portal, '</option>';
	}

	echo '
							</select>
						</dd>';

	if (!empty($modSettings['optimus_portal_compat'])) {
		echo '
						<dt>
							<span>
								<label for="optimus_portal_index">', $txt['optimus_portal_index'], '</label>
							</span>
						</dt>
						<dd>
							<em>', $context['forum_name'], '</em> - <input type="text" class="input_text" value="', !empty($modSettings['optimus_portal_index']) ? $modSettings['optimus_portal_index'] : '', '" id="optimus_portal_index" name="optimus_portal_index" size="', !empty($modSettings['optimus_portal_index']) ? $smcFunc['strlen']($modSettings['optimus_portal_index']) + 4 : 16, '" />
						</dd>';
	}

	echo '
						<dt>
							<span>
								<label for="optimus_forum_index">', $txt['optimus_forum_index'], '</label>
							</span>
						</dt>
						<dd>
							<em>', $context['forum_name'], '</em> - <input type="text" class="input_text" value="', !empty($modSettings['optimus_forum_index']) ? $modSettings['optimus_forum_index'] : '', '" id="optimus_forum_index" name="optimus_forum_index" size="', !empty($modSettings['optimus_forum_index']) ? $smcFunc['strlen']($modSettings['optimus_forum_index']) + 4 : 16, '"/>
						</dd>
						<dt>
							<span>
								<label for="optimus_description">', $txt['optimus_description'], '</label>
							</span>
						</dt>
						<dd>
							<textarea id="optimus_description" name="optimus_description" cols="60" rows="2">', !empty($modSettings['optimus_description']) ? $modSettings['optimus_description'] : '', '</textarea> ', !empty($modSettings['optimus_description']) ? $smcFunc['strlen']($modSettings['optimus_description']) : '' ,'
						</dd>
					</dl>
				</div>
				<span class="botslice"><span></span></span>
			</div>

			<div class="cat_bar">
				<h3 class="catbg">', $txt['optimus_all_pages'], '</h3>
			</div>
			<p class="description">', $txt['optimus_tpl_info'], '</p>

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
							<input type="text" class="input_text" value="', isset($templates[$name]['name']) ? $templates[$name]['name'] : $template[0], '" name="', $name, '_name" size="', !empty($templates[$name]['name']) ? $smcFunc['strlen']($templates[$name]['name']) : 14, '" />&nbsp;
							<input type="text" class="input_text" value="', isset($templates[$name]['page']) ? $templates[$name]['page'] : $template[1], '" name="', $name, '_page" size="', !empty($templates[$name]['page']) ? $smcFunc['strlen']($templates[$name]['page']) : 14, '" />&nbsp;
							<input type="text" class="input_text" value="', isset($templates[$name]['site']) ? $templates[$name]['site'] : $template[2], '" name="', $name, '_site" size="', !empty($templates[$name]['site']) ? $smcFunc['strlen']($templates[$name]['site']) : 28, '" />
						</dd>';
	}

	echo '
					</dl>
					<dl class="settings">
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
	</div>
	<br class="clear" />';
}

function template_extra()
{
	global $context, $txt, $smcFunc, $modSettings;

	echo '
	<div id="optimus">
		<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">

			<div class="cat_bar">
				<h3 class="catbg">', $txt['optimus_extra_title'], '</h3>
			</div>

			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl class="settings">
						<dt>
							<span>
								<label for="optimus_remove_indexphp">', $txt['optimus_remove_indexphp'], '</label>
							</span>
						</dt>
						<dd>
							<input type="checkbox" name="optimus_remove_indexphp" id="optimus_remove_indexphp"', !empty($modSettings['optimus_remove_indexphp']) ? ' checked="checked"' : '', ' />
						</dd>
						<dt>
							<span>
								<label for="optimus_correct_prevnext">', $txt['optimus_correct_prevnext'], '</label>
							</span>
						</dt>
						<dd>
							<input type="checkbox" name="optimus_correct_prevnext" id="optimus_correct_prevnext"', !empty($modSettings['optimus_correct_prevnext']) ? ' checked="checked"' : '', ' />
						</dd>
						<dt>
							<span>
								<label for="optimus_open_graph">', $txt['optimus_open_graph'], '</label>
							</span>
						</dt>
						<dd>
							<input type="checkbox" name="optimus_open_graph" id="optimus_open_graph"', !empty($modSettings['optimus_open_graph']) ? ' checked="checked"' : '', ' />
						</dd>';
						
	if (!empty($modSettings['optimus_open_graph'])) {
		echo '
						<dt>
							<span>
								<label for="optimus_og_image">', $txt['optimus_og_image'], '</label>
							</span>
						</dt>
						<dd>
							<input type="text" class="input_text" value="', !empty($modSettings['optimus_og_image']) ? $modSettings['optimus_og_image'] : '', '" id="optimus_og_image" name="optimus_og_image" size="60" />
						</dd>';
	}
	
	echo '
					</dl>
					<hr class="hrcolor clear" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
				</div>
				<span class="botslice"><span></span></span>
			</div>

		</form>
	</div>
	<br class="clear" />';
}

function template_verification()
{
	global $context, $txt, $modSettings;

	echo '
	<div id="optimus">
		<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">

			<div class="cat_bar">
				<h3 class="catbg">', $txt['optimus_codes'], '</h3>
			</div>
			<p class="description">', $txt['optimus_meta_info'], '</p>

			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content centertext">
					<table>
						<tr>
							<th>', $txt['optimus_titles'], '</th>
							<th>', $txt['optimus_name'], '</th>
							<th>', $txt['optimus_content'], '</th>
						</tr>';

	$meta = !empty($modSettings['optimus_meta']) ? unserialize($modSettings['optimus_meta']) : '';
	foreach ($txt['optimus_search_engines'] as $engine => $data) {
		echo '
						<tr>
							<td>', $engine, ' (<strong>', $data[1], '</strong>)</td>
							<td>
								<input type="text" name="', $engine, '_name" size="24" value="', isset($meta[$engine]['name']) ? $meta[$engine]['name'] : $data[0], '" />
							</td>
							<td>
								<input type="text" name="', $engine, '_content" size="40" value="', isset($meta[$engine]['content']) ? $meta[$engine]['content'] : '', '" />
							</td>
						</tr>';
	}

	echo '
					</table>
					<hr class="hrcolor clear" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
				</div>
				<span class="botslice"><span></span></span>
			</div>

		</form>
	</div>
	<br class="clear" />';
}

function template_counters()
{
	global $context, $txt, $modSettings, $settings;

	echo '
	<div id="optimus">
		<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">

			<div class="cat_bar">
				<h3 class="catbg">', $txt['optimus_counters'], '</h3>
			</div>

			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<label for="optimus_head_code">', $txt['optimus_head_code'], '</label><br />
					<textarea id="optimus_head_code" name="optimus_head_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_head_code']) ? $modSettings['optimus_head_code'] : '', '</textarea>
					<div class="smalltext">', $txt['optimus_ga_note'], '</div>
					<br />
					<label for="optimus_stat_code">', $txt['optimus_stat_code'], '</label><br />
					<textarea id="optimus_stat_code" name="optimus_stat_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_stat_code']) ? $modSettings['optimus_stat_code'] : '', '</textarea>
					<br /><br />
					<label for="optimus_count_code">', $txt['optimus_count_code'], '</label><br />
					<textarea id="optimus_count_code" name="optimus_count_code" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_count_code']) ? $modSettings['optimus_count_code'] : '', '</textarea>
					<br /><br />
					<label for="optimus_count_code_css">', $txt['optimus_count_code_css'], '</label><br />
					<textarea id="optimus_count_code_css" name="optimus_count_code_css" cols="60" rows="4" style="width: 99%">', !empty($modSettings['optimus_count_code_css']) ? $modSettings['optimus_count_code_css'] : '', '</textarea>
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
	</div>
	<br class="clear" />';
}

function template_robots()
{
	global $context, $txt, $modSettings, $robots_path, $settings;

	echo '
	<div id="optimus">
		<form action="', $context['post_url'], '" method="post">

			<div class="cat_bar">
				<h3 class="catbg">', $txt['optimus_manage'], '</h3>
			</div>';

	echo '
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<div class="min">
						<div class="content">
							<h4>', $txt['optimus_rules'], '</h4>
							<span class="smalltext">', $txt['optimus_rules_hint'], '</span>
							', $context['new_robots_content'], '
							<span class="smalltext">', $txt['optimus_useful'], '</span>
						</div>
					</div>
					<div class="min">
						<div class="content">
							<h4>', file_exists($robots_path) ? '<a href="/robots.txt" target="_blank">robots.txt</a>' : 'robots.txt', '</h4>
							<textarea cols="70" rows="22" name="robots">', $context['robots_content'], '</textarea>
						</div>
					</div>
					<hr class="hrcolor clear" />
					<div class="min">
						<div class="content floatleft">
							<h4>', $txt['optimus_links_title'], '</h4>
							<ul>';

	foreach ($txt['optimus_links'] as $ankor => $url) {
		echo '
								<li><a href="', $url, '" target="_blank">', $ankor, '</a></li>';
	}

	echo '
							</ul>
						</div>
						', $txt['lang_dictionary'] == 'ru' ? '<img class="floatright" src="http://1ps.ru/identic/bonusfiles/course-seo-5.jpg" alt="" />' : '', '
					</div>
					<div class="min">
						<div class="content">
							', $txt['lang_dictionary'] == 'ru' ? $txt['optimus_1ps_ads'] : '', '
						</div>
					</div>

					<div class="clear"></div>
					<span class="botslice"><span></span></span>

					<hr class="hrcolor clear" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
				</div>
				<span class="botslice"><span></span></span>
			</div>

		</form>
	</div>
	<br class="clear" />';
}

function template_map()
{
	global $context, $txt, $modSettings, $boarddir, $boardurl;

	echo '
	<div id="optimus">
		<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">

			<div class="cat_bar">
				<h3 class="catbg">', $txt['optimus_sitemap_xml_link'], '</h3>
			</div>

			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="content">
					<dl class="settings">
						<dt>
							<span>
								<label for="optimus_sitemap_enable">', $txt['optimus_sitemap_enable'], ' (<span class="smalltext">', (file_exists($boarddir . "/sitemap.xml") ? '<strong><a href="' . $boardurl . '/sitemap.xml" target="_blank">sitemap.xml</a></strong>' : ''), '</span>)</label>
							</span>
						</dt>
						<dd>
							<input type="checkbox" name="optimus_sitemap_enable" id="optimus_sitemap_enable"', !empty($modSettings['optimus_sitemap_enable']) ? ' checked="checked"' : '', ' />
						</dd>
						<dt>
							<span>
								<label for="optimus_sitemap_link">', $txt['optimus_sitemap_link'], '</label>
							</span>
						</dt>
						<dd>
							<input type="checkbox" name="optimus_sitemap_link" id="optimus_sitemap_link"', !empty($modSettings['optimus_sitemap_link']) ? ' checked="checked"' : '', ' />
						</dd>
						<dt>
							<span>
								<label for="optimus_sitemap_topic_size">', $txt['optimus_sitemap_topic_size'], '</label>
							</span>
						</dt>
						<dd>
							<input type="text" class="input_text" value="', !empty($modSettings['optimus_sitemap_topic_size']) ? $modSettings['optimus_sitemap_topic_size'] : '', '" id="optimus_sitemap_topic_size" name="optimus_sitemap_topic_size" size="6"/>
						</dd>
					</dl>
					<hr class="hrcolor clear" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<div class="righttext"><input type="submit" class="button_submit" value="', $txt['save'], '" /></div>
				</div>
				<span class="botslice"><span></span></span>
			</div>

		</form>
	</div>
	<br class="clear" />';
}

function template_404()
{
	global $txt;

	echo '
		<div class="centertext" style="width: 50%">
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
		<div class="centertext" style="width: 50%">
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

?>