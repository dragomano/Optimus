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

$txt['optimus_main_page']   = 'Homepage';
$txt['optimus_forum_index'] = 'Forum Homepage title';
$txt['optimus_description'] = 'The forum annotation<br><span class="smalltext">Will be used as content of the meta-tag <strong>description</strong>.</span>';

$txt['optimus_all_pages']              = 'Topic & board pages';
$txt['optimus_board_extend_title']     = 'Add forum name to board titles';
$txt['optimus_board_extend_title_set'] = array('None', 'Before board title', 'After board title');
$txt['optimus_topic_extend_title']     = 'Add title of section and forum to topic titles';
$txt['optimus_topic_extend_title_set'] = array('None', 'Before topic title', 'After topic title');

$txt['optimus_extra_title'] = 'Metadata';
$txt['optimus_extra_desc']  = 'Here you can add an additional <a href="http://ogp.me/" target="_blank" rel="noopener"><strong>markup</strong></a> for forum pages.';

$txt['optimus_default_og_image']      = 'Default image link for the <strong>og:image</strong> meta tag';
$txt['optimus_topic_body_og_image']   = 'Use the image in the text of the first message on the topic page for the <strong>og:image</strong> meta tag (if exists)';
$txt['optimus_topic_attach_og_image'] = 'Use attached image for <strong>og:image</strong> meta tag (if exists)';
$txt['optimus_fb_appid']              = 'Facebook Application ID (if you have)';
$txt['optimus_fb_appid_help']         = 'Create an application <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener"><strong>here</strong></a>, copy its ID and fill this field.';
$txt['optimus_tw_cards']              = 'Twitter account name (if you have)';
$txt['optimus_tw_cards_help']         = 'Read more about Twitter cards <a href="https://dev.twitter.com/cards/overview" target="_blank" rel="noopener"><strong>here</strong></a>.';
$txt['optimus_json_ld']               = 'JSON-LD markup for "breadcrumbs"';
$txt['optimus_json_ld_help']          = 'JSON-LD is a lightweight Linked Data format. It is easy for humans to read and write. It is based on the already successful JSON format and provides a way to help JSON data interoperate at Web-scale. JSON-LD is an ideal data format for programming environments, REST Web services, and unstructured databases such as CouchDB and MongoDB.<br><br>Enable this option to generate JSON-LD markup for "<a href="https://developers.google.com/search/docs/data-types/breadcrumbs?hl=en" target="_blank" rel="noopener"><strong>breadcrumbs</strong></a>".';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc']  = 'Create your own forum icon. It will be displayed by the browser in the tab before the page name, as well as an image next to the open tab and other interface elements.';

$txt['optimus_favicon_create']  = 'Create the favicon';
$txt['optimus_favicon_api_key'] = 'API key to work with Favicon Generator (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank" rel="noopener"><strong>Get API key</strong></a>)';
$txt['optimus_favicon_text']    = 'The favicon code';
$txt['optimus_favicon_help']    = 'Generate your own favicon <a href="http://www.favicomatic.com/" target="_blank" rel="noopener"><strong>here</strong></a>, or use a special generator (it needs to enter the API key on the field above).<br>Then upload the favicon files to the forum root, and save the code from the generator site in the field on the right. This code will be load at the top of the site pages, between the &lt;head&gt;&lt;/head&gt; tags.';

$txt['optimus_meta_title'] = 'Meta tags';
$txt['optimus_meta_desc']  = 'On this page you can add any regular/verification code(s) from list below.';

$txt['optimus_meta_addtag']    = 'Click here to add a new tag';
$txt['optimus_meta_name']      = 'Name';
$txt['optimus_meta_content']   = 'Content';
$txt['optimus_meta_info']      = 'Please use only the values from <strong>content</strong> parameter of the meta tags.<br>Example: <span class="smalltext">&lt;meta name="<strong>NAME</strong>" content="<strong>VALUE</strong>"&gt;</span>';
$txt['optimus_search_engines'] = array('google-site-verification', 'yandex-verification', 'wmail-verification', 'msvalidate.01');

$txt['optimus_counters']      = 'Counters';
$txt['optimus_counters_desc'] = 'You can add and change any counters in this section to log visits of your forum.';

$txt['optimus_head_code']       = 'Invisible counters loading on <strong>head</strong> section (<a href="https://www.google.com/analytics/sign_up.html" target="_blank" rel="noopener"><strong>Google Analytics</strong></a>)';
$txt['optimus_stat_code']       = 'Other invisible counters (<a href="https://matomo.org/" target="_blank" rel="noopener"><strong>Matomo</strong></a> etc)';
$txt['optimus_count_code']      = 'Visible counters (<a href="http://www.freestats.com/" target="_blank" rel="noopener"><strong>FreeStats</strong></a>, <a href="http://www.superstats.com/" target="_blank" rel="noopener"><strong>SuperStats</strong></a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank" rel="noopener"><strong>PRTracker</strong></a> etc)';
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
$txt['optimus_sitemap_desc']  = 'Do you want a simple sitemap? Optimus can generate sitemap.xml for small forums. Just enable this option below. This sitemap will be updated depending on settings in the <a href="%1$s"><strong>Task Manager</strong></a>.';

$txt['optimus_sitemap_enable'] = 'Create and periodically update Sitemap XML file';
$txt['optimus_sitemap_link']   = 'Show Sitemap XML-link on the footer';
$txt['optimus_sitemap_boards'] = 'Add links to boards to the sitemap<br><span class="smalltext error">Boards that closed to guests will NOT be added.</span>';
$txt['optimus_sitemap_topics'] = 'Add to the sitemap only those topics that have the number of replies is more than';

$txt['optimus_sitemap_rec']       = ' Optimus is not able to split files into several parts.';
$txt['optimus_sitemap_url_limit'] = 'Sitemap file must have no more than 50,000 URLs!';
$txt['optimus_sitemap_xml_link']  = 'Sitemap XML';

$txt['optimus_donate_title'] = 'Donations';
$txt['optimus_donate_desc']  = 'From here you can send donations to the mod author.';

// Task Manager
$txt['scheduled_task_Optimus::scheduledTask']      = 'Sitemap XML Gereration';
$txt['scheduled_task_desc_Optimus::scheduledTask'] = 'You can set the frequency of the sitemap\'s creation.';
