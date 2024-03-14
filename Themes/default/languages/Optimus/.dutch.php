<?php

/**
 * dutch language file
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_title'] = 'Zoekmachine Optimalisatie';

$txt['optimus_base_title'] = 'Algemene instellingen';
$txt['optimus_base_desc'] = 'Versie van de Mod: <strong>%1$s</strong>, PHP versie: <strong>%2$s</strong>, %3$s versie: <strong>%4$s</strong>.<br>Bugs en features van de mod kunnen besproken worden op <a class="bbc_link" href="https://www.simplemachines.org/community/index.php?topic=422210.0">simplemachines.org</a>.<br>Je kunt ook <a class="bbc_link" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SJLXR6X7XGEDC">een eenmalige donatie doen via PayPal</a>.';

$txt['optimus_main_page'] = 'Homepage';
$txt['optimus_base_info'] = 'Inhoud van de \'description tag\' kan worden gebruikt door een robot om te bepalen of een pagina aan de zoektermen voldoet.';
$txt['optimus_forum_index'] = 'Forum homepage titel';
$txt['optimus_description'] = 'Een korte maar pakkende forum beschrijving<br /><span class="smalltext">Wordt gebruikt als inhoud van de meta-tag <em>\'description\'</em>.</span>';

$txt['optimus_all_pages'] = 'Topic & board pagina instellingen';
$txt['optimus_tpl_info'] = 'Mogelijke variabelen:<br/><strong>{board_name}</strong> &mdash; board naam, <strong>{topic_name}</strong> &mdash; topic onderwerp,<br/><strong>{#}</strong> &mdash; huidige pagina nummer, <strong>{cat_name}</strong> &mdash; categorie naam, <strong>{forum_name}</strong> &mdash; jouw forum naam.';
$txt['optimus_board_tpl'] = 'Template voor board pagina titel';
$txt['optimus_topic_tpl'] = 'Template voor topic pagina titel';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - pagina {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - pagina {#} - ', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number'] = 'Toon geen nummer voor een eerste bladzijde';
$txt['optimus_board_description'] = 'Gebruik de board omschrijving voor meta-tag <strong>description</strong>';
$txt['optimus_topic_description'] = 'Gebruik de topic omschrijving voor meta-tag <strong>description</strong><br /><span class="smalltext">Gebruik <a class="bbc_link" href="http://custom.simplemachines.org/mods/index.php?mod=3012" target="_blank">Topic Descriptions mod</a> om een korte beschrijving voor topics aan te leggen.</span>';
$txt['optimus_404_status'] = 'Geef <a class="bbc_link" href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">403/404 code</a> afhankelijk van de status van de opgevraagde pagina';
$txt['optimus_404_page_title'] = '404 - Pagina niet gevonden';
$txt['optimus_404_h2'] = 'Error 404';
$txt['optimus_404_h3'] = 'Sorry, de gevraagde pagina bestaat niet.';
$txt['optimus_403_page_title'] = '403 - Geen toegang';
$txt['optimus_403_h2'] = 'Error 403';
$txt['optimus_403_h3'] = 'Sorry, maar je hebt geen toegang tot deze pagina.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc'] = 'Maak je eigen forum icoon. Dit zal getoond worden in de brwoser tab voor de naam, samen met een afbeelding naast de geopende tab en verdere visuele elementen.';

$txt['optimus_favicon_text'] = 'De favicon code';
$txt['optimus_favicon_help'] = '<hr />Genereer je eigen favicon <a class="bbc_link" href="http://www.favicomatic.com/" target="_blank">hier</a>. Plaats dan de favicon bestanden in de forum root, en bewaar de code van de de generator site in het veld rechts. Deze code zal laden bovenaan de site paginas, tussen de &lt;head&gt;&lt;/head&gt; tags.';

$txt['optimus_extra_title'] = 'Metadata';
$txt['optimus_extra_desc'] = 'Hier kun je wat aanpassingen voor je forum vinden. Verder is het mogelijk Open Graph en JSON-LD ondersteuning in te schakelen. Enjoy!';

$txt['optimus_open_graph'] = 'Activeer Open Graph ondersteuning';
$txt['optimus_og_image'] = 'Link naar je standaard Open Graph afbeelding<br /><span class="smalltext">Deze wordt vervangen door de eerste afbeeldings bijlage uit het eerste bericht van het topic (indien aanwezig).</span>';
$txt['optimus_fb_appid'] = '<a class="bbc_link" href="https://developers.facebook.com/apps" target="_blank">APP ID</a> (Application ID) <a class="bbc_link" href="https://www.facebook.com/" target="_blank">Facebook</a> (indien aanwezig)';
$txt['optimus_tw_cards'] = '<a class="bbc_link" href="https://twitter.com/" target="_blank">Twitter</a> account naam (geef deze op voor <a class="bbc_link" href="https://dev.twitter.com/cards/overview" target="_blank">Twitter Cards</a>)';

$txt['optimus_meta_title'] = 'Meta tags';
$txt['optimus_meta_desc'] = 'Op deze pagina kun je algemene/verificatie code(s) van ondersteende lijst onderhouden.';

$txt['optimus_meta_addtag'] = 'Klik hier om een nieuwe tag toe te voegen';
$txt['optimus_meta_customtag'] = 'Aangepaste meta tag';
$txt['optimus_meta_tools'] = 'Zoek machine (Tools)';
$txt['optimus_name'] = 'Naam';
$txt['optimus_content'] = 'Inhoud';
$txt['optimus_meta_info'] = 'Gebruik alleen de waarden van de <strong>inhoud</strong> parameter van de meta tags.<br />Bijvoorbeeld: <span class="smalltext">&lt;meta naam="google-site-verification" inhoud="<strong>WAARDE DIE IN DE RECHTER KOLOM MOET STAAN</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a class="bbc_link" href="https://www.google.com/webmasters/tools/" target="_blank">Google Search Console</a>'),
	'Yandex' => array('yandex-verification', '<a class="bbc_link" href="https://webmaster.yandex.com/" target="_blank">Yandex.Webmaster</a>'),
	'Bing' => array('msvalidate.01', '<a class="bbc_link" href="https://www.bing.com/toolbox/webmaster/" target="_blank">Bing Webmaster</a>')
);

$txt['optimus_counters'] = 'Counters';
$txt['optimus_counters_desc'] = 'Voor het bijhouden van bezoeken aan je forum is het mogelijk een varieteit aan counters in te voeren.';

$txt['optimus_head_code'] = 'Onzichtbare counters die geladen worden in de <strong>head</strong> sectie (<a class="bbc_link" href="https://www.google.com/analytics/sign_up.html" target="_blank">Google Analytics</a>)';
$txt['optimus_stat_code'] = 'Andere onzichtbare counters';
$txt['optimus_count_code'] = 'Zichtbare counters';
$txt['optimus_counters_css'] = 'Weergave van zichtbare counters (CSS code)';
$txt['optimus_ignored_actions'] = 'Acties die genegeerd worden';

$txt['optimus_robots_title'] = 'Wijzig robots.txt';
$txt['optimus_robots_desc'] = 'Op deze pagina kun je sommige instellingen voor het maken van de forum sitemap, alsmede een robots.txt bestand onderhouden middels een speciale generator.';

$txt['optimus_manage'] = 'Beheer robots.txt';
$txt['optimus_rules'] = 'Robots.txt Generator';
$txt['optimus_rules_hint'] = 'Je kunt deze regels kopieren naar het veld aan de rechterzijde:';
$txt['optimus_robots_hint'] = 'Hier kun je eigen regels invoeren of bestaande regels aanpassen:';
$txt['optimus_useful'] = '';

$txt['optimus_sitemap_title'] = 'Optimus Sitemap';
$txt['optimus_sitemap_desc'] = 'Wil je een eenvoudige sitemap aanmaken? Optimus kan het sitemap.xml aanmaken voor minder omvangrijke fora. Activeer hiervoor onderstaande optie. De sitempa zal wordne bijgewerkt volgens de instellingen in de <a href="%1$s">Geplande taken</a>.';

$txt['optimus_sitemap_enable'] = 'Activeer de Sitemap instellingen';
$txt['optimus_sitemap_link'] = 'Toon een link naar de Sitemap in de footer';
$txt['optimus_remove_previous_xml_files'] = 'Verwijder eerder aangemaakte sitemap*.xml bestanden';
$txt['optimus_main_page_frequency'] = 'De vernieuwings frequentie van de hoofd pagina';
$txt['optimus_main_page_frequency_set'] = array('Direct (voortdurend)', 'Afhankelijk van de datum van het nieuwste bericht');
$txt['optimus_sitemap_boards'] = 'Neem links naar de boards op in de sitemap';
$txt['optimus_sitemap_boards_subtext'] = 'Boards die niet toegankelijk zijn voor gasten worden NIET opgenomen.';
$txt['optimus_sitemap_topics_num_replies'] = 'Voeg alleen topics aan de sitemap toe die meer dan zoveel reacties >=';
$txt['optimus_sitemap_items_display'] = 'Maximum aantal items per pagina';

// Task Manager
$txt['scheduled_task_optimus_sitemap'] = 'Sitemap XML verversen';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Je kunt de frequentie voor het verversen van de sitemap opgeven.';