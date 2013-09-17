<?php

$txt['optimus_main'] = 'Optimus Brave';
$txt['optimus_title'] = 'Search Engine Optimization';

$txt['optimus_common_title'] = 'Common settings';
$txt['optimus_common_desc'] = 'On this page you can change a forum description, manage of pages titles\'s templates.';
$txt['optimus_verification_title'] = 'Verification meta tags';
$txt['optimus_verification_desc'] = 'On this page you can add any common or verification code(s) from list below.';
$txt['optimus_robots_title'] = 'robots.txt';
$txt['optimus_robots_desc'] = 'On this page you can change some options of forum map\'s creating, as well as modify a robots.txt file by using special generator.';
$txt['optimus_terms_title'] = 'Search terms';
$txt['optimus_terms_desc'] = 'Search terms are the words and phrases that people type into the search forms of search engines to find your forum.';

$txt['optimus_main_page'] = 'Homepage';
$txt['optimus_common_info'] = 'Well, content of the description tag may be taken into account when the robot determines if a page matches a search query.';
$txt['optimus_portal_compat'] = 'Portal integration';
$txt['optimus_portal_compat_set'] = array('None','Adk Portal','Dream Portal','EzPortal','PortaMx','SimplePortal','TinyPortal');
$txt['optimus_portal_index'] = 'Portal homepage title';
$txt['optimus_forum_index'] = 'Forum homepage title';
$txt['optimus_description'] = 'A short but interesting forum review<br /><span class="smalltext">Will be used as content of the meta-tag <em>description</em>.</span>';
$txt['optimus_all_pages'] = 'Topic/board pages settings';
$txt['optimus_tpl_info'] = 'Possible variables:<br/><strong>{board_name}</strong> &mdash; board name, <strong>{topic_name}</strong> &mdash; topic subject,<br/><strong>{#}</strong> &mdash; current page number, <strong>{cat_name}</strong> &mdash; category name, <strong>{forum_name}</strong> &mdash; your forum name.';
$txt['optimus_board_tpl'] = 'Template of board pages title';
$txt['optimus_topic_tpl'] = 'Template of topic pages title';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - page {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - page {#} - ', '{board_name} - {forum_name}')
);

$txt['optimus_board_description'] = 'Display board description as the meta-tag <em>description</em>';
$txt['optimus_topic_description'] = 'Display the first sentence of the current page first message as the meta-tag <em>description</em>';
$txt['optimus_404_status'] = 'Return <a href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">403/404 code</a> depending on the requested page\'s status';
$txt['optimus_404_page_title'] = '404 - Page not found';
$txt['optimus_404_h2'] = 'Error 404';
$txt['optimus_404_h3'] = 'Sorry, but the requested page does not exist.';
$txt['optimus_403_page_title'] = '403 - Access forbidden';
$txt['optimus_403_h2'] = 'Error 403';
$txt['optimus_403_h3'] = 'Sorry, but you have no access to this page.';

$txt['optimus_sitemap_section'] = 'Forum map';
$txt['optimus_sitemap_desc'] = 'Do you want a simple sitemap? Optimus Brave can generate sitemap.xml for small forums. Just enable this option below.';
$txt['optimus_sitemap_enable'] = 'Create and periodically update Sitemap XML-file';
$txt['optimus_sitemap_link'] = 'Show Sitemap XML-link on the footer';
$txt['optimus_sitemap_topic_size'] = 'Add to sitemap only those topics that have the number of replies is more than';

$txt['optimus_codes'] = 'Verification meta tags';
$txt['optimus_titles'] = 'Search engine (Tools)';
$txt['optimus_name'] = 'Name';
$txt['optimus_content'] = 'Content';
$txt['optimus_meta_info'] = 'Info: <span class="error">WHAT IS <a href="http://support.google.com/webmasters/bin/answer.py?hl=en&amp;answer=35659" target="_blank">verification meta tag</a>?</span><br />Please use only the values from <strong>content</strong> parameter of the meta tags.<br />Example: <span class="smalltext">&lt;meta name="google-site-verification" content="<strong>VALUE THAT YOU MUST PASTE INTO THE RIGHT COLUMN</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="http://www.google.com/webmasters/tools" target="_blank">Webmasters tools</a>'),
	'Yandex' => array('yandex-verification','<a href="http://webmaster.yandex.com/" target="_blank">Yandex.Webmaster</a>'),
	'MSN' => array('msvalidate.01','<a href="http://www.bing.com/webmaster" target="_blank">MSN Webmaster Tools</a>'),
	'Yahoo' => array('y_key','<a href="https://siteexplorer.search.yahoo.com/" target="_blank">Yahoo Site Explorer</a>'),
	'Alexa' => array('alexaVerifyID','<a href="http://www.alexa.com/siteowners" target="_blank">Alexa Site Tools</a>')
);

$txt['optimus_counters'] = 'Counters';
$txt['optimus_counters_desc'] = 'For counting visits to your forum you can add and change a variety of counters in this section.';
$txt['optimus_head_code'] = 'Invisible counters loading on <strong>head</strong> section (<a href="http://www.google.com/analytics/sign_up.html" target="_blank">Google Analytics</a>)';
$txt['optimus_stat_code'] = 'Other invisible counters (<a href="http://piwik.org/" target="_blank">Piwik</a> etc)';
$txt['optimus_count_code'] = 'Visible counters (<a href="http://www.freestats.com/" target="_blank">FreeStats</a>, <a href="http://www.superstats.com/" target="_blank">SuperStats</a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank">PRTracker</a> etc)';
$txt['optimus_count_code_css'] = 'Appearance for visible counters (CSS code)';
$txt['optimus_ignored_actions'] = 'Ignored actions';
$txt['optimus_ga_note'] = '';

$txt['optimus_manage'] = 'Manage robots.txt';
$txt['optimus_robots_old'] = 'The contents of the old (before to installation) robots.txt you can see on <a href="/old_robots.txt" target="_blank">this link</a>.';
$txt['optimus_links_title'] = 'Useful links';
$txt['optimus_links'] = array(
	'Using robots.txt' => 'http://help.yandex.com/webmaster/?id=1113851',
	'Block or remove pages using a robots.txt' => 'http://www.google.com/support/webmasters/bin/answer.py?hl=en&amp;answer=156449',
	'Changing of .htaccess' => 'http://httpd.apache.org/docs/trunk/howto/htaccess.html'
);

$txt['optimus_rules'] = 'Generator of rules';
$txt['optimus_rules_hint'] = 'You can copy these rules into the field on the right:';
$txt['optimus_robots_hint'] = 'Here you can insert your own rules or modify existing ones:';
$txt['optimus_useful'] = '';

$txt['scheduled_task_optimus_sitemap'] = 'Create Forum Map';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Recommended regularity &mdash; once a day.';
$txt['optimus_sitemap_rec'] = ' Optimus Brave is not able to split files into several parts.';
$txt['optimus_sitemap_url_limit'] = 'Sitemap file must have no more than 50,000 URLs!';
$txt['optimus_sitemap_size_limit'] = '%1$s file must be no larger than 10MB!';
$txt['optimus_sitemap_xml_link'] = 'Sitemap XML';

?>