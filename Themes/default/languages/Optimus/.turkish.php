<?php

/**
 * Turkish translation by snrj (http://smf.konusal.com)
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_main'] = 'Optimus';
$txt['optimus_title'] = 'Arama Motoru Optimizasyonu';

$txt['optimus_base_title'] = 'Temel ayarlar';
$txt['optimus_base_desc'] = 'Bu sayfada bir forum a��klamas�n� de�i�tirebilir, sayfa ba�l�klar�n�n �ablonlar�n� y�netebilir, Site Haritas� XML neslini etkinle�tirebilir / devre d��� b�rakabilirsiniz.';


$txt['optimus_main_page'] = 'AnaSayfa';
$txt['optimus_base_info'] = 'Peki, e�er robot bir sayfa ile bir arama sorgusunun e�le�ti�ini belirlerse a��klama etiketi i�eri�i dikkate al�nabilir.';
$txt['optimus_portal_compat'] = 'Portal Uyumlulu�u';
$txt['optimus_portal_compat_set'] = array('Hi�biri', 'PortaMx', 'SimplePortal', 'TinyPortal');
$txt['optimus_portal_index'] = 'Portal Anasayfa ba�l���';
$txt['optimus_forum_index'] = 'Forum Anasayfa ba�l���';
$txt['optimus_description'] = 'Forum ek a��klamas� <br /> <span class = "smalltext"> Meta etiketin i�eri�i olarak kullan�lacak <strong> a��klama </ strong>. </ span>';

$txt['optimus_all_pages'] = 'Konu ve y�netim sayfalar�';
$txt['optimus_tpl_info'] = 'Olas� de�i�kenler: <br/> <strong> {board_name} </ strong> & mdash; b�l�m ad�, <strong> {topic_name} </ strong> & mdash; konu ba�l���, <br/> <strong> {#} </ strong> & mdash; ge�erli sayfa numaras�, <strong> {cat_name} </ strong> & mdash; kategori ad�, <strong> {forum_name} </ strong> & mdash; forumunuzun ad�. ';
$txt['optimus_board_tpl'] = 'B�l�m sayfalar�n�n ba�l�k �ablonu';
$txt['optimus_topic_tpl'] = 'Konu sayfalar�n�n ba�l�k �ablonu';
$txt['optimus_templates'] = array(
'board' => array ('{board_name}', '- sayfa {#} -', '{forum_name}'),
'topic' => array ('{topic_name}', '- sayfa {#} -', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number'] = '�lk sayfan�n numaras�n� g�sterme';
$txt['optimus_board_description'] = 'B�l�m a��klamas�n� <strong> a��klama </ strong> meta-etikete g�nder';
$txt['optimus_topic_description'] = 'Konunun ilk mesaj�n�n ilk c�mlesini meta-etikete g�nder <strong> description </ strong> <br /> <span class = "smalltext"> Use <a href = "https: //custom.simplemachines .org / mods / index.php? mod = 3012 "target =" _ blank "> Konular�n k�sa a��klamalar�n� olu�turmak i�in </a> Konu A��klama modlar�. </ span> ';
$txt['optimus_404_status'] = '�stenen sayfa durumuna g�re <a href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">403/404 kodunu</a> getir ';
$txt['optimus_404_page_title'] = '404 - Sayfa bulunamad�';
$txt['optimus_404_h2'] = 'Hata 404';
$txt['optimus_404_h3'] = '�zg�n�z, ancak istenen sayfa mevcut de�il.';
$txt['optimus_403_page_title'] = '403 - Eri�im yasak';
$txt['optimus_403_h2'] = 'Hata 403';
$txt['optimus_403_h3'] = '�zg�n�z, ancak bu sayfaya eri�iminiz yok.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc'] = 'Kendi forum ikonunuzu olu�turun. Taray�c�, sayfa ad�n�n �n�ndeki sekmede yan� s�ra a��k sekmenin ve di�er arabirim ��elerinin yan�ndaki bir g�r�nt�de g�r�nt�lenecektir. ';

$txt['optimus_favicon_create'] = 'Favicon olu�turun';
$txt['optimus_favicon_api_key'] = 'Favicon Generator ile �al��mak i�in API anahtar� (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank"> API anahtar� al </a>)' ;
$txt['optimus_favicon_text'] = 'Favicon kodu';
$txt['optimus_favicon_help'] = 'Kendi favicon\'�n�z� olu�turun <a href="http://www.favicomatic.com/" target="_blank"> buray� </a> kullan�n veya �zel bir jenerat�r kullan�n. Yukar�daki alandaki API anahtar�n� girin. <br /> Ard�ndan, favicon dosyalar�n� forum k�k�ne y�kleyin ve kodu sa�daki alana jenerat�r sitesinden kaydedin. <br /> Bu kod, site sayfalar�n�n en �st�nde, &lt;head&gt;&lt;/head&gt; etiketleri ';

$txt['optimus_extra_title'] = 'Meta veri';
$txt['optimus_extra_desc'] = 'Burada forumunuz i�in baz� d�zeltmeleri bulabilirsiniz. Ayr�ca A��k Grafik ve JSON-LD deste�ini etkinle�tirebilirsiniz. Keyfini ��kar�n!';

$txt['optimus_open_graph'] = '<a href="http://ogp.me/" target="_blank"> A��k Grafikler </a> forum sayfalar� i�in meta etiketler';
$txt['optimus_og_image'] = 'Varsay�lan A��k Grafik resminize ba�lant� <br /> <span class = "smalltext"> �lk mesaj�n ekleri (e�er varsa) ba�l�klar�nda yer alacak. </ span> ';
$txt['optimus_fb_appid'] = '<a href="https://developers.facebook.com/apps" target="_blank"> APP ID </a> (Uygulama Kimli�i) <a href = "https: / /www.facebook.com/ "target =" _ blank "> Facebook </a> ';
$txt['optimus_tw_cards'] = '<a href="https://twitter.com/" target="_blank"> Twitter </a> hesap ad� (<a href = "https: // dev etkinle�tirmeyi belirtin) .twitter.com / cards / overview "target =" _ blank "> Twitter Kartlar� </a>) ';
$txt['optimus_json_ld'] = '<a href="https://json-ld.org/" target="_blank"> JSON-LD </a> "<a href =" https: // i�in bi�imlendirme developers.google.com/search/docs/data-types/breadcrumbs?hl= '. $txt['lang_dictionary']. '"target =" _ blank "> ekmek k�r�nt�lar� </a>"';

$txt['optimus_meta_title'] = 'Meta etiketler';
$txt['optimus_meta_desc'] = 'Bu sayfada a�a��daki listeden herhangi bir normal / do�rulama kodu ekleyebilirsiniz.';

$txt['optimus_meta_addtag'] = 'Yeni bir etiket eklemek i�in buraya t�klay�n';
$txt['optimus_meta_customtag'] = '�zel meta etiketi';
$txt['optimus_meta_tools'] = 'Arama motoru (Ara�lar)';
$txt['optimus_meta_name'] = '�sim';
$txt['optimus_meta_content'] = '��erik';
$txt['optimus_meta_info'] = ' L�tfen sadece <strong>content</strong> meta etiketinin parametre de�erlerini kullan�n.<br />�rne�in: <span class="smalltext">&lt;meta name="google-site-verification" content="<strong>SA� S�TUNA YAPI�TIRMANIZ GEREKEN DE�ER</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
'Google' => array('google-site-verification', '<a href="https://www.google.com/webmasters/tools/" target="_blank"> Google Search Console </a>' ),
'Yandex' => array('yandex-do�rulama', '<a href="https://webmaster.yandex.com/" target="_blank"> Yandex.Webmaster </a>'),
'Bing' => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank"> Bing Web Y�neticisi </a>')
);

$txt['optimus_counters'] = 'Saya�lar';
$txt['optimus_counters_desc'] = 'Forumunuzun ziyaretlerini g�nl��e kaydetmek i�in bu b�l�mdeki saya�lar� ekleyebilir ve de�i�tirebilirsiniz.';

$txt['optimus_head_code'] = '<strong> head </ strong> b�l�m�nde g�r�nmez saya�lar y�kleniyor (<a href="https://www.google.com/analytics/sign_up.html" target="_blank"> Google Analytics </a>) ';
$txt['optimus_stat_code'] = 'Di�er g�r�nmez say�c�lar (<a href="https://matomo.org/" target="_blank"> Matomo </a> vs.)';
$txt['optimus_count_code'] = 'G�r�n�r saya�lar (<a href="http://www.freestats.com/" target="_blank"> FreeStats </a>, <a href = "http: // www .superstats.com / "target =" _ blank "> SuperStats </a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank"> PRTracker </a> vb.) ';
$txt['optimus_counters_css'] = 'G�r�n�r say�c�lar (CSS) i�in g�r�n�m';
$txt['optimus_ignored_actions'] = 'G�z ard� edilen eylemler';

$txt['optimus_robots_title'] = 'Edit�r robots.txt';
$txt['optimus_robots_desc'] = 'Bu sayfada forum haritas� olu�turman�n baz� se�eneklerini de�i�tirebilirsiniz, bunun yan� s�ra �zel �reteci kullanarak bir robots.txt dosyas� de�i�tirebilirsiniz.';

$txt['optimus_manage'] = 'Robots.txt\'yi y�netin';
$txt['optimus_rules'] = 'Robots.txt �reteci';
$txt['optimus_rules_hint'] = 'Bu kurallar� sa�daki alana kopyalayabilirsiniz:';
$txt['optimus_robots_hint'] = 'Burada kendi kurallar�n�z� ekleyebilir veya mevcut olanlar� de�i�tirebilirsiniz:';
$txt['optimus_useful'] = '';
$txt['optimus_links_title'] = 'Faydal� Linkler';
$txt['optimus_links'] = array(
'Bir robots.txt dosyas� olu�tur' => 'https://support.google.com/webmasters/answer/6062596?hl=tr',
'Robots.txt kullanarak' => 'https://help.yandex.com/webmaster/?id=1113851',
'T�m web sitesinin teknik denetimi' => 'https://netpeaksoftware.com/ucp?invite=94cdaf6a'
);

$txt['optimus_sitemap_title'] = 'Optimus Site Haritas�';
$txt['optimus_sitemap_desc'] = 'Basit bir site haritas� m� istiyorsunuz? Optimus, k���k forumlar i�in sitemap.xml olu�turabilir. Sadece bu se�ene�i a�a��da etkinle�tirin. Bu site haritas�, <a href="%1$s"> G�rev Y�neticisi </a> \'ndeki ayarlara ba�l� olarak g�ncellenecektir.';

$txt['optimus_sitemap_enable'] = 'Site Haritas� XML dosyas�n� olu�turun ve periyodik olarak g�ncelleyin';
$txt['optimus_sitemap_link'] = 'Altbilgideki Site Haritas� XML ba�lant�s�n� g�ster';
$txt['optimus_sitemap_boards'] = 'Site haritas�na ba�lant�lar ekle <br /> <span class = "smalltext error"> Konuklara kapat�lan panolar eklenmeyecek. </ span>';
$txt['optimus_sitemap_topics'] = 'Site haritas�na ekle, sadece cevap say�s�na sahip olan konular daha fazla';

$txt['optimus_sitemap_rec'] = 'Optimus, dosyalar� birka� par�aya b�lemez.';
$txt['optimus_sitemap_url_limit'] = 'Site Haritas� dosyas� 50.000\'den fazla URL i�ermemelidir!';
$txt['optimus_sitemap_size_limit'] = '%1$s dosyas� 10MB\'den b�y�k olmamal�d�r!';
$txt['optimus_sitemap_xml_link'] = 'Site Haritas� XML';

$txt['optimus_donate_title'] = 'Ba���lar';
$txt['optimus_donate_desc'] = 'Buradan mod yazara ba���ta bulunabilirsiniz.';

// G�rev Y�neticisi
$txt['scheduled_task_optimus_sitemap'] = 'Site Haritas� XML Gererasyonu';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Site haritas�n�n s�kl���n� ayarlayabilirsiniz.';
