<?php

$txt['optimus_title'] = 'Search Engine Optimization';

$txt['optimus_basic_title'] = 'Base settings';
$txt['optimus_basic_desc'] = 'The mod version: <strong>%1$s</strong>, PHP version: <strong>%2$s</strong>, %3$s version: <strong>%4$s</strong>.<br>One can discuss bugs and features of the mod at <a class="bbc_link" href="https://www.simplemachines.org/community/index.php?topic=422210.0">simplemachines.org</a>.';

$txt['optimus_main_page'] = 'Homepage';
$txt['optimus_forum_index'] = 'Forum Homepage title';
$txt['optimus_description'] = 'The forum description';
$txt['optimus_description_subtext'] = 'It will be used as content of the meta-tag <strong>description</strong>.';

$txt['optimus_all_pages'] = 'Topic & board pages';
$txt['optimus_board_extend_title'] = 'Add forum name to board titles';
$txt['optimus_board_extend_title_set'] = array('Don\'t add', 'Before board title', 'After board title');
$txt['optimus_topic_extend_title'] = 'Add title of section and forum to topic titles';
$txt['optimus_topic_extend_title_set'] = array('Don\'t add', 'Before topic title', 'After topic title');
$txt['optimus_topic_description'] = 'Display the topic first message snippet as the meta-tag <strong>description</strong>';
$txt['optimus_allow_change_topic_desc'] = 'Allow a separate field for the topic description';
$txt['optimus_allow_change_topic_desc_subtext'] = 'It is displayed when editing a topic.';
$txt['optimus_allow_change_topic_keywords'] = 'Allow a separate field for the topic keywords';
$txt['optimus_allow_change_topic_keywords_subtext'] = 'It is displayed when editing a topic.';
$txt['optimus_show_keywords_block'] = 'Display a block with keywords above the first post of the topic';
$txt['optimus_show_keywords_on_message_index'] = 'Display keywords in topic lists within boards';
$txt['optimus_allow_keyword_phrases'] = 'Allow to add entire key phrases';
$txt['optimus_correct_http_status'] = 'Return <a href="https://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank" rel="noopener" class="bbc_link">403/404 code</a> depending on the requested page\'s status';

$txt['optimus_extra_settings'] = 'Additional settings';
$txt['optimus_log_search'] = 'Enable logging of search terms';
$txt['optimus_disable_syntax_highlighting'] = 'Disable syntax highlighting in text areas';

$txt['optimus_extra_title'] = 'Metadata';
$txt['optimus_extra_desc'] = 'Here you can add an additional <a href="https://ogp.me/" target="_blank" rel="noopener" class="bbc_link">markup</a> for forum pages.';
$txt['optimus_extra_info'] = 'Use <a href="https://webmaster.yandex.ru/tools/microtest/" target="_blank" rel="noopener" class="bbc_link">structured data validator</a> (Yandex.Webmaster) or <a href="https://developers.facebook.com/tools/debug" target="_blank" rel="noopener" class="bbc_link">Facebook Sharing Debugger</a> to debug your Open Graph tags.<hr><strong>Note</strong>: Facebook caches images and other OG data. To reset the cache, in the repost debugger, type the page address with the parameter <em>fbrefresh</em>, i.e. %1$s?fbrefresh=reset.';

$txt['optimus_og_image'] = 'Use the image from the first topic message in the meta tag <strong>og:image</strong>';
$txt['optimus_og_image_subtext'] = 'By default, the image specified in <a href="%s" class="bbc_link">current theme settings</a> is used.';
$txt['optimus_og_image_help'] = 'If enabled, the <strong>og:image</strong> meta tag will include a link to the first image attached to the first topic message. If there aren\'t any attachment, and the image inside the <strong>img</strong> tag is found in the message text, it is used.';
$txt['optimus_allow_change_board_og_image'] = 'Allow a separate field for the board <strong>OG Image</strong>';
$txt['optimus_allow_change_board_og_image_subtext'] = 'It is displayed when editing a board.';
$txt['optimus_fb_appid'] = 'Facebook Application ID (if you have)';
$txt['optimus_fb_appid_help'] = 'Create an application <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener" class="bbc_link"><strong>here</strong></a>, copy its ID and fill this field.';
$txt['optimus_tw_cards'] = 'Twitter account name (if you have)';
$txt['optimus_tw_cards_help'] = 'Read more about Twitter cards <a href="https://dev.twitter.com/cards/overview" target="_blank" rel="noopener" class="bbc_link"><strong>here</strong></a>.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc'] = 'Create your own forum icon. It will be displayed by the browser as an image next to the open tab and other interface elements.';

