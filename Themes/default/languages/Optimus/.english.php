<?php

/**
 * .english language file
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_main']  = 'Optimus';
$txt['optimus_title'] = 'Search Engine Optimization';

$txt['optimus_base_title'] = 'Base settings';
$txt['optimus_base_desc']  = 'On this page you can change a forum description, manage of pages titles\'s templates, enable/disable Sitemap XML generation.';

$txt['optimus_main_page']         = 'Homepage';
$txt['optimus_base_info']         = 'Well, the description tag\'s content may be taken into account when the robot determines if a page matches a search query.';
$txt['optimus_portal_compat']     = 'Portal Compatibility';
$txt['optimus_portal_compat_set'] = array('None', 'PortaMx', 'SimplePortal', 'TinyPortal');
$txt['optimus_portal_index']      = 'Portal Homepage title';
$txt['optimus_forum_index']       = 'Forum Homepage title';
$txt['optimus_description']       = 'The forum annotation<br /><span class="smalltext">Will be used as content of the meta-tag <strong>description</strong>.</span>';

$txt['optimus_all_pages'] = 'Topic & board pages';
$txt['optimus_tpl_info']  = 'Possible variables:<br/><strong>{board_name}</strong> &mdash; board name, <strong>{topic_name}</strong> &mdash; topic subject,<br/><strong>{#}</strong> &mdash; current page number, <strong>{cat_name}</strong> &mdash; category name, <strong>{forum_name}</strong> &mdash; your forum name.';
$txt['optimus_board_tpl'] = 'Template of board pages title';
$txt['optimus_topic_tpl'] = 'Template of topic pages title';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - page {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - page {#} - ', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number']   = 'Don\'t show number for a first page';
$txt['optimus_board_description'] = 'Display board description as the meta-tag <strong>description</strong>';
$txt['optimus_topic_description'] = 'Display topic description as the meta-tag <strong>description</strong><br /><span class="smalltext">Use <a href="https://custom.simplemachines.org/mods/index.php?mod=3012" target="_blank">Topic Descriptions mod</a> to create short descriptions for topics.</span>';
$txt['optimus_404_status']        = 'Return <a href="https://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">403/404 code</a> depending on the requested page\'s status';
$txt['optimus_404_page_title']    = '404 - Page not found';
$txt['optimus_404_h2']            = 'Error 404';
$txt['optimus_404_h3']            = 'Sorry, but the requested page does not exist.';
$txt['optimus_403_page_title']    = '403 - Access forbidden';
$txt['optimus_403_h2']            = 'Error 403';
$txt['optimus_403_h3']            = 'Sorry, but you have no access to this page.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc']  = 'Create your own forum icon. It will be displayed by the browser in the tab before the page name, as well as an image next to the open tab and other interface elements.';

$txt['optimus_favicon_create']  = 'Create the favicon';
$txt['optimus_favicon_api_key'] = 'API key to work with Favicon Generator (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank">Get API key</a>)';
$txt['optimus_favicon_text']    = 'The favicon code';
$txt['optimus_favicon_help']    = 'Generate your own favicon <a href="http://www.favicomatic.com/" target="_blank">here</a>, or use a special generator (it needs to enter the API key on the field above).<br />Then upload the favicon files to the forum root, and save the code from the generator site in the field on the right.<br />This code will be load at the top of the site pages, between the &lt;head&gt;&lt;/head&gt; tags.';

$txt['optimus_extra_title'] = 'Metadata';
$txt['optimus_extra_desc']  = 'Here you can find some fixes for your forum. Additionally you can enable Open Graph and JSON-LD support. Enjoy!';

$txt['optimus_open_graph'] = '<a href="http://ogp.me/" target="_blank">Open Graph</a> meta tags for forum pages';
$txt['optimus_og_image']   = 'Link to your default Open Graph image<br /><span class="smalltext">It will be replaced by the attachment of the first message in topics (if exists).</span>';
$txt['optimus_fb_appid']   = '<a href="https://developers.facebook.com/apps" target="_blank">APP ID</a> (Application ID) <a href="https://www.facebook.com/" target="_blank">Facebook</a>';
$txt['optimus_tw_cards']   = '<a href="https://twitter.com/" target="_blank">Twitter</a> account name (specify to enable <a href="https://dev.twitter.com/cards/overview" target="_blank">Twitter Cards</a>)';
$txt['optimus_json_ld']    = '<a href="https://json-ld.org/" target="_blank">JSON-LD</a> markup for "<a href="https://developers.google.com/search/docs/data-types/breadcrumbs?hl=' . $txt['lang_dictionary'] . '" target="_blank">breadcrumbs</a>"';

$txt['optimus_meta_title'] = 'Meta tags';
$txt['optimus_meta_desc']  = 'On this page you can add any regular/verification code(s) from list below.';

$txt['optimus_meta_addtag']    = 'Click here to add a new tag';
$txt['optimus_meta_customtag'] = 'Custom meta tag';
$txt['optimus_meta_tools']     = 'Search engine (Tools)';
$txt['optimus_meta_name']      = 'Name';
$txt['optimus_meta_content']   = 'Content';
$txt['optimus_meta_info']      = 'Please use only the values from <strong>content</strong> parameter of the meta tags.<br />Example: <span class="smalltext">&lt;meta name="<strong>NAME</strong>" content="<strong>VALUE</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="https://www.google.com/webmasters/tools/" target="_blank">Google Search Console</a>'),
	'Yandex' => array('yandex-verification', '<a href="https://webmaster.yandex.com/" target="_blank">Yandex.Webmaster</a>'),
	'Bing'   => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank">Bing Webmaster</a>')
);

$txt['optimus_counters']      = 'Counters';
$txt['optimus_counters_desc'] = 'You can add and change any counters in this section to log visits of your forum.';

$txt['optimus_head_code']       = 'Invisible counters loading on <strong>head</strong> section (<a href="https://www.google.com/analytics/sign_up.html" target="_blank">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Other invisible counters (<a href="https://matomo.org/" target="_blank">Matomo</a> etc)';
$txt['optimus_count_code']      = 'Visible counters (<a href="http://www.freestats.com/" target="_blank">FreeStats</a>, <a href="http://www.superstats.com/" target="_blank">SuperStats</a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank">PRTracker</a> etc)';
$txt['optimus_counters_css']    = 'Appearance for visible counters (CSS)';
$txt['optimus_ignored_actions'] = 'Ignored actions';

$txt['optimus_robots_title'] = 'Editor robots.txt';
$txt['optimus_robots_desc']  = 'On this page you can change some options of forum map\'s creating, as well as modify a robots.txt file by using special generator.';

$txt['optimus_manage']      = 'Manage robots.txt';
$txt['optimus_rules']       = 'Robots.txt Generator';
$txt['optimus_rules_hint']  = 'You can copy these rules into the field on the right:';
$txt['optimus_robots_hint'] = 'Here you can insert your own rules or modify existing ones:';
$txt['optimus_useful']      = '';
$txt['optimus_links_title'] = 'Useful links';
$txt['optimus_links']       = array(
	'Create a robots.txt file'              => 'https://support.google.com/webmasters/answer/6062596?hl=en',
	'Using robots.txt'                      => 'https://help.yandex.com/webmaster/?id=1113851',
	'Technical audit of the entire website' => 'https://netpeaksoftware.com/ucp?invite=94cdaf6a'
);

$txt['optimus_sitemap_title'] = 'Optimus Sitemap';
$txt['optimus_sitemap_desc']  = 'Do you want a simple sitemap? Optimus can generate sitemap.xml for small forums. Just enable this option below. This sitemap will be updated depending on settings in the <a href="%1$s">Task Manager</a>.';

$txt['optimus_sitemap_enable']      = 'Create and periodically update Sitemap XML file';
$txt['optimus_sitemap_link']        = 'Show Sitemap XML-link on the footer';
$txt['optimus_sitemap_boards']      = 'Add links to boards to the sitemap<br /><span class="smalltext error">Boards that closed to guests will NOT be added.</span>';
$txt['optimus_sitemap_topics']      = 'Add to the sitemap only those topics that have the number of replies is more than';

$txt['optimus_sitemap_xml_link']   = 'Sitemap XML';

$txt['optimus_donate_title'] = 'Donations';
$txt['optimus_donate_desc']  = 'From here you can send donations to the mod author.';

// Task Manager
$txt['scheduled_task_optimus_sitemap']      = 'Sitemap XML Gereration';
$txt['scheduled_task_desc_optimus_sitemap'] = 'You can set the frequency of the sitemap\'s creation.';
