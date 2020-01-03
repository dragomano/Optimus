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
$txt['optimus_base_desc'] = 'Bu sayfada bir forum açýklamasýný deðiþtirebilir, sayfa baþlýklarýnýn þablonlarýný yönetebilir, Site Haritasý XML neslini etkinleþtirebilir / devre dýþý býrakabilirsiniz.';


$txt['optimus_main_page'] = 'AnaSayfa';
$txt['optimus_base_info'] = 'Peki, eðer robot bir sayfa ile bir arama sorgusunun eþleþtiðini belirlerse açýklama etiketi içeriði dikkate alýnabilir.';
$txt['optimus_portal_compat'] = 'Portal Uyumluluðu';
$txt['optimus_portal_compat_set'] = array('Hiçbiri', 'PortaMx', 'SimplePortal', 'TinyPortal');
$txt['optimus_portal_index'] = 'Portal Anasayfa baþlýðý';
$txt['optimus_forum_index'] = 'Forum Anasayfa baþlýðý';
$txt['optimus_description'] = 'Forum ek açýklamasý <br /> <span class = "smalltext"> Meta etiketin içeriði olarak kullanýlacak <strong> açýklama </ strong>. </ span>';

$txt['optimus_all_pages'] = 'Konu ve yönetim sayfalarý';
$txt['optimus_tpl_info'] = 'Olasý deðiþkenler: <br/> <strong> {board_name} </ strong> & mdash; bölüm adý, <strong> {topic_name} </ strong> & mdash; konu baþlýðý, <br/> <strong> {#} </ strong> & mdash; geçerli sayfa numarasý, <strong> {cat_name} </ strong> & mdash; kategori adý, <strong> {forum_name} </ strong> & mdash; forumunuzun adý. ';
$txt['optimus_board_tpl'] = 'Bölüm sayfalarýnýn baþlýk þablonu';
$txt['optimus_topic_tpl'] = 'Konu sayfalarýnýn baþlýk þablonu';
$txt['optimus_templates'] = array(
'board' => array ('{board_name}', '- sayfa {#} -', '{forum_name}'),
'topic' => array ('{topic_name}', '- sayfa {#} -', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number'] = 'Ýlk sayfanýn numarasýný gösterme';
$txt['optimus_board_description'] = 'Bölüm açýklamasýný <strong> açýklama </ strong> meta-etikete gönder';
$txt['optimus_topic_description'] = 'Konunun ilk mesajýnýn ilk cümlesini meta-etikete gönder <strong> description </ strong> <br /> <span class = "smalltext"> Use <a href = "https: //custom.simplemachines .org / mods / index.php? mod = 3012 "target =" _ blank "> Konularýn kýsa açýklamalarýný oluþturmak için </a> Konu Açýklama modlarý. </ span> ';
$txt['optimus_404_status'] = 'Ýstenen sayfa durumuna göre <a href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">403/404 kodunu</a> getir ';
$txt['optimus_404_page_title'] = '404 - Sayfa bulunamadý';
$txt['optimus_404_h2'] = 'Hata 404';
$txt['optimus_404_h3'] = 'Üzgünüz, ancak istenen sayfa mevcut deðil.';
$txt['optimus_403_page_title'] = '403 - Eriþim yasak';
$txt['optimus_403_h2'] = 'Hata 403';
$txt['optimus_403_h3'] = 'Üzgünüz, ancak bu sayfaya eriþiminiz yok.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc'] = 'Kendi forum ikonunuzu oluþturun. Tarayýcý, sayfa adýnýn önündeki sekmede yaný sýra açýk sekmenin ve diðer arabirim öðelerinin yanýndaki bir görüntüde görüntülenecektir. ';

$txt['optimus_favicon_create'] = 'Favicon oluþturun';
$txt['optimus_favicon_api_key'] = 'Favicon Generator ile çalýþmak için API anahtarý (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank"> API anahtarý al </a>)' ;
$txt['optimus_favicon_text'] = 'Favicon kodu';
$txt['optimus_favicon_help'] = 'Kendi favicon\'ýnýzý oluþturun <a href="http://www.favicomatic.com/" target="_blank"> burayý </a> kullanýn veya özel bir jeneratör kullanýn. Yukarýdaki alandaki API anahtarýný girin. <br /> Ardýndan, favicon dosyalarýný forum köküne yükleyin ve kodu saðdaki alana jeneratör sitesinden kaydedin. <br /> Bu kod, site sayfalarýnýn en üstünde, &lt;head&gt;&lt;/head&gt; etiketleri ';

$txt['optimus_extra_title'] = 'Meta veri';
$txt['optimus_extra_desc'] = 'Burada forumunuz için bazý düzeltmeleri bulabilirsiniz. Ayrýca Açýk Grafik ve JSON-LD desteðini etkinleþtirebilirsiniz. Keyfini çýkarýn!';

$txt['optimus_open_graph'] = '<a href="http://ogp.me/" target="_blank"> Açýk Grafikler </a> forum sayfalarý için meta etiketler';
$txt['optimus_og_image'] = 'Varsayýlan Açýk Grafik resminize baðlantý <br /> <span class = "smalltext"> Ýlk mesajýn ekleri (eðer varsa) baþlýklarýnda yer alacak. </ span> ';
$txt['optimus_fb_appid'] = '<a href="https://developers.facebook.com/apps" target="_blank"> APP ID </a> (Uygulama Kimliði) <a href = "https: / /www.facebook.com/ "target =" _ blank "> Facebook </a> ';
$txt['optimus_tw_cards'] = '<a href="https://twitter.com/" target="_blank"> Twitter </a> hesap adý (<a href = "https: // dev etkinleþtirmeyi belirtin) .twitter.com / cards / overview "target =" _ blank "> Twitter Kartlarý </a>) ';
$txt['optimus_json_ld'] = '<a href="https://json-ld.org/" target="_blank"> JSON-LD </a> "<a href =" https: // için biçimlendirme developers.google.com/search/docs/data-types/breadcrumbs?hl= '. $txt['lang_dictionary']. '"target =" _ blank "> ekmek kýrýntýlarý </a>"';

$txt['optimus_meta_title'] = 'Meta etiketler';
$txt['optimus_meta_desc'] = 'Bu sayfada aþaðýdaki listeden herhangi bir normal / doðrulama kodu ekleyebilirsiniz.';

$txt['optimus_meta_addtag'] = 'Yeni bir etiket eklemek için buraya týklayýn';
$txt['optimus_meta_customtag'] = 'Özel meta etiketi';
$txt['optimus_meta_tools'] = 'Arama motoru (Araçlar)';
$txt['optimus_meta_name'] = 'Ýsim';
$txt['optimus_meta_content'] = 'Ýçerik';
$txt['optimus_meta_info'] = ' Lütfen sadece <strong>content</strong> meta etiketinin parametre deðerlerini kullanýn.<br />Örneðin: <span class="smalltext">&lt;meta name="google-site-verification" content="<strong>SAÐ SÜTUNA YAPIÞTIRMANIZ GEREKEN DEÐER</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
'Google' => array('google-site-verification', '<a href="https://www.google.com/webmasters/tools/" target="_blank"> Google Search Console </a>' ),
'Yandex' => array('yandex-doðrulama', '<a href="https://webmaster.yandex.com/" target="_blank"> Yandex.Webmaster </a>'),
'Bing' => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank"> Bing Web Yöneticisi </a>')
);

