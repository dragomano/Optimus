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

$txt['optimus_main_page']           = 'Homepage';
$txt['optimus_forum_index']         = 'Forum Homepage title';
$txt['optimus_description']         = 'The forum annotation';
$txt['optimus_description_subtext'] = 'It will be used as content of the meta-tag <strong>description</strong>.';

$txt['optimus_all_pages']                           = 'Topic & board pages';
$txt['optimus_board_extend_title']                  = 'Add forum name to board titles';
$txt['optimus_board_extend_title_set']              = array('None', 'Before board title', 'After board title');
$txt['optimus_topic_extend_title']                  = 'Add title of section and forum to topic titles';
$txt['optimus_topic_extend_title_set']              = array('None', 'Before topic title', 'After topic title');
$txt['optimus_topic_description']                   = 'Display the topic first message snippet as the meta-tag <strong>description</strong><br><span class="smalltext">Use <a href="https://custom.simplemachines.org/mods/index.php?mod=3012" target="_blank" rel="noopener">Topic Descriptions mod</a> to create short descriptions for topics.</span>';
$txt['optimus_allow_change_board_og_image']         = 'Allow a separate field for the board <strong>OG Image</strong>';
$txt['optimus_allow_change_board_og_image_subtext'] = 'It is displayed when editing a board.';
$txt['optimus_allow_change_topic_desc']             = 'Allow a separate field for the topic description';
$txt['optimus_allow_change_topic_desc_subtext']     = 'It is displayed when editing a topic.';
$txt['optimus_allow_change_topic_keywords']         = 'Allow a separate field for the topic keywords';
$txt['optimus_allow_change_topic_keywords_subtext'] = 'It is displayed when editing a topic.';
$txt['optimus_show_keywords_block']                 = 'Show a block with keywords above the first post of the topic';
$txt['optimus_correct_http_status']                 = 'Return <a href="https://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank" rel="noopener" class="bbc_link">403/404 code</a> depending on the requested page\'s status';

$txt['optimus_extra_settings']        = 'Extra settings';
$txt['optimus_use_only_cookies']      = 'Use cookies to store the session id on the client side';
$txt['optimus_use_only_cookies_help'] = 'Enabling the setting <a href="https://www.php.net/manual/en/session.configuration.php#ini.session.use-only-cookies" target="_blank" rel="noopener" class="bbc_link">session.use_only_cookies</a> prevents attacks involved passing session ids in URLs.<br>In addition, you will be able to get rid of the session ids in the canonical addresses of the forum pages.';
$txt['optimus_remove_index_php']      = 'Remove "index.php" from the forum urls';
$txt['optimus_extend_h1']             = 'Add a page title to the <strong>H1</strong> tag';

$txt['optimus_extra_title'] = 'Metadata';
$txt['optimus_extra_desc']  = 'Here you can add an additional <a href="https://ogp.me/" target="_blank" rel="noopener" class="bbc_link">markup</a> for forum pages.';

$txt['optimus_og_image']         = 'Use the image from the first topic message in the meta tag <strong>og:image</strong>';
$txt['optimus_og_image_subtext'] = 'By default, the image specified in <a href="%s" class="bbc_link">current theme settings</a> is used.';
$txt['optimus_og_image_help']    = 'If enabled, the <strong>og:image</strong> meta tag will include a link to the first image attached to the first topic message. If there is no attachment, and the image inside the <strong>img</strong> tag is found in the message text, it is used.';
$txt['optimus_fb_appid']         = 'Facebook Application ID (if you have)';
$txt['optimus_fb_appid_help']    = 'Create an application <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener" class="bbc_link"><strong>here</strong></a>, copy its ID and fill this field.';
$txt['optimus_tw_cards']         = 'Twitter account name (if you have)';
$txt['optimus_tw_cards_help']    = 'Read more about Twitter cards <a href="https://dev.twitter.com/cards/overview" target="_blank" rel="noopener" class="bbc_link"><strong>here</strong></a>.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc']  = 'Create your own forum icon. It will be displayed by the browser in the tab before the page name, as well as an image next to the open tab and other interface elements.';

