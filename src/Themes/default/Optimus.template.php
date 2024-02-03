<?php

use Bugo\Compat\{Config, Lang, Utils};

function template_favicon(): void
{
	echo '
	<div class="cat_bar">
		<h3 class="catbg">', Lang::$txt['optimus_favicon_title'], '</h3>
	</div>
	<div class="optimus windowbg noup">
		<form action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], '">
			<div class="title_bar centertext">
				<label for="optimus_favicon_text">', Lang::$txt['optimus_favicon_text'], '</label>
			</div>
			<div class="information centertext">
				<td>', Lang::$txt['optimus_favicon_help'], '</td>
			</div>
			<div class="descbox">
				<textarea rows="5" name="optimus_favicon_text" id="optimus_favicon_text">', empty(Config::$modSettings['optimus_favicon_text']) ? '' : Config::$modSettings['optimus_favicon_text'], '</textarea>
			</div>
			<div class="windowbg" id="op_settings_footer">
				<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
				<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
				<input type="submit" class="button" value="', Lang::$txt['save'], '">
			</div>
		</form>
	</div>';
}

function template_metatags(): void
{
	echo '
	<form action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', Lang::$txt['optimus_meta_title'], '</h3>
		</div>
		<div class="information centertext">', Lang::$txt['optimus_meta_info'], '</div>
		<div class="windowbg">
			<table class="table_grid metatags centertext">
				<thead>
					<tr class="title_bar">
						<th>', Lang::$txt['optimus_meta_tools'], '</th>
						<th>', Lang::$txt['optimus_meta_name'], '</th>
						<th>', Lang::$txt['optimus_meta_content'], '</th>
					</tr>
				</thead>
				<tbody>';

	$engines  = [];

	foreach (Lang::$txt['optimus_search_engines'] as $engine => $data) {
		$engines[] = $data[0];

		echo '
					<tr class="windowbg">
						<td>', $engine, ' (<strong><a class="bbc_link" href="', $data[1], '" target="_blank" rel="noopener">', $data[2], '</a></strong>)</td>
						<td>
							<input type="text" name="custom_tag_name[]" size="24" value="', $data[0], '">
						</td>
						<td>
							<input type="text" name="custom_tag_value[]" size="40" value="', Utils::$context['optimus_metatags_rules'][$data[0]] ?? '', '">
						</td>
					</tr>';
	}

	foreach (Utils::$context['optimus_metatags_rules'] as $name => $value) {
		if (! in_array($name, $engines)) {
			echo '
					<tr class="windowbg">
						<td>', Lang::$txt['optimus_meta_customtag'], '</td>
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
				<a href="#" onclick="addNewTag(); return false;" class="bbc_link">', Lang::$txt['optimus_meta_addtag'], /** @lang text */ '</a>
			</div>
			<script>
				document.getElementById("newtag_link").style.display = "";
				function addNewTag() {
					setOuterHTML(document.getElementById("moreTags"), \'<div style="margin-top: 1ex"><input type="text" name="custom_tag_name[]" size="24" class="input_text"> => <input type="text" name="custom_tag_value[]" size="40" class="input_text"><\' + \'/div><div id="moreTags"><\' + \'/div>\');
				}
			</script>
			<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
			<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', Lang::$txt['save'], '">
		</div>
	</form>';
}

function template_redirect(): void
{
	echo '
	<form action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], /** @lang text */ '">
		<div class="cat_bar">
			<h3 class="catbg">', Lang::$txt['optimus_redirect_title'], /** @lang text */ '</h3>
		</div>
		<div class="information centertext">', Lang::$txt['optimus_redirect_info'], '</div>';

	if (! empty(Utils::$context['optimus_redirect_rules'])) {
		echo /** @lang text */ '
		<div class="windowbg">
			<table class="table_grid centertext">
				<thead>
					<tr class="title_bar">
						<th>', Lang::$txt['optimus_redirect_from'], '</th>
						<th>', Lang::$txt['optimus_redirect_to'], '</th>
					</tr>
				</thead>
				<tbody>';

		foreach (Utils::$context['optimus_redirect_rules'] as $from => $to) {
			echo /** @lang text */ '
					<tr class="windowbg">
						<td>
							<input type="text" name="custom_redirect_from[]" value="', $from, '">
						</td>
						<td>
							<input type="text" name="custom_redirect_to[]" value="', $to, '">
						</td>
					</tr>';
		}

		echo /** @lang text */ '
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
				<a href="#" onclick="addNewRedirect(); return false;" class="bbc_link">', Lang::$txt['optimus_add_redirect'], /** @lang text */ '</a>
			</div>
			<script>
				document.getElementById("new_redirect_link").style.display = "";
				function addNewRedirect() {
					setOuterHTML(document.getElementById("moreRedirects"), \'<div style="margin-top: 1ex"><input type="text" name="custom_redirect_from[]" placeholder="action=mlist" size="40" class="input_text"> => <input type="text" name="custom_redirect_to[]" placeholder="action=help" size="40" class="input_text"><\' + \'/div><div id="moreRedirects"><\' + \'/div>\');
				}
			</script>
			<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
			<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', Lang::$txt['save'], '">
		</div>
	</form>';
}

function template_counters(): void
{
	echo '
	<form class="optimus" action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], /** @lang text */ '">
		<div class="cat_bar">
			<h3 class="catbg">', Lang::$txt['optimus_counters'], '</h3>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_head_code">', Lang::$txt['optimus_head_code'], '</label>
		</div>
		<div class="information centertext">
			<td>', Lang::$txt['optimus_head_code_subtext'], '</td>
		</div>
		<div class="descbox">
			<textarea id="optimus_head_code" name="optimus_head_code" rows="6" placeholder="<script>/* ', Lang::$txt['code'], ' */</script>">', Config::$modSettings['optimus_head_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_stat_code">', Lang::$txt['optimus_stat_code'], '</label>
		</div>
		<div class="information centertext">
			<td>', Lang::$txt['optimus_stat_code_subtext'], '</td>
		</div>
		<div class="descbox">
			<textarea id="optimus_stat_code" name="optimus_stat_code" rows="6" placeholder="<script>/* ', Lang::$txt['code'], ' */</script>">', Config::$modSettings['optimus_stat_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_count_code">', Lang::$txt['optimus_count_code'], '</label>
		</div>
		<div class="descbox">
			<textarea id="optimus_count_code" name="optimus_count_code" rows="6" placeholder="<script>/* ', Lang::$txt['code'], ' */</script>">', Config::$modSettings['optimus_count_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_counters_css">', Lang::$txt['optimus_counters_css'], '</label>
		</div>
		<div class="descbox">
			<textarea id="optimus_counters_css" name="optimus_counters_css" rows="6">', Config::$modSettings['optimus_counters_css'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_ignored_actions">', Lang::$txt['optimus_ignored_actions'], '</label>
		</div>
		<div class="information centertext">
			<td>', Lang::$txt['optimus_ignored_actions_subtext'], '</td>
		</div>
		<div class="errorbox">
			<input id="optimus_ignored_actions" name="optimus_ignored_actions" value="', Config::$modSettings['optimus_ignored_actions'] ?? '', '" style="width: 100%">
		</div>
		<div class="windowbg" id="op_settings_footer">
			<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
			<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', Lang::$txt['save'], '">
		</div>
	</form>';
}

function template_robots(): void
{
	echo '
	<form action="', Utils::$context['post_url'], '" method="post">
		<div class="cat_bar">
			<h3 class="catbg">', Lang::$txt['optimus_robots_title'], '</h3>
		</div>
		<div class="optimus roundframe">
			<div class="half_content">
				<div class="title_bar">
					<h4 class="titlebg">', Lang::$txt['optimus_rules'], '</h4>
				</div>
				<div class="inner">
					<span class="smalltext">', Lang::$txt['optimus_rules_hint'], '</span>
					', Utils::$context['new_robots_content'], '
				</div>
				<div class="title_bar">
					<h4 class="titlebg">', Lang::$txt['optimus_links_title'], '</h4>
				</div>
				<div class="inner">
					<ul class="bbc_list">';

	foreach (Lang::$txt['optimus_links'] as $link) {
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
					<textarea rows="18" id="optimus_robots" name="optimus_robots">', Utils::$context['robots_content'], '</textarea>
				</div>
			</div>
			<hr>
			<div id="op_settings_footer">
				<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
				<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
				<input type="submit" class="button" value="', Lang::$txt['save'], '">
			</div>
		</div>
	</form>';
}

function template_htaccess(): void
{
	echo '
	<div class="cat_bar">
		<h3 class="catbg">', Lang::$txt['optimus_htaccess_title'], '</h3>
	</div>
	<div class="optimus windowbg noup">
		<form action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], '">
			<div class="descbox">
				<textarea rows="10" name="optimus_htaccess" id="optimus_htaccess">', Utils::$context['htaccess_content'], '</textarea>
			</div>
			<div class="windowbg" id="op_settings_footer">
				<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
				<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
				<input type="submit" class="button" value="', Lang::$txt['save'], '">
			</div>
		</form>
	</div>';
}

function template_footer_counters_above()
{
}

function template_footer_counters_below(): void
{
	if (! empty(Config::$modSettings['optimus_count_code']))
		echo '
	<div class="counters">', Config::$modSettings['optimus_count_code'], '</div>';
}

function template_sitemap_xml(): void
{
	$imageNamespace = empty(Config::$modSettings['optimus_sitemap_add_found_images']) ? '' : ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';

	echo /** @lang text */ '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="' . Config::$scripturl . '?action=sitemap_xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . $imageNamespace . '>';

	foreach (Utils::$context['sitemap'] as $item) {
		echo '
	<url>
		<loc>', $item['loc'], '</loc>';

		if ( empty($item['lastmod']))
			echo '
		<lastmod>', $item['lastmod'], '</lastmod>';

		if (! empty($item['changefreq']))
			echo '
		<changefreq>', $item['changefreq'], '</changefreq>';

		if (! empty($item['priority']))
			echo '
		<priority>', $item['priority'], '</priority>';

		if (! empty($item['image'])) {
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
	echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="' . Config::$scripturl . '?action=sitemap_xsl"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	foreach (Utils::$context['sitemap'] as $item)
		echo '
	<sitemap>
		<loc>', $item['loc'], '</loc>
	</sitemap>';

	echo '
</sitemapindex>';
}

function template_keywords_above(): void
{
	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			<span class="main_icons optimus">' . Utils::$context['page_title'] . '</span>
		</h3>
	</div>';
}

function template_keywords_below()
{
}

function template_search_terms_above(): void
{
	echo '
	<div class="cat_bar">
		<h3 class="catbg">', Lang::$txt['optimus_top_queries'], '</h3>
	</div>';

	if (! empty(Utils::$context['search_terms'])) {
		echo '
	<div class="windowbg noup">';

		$i = 0;
		$rows = '';
		foreach (Utils::$context['search_terms'] as $data) {
			if ($data['hit'] > 10) {
				$i++;
				$rows .= '["' . $data['text'] . '",' . $data['hit'] . '],';
			}
		}

		if (! empty($rows)) {
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
				let options = {"title":"' . sprintf(Lang::$txt['optimus_chart_title'], $i) . '", "backgroundColor":"transparent", "width":"800"};
				let chart = new google.visualization.PieChart(document.getElementById("chart_div"));
				chart.draw(data, options);
			}
		</script>
		<div id="chart_div" class="centertext"></div>';
		}

		echo '
		<dl class="stats">';

		foreach (Utils::$context['search_terms'] as $data) {
			if (! empty($data['text'])) {
				echo '
			<dt>
				<a href="', Config::$scripturl, '?action=search2;search=', urlencode($data['text']), '">', $data['text'], '</a>
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
	<div class="information">', Lang::$txt['optimus_no_search_terms'], '</div>';
	}
}

function template_search_terms_below()
{
}