$txt['optimus_favicon_text'] = 'The favicon code';
$txt['optimus_favicon_help'] = 'Generate your own favicon <a href="https://www.favicomatic.com/" target="_blank" rel="noopener" class="bbc_link">here</a>, or <a href="https://digitalagencyrankings.com/iconogen/" target="_blank" rel="noopener" class="bbc_link">here</a>.<br>Then upload the favicon files to the forum root, and save the code from the generator site in the field on the right.<br>This code will be load at the top of the site pages, between the &lt;head&gt;&lt;/head&gt; tags.';

$txt['optimus_meta_title'] = 'Meta tags';
$txt['optimus_meta_desc'] = 'On this page you can add any regular/verification code(s) from the list below.';

$txt['optimus_meta_addtag'] = 'Click here to add a new tag';
$txt['optimus_meta_customtag'] = 'Custom meta tag';
$txt['optimus_meta_tools'] = 'Search engine (Tool)';
$txt['optimus_meta_name'] = 'Name';
$txt['optimus_meta_content'] = 'Content';
$txt['optimus_meta_info'] = 'Please use only the values from <strong>content</strong> parameter of the meta tags.<br>Example: <span class="smalltext">&lt;meta name="<strong>NAME</strong>" content="<strong>VALUE</strong>"&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','https://www.google.com/webmasters/tools/', 'Google Search Console'),
	'Bing' => array('msvalidate.01', 'https://www.bing.com/toolbox/webmaster/', 'Bing Webmaster'),
	'Yandex' => array('yandex-verification', 'https://webmaster.yandex.com/', 'Yandex.Webmaster'),
);


$txt['optimus_counters'] = 'AdSense/JS code';
$txt['optimus_counters_desc'] = 'You can add and change any JS code in this section to log stats/visits of your forum.';

$txt['optimus_head_code'] = 'Invisible JS with loading in the <strong>head</strong> section';
$txt['optimus_head_code_subtext'] = 'For example, <a href="https://www.google.com/analytics/sign_up.html" target="_blank" rel="noopener" class="bbc_link">Google Analytics</a>, or <a href="https://www.google.com/adsense/start/" target="_blank" rel="noopener" class="bbc_link">Google AdSense</a>';
$txt['optimus_stat_code'] = 'Invisible JS with loading in the <strong>body</strong> section';
$txt['optimus_stat_code_subtext'] = 'For example, <a href="https://matomo.org/" target="_blank" rel="noopener" class="bbc_link">Matomo</a> etc';
$txt['optimus_count_code'] = 'Visible JS (image counters, banners, etc)';
$txt['optimus_counters_css'] = 'Appearance for visible counters (CSS code)';
$txt['optimus_ignored_actions'] = 'Ignored actions';
$txt['optimus_ignored_actions_subtext'] = 'Counters will not be loaded on these areas!';

$txt['optimus_robots_title'] = 'Manage robots.txt';
$txt['optimus_robots_desc'] = 'The rule generator is updated depending on the installed mods and some settings of your SMF.';

$txt['optimus_rules'] = 'Rule generator';
$txt['optimus_rules_hint'] = 'You can use these rules as an example for your robots.txt (on the right textarea):';
$txt['optimus_links_title'] = 'Useful links';
$txt['optimus_links'][0] = array('Create a robots.txt file', 'https://support.google.com/webmasters/answer/6062596?hl=en');
$txt['optimus_links'][1] = array('Using robots.txt', 'https://yandex.com/support/webmaster/controlling-robot/robots-txt.html?lang=en');

$txt['optimus_htaccess_title'] = 'Manage .htaccess';
$txt['optimus_htaccess_desc'] = 'Here you can modify the .htaccess file for your forum. Be careful!';

$txt['optimus_sitemap_title'] = 'Sitemap';
$txt['optimus_sitemap_desc'] = '%1$s can generate a simple XML map in accordance with the settings below.';

