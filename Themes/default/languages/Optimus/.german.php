<?php

/**
 * .german language file
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 * @german translation by Feline
 */

$txt['optimus_main']  = 'Optimus';
$txt['optimus_title'] = 'Suchmaschinen Optimierung';

$txt['optimus_base_title'] = 'Grundeinstellungen';
$txt['optimus_base_desc'] = 'Auf dieser Seite können Sie eine Forum-Beschreibung ändern, Seitenvorlagen-Vorlagen verwalten, XML-Sitemap aktivieren/deaktivieren.';

$txt['optimus_main_page'] = 'Homepage';
$txt['optimus_forum_index'] = 'Forum Homepage Titel';
$txt['optimus_description'] = 'Die Anmerkung (Annotation) des Forums<br><span class="smalltext">Wird als Inhalt des Meta-Tags <strong>description</strong> verwendet.</span>';

$txt['optimus_all_pages'] = 'Themen- und Board-Seiten';
$txt['optimus_board_extend_title'] = 'Forenname zu Boardname hinzufügen';
$txt['optimus_board_extend_title_set'] = array('None', 'Vor dem Boardnamen', 'Nach dem Boardnamen');
$txt['optimus_topic_extend_title'] = 'Seitentitel und Forumname den Thementiteln hinzufügen';
$txt['optimus_topic_extend_title_set'] = array('None', 'Vorher Thementitel', 'Nach Thementitel');
$txt['optimus_topic_description'] = 'Zeigt das erste Nachrichten Schnipsel des Themas als Meta-Tag <strong>Beschreibung</strong> an';
$txt['optimus_404_status'] = 'Gibt 403/404 Code zurück, abhängig vom Status der angeforderten Seite';
$txt['optimus_404_status_help'] = 'Wenn diese Option aktiviert ist, wird der entsprechende Fehlercode (404 oder 403) zurückgegeben, wenn eine Seite angefordert wird, die nicht existiert oder nicht erlaubt ist. Details siehe <a href="https://de.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank" rel="noopener"><strong>hier</strong></a>.';
$txt['optimus_404_page_title'] = '404 - Seite nicht gefunden';
$txt['optimus_404_h2'] = 'Fehler 404';
$txt['optimus_404_h3'] = 'Entschuldigung, aber die angeforderte Seite existiert nicht.';
$txt['optimus_403_page_title'] = '403 - Zugriff verboten';
$txt['optimus_403_h2'] = 'Fehler 403';
$txt['optimus_403_h3'] = 'Entschuldigung, aber Sie haben keinen Zugriff auf diese Seite.';

$txt['optimus_extra_title'] = 'Metadaten';
$txt['optimus_extra_desc'] = 'Hier können Sie ein zusätzliches <a href="http://ogp.me/" target="_blank" rel="noopener"><strong>Markup</strong></a> für Forenseiten hinzufügen.';