$txt['optimus_counters'] = 'Sayaçlar';
$txt['optimus_counters_desc'] = 'Forumunuzun ziyaretlerini günlüðe kaydetmek için bu bölümdeki sayaçlarý ekleyebilir ve deðiþtirebilirsiniz.';

$txt['optimus_head_code'] = '<strong> head </ strong> bölümünde görünmez sayaçlar yükleniyor (<a href="https://www.google.com/analytics/sign_up.html" target="_blank"> Google Analytics </a>) ';
$txt['optimus_stat_code'] = 'Diðer görünmez sayýcýlar (<a href="https://matomo.org/" target="_blank"> Matomo </a> vs.)';
$txt['optimus_count_code'] = 'Görünür sayaçlar (<a href="http://www.freestats.com/" target="_blank"> FreeStats </a>, <a href = "http: // www .superstats.com / "target =" _ blank "> SuperStats </a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank"> PRTracker </a> vb.) ';
$txt['optimus_counters_css'] = 'Görünür sayýcýlar (CSS) için görünüm';
$txt['optimus_ignored_actions'] = 'Göz ardý edilen eylemler';

$txt['optimus_robots_title'] = 'Editör robots.txt';
$txt['optimus_robots_desc'] = 'Bu sayfada forum haritasý oluþturmanýn bazý seçeneklerini deðiþtirebilirsiniz, bunun yaný sýra özel üreteci kullanarak bir robots.txt dosyasý deðiþtirebilirsiniz.';