$txt['optimus_sitemap_enable'] = 'Activate the Sitemap';
$txt['optimus_sitemap_enable_subtext'] = 'The map will be created/updated after saving the settings.';
$txt['optimus_sitemap_link'] = 'Show the Sitemap link on the footer';
$txt['optimus_remove_previous_xml_files'] = 'Remove previously generated sitemap*.xml files';
$txt['optimus_main_page_frequency'] = 'The update frequency of the main page';
$txt['optimus_main_page_frequency_set'] = array('Constant (always)', 'Depending on the date of the last message');
$txt['optimus_sitemap_boards'] = 'Add links to boards to the Sitemap';
$txt['optimus_sitemap_boards_subtext'] = 'Boards that are closed to guests will NOT be added.';
$txt['optimus_sitemap_topics_num_replies'] = 'Add links to topics that have the number of replies >=';
$txt['optimus_sitemap_items_display'] = 'Maximum number of items per page';
$txt['optimus_sitemap_all_topic_pages'] = 'Add ALL topic pages to the sitemap';
$txt['optimus_sitemap_all_topic_pages_subtext'] = 'If not checked, only the first pages of topics will be added to the sitemap.';
$txt['optimus_start_year'] = 'The Sitemap must contain entries starting from the specified year';
$txt['optimus_update_frequency'] = 'How often the Sitemap is updated';
$txt['optimus_update_frequency_set'] = array('Once a day', 'Every 3 days', 'Once a week', 'Every 2 weeks', 'Once a month');

$txt['optimus_mobile'] = 'Mobile';
$txt['optimus_images'] = 'Images';
$txt['optimus_news'] = 'News';
$txt['optimus_video'] = 'Video';
$txt['optimus_index'] = 'Index';
$txt['optimus_total_files'] = 'Total files';
$txt['optimus_total_urls'] = 'Total URLs';
$txt['optimus_last_modified'] = 'Last Modified';
$txt['optimus_frequency'] = 'Frequency';
$txt['optimus_priority'] = 'Priority';
$txt['optimus_direct_link'] = 'Direct link';
$txt['optimus_caption'] = 'Caption';
$txt['optimus_thumbnail'] = 'Thumbnail';

$txt['permissionname_optimus_add_descriptions'] = $txt['group_perms_name_optimus_add_descriptions'] = 'Adding descriptions for topics';
$txt['permissionhelp_optimus_add_descriptions'] = 'Ability to add a description when creating/editing a topic.';
$txt['permissionname_optimus_add_keywords'] = $txt['group_perms_name_optimus_add_keywords'] = 'Adding keywords for topics';
$txt['permissionhelp_optimus_add_keywords'] = 'Ability to add keywords when creating/editing a topic.';
$txt['permissionname_optimus_add_descriptions_own'] = $txt['permissionname_optimus_add_keywords_own'] = 'Own topic';
$txt['permissionname_optimus_add_descriptions_any'] = $txt['permissionname_optimus_add_keywords_any'] = 'Any topic';
$txt['group_perms_name_optimus_add_descriptions_own'] = 'Add descriptions for own topics';
$txt['group_perms_name_optimus_add_descriptions_any'] = 'Add descriptions for any topics';
$txt['permissionname_optimus_view_search_terms'] = $txt['group_perms_name_optimus_view_search_terms'] = 'View the statistics of search terms';
$txt['permissionhelp_optimus_view_search_terms'] = 'Ability to view search statistics on the forum.';

$txt['optimus_404_page_title'] = '404 - Page not found';
$txt['optimus_404_h2'] = 'Error 404';
$txt['optimus_404_h3'] = 'Sorry, but the requested page does not exist.';
$txt['optimus_403_page_title'] = '403 - Access forbidden';
$txt['optimus_403_h2'] = 'Error 403';
$txt['optimus_403_h3'] = 'Sorry, but you have no access to this page.';
$txt['optimus_goto_main_page'] = 'Go to the <a class="bbc_link" href="%1$s">main page</a>.';
$txt['optimus_seo_description'] = 'Topic description [SEO]';
$txt['optimus_seo_keywords'] = 'Topic keywords [SEO]';
$txt['optimus_enter_keywords'] = 'Enter one or more keywords';
$txt['optimus_topics_with_keyword'] = 'Forum topics with keyword "%s"';
$txt['optimus_keyword_id_not_found'] = 'The specified keyword ID was not found.';
$txt['optimus_no_keywords'] = 'There is no information about this keyword identifier.';
$txt['optimus_all_keywords'] = 'All keywords in the forum topics';
$txt['optimus_keyword_column'] = 'Keyword';
$txt['optimus_frequency_column'] = 'Frequency';
$txt['optimus_top_queries'] = 'Popular search queries';
$txt['optimus_chart_title'] = 'Top %1$s';
$txt['optimus_no_search_terms'] = 'Statistics are not yet available.';