$txt['optimus_og_image'] = 'Verwenden Sie das Bild aus der ersten Nachricht im Metatag <strong>og:image</strong>';
$txt['optimus_og_image_help'] = 'Wenn aktiviert, enthält das <strong>og:image</strong> Meta-Tag einen Link zum ersten Bild, das an die erste Nachricht angehängt ist. Wenn kein Anhang vorhanden ist, das Bild aber als <strong>img</strong>-Tag im Nachrichtentext vorhanden ist, wird dieses verwendet.';
$txt['optimus_fb_appid'] = 'Facebook Application ID (falls vorhanden)';
$txt['optimus_fb_appid_help'] = 'Erstellen Sie eine Anwendung <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener"><strong>hier</strong></a>, kopieren Sie ihre ID und fülle dieses Feld aus.';
$txt['optimus_tw_cards'] = 'Twitter account name (falls vorhanden)';
$txt['optimus_tw_cards_help'] = 'Lesen Sie mehr über Twitter-Karten <a href="https://dev.twitter.com/cards/overview" target="_blank" rel="noopener"><strong>hier</strong></a>.';
$txt['optimus_json_ld'] = 'JSON-LD Markup für "Breadcrumbs"';
$txt['optimus_json_ld_help'] = 'JSON-LD ist ein kompaktes Linked Data Format. Es ist einfach für Menschen zu lesen und zu schreiben. Es basiert auf dem bereits erfolgreichen JSON-Format und bietet eine Möglichkeit, JSON-Daten im Web-Maßstab zu interoperieren. JSON-LD ist ein ideales Datenformat für Programmierumgebungen, Webservices und unstrukturierte Datenbanken wie CouchDB und MongoDB.<br><br>Aktivieren Sie diese Option, um JSON-LD-Markup für "<a href ="https://developers.google.com/search/docs/data-types/breadcrumbs?hl='. $txt['lang_dictionary']. '" target="_ blank" rel= noopener"><strong>Breadcrumps</strong></a>" zu verwenden.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc'] = 'Erstelle dein eigenes Forumsymbol. Es wird vom Browser in der Registerkarte vor dem Seitennamen sowie einem Bild neben der geöffneten Registerkarte und anderen Schnittstellenelementen angezeigt.';

$txt['optimus_favicon_create'] = 'Erzeuge das Favicon';
$txt['optimus_favicon_api_key'] = 'API Schlüssel um mit dem Favicon Generator zu arbeiten, hier den (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank" rel="noopener"><strong>API Schlüssel holen</strong></a>)';
$txt['optimus_favicon_text'] = 'Der Favicon-Code';
$txt['optimus_favicon_help'] = 'Generiere dein eigenes Favicon <a href="http://www.favicomatic.com/" target="_blank" rel="noopener"><strong>hier</strong></a> oder verwende ein spezielles Generator (es muss den API-Schlüssel in das obige Feld eingeben werden). Laden Sie dann die Favicon-Dateien in das Forum-Stammverzeichnis hoch und speichern Sie den Code von der Generator-Site im Feld rechts. Dieser Code wird oben auf den Seiten der Website zwischen dem &lt;head&gt; &lt;/head&gt; eingefügt.';

$txt['optimus_meta_title'] = 'Meta-Tags';
$txt['optimus_meta_desc'] = 'Auf dieser Seite können Sie alle regulären Bestätigungscodes aus der Liste unten hinzufügen.';

$txt['optimus_meta_addtag'] = 'Klicken Sie hier, um einen neuen Tag hinzuzufügen';
$txt['optimus_meta_customtag'] = 'Benutzerdefinierter Meta-Tag';
$txt['optimus_meta_tools'] = 'Suchmaschine (Tools)';
$txt['optimus_meta_name'] = 'Name';
$txt['optimus_meta_content'] = 'Inhalt';
$txt['optimus_meta_info'] = 'Bitte verwenden Sie nur die Werte des <strong>content</strong> Parameters der Metatags.<br>Beispiel: <span class="smalltext">&lt;meta name="<strong>NAME</strong>" content="<strong>VALUE</strong>"&gt;</span>';
$txt['optimus_search_engines'] = array(
'Google' => array('google-site-verification', '<a href="https://www.google.com/webmasters/tools/" target="_blank" rel="noopener"><strong>Google Search Console</strong></a>'),
'Yandex' => array('yandex-verification', '<a href="https://webmaster.yandex.com/" target="_blank" rel="noopener"><strong>.</strong></a>') ,
'Bing' => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank" rel="noopener"><strong> </strong></a>')
);

$txt['optimus_counters'] = 'Zähler';
$txt['optimus_counters_desc'] = 'Sie können in diesem Bereich Zähler hinzufügen und ändern, um Besuche Ihres Forums zu protokollieren.';