$txt['optimus_manage'] = 'Robots.txt\'yi yönetin';
$txt['optimus_rules'] = 'Robots.txt Üreteci';
$txt['optimus_rules_hint'] = 'Bu kurallarý saðdaki alana kopyalayabilirsiniz:';
$txt['optimus_robots_hint'] = 'Burada kendi kurallarýnýzý ekleyebilir veya mevcut olanlarý deðiþtirebilirsiniz:';
$txt['optimus_useful'] = '';
$txt['optimus_links_title'] = 'Faydalý Linkler';
$txt['optimus_links'] = array(
'Bir robots.txt dosyasý oluþtur' => 'https://support.google.com/webmasters/answer/6062596?hl=tr',
'Robots.txt kullanarak' => 'https://help.yandex.com/webmaster/?id=1113851',
'Tüm web sitesinin teknik denetimi' => 'https://netpeaksoftware.com/ucp?invite=94cdaf6a'
);

$txt['optimus_sitemap_title'] = 'Optimus Site Haritasý';
$txt['optimus_sitemap_desc'] = 'Basit bir site haritasý mý istiyorsunuz? Optimus, küçük forumlar için sitemap.xml oluþturabilir. Sadece bu seçeneði aþaðýda etkinleþtirin. Bu site haritasý, <a href="%1$s"> Görev Yöneticisi </a> \'ndeki ayarlara baðlý olarak güncellenecektir.';

$txt['optimus_sitemap_enable'] = 'Site Haritasý XML dosyasýný oluþturun ve periyodik olarak güncelleyin';
$txt['optimus_sitemap_link'] = 'Altbilgideki Site Haritasý XML baðlantýsýný göster';
$txt['optimus_sitemap_boards'] = 'Site haritasýna baðlantýlar ekle <br /> <span class = "smalltext error"> Konuklara kapatýlan panolar eklenmeyecek. </ span>';
$txt['optimus_sitemap_topics'] = 'Site haritasýna ekle, sadece cevap sayýsýna sahip olan konular daha fazla';

$txt['optimus_sitemap_rec'] = 'Optimus, dosyalarý birkaç parçaya bölemez.';
$txt['optimus_sitemap_url_limit'] = 'Site Haritasý dosyasý 50.000\'den fazla URL içermemelidir!';
$txt['optimus_sitemap_size_limit'] = '%1$s dosyasý 10MB\'den büyük olmamalýdýr!';
$txt['optimus_sitemap_xml_link'] = 'Site Haritasý XML';

// Görev Yöneticisi
$txt['scheduled_task_optimus_sitemap'] = 'Site Haritasý XML Gererasyonu';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Site haritasýnýn sýklýðýný ayarlayabilirsiniz.';
