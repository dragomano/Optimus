<?php

use Bugo\Compat\{Config, Lang, Utils};
use Bugo\Optimus\Utils\Input;

function template_tips_above(): void
{
	$links = [
		'basic'    => 'https://developers.google.com/search/docs/fundamentals/seo-starter-guide?hl=',
		'extra'    => 'https://developers.facebook.com/docs/sharing/webmasters',
		'favicon'  => 'https://developers.google.com/search/docs/appearance/favicon-in-search?hl=',
		'metatags' => 'https://developers.google.com/search/docs/fundamentals/get-on-google?hl=',
		'robots'   => 'https://developers.google.com/search/docs/crawling-indexing/robots/create-robots-txt?hl=',
		'sitemap'  => 'https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap?hl=',
	];

	if (empty(Input::isRequest('sa')))
		return;

	$sa = Input::request('sa');

	if (! array_key_exists($sa, Lang::getTxt('optimus_tips')))
		return;

	echo '
	<div class="noticebox">
		<a class="bbc_link" href="' . $links[$sa] . ($sa !== 'extra' ? Lang::getTxt('lang_dictionary') : '') . '" target="_blank" rel="noopener">
			', Lang::getTxt('optimus_tips')[$sa], '
		</a>
	</div>';
}

function template_tips_below(): void
{
}

function template_favicon(): void
{
	echo '
	<div class="cat_bar">
		<h3 class="catbg">', Lang::getTxt('optimus_favicon_title'), '</h3>
	</div>
	<div class="optimus windowbg noup">
		<form action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], '">
			<div class="title_bar centertext">
				<label for="optimus_favicon_text">', Lang::getTxt('optimus_favicon_text'), '</label>
			</div>
			<div class="information centertext">
				<td>', Lang::getTxt('optimus_favicon_help'), '</td>
			</div>
			<div class="descbox">
				<textarea rows="5" name="optimus_favicon_text" id="optimus_favicon_text">', empty(Config::$modSettings['optimus_favicon_text']) ? '' : Config::$modSettings['optimus_favicon_text'], '</textarea>
			</div>
			<div class="windowbg" id="op_settings_footer">
				<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
				<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
				<input type="submit" class="button" value="', Lang::getTxt('save'), '">
			</div>
		</form>
	</div>';
}