$txt['optimus_head_code'] = 'Unsichtbare Zähler werden im <strong>Kopf</strong> geladen (<a href="https://www.google.com/analytics/sign_up.html" target="_ blank" rel="noopener"><strong>Google Analytics</strong></a>)';
$txt['optimus_stat_code'] = 'Andere unsichtbare Zähler (<a href="https://matomo.org/" target="_blank" rel="noopener"><strong>Matomo</strong></a> usw.)';
$txt['optimus_count_code'] = 'Sichtbare Zähler (<a href="http://www.freestats.com/" target="_blank" rel="noopener"><strong>FreeStats</strong></a>, <a href="http://www.superstats.com/ "target="_ blank" rel="noopener"><strong>SuperStats</strong></a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank "rel="noopener"><strong>PRTracker</strong></a> usw.)';
$txt['optimus_counters_css'] = 'Aussehen für sichtbare Zähler (CSS)';
$txt['optimus_ignored_actions'] = 'Ignorierte Aktionen';

$txt['optimus_robots_title'] = 'Editor robots.txt';
$txt['optimus_robots_desc'] = 'Auf dieser Seite können Sie einige Optionen zum Erstellen von Foren-Maps ändern und eine robots.txt-Datei mit einem speziellen Generator ändern.';

$txt['optimus_manage'] = 'Bearbeiten der robots.txt';
$txt['optimus_rules'] = 'Robots.txt Generator';
$txt['optimus_rules_hint'] = 'Sie können diese Regeln in das Feld rechts kopieren:';
$txt['optimus_robots_hint'] = 'Hier können Sie Ihre eigenen Regeln einfügen oder bestehende ändern';
$txt['optimus_useful'] = '';
$txt['optimus_links_title'] = 'Nützliche Links';
$txt['optimus_links'] = array(
'Erstellen Sie eine robots.txt Datei' => 'https://support.google.com/webmasters/answer/6062596?hl=de',
'Verwenden von robots.txt' => 'https://help.yandex.com/webmaster/?id=1113851',
"Technische Überprüfung der gesamten Website" => "https://netpeaksoftware.com/ucp?invite=94cdaf6a"
);

$txt['optimus_sitemap_title'] = 'Optimus Sitemap';
$txt['optimus_sitemap_desc'] = 'Möchten Sie eine einfache Sitemap? Optimus kann sitemap.xml für kleine Foren generieren. Aktivieren Sie einfach diese Option. Diese Sitemap wird abhängig von den Einstellungen im <a href="%1$s"><strong>Task-Manager</strong></a> aktualisiert.';

$txt['optimus_sitemap_enable'] = 'Sitemap-XML-Datei erstellen und regelmäßig aktualisieren';
$txt['optimus_sitemap_link'] = 'Sitemap XML-Link in der Fußzeile anzeigen';
$txt['optimus_sitemap_boards'] = 'Hinzufügen von Board-Links zur Sitemap<br><span class="smalltext error">Boards, die für Gäste geschlossen sind, werden NICHT hinzugefügt.</span>';
$txt['optimus_sitemap_topics'] = 'Fügen Sie der Sitemap nur die Themen hinzu, bei denen die Anzahl der Beiträge größer ist als';

$txt['optimus_sitemap_rec'] = 'Optimus kann Dateien nicht in mehrere Teile aufteilen.';
$txt['optimus_sitemap_url_limit'] = 'Die Sitemap-Datei darf nicht mehr als 50.000 URLs haben!';
$txt['optimus_sitemap_size_limit'] = '%1$s Datei darf nicht größer als 10MB sein!';
$txt['optimus_sitemap_xml_link'] = 'Sitemap XML';

$txt['optimus_donate_title'] = 'Spenden';
$txt['optimus_donate_desc'] = 'Von hier können Sie Spenden an den Mod-Autor senden.';
$txt['optimus_donate_info'] = 'Hier können Sie den Entwickler mit Ihrer Spende unterstützen ;)';

// Taskmanager
$txt['planed_task_optimus_sitemap'] = 'Optimus XML-Sitemap';
$txt['planed_task_desc_optimus_sitemap'] = 'Sie können die Häufigkeit der Erstellung der Sitemap festlegen.';
?>