$txt['optimus_favicon_create']  = 'Create the favicon';
$txt['optimus_favicon_api_key'] = 'API key to work with Favicon Generator (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank" rel="noopener" class="bbc_link">Get API key</a>)';
$txt['optimus_favicon_text']    = 'The favicon code';
$txt['optimus_favicon_help']    = 'Generate your own favicon <a href="https://www.favicomatic.com/" target="_blank" rel="noopener" class="bbc_link">here</a>, or <a href="https://digitalagencyrankings.com/iconogen/" target="_blank" rel="noopener" class="bbc_link">here</a>, or use a special generator (it needs to enter the API key on the field above).<br>Then upload the favicon files to the forum root, and save the code from the generator site in the field on the right.<br>This code will be load at the top of the site pages, between the &lt;head&gt;&lt;/head&gt; tags.';

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

$txt['optimus_head_code']       = 'Invisible counters loading on <strong>head</strong> section (<a href="https://www.google.com/analytics/sign_up.html" target="_blank" rel="noopener" class="bbc_link">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Other invisible counters (<a href="https://matomo.org/" target="_blank" rel="noopener" class="bbc_link">Matomo</a> etc)';
$txt['optimus_count_code']      = 'Visible counters (<a href="http://www.prtracker.com/FreeCounter.html" target="_blank" rel="noopener" class="bbc_link">PRTracker</a> etc)';
$txt['optimus_counters_css']    = 'Appearance for visible counters (CSS)';
$txt['optimus_ignored_actions'] = 'Ignored actions';

$txt['optimus_robots_title'] = 'Customizing a robots.txt';
$txt['optimus_robots_desc']  = 'The rule generator is updated depending on the installed mods and some settings of your SMF.';

$txt['optimus_manage']      = 'Manage robots.txt';
$txt['optimus_root_path']   = 'Path to the site root directory';
$txt['optimus_rules']       = 'Rule generator';
$txt['optimus_rules_hint']  = 'You can use these rules as an example for your robots.txt (on the right textarea):';
$txt['optimus_useful']      = '';
$txt['optimus_links_title'] = 'Useful links';
$txt['optimus_links']       = array(
	'Create a robots.txt file'              => 'https://support.google.com/webmasters/answer/6062596?hl=en',
	'Using robots.txt'                      => 'https://help.yandex.com/webmaster/?id=1113851',
	'Technical audit of the entire website' => 'https://goo.gl/itx8Fp'
);




$txt['optimus_sitemap_title'] = 'Sitemap';
$txt['optimus_sitemap_desc']  = 'Do you want a simple sitemap? Optimus can generate a XML-map for forums of any size. Just enable this option below.';

$txt['optimus_sitemap_enable']                  = 'Activate the Sitemap area';
$txt['optimus_sitemap_link']                    = 'Show the Sitemap link on the footer';
$txt['optimus_main_page_frequency']             = 'The update frequency of the main page';
$txt['optimus_main_page_frequency_set']         = array('Constant (always)', 'Depending on the date of the last message');
$txt['optimus_sitemap_boards']                  = 'Add links to boards to the sitemap';
$txt['optimus_sitemap_boards_subtext']          = 'Boards that closed to guests will NOT be added.';
$txt['optimus_sitemap_topics_num_replies']      = 'Add to the sitemap only those topics that have the number of replies is more than';
$txt['optimus_sitemap_items_display']           = 'Maximum number items to display (in XML-file)';
$txt['optimus_sitemap_all_topic_pages']         = 'Add ALL topic pages to the sitemap';
$txt['optimus_sitemap_all_topic_pages_subtext'] = 'If not checked, only the first pages of topics will be added to the sitemap.';

$txt['optimus_404_page_title']       = '404 - Page not found';
$txt['optimus_404_h2']               = 'Error 404';
$txt['optimus_404_h3']               = 'Sorry, but the requested page does not exist.';
$txt['optimus_403_page_title']       = '403 - Access forbidden';
$txt['optimus_403_h2']               = 'Error 403';
$txt['optimus_403_h3']               = 'Sorry, but you have no access to this page.';
$txt['optimus_seo_description']      = 'Topic description [SEO]';
$txt['optimus_seo_keywords']         = 'Topic keywords [SEO]';
$txt['optimus_enter_keywords']       = 'Enter one or more keywords';
$txt['optimus_topics_with_keyword']  = 'Forum topics with keyword "%s"';
$txt['optimus_keyword_id_not_found'] = 'The specified keyword ID was not found.';
$txt['optimus_no_keywords']          = 'There is no information about this keyword identifier.';
$txt['optimus_all_keywords']         = 'All keywords in the forum topics';
$txt['optimus_keyword_column']       = 'Keyword';
$txt['optimus_frequency_column']     = 'Frequency';
