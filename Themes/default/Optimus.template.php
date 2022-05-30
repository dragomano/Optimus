<?php

function template_favicon()
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
		</form>';

	if (empty($modSettings['optimus_disable_syntax_highlighting']))
		echo '
		<script>
			let cmTextarea = new CodeMirror.fromTextArea(document.getElementById("optimus_favicon_text"), {
				lineNumbers: true,
				mode: "text/html",
				firstLineNumber: 1,
				lineWrapping: true,
				direction: "' . ($context['right_to_left'] ? 'rtl' : 'ltr') . '",
				styleActiveLine: true,
				matchBrackets: true,
				scrollbarStyle: "simple"
			});
		</script>';

	echo '
	</div>';

	show_cm_switcher();
}

function template_metatags()
{
	global $context, $txt, $modSettings;

	echo '
	<form action="', $context['post_url'], '" method="post" accept-charset="', $context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', $txt['optimus_meta_title'], '</h3>
		</div>
		<div class="information centertext">', $txt['optimus_meta_info'], '</div>
		<table class="table_grid metatags centertext">
			<thead>
				<tr class="title_bar">
					<th>', $txt['optimus_meta_tools'], '</th>
					<th>', $txt['optimus_meta_name'], '</th>
					<th>', $txt['optimus_meta_content'], '</th>
				</tr>
			</thead>
			<tbody>';

	$metatags = !empty($modSettings['optimus_meta']) ? unserialize($modSettings['optimus_meta']) : '';
	$engines  = array();

	foreach ($txt['optimus_search_engines'] as $engine => $data) {
		$engines[] = $data[0];

		echo '
				<tr class="windowbg">
					<td>', $engine, ' (<strong><a class="bbc_link" href="', $data[1], '" target="_blank" rel="noopener">', $data[2], '</a></strong>)</td>
					<td>
						<input type="text" name="custom_tag_name[]" size="24" value="', $data[0], '">
					</td>
					<td>
						<input type="text" name="custom_tag_value[]" size="40" value="', $metatags[$data[0]] ?? '', '">
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
			</tbody>
		</table>
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

function template_counters()
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
			<textarea id="optimus_head_code" name="optimus_head_code" rows="6">', $modSettings['optimus_head_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_stat_code">', $txt['optimus_stat_code'], '</label>
		</div>
		<div class="information centertext">
			<td>', $txt['optimus_stat_code_subtext'], '</td>
		</div>
		<div class="descbox">
			<textarea id="optimus_stat_code" name="optimus_stat_code" rows="6">', $modSettings['optimus_stat_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_count_code">', $txt['optimus_count_code'], '</label>
		</div>
		<div class="descbox">
			<textarea id="optimus_count_code" name="optimus_count_code" rows="6">', $modSettings['optimus_count_code'] ?? '', '</textarea>
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

	if (empty($modSettings['optimus_disable_syntax_highlighting']))
		echo '
	<script>
		const textareas = ["optimus_head_code", "optimus_stat_code", "optimus_count_code", "optimus_counters_css"];
		let cmArea = {};
		textareas.forEach(function (el, i) {
			cmArea[`obj${i}`] = CodeMirror.fromTextArea(document.getElementById(el), {
				lineNumbers: true,
				mode: el === "optimus_counters_css" ? "text/css" : "text/html",
				firstLineNumber: 1,
				lineWrapping: true,
				direction: "' . ($context['right_to_left'] ? 'rtl' : 'ltr') . '",
				styleActiveLine: true,
				matchBrackets: true,
				scrollbarStyle: "simple"
			})
		});
	</script>';

	show_cm_switcher();
}

function template_robots()
{
	global $context, $txt, $modSettings;

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

	if (empty($modSettings['optimus_disable_syntax_highlighting']))
		echo '
	<script>
		let cmTextarea = new CodeMirror.fromTextArea(document.getElementById("optimus_robots"), {
			lineNumbers: true,
			mode: "message/http",
			firstLineNumber: 1,
			lineWrapping: true,
			direction: "' . ($context['right_to_left'] ? 'rtl' : 'ltr') . '",
			styleActiveLine: true,
			matchBrackets: true,
			scrollbarStyle: "simple"
		});
	</script>';

	show_cm_switcher();
}

function template_htaccess()
{
	global $txt, $context, $modSettings;

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
		</form>';

	if (empty($modSettings['optimus_disable_syntax_highlighting']))
		echo '
		<script>
			let cmTextarea = new CodeMirror.fromTextArea(document.getElementById("optimus_htaccess"), {
				lineNumbers: true,
				mode: "apache",
				firstLineNumber: 1,
				lineWrapping: true,
				direction: "' . ($context['right_to_left'] ? 'rtl' : 'ltr') . '",
				styleActiveLine: true,
				matchBrackets: true,
				scrollbarStyle: "simple"
			});
		</script>';

	echo '
	</div>';

	show_cm_switcher();
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

function template_sitemap_xml()
{
	global $scripturl, $context;

	echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="' . $scripturl . '?action=sitemap_xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

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

function template_sitemapindex_xml()
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

function template_keywords_above()
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

function template_search_terms_above()
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
		foreach ($context['search_terms'] as $id => $data) {
			if ($data['hit'] > 10) {
				$i++;
				$rows .= '["' . $data['text'] . '",' . $data['hit'] . '],';
			}
		}

		if (!empty($rows)) {
			echo '
		<script src="https://www.gstatic.com/charts/loader.js"></script>
		<script>
			google.charts.load(\'current\', {\'packages\':[\'corechart\']});
			google.charts.setOnLoadCallback(drawChart);
			function drawChart() {
				let data = new google.visualization.DataTable();
				data.addColumn("string", "Query");
				data.addColumn("number", "Hits");
				data.addRows([', $rows, ']);
				let options = {"title":"' . sprintf($txt['optimus_chart_title'], $i) . '", "backgroundColor":"transparent", "width":"800"};
				let chart = new google.visualization.PieChart(document.getElementById("chart_div"));
				chart.draw(data, options);
			}
		</script>
		<div id="chart_div" class="centertext"></div>';
		}

		echo '
		<dl class="stats">';

		foreach ($context['search_terms'] as $id => $data) {
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

function show_cm_switcher()
{
	global $modSettings, $txt;

	if (! empty($modSettings['optimus_disable_syntax_highlighting']))
		return;

	echo '
	<script>
		document.querySelector("#op_settings_footer").insertAdjacentHTML("beforeEnd", \'<span><label>' . $txt['theme'] . '</label> <select id="cmThemeChanger"><option value="3024-day">3024 Day</option><option value="3024-night">3024 Night</option><option value="abcdef">Abcdef</option><option value="ambiance">Ambiance</option><option value="base16-dark">Base16 Dark</option><option value="base16-light">Base16 Light</option><option value="bespin">Bespin</option><option value="blackboard">Blackboard</option><option value="cobalt">Cobalt</option><option value="default">Default</option><option value="colorforth">Colorforth</option><option value="darcula">Darcula</option><option value="dracula">Dracula</option><option value="duotone-dark">Duotone Dark</option><option value="duotone-light">Duotone Light</option><option value="eclipse">Eclipse</option><option value="elegant">Elegant</option><option value="erlang-dark">Erlang Dark</option><option value="gruvbox-dark">Gruvbox Dark</option><option value="hopscotch">Hopscotch</option><option value="icecoder">Icecoder</option><option value="idea">Idea</option><option value="isotope">Isotope</option><option value="lesser-dark">Lesser Dark</option><option value="liquibyte">Liquibyte</option><option value="lucario">Lucario</option><option value="material">Material</option><option value="mbo">Mbo</option><option value="mdn-like">Mdn Like</option><option value="midnight">Midnight</option><option value="monokai">Monokai</option><option value="neat">Neat</option><option value="neo">Neo</option><option value="night">Night</option><option value="nord">Nord</option><option value="oceanic-next">Oceanic Next</option><option value="panda-syntax">Panda Syntax</option><option value="paraiso-dark">Paraiso Dark</option><option value="paraiso-light">Paraiso Light</option><option value="pastel-on-dark">Pastel On Dark</option><option value="railscasts">Railscasts</option><option value="rubyblue">Rubyblue</option><option value="seti">Seti</option><option value="shadowfox">Shadowfox</option><option value="solarized">Solarized</option><option value="ssms">Ssms</option><option value="the-matrix">The Matrix</option><option value="tomorrow-night-bright">Tomorrow Night Bright</option><option value="tomorrow-night-eighties">Tomorrow Night Eighties</option><option value="ttcn">Ttcn</option><option value="twilight">Twilight</option><option value="vibrant-ink">Vibrant Ink</option><option value="xq-dark">Xq Dark</option><option value="xq-light">Xq Light</option><option value="yeti">Yeti</option><option value="yonce">Yonce</option><option value="zenburn">Zenburn</option></select></span>\');
		let data = localStorage.getItem("cmTheme"),
			themeChanger = document.getElementById("cmThemeChanger");
		if (data !== null) {
			themeChanger.value = data;
			if (typeof cmTextarea !== "undefined") {
				cmTextarea.setOption("theme", data);
			} else if (typeof cmArea !== "undefined") {
				Object.values(cmArea).forEach(el => el.setOption("theme", data));
			}
		} else {
			themeChanger.querySelector(\'option[value="default"]\').selected = true;
		}
		themeChanger.addEventListener("change", function () {
			if (typeof cmTextarea !== "undefined") {
				cmTextarea.setOption("theme", this.value);
			} else if (typeof cmArea !== "undefined") {
				Object.values(cmArea).forEach(el => el.setOption("theme", this.value));
			}
			localStorage.setItem("cmTheme", this.value);
		});
	</script>';
}
