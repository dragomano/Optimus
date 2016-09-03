<?php

$txt['optimus_main'] = 'Optimus';
$txt['optimus_title'] = 'Search Engine Optimization';

$txt['optimus_common_title'] = 'Base settings';
$txt['optimus_common_desc'] = 'On this page you can change a forum description, manage of pages titles\'s templates.';

$txt['optimus_main_page'] = 'Homepage';
$txt['optimus_common_info'] = 'Well, content of the description tag may be taken into account when the robot determines if a page matches a search query.';
$txt['optimus_portal_compat'] = 'Portal integration';
$txt['optimus_portal_compat_set'] = array('None', 'PortaMx', 'SimplePortal');
$txt['optimus_portal_index'] = 'Portal homepage title';
$txt['optimus_forum_index'] = 'Forum homepage title';
$txt['optimus_description'] = 'The forum annotation<br /><span class="smalltext">Will be used as content of the meta-tag <strong>description</strong>.</span>';

$txt['optimus_all_pages'] = 'Topic & board pages';
$txt['optimus_tpl_info'] = 'Possible variables:<br/><strong>{board_name}</strong> &mdash; board name, <strong>{topic_name}</strong> &mdash; topic subject,<br/><strong>{#}</strong> &mdash; current page number, <strong>{cat_name}</strong> &mdash; category name, <strong>{forum_name}</strong> &mdash; your forum name.';
$txt['optimus_board_tpl'] = 'Template of board pages title';
$txt['optimus_topic_tpl'] = 'Template of topic pages title';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - page {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - page {#} - ', '{board_name} - {forum_name}')
);
$txt['optimus_board_description'] = 'Display board description as the meta-tag <strong>description</strong>';
$txt['optimus_topic_description'] = 'Display topic description as the meta-tag <strong>description</strong><br /><span class="smalltext">Use <a href="http://custom.simplemachines.org/mods/index.php?mod=3012" target="_blank">Topic Descriptions mod</a> to create short descriptions for topics.</span>';
$txt['optimus_404_status'] = 'Return <a href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">403/404 code</a> depending on the requested page\'s status';
$txt['optimus_404_page_title'] = '404 - Page not found';
$txt['optimus_404_h2'] = 'Error 404';
$txt['optimus_404_h3'] = 'Sorry, but the requested page does not exist.';
$txt['optimus_403_page_title'] = '403 - Access forbidden';
$txt['optimus_403_h2'] = 'Error 403';
$txt['optimus_403_h3'] = 'Sorry, but you have no access to this page.';

$txt['optimus_extra_title'] = 'Extra';
$txt['optimus_extra_desc'] = 'Here you can find some fixes for your forum. Additionally you can enable Open Graph support. Enjoy!';

$txt['optimus_remove_indexphp'] = 'Remove "index.php" from URLs';
$txt['optimus_correct_prevnext'] = 'The correct rel="next" and rel="prev" (pagination for topics)';
$txt['optimus_open_graph'] = 'Enable Open Graph support';
$txt['optimus_og_image'] = 'Link to your default Open Graph image<br /><span class="smalltext">It will be replaced by the attachment of the first message in topics (if exists).</span>';

$txt['optimus_verification_title'] = 'Verification meta tags';
$txt['optimus_verification_desc'] = 'On this page you can add any common or verification code(s) from list below.';

$txt['optimus_codes'] = 'Verification meta tags';
$txt['optimus_titles'] = 'Search engine (Tools)';
$txt['optimus_name'] = 'Name';
$txt['optimus_content'] = 'Content';
$txt['optimus_meta_info'] = 'Please use only the values from <strong>content</strong> parameter of the meta tags.<br />Example: <span class="smalltext">&lt;meta name="<strong>NAME</strong>" content="<strong>VALUE</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="http://www.google.com/webmasters/tools" target="_blank">Webmasters tools</a>'),
	'Yandex' => array('yandex-verification','<a href="http://webmaster.yandex.com/"                target="_blank">Yandex.Webmaster</a>'),
	'MSN'    => array('msvalidate.01','<a href="http://www.bing.com/webmaster"                     target="_blank">MSN Webmaster Tools</a>'),
	'Yahoo'  => array('y_key','<a href="https://siteexplorer.search.yahoo.com/"                    target="_blank">Yahoo Site Explorer</a>'),
	'Alexa'  => array('alexaVerifyID','<a href="http://www.alexa.com/siteowners"                   target="_blank">Alexa Site Tools</a>')
);

$txt['optimus_counters'] = 'Counters';
$txt['optimus_counters_desc'] = 'You can add and change any counters in this section to log visits of your forum.';

$txt['optimus_head_code'] = 'Invisible counters loading on <strong>head</strong> section (<a href="http://www.google.com/analytics/sign_up.html" target="_blank">Google Analytics</a>)';
$txt['optimus_stat_code'] = 'Other invisible counters (<a href="http://piwik.org/" target="_blank">Piwik</a> etc)';
$txt['optimus_count_code'] = 'Visible counters (<a href="http://www.freestats.com/" target="_blank">FreeStats</a>, <a href="http://www.superstats.com/" target="_blank">SuperStats</a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank">PRTracker</a> etc)';
$txt['optimus_count_code_css'] = 'Appearance for visible counters (CSS code)';
$txt['optimus_ignored_actions'] = 'Ignored actions';
$txt['optimus_ga_note'] = '';

$txt['optimus_robots_title'] = 'File robots.txt';
$txt['optimus_robots_desc'] = 'On this page you can change some options of forum map\'s creating, as well as modify a robots.txt file by using special generator.';

$txt['optimus_manage'] = 'Manage robots.txt';
$txt['optimus_rules'] = 'Robots.txt Generator';
$txt['optimus_rules_hint'] = 'You can copy these rules into the field on the right:';
$txt['optimus_robots_hint'] = 'Here you can insert your own rules or modify existing ones:';
$txt['optimus_useful'] = '';
$txt['optimus_robots_old'] = 'The contents of the old (before to installation) robots.txt you can see on <a href="/old_robots.txt" target="_blank">this link</a>.';
$txt['optimus_links_title'] = 'Useful links';
$txt['optimus_links'] = array(
	'Using robots.txt' => 'http://help.yandex.com/webmaster/?id=1113851',
	'Create a robots.txt file' => 'https://support.google.com/webmasters/answer/6062596?hl=en',
	'Changing of .htaccess' => 'http://httpd.apache.org/docs/trunk/howto/htaccess.html'
);

$txt['optimus_sitemap_title'] = 'Optimus Sitemap';
$txt['optimus_sitemap_desc'] = 'Do you want a simple sitemap? Optimus can generate sitemap.xml for small forums. Just enable this option below.';

$txt['optimus_sitemap_enable'] = 'Create and periodically update Sitemap XML file';
$txt['optimus_sitemap_link'] = 'Show Sitemap XML-link on the footer';
$txt['optimus_sitemap_topic_size'] = 'Add to sitemap only those topics that have the number of replies is more than';

$txt['optimus_sitemap_rec'] = ' Optimus is not able to split files into several parts.';
$txt['optimus_sitemap_url_limit'] = 'Sitemap file must have no more than 50,000 URLs!';
$txt['optimus_sitemap_size_limit'] = '%1$s file must be no larger than 10MB!';
$txt['optimus_sitemap_xml_link'] = 'Sitemap XML';

// Ads
$txt['optimus_1ps_ads'] = '';

?>