function template_metatags(): void
{
	echo '
	<form action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], '">
		<div class="cat_bar">
			<h3 class="catbg">', Lang::getTxt('optimus_meta_title'), '</h3>
		</div>
		<div class="information centertext">', Lang::getTxt('optimus_meta_info'), '</div>
		<div class="windowbg">
			<table class="table_grid metatags centertext">
				<thead>
					<tr class="title_bar">
						<th>', Lang::getTxt('optimus_meta_tools'), '</th>
						<th>', Lang::getTxt('optimus_meta_name'), '</th>
						<th>', Lang::getTxt('optimus_meta_content'), '</th>
					</tr>
				</thead>
				<tbody>';

	$engines  = [];

	foreach (Lang::getTxt('optimus_search_engines') as $engine => $data) {
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
						<td>', Lang::getTxt('optimus_meta_customtag'), '</td>
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
				<a href="#" onclick="addNewTag(); return false;" class="bbc_link">', Lang::getTxt('optimus_meta_addtag'), /** @lang text */ '</a>
			</div>
			<script>
				document.getElementById("newtag_link").style.display = "";
				function addNewTag() {
					setOuterHTML(document.getElementById("moreTags"), \'<div style="margin-top: 1ex"><input type="text" name="custom_tag_name[]" size="24" class="input_text"> => <input type="text" name="custom_tag_value[]" size="40" class="input_text"><\' + \'/div><div id="moreTags"><\' + \'/div>\');
				}
			</script>
			<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
			<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', Lang::getTxt('save'), '">
		</div>
	</form>';
}

function template_redirect(): void
{
	echo '
	<form action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], /** @lang text */ '">
		<div class="cat_bar">
			<h3 class="catbg">', Lang::getTxt('optimus_redirect_title'), /** @lang text */ '</h3>
		</div>
		<div class="information centertext">', Lang::getTxt('optimus_redirect_info'), '</div>';

	if (! empty(Utils::$context['optimus_redirect_rules'])) {
		echo /** @lang text */ '
		<div class="windowbg">
			<table class="table_grid centertext">
				<thead>
					<tr class="title_bar">
						<th>', Lang::getTxt('optimus_redirect_from'), '</th>
						<th>', Lang::getTxt('optimus_redirect_to'), '</th>
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
				<a href="#" onclick="addNewRedirect(); return false;" class="bbc_link">', Lang::getTxt('optimus_add_redirect'), /** @lang text */ '</a>
			</div>
			<script>
				document.getElementById("new_redirect_link").style.display = "";
				function addNewRedirect() {
					setOuterHTML(document.getElementById("moreRedirects"), \'<div style="margin-top: 1ex"><input type="text" name="custom_redirect_from[]" placeholder="action=mlist" size="40" class="input_text"> => <input type="text" name="custom_redirect_to[]" placeholder="action=help" size="40" class="input_text"><\' + \'/div><div id="moreRedirects"><\' + \'/div>\');
				}
			</script>
			<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
			<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', Lang::getTxt('save'), '">
		</div>
	</form>';
}

function template_counters(): void
{
	echo '
	<form class="optimus" action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], /** @lang text */ '">
		<div class="cat_bar">
			<h3 class="catbg">', Lang::getTxt('optimus_counters'), '</h3>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_head_code">', Lang::getTxt('optimus_head_code'), '</label>
		</div>
		<div class="information centertext">
			<td>', Lang::getTxt('optimus_head_code_subtext'), '</td>
		</div>
		<div class="descbox">
			<textarea id="optimus_head_code" name="optimus_head_code" rows="6" placeholder="<script>/* ', Lang::getTxt('code'), ' */</script>">', Config::$modSettings['optimus_head_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_stat_code">', Lang::getTxt('optimus_stat_code'), '</label>
		</div>
		<div class="information centertext">
			<td>', Lang::getTxt('optimus_stat_code_subtext'), '</td>
		</div>
		<div class="descbox">
			<textarea id="optimus_stat_code" name="optimus_stat_code" rows="6" placeholder="<script>/* ', Lang::getTxt('code'), ' */</script>">', Config::$modSettings['optimus_stat_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_count_code">', Lang::getTxt('optimus_count_code'), '</label>
		</div>
		<div class="descbox">
			<textarea id="optimus_count_code" name="optimus_count_code" rows="6" placeholder="<script>/* ', Lang::getTxt('code'), ' */</script>">', Config::$modSettings['optimus_count_code'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_counters_css">', Lang::getTxt('optimus_counters_css'), '</label>
		</div>
		<div class="descbox">
			<textarea id="optimus_counters_css" name="optimus_counters_css" rows="6">', Config::$modSettings['optimus_counters_css'] ?? '', '</textarea>
		</div>
		<div class="title_bar centertext">
			<label class="titlebg" for="optimus_ignored_actions">', Lang::getTxt('optimus_ignored_actions'), '</label>
		</div>
		<div class="information centertext">
			<td>', Lang::getTxt('optimus_ignored_actions_subtext'), '</td>
		</div>
		<div class="errorbox">
			<input id="optimus_ignored_actions" name="optimus_ignored_actions" value="', Config::$modSettings['optimus_ignored_actions'] ?? '', '" style="width: 100%">
		</div>
		<div class="windowbg" id="op_settings_footer">
			<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
			<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
			<input type="submit" class="button" value="', Lang::getTxt('save'), '">
		</div>
	</form>';
}

function template_robots(): void
{
	echo '
	<form action="', Utils::$context['post_url'], '" method="post">
		<div class="cat_bar">
			<h3 class="catbg">', Lang::getTxt('optimus_robots_title'), '</h3>
		</div>
		<div class="optimus roundframe">
			<div class="half_content">
				<div class="title_bar">
					<h4 class="titlebg">', Lang::getTxt('optimus_rules'), '</h4>
				</div>
				<div class="inner">
					<span class="smalltext">', Lang::getTxt('optimus_rules_hint'), '</span>
					', Utils::$context['new_robots_content'], '
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
				<input type="submit" class="button" value="', Lang::getTxt('save'), '">
			</div>
		</div>
	</form>';
}

function template_htaccess(): void
{
	echo '
	<div class="cat_bar">
		<h3 class="catbg">', Lang::getTxt('optimus_htaccess_title'), '</h3>
	</div>
	<div class="optimus windowbg noup">
		<form action="', Utils::$context['post_url'], '" method="post" accept-charset="', Utils::$context['character_set'], '">
			<div class="descbox">
				<textarea rows="10" name="optimus_htaccess" id="optimus_htaccess">', Utils::$context['htaccess_content'], '</textarea>
			</div>
			<div class="windowbg" id="op_settings_footer">
				<input type="hidden" name="', Utils::$context['session_var'], '" value="', Utils::$context['session_id'], '">
				<input type="hidden" name="', Utils::$context['admin-dbsc_token_var'], '" value="', Utils::$context['admin-dbsc_token'], '">
				<input type="submit" class="button" value="', Lang::getTxt('save'), '">
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
		<h3 class="catbg">', Lang::getTxt('optimus_top_queries'), '</h3>
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
				let options = {"title":"' . sprintf(Lang::getTxt('optimus_chart_title'), $i) . '", "backgroundColor":"transparent", "width":"800"};
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
	<div class="information">', Lang::getTxt('optimus_no_search_terms'), '</div>';
	}
}

function template_search_terms_below()
{
}
