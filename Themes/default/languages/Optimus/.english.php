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
$txt['optimus_topic_description']      = 'Display the topic first message snippet as the meta-tag <strong>description</strong>';
$txt['optimus_404_status']             = 'Return 403/404 code depending on the requested page\'s status';
$txt['optimus_404_status_help']        = 'If this option is enabled, the corresponding error code (404 or 403) will be returned when requesting a page that does not exist or that is not allowed). See details <a href="https://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank" rel="noopener">here</a>.';
$txt['optimus_404_page_title']         = '404 - Page not found';
$txt['optimus_404_h2']                 = 'Error 404';
$txt['optimus_404_h3']                 = 'Sorry, but the requested page does not exist.';
$txt['optimus_403_page_title']         = '403 - Access forbidden';
$txt['optimus_403_h2']                 = 'Error 403';
$txt['optimus_403_h3']                 = 'Sorry, but you have no access to this page.';

$txt['optimus_extra_title'] = 'Metadata';
$txt['optimus_extra_desc']  = 'Here you can add an additional <a href="http://ogp.me/" target="_blank" rel="noopener">markup</a> for forum pages.';

$txt['optimus_og_image']      = 'Use the image from the first topic message in the meta tag <strong>og:image</strong>';
$txt['optimus_og_image_help'] = 'If enabled, the <strong>og:image</strong> meta tag will include a link to the first image attached to the first topic message. If there is no attachment, and the image inside the <strong>img</strong> tag is found in the message text, it is used.';
$txt['optimus_fb_appid']      = 'Facebook Application ID (if you have)';
$txt['optimus_fb_appid_help'] = 'Create an application <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener">here</a>, copy its ID and fill this field.';
$txt['optimus_tw_cards']      = 'Twitter account name (if you have)';
$txt['optimus_tw_cards_help'] = 'Read more about Twitter cards <a href="https://dev.twitter.com/cards/overview" target="_blank" rel="noopener">here</a>.';
$txt['optimus_json_ld']       = 'JSON-LD markup for "breadcrumbs"';
$txt['optimus_json_ld_help']  = 'JSON-LD is a lightweight Linked Data format. It is easy for humans to read and write. It is based on the already successful JSON format and provides a way to help JSON data interoperate at Web-scale. JSON-LD is an ideal data format for programming environments, REST Web services, and unstructured databases such as CouchDB and MongoDB.<br><br>Enable this option to generate JSON-LD markup for "<a href="https://developers.google.com/search/docs/data-types/breadcrumbs?hl=' . $txt['lang_dictionary'] . '" target="_blank" rel="noopener">breadcrumbs</a>".';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc']  = 'Create your own forum icon. It will be displayed by the browser in the tab before the page name, as well as an image next to the open tab and other interface elements.';

$txt['optimus_favicon_create']  = 'Create the favicon';
$txt['optimus_favicon_api_key'] = 'API key to work with Favicon Generator (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank" rel="noopener">Get API key</a>)';
$txt['optimus_favicon_text']    = 'The favicon code';
$txt['optimus_favicon_help']    = 'Generate your own favicon <a href="http://www.favicomatic.com/" target="_blank" rel="noopener">here</a>, or use a special generator (it needs to enter the API key on the field above).<br>Then upload the favicon files to the forum root, and save the code from the generator site in the field on the right. This code will be load at the top of the site pages, between the &lt;head&gt;&lt;/head&gt; tags.';

$txt['optimus_meta_title'] = 'Meta tags';
$txt['optimus_meta_desc']  = 'On this page you can add any regular/verification code(s) from list below.';

$txt['optimus_meta_addtag']    = 'Click here to add a new tag';
$txt['optimus_meta_customtag'] = 'Custom meta tag';
$txt['optimus_meta_tools']     = 'Search engine (Tools)';
$txt['optimus_meta_name']      = 'Name';
$txt['optimus_meta_content']   = 'Content';
$txt['optimus_meta_info']      = 'Please use only the values from <strong>content</strong> parameter of the meta tags.<br>Example: <span class="smalltext">&lt;meta name="<strong>NAME</strong>" content="<strong>VALUE</strong>"&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="https://www.google.com/webmasters/tools/" target="_blank" rel="noopener">Google Search Console</a>'),
	'Yandex' => array('yandex-verification', '<a href="https://webmaster.yandex.com/" target="_blank" rel="noopener">Yandex.Webmaster</a>'),
	'Bing'   => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank" rel="noopener">Bing Webmaster</a>')
);

$txt['optimus_counters']      = 'Counters';
$txt['optimus_counters_desc'] = 'You can add and change any counters in this section to log visits of your forum.';

$txt['optimus_head_code']       = 'Invisible counters loading on <strong>head</strong> section (<a href="https://www.google.com/analytics/sign_up.html" target="_blank" rel="noopener">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Other invisible counters (<a href="https://matomo.org/" target="_blank" rel="noopener">Matomo</a> etc)';
$txt['optimus_count_code']      = 'Visible counters (<a href="http://www.freestats.com/" target="_blank" rel="noopener">FreeStats</a>, <a href="http://www.superstats.com/" target="_blank" rel="noopener">SuperStats</a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank" rel="noopener">PRTracker</a> etc)';
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
$txt['optimus_sitemap_boards']      = 'Add links to boards to the sitemap<br><span class="smalltext error">Boards that closed to guests will NOT be added.</span>';
$txt['optimus_sitemap_topics']      = 'Add to the sitemap only those topics that have the number of replies is more than';

$txt['optimus_sitemap_rec']        = ' Optimus is not able to split files into several parts.';
$txt['optimus_sitemap_url_limit']  = 'Sitemap file must have no more than 50,000 URLs!';
$txt['optimus_sitemap_size_limit'] = '%1$s file must be no larger than 10MB!';
$txt['optimus_sitemap_xml_link']   = 'Sitemap XML';

$txt['optimus_donate_title'] = 'Donations';
$txt['optimus_donate_desc']  = 'From here you can send donations to the mod author.';
$txt['optimus_donate_info']  = 'Here you can support the developer with your donation ;)';

// Task Manager
$txt['scheduled_task_optimus_sitemap']      = 'Sitemap XML Gereration';
$txt['scheduled_task_desc_optimus_sitemap'] = 'You can set the frequency of the sitemap\'s creation.';
