<?php

function template_favicon(): void
{
	global $txt, $context, $modSettings;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">', $txt['optimus_favicon_title'], '</h3>
	</div>
	<div class="optimus windowbg noup">
		<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
			<div class="title_bar centertext">
				<label for="optimus_favicon_text">', $txt['optimus_favicon_text'], '</label>
			</div>
			<div class="information centertext">
				<td>', $txt['optimus_favicon_help'], '</td>
			</div>
			<div class="descbox">
				<textarea rows="5" name="optimus_favicon_text" id="optimus_favicon_text">', empty($modSettings['optimus_favicon_text']) ? '' : $modSettings['optimus_favicon_text'], '</textarea>
			</div>
			<div class="windowbg" id="op_settings_footer">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
				<input type="submit" class="button" value="', $txt['save'], '">
			</div>
		</form>
	</div>';
}

function template_metatags(): void
{
	global $context, $txt;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_meta_title'], '</h3>
		</div>
		<div class="information centertext">', $txt['optimus_meta_info'], '</div>
		<div class="windowbg">
			<table class="table_grid metatags centertext">
				<thead>
					<tr class="title_bar">
						<th>', $txt['optimus_meta_tools'], '</th>
						<th>', $txt['optimus_meta_name'], '</th>
						<th>', $txt['optimus_meta_content'], '</th>
					</tr>
				</thead>
				<tbody>';

	$engines  = [];

	foreach ($txt['optimus_search_engines'] as $engine => $data) {
		$engines[] = $data[0];

		echo '
					<tr class="windowbg">
						<td>', $engine, ' (<strong><a class="bbc_link" href="', $data[1], '" target="_blank" rel="noopener">', $data[2], '</a></strong>)</td>
						<td>
							<input type="text" name="custom_tag_name[]" size="24" value="', $data[0], '">
						</td>
						<td>
							<input type="text" name="custom_tag_value[]" size="40" value="', $context['optimus_metatags_rules'][$data[0]] ?? '', '">
						</td>
					</tr>';
	}

	foreach ($context['optimus_metatags_rules'] as $name => $value) {
		if (! in_array($name, $engines)) {
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

	echo /** @lang text */ '
				</tbody>
			</table>
		</div>
		<div class="windowbg centertext">
			<noscript>
				<div style="margin-top: 1ex">
					<input type="text" name="custom_tag_name[]" size="24" class="input_text"> => <input type="text" name="custom_tag_value[]" size="40" class="input_text">
				</div>
			</noscript>
			<div id="moreTags"></div>
			<div style="margin-top: 1ex; display: none" id="newtag_link">
				<a href="#" onclick="addNewTag(); return false;" class="bbc_link">', $txt['optimus_meta_addtag'], '</a>
			</div>
			<script>
				document.getElementById("newtag_link").style.display = "";
				function addNewTag() {
					setOuterHTML(document.getElementById("moreTags"), \'<div style="margin-top: 1ex"><input type="text" name="custom_tag_name[]" size="24" class="input_text"> => <input type="text" name="custom_tag_value[]" size="40" class="input_text"><\' + \'/div><div id="moreTags"><\' + \'/div>\');
				}
			</script>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', $txt['save'], '">
		</div>
	</form>';
}

function template_redirect(): void
{
	global $context, $txt;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_redirect_title'], '</h3>
		</div>
		<div class="information centertext">', $txt['optimus_redirect_info'], '</div>';

	if (! empty($context['optimus_redirect_rules'])) {
		echo '
		<div class="windowbg">
			<table class="table_grid centertext">
				<thead>
					<tr class="title_bar">
						<th>', $txt['optimus_redirect_from'], '</th>
						<th>', $txt['optimus_redirect_to'], '</th>
					</tr>
				</thead>
				<tbody>';

		foreach ($context['optimus_redirect_rules'] as $from => $to) {
			echo '
					<tr class="windowbg">
						<td>
							<input type="text" name="custom_redirect_from[]" value="', $from, '">
						</td>
						<td>
							<input type="text" name="custom_redirect_to[]" value="', $to, '">
						</td>
					</tr>';
		}

		echo '
				</tbody>
			</table>
		</div>';
	}

	echo /** @lang text */ '
		<div class="windowbg centertext">
			<noscript>
				<div style="margin-top: 1ex">
					<input type="text" name="custom_redirect_from[]" placeholder="action=mlist" size="40" class="input_text"> => <input type="text" name="custom_redirect_to[]" placeholder="action=help" size="40" class="input_text">
				</div>
			</noscript>
			<div id="moreRedirects"></div>
			<div style="margin-top: 1ex; display: none" id="new_redirect_link">
				<a href="#" onclick="addNewRedirect(); return false;" class="bbc_link">', $txt['optimus_add_redirect'], '</a>
			</div>
			<script>
				document.getElementById("new_redirect_link").style.display = "";
				function addNewRedirect() {
					setOuterHTML(document.getElementById("moreRedirects"), \'<div style="margin-top: 1ex"><input type="text" name="custom_redirect_from[]" placeholder="action=mlist" size="40" class="input_text"> => <input type="text" name="custom_redirect_to[]" placeholder="action=help" size="40" class="input_text"><\' + \'/div><div id="moreRedirects"><\' + \'/div>\');
				}
			</script>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', $txt['save'], '">
		</div>
	</form>';
}

function template_counters(): void
{
	global $context, $txt, $modSettings;

	echo '
	<form class="optimus" action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_counters'], '</h3>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_head_code">', $txt['optimus_head_code'], '</label>
		</div>
		<div class="information centertext">
			<td>', $txt['optimus_head_code_subtext'], '</td>
		</div>
		<div class="descbox">
			<textarea id="optimus_head_code" name="optimus_head_code" rows="6" placeholder="<script>/* ', $txt['code'], ' */</script>">', $modSettings['optimus_head_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_stat_code">', $txt['optimus_stat_code'], '</label>
		</div>
		<div class="information centertext">
			<td>', $txt['optimus_stat_code_subtext'], '</td>
		</div>
		<div class="descbox">
			<textarea id="optimus_stat_code" name="optimus_stat_code" rows="6" placeholder="<script>/* ', $txt['code'], ' */</script>">', $modSettings['optimus_stat_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_count_code">', $txt['optimus_count_code'], '</label>
		</div>
		<div class="descbox">
			<textarea id="optimus_count_code" name="optimus_count_code" rows="6" placeholder="<script>/* ', $txt['code'], ' */</script>">', $modSettings['optimus_count_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_counters_css">', $txt['optimus_counters_css'], '</label>
		</div>
		<div class="descbox">
			<textarea id="optimus_counters_css" name="optimus_counters_css" rows="6">', $modSettings['optimus_counters_css'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_ignored_actions">', $txt['optimus_ignored_actions'], '</label>
		</div>
		<div class="information centertext">
			<td>', $txt['optimus_ignored_actions_subtext'], '</td>
		</div>
		<div class="errorbox">
			<input id="optimus_ignored_actions" name="optimus_ignored_actions" value="', $modSettings['optimus_ignored_actions'] ?? '', '" style="width: 100%">
		</div>
		<div class="windowbg" id="op_settings_footer">
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', $txt['save'], '">
		</div>
	</form>';
}

function template_robots(): void
{
	global $context, $txt;

	echo '
	<form action="', $context['post_url'], '" method="post">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_robots_title'], '</h3>
		</div>
		<div class="optimus roundframe">
			<div class="half_content">
				<div class="title_bar">
					<h4 class="titlebg">', $txt['optimus_rules'], '</h4>
				</div>
				<div class="inner">
					<span class="smalltext">', $txt['optimus_rules_hint'], '</span>
					', $context['new_robots_content'], '
				</div>
				<div class="title_bar">
					<h4 class="titlebg">', $txt['optimus_links_title'], '</h4>
				</div>
				<div class="inner">
					<ul class="bbc_list">';

	foreach ($txt['optimus_links'] as $link) {
		echo '
							<li><a href="', $link[1], '" target="_blank">', $link[0], '</a></li>';
	}

	echo '
					</ul>
				</div>
			</div>
			<div class="half_content">
				<div class="title_bar">
					<h4 class="titlebg"><a href="/robots.txt">robots.txt</a></h4>
				</div>
				<div class="inner">
					<textarea rows="18" id="optimus_robots" name="optimus_robots">', $context['robots_content'], '</textarea>
				</div>
			</div>
			<hr>
			<div id="op_settings_footer">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
				<input type="submit" class="button" value="', $txt['save'], '">
			</div>
		</div>
	</form>';
}

function template_htaccess(): void
{
	global $txt, $context;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">', $txt['optimus_htaccess_title'], '</h3>
	</div>
	<div class="optimus windowbg noup">
		<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
			<div class="descbox">
				<textarea rows="10" name="optimus_htaccess" id="optimus_htaccess">', $context['htaccess_content'], '</textarea>
			</div>
			<div class="windowbg" id="op_settings_footer">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				<input type="hidden" name="', $context['admin-dbsc_token_var'], '" value="', $context['admin-dbsc_token'], '">
				<input type="submit" class="button" value="', $txt['save'], '">
			</div>
		</form>
	</div>';
}

function template_footer_counters_above()
{
}

function template_footer_counters_below(): void
{
	global $modSettings;

	if (!empty($modSettings['optimus_count_code']))
		echo '
	<div class="counters">', $modSettings['optimus_count_code'], '</div>';
}

function template_sitemap_xml(): void
{
	global $modSettings, $scripturl, $context;

	$imageNamespace = empty($modSettings['optimus_sitemap_add_found_images']) ? '' : ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';

	echo /** @lang text */ '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="' . $scripturl . '?action=sitemap_xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . $imageNamespace . '>';

	foreach ($context['sitemap'] as $item) {
		echo '
	<url>
		<loc>', $item['loc'], '</loc>';

		if (!empty($item['lastmod']))
			echo '
		<lastmod>', $item['lastmod'], '</lastmod>';

		if (!empty($item['changefreq']))
			echo '
		<changefreq>', $item['changefreq'], '</changefreq>';

		if (!empty($item['priority']))
			echo '
		<priority>', $item['priority'], '</priority>';

		if (!empty($item['image'])) {
			echo '
		<image:image>
			<image:loc>' . $item['image']['loc'] . '</image:loc>
			<image:title>' . $item['image']['title'] . '</image:title>
		</image:image>';
		}

		echo '
	</url>';
	}

	echo '
</urlset>';
}

function template_sitemapindex_xml(): void
{
	global $scripturl, $context;

	echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="' . $scripturl . '?action=sitemap_xsl"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	foreach ($context['sitemap'] as $item)
		echo '
	<sitemap>
		<loc>', $item['loc'], '</loc>
	</sitemap>';

	echo '
</sitemapindex>';
}

function template_keywords_above(): void
{
	global $context;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			<span class="main_icons optimus">' . $context['page_title'] . '</span>
		</h3>
	</div>';
}

function template_keywords_below()
{
}

function template_search_terms_above(): void
{
	global $txt, $context, $scripturl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">', $txt['optimus_top_queries'], '</h3>
	</div>';

	if (!empty($context['search_terms'])) {
		echo '
	<div class="windowbg noup">';

		$i = 0;
		$rows = '';
		foreach ($context['search_terms'] as $data) {
			if ($data['hit'] > 10) {
				$i++;
				$rows .= '["' . $data['text'] . '",' . $data['hit'] . '],';
			}
		}

		if (!empty($rows)) {
			echo /** @lang text */ '
		<script src="https://www.gstatic.com/charts/loader.js"></script>
		<script>
			google.charts.load(\'current\', {\'packages\':[\'corechart\']});
			google.charts.setOnLoadCallback(drawChart);
			function drawChart() {
				let data = new google.visualization.DataTable();
				data.addColumn("string", "Query");
				data.addColumn("number", "Hits");
				data.addRows([', $rows, /** @lang text */ ']);
				let options = {"title":"' . sprintf($txt['optimus_chart_title'], $i) . '", "backgroundColor":"transparent", "width":"800"};
				let chart = new google.visualization.PieChart(document.getElementById("chart_div"));
				chart.draw(data, options);
			}
		</script>
		<div id="chart_div" class="centertext"></div>';
		}

		echo '
		<dl class="stats">';

		foreach ($context['search_terms'] as $data) {
			if (!empty($data['text'])) {
				echo '
			<dt>
				<a href="', $scripturl, '?action=search2;search=', urlencode($data['text']), '">', $data['text'], '</a>
			</dt>
			<dd class="statsbar generic_bar righttext">
				<div class="bar" style="width: ', $data['scale'], '%"></div>
				<span>', $data['hit'], '</span>
			</dd>';
			}
		}

		echo '
		</dl>
	</div>';
	} else {
		echo '
	<div class="information">', $txt['optimus_no_search_terms'], '</div>';
	}
}

function template_search_terms_below()
{
}
