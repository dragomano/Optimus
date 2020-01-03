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
$txt['optimus_base_desc'] = 'Bu sayfada bir forum açıklamasını değiştirebilir, sayfa başlıklarının şablonlarını yönetebilir, Site Haritası XML neslini etkinleştirebilir / devre dışı bırakabilirsiniz.';


$txt['optimus_main_page'] = 'AnaSayfa';
$txt['optimus_base_info'] = 'Peki, eğer robot bir sayfa ile bir arama sorgusunun eşleştiğini belirlerse açıklama etiketi içeriği dikkate alınabilir.';
$txt['optimus_portal_compat'] = 'Portal Uyumluluğu';
$txt['optimus_portal_compat_set'] = array('Hiçbiri', 'PortaMx', 'SimplePortal', 'TinyPortal');
$txt['optimus_portal_index'] = 'Portal Anasayfa başlığı';
$txt['optimus_forum_index'] = 'Forum Anasayfa başlığı';
$txt['optimus_description'] = 'Forum ek açıklaması <br /> <span class = "smalltext"> Meta etiketin içeriği olarak kullanılacak <strong> açıklama </ strong>. </ span>';

$txt['optimus_all_pages'] = 'Konu ve yönetim sayfaları';
$txt['optimus_tpl_info'] = 'Olası değişkenler: <br/> <strong> {board_name} </ strong> & mdash; bölüm adı, <strong> {topic_name} </ strong> & mdash; konu başlığı, <br/> <strong> {#} </ strong> & mdash; geçerli sayfa numarası, <strong> {cat_name} </ strong> & mdash; kategori adı, <strong> {forum_name} </ strong> & mdash; forumunuzun adı. ';
$txt['optimus_board_tpl'] = 'Bölüm sayfalarının başlık şablonu';
$txt['optimus_topic_tpl'] = 'Konu sayfalarının başlık şablonu';
$txt['optimus_templates'] = array(
'board' => array ('{board_name}', '- sayfa {#} -', '{forum_name}'),
'topic' => array ('{topic_name}', '- sayfa {#} -', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number'] = 'İlk sayfanın numarasını gösterme';
$txt['optimus_board_description'] = 'Bölüm açıklamasını <strong> açıklama </ strong> meta-etikete gönder';
$txt['optimus_topic_description'] = 'Konunun ilk mesajının ilk cümlesini meta-etikete gönder <strong> description </ strong> <br /> <span class = "smalltext"> Use <a href = "https: //custom.simplemachines .org / mods / index.php? mod = 3012 "target =" _ blank "> Konuların kısa açıklamalarını oluşturmak için </a> Konu Açıklama modları. </ span> ';
$txt['optimus_404_status'] = 'İstenen sayfa durumuna göre <a href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">403/404 kodunu</a> getir ';
$txt['optimus_404_page_title'] = '404 - Sayfa bulunamadı';
$txt['optimus_404_h2'] = 'Hata 404';
$txt['optimus_404_h3'] = 'Üzgünüz, ancak istenen sayfa mevcut değil.';
$txt['optimus_403_page_title'] = '403 - Erişim yasak';
$txt['optimus_403_h2'] = 'Hata 403';
$txt['optimus_403_h3'] = 'Üzgünüz, ancak bu sayfaya erişiminiz yok.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc'] = 'Kendi forum ikonunuzu oluşturun. Tarayıcı, sayfa adının önündeki sekmede yanı sıra açık sekmenin ve diğer arabirim öğelerinin yanındaki bir görüntüde görüntülenecektir. ';

$txt['optimus_favicon_create'] = 'Favicon oluşturun';
$txt['optimus_favicon_api_key'] = 'Favicon Generator ile çalışmak için API anahtarı (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank"> API anahtarı al </a>)' ;
$txt['optimus_favicon_text'] = 'Favicon kodu';
$txt['optimus_favicon_help'] = 'Kendi favicon\'ınızı oluşturun <a href="http://www.favicomatic.com/" target="_blank"> burayı </a> kullanın veya özel bir jeneratör kullanın. Yukarıdaki alandaki API anahtarını girin. <br /> Ardından, favicon dosyalarını forum köküne yükleyin ve kodu sağdaki alana jeneratör sitesinden kaydedin. <br /> Bu kod, site sayfalarının en üstünde, &lt;head&gt;&lt;/head&gt; etiketleri ';

$txt['optimus_extra_title'] = 'Meta veri';
$txt['optimus_extra_desc'] = 'Burada forumunuz için bazı düzeltmeleri bulabilirsiniz. Ayrıca Açık Grafik ve JSON-LD desteğini etkinleştirebilirsiniz. Keyfini çıkarın!';

$txt['optimus_open_graph'] = '<a href="http://ogp.me/" target="_blank"> Açık Grafikler </a> forum sayfaları için meta etiketler';
$txt['optimus_og_image'] = 'Varsayılan Açık Grafik resminize bağlantı <br /> <span class = "smalltext"> İlk mesajın ekleri (eğer varsa) başlıklarında yer alacak. </ span> ';
$txt['optimus_fb_appid'] = '<a href="https://developers.facebook.com/apps" target="_blank"> APP ID </a> (Uygulama Kimliği) <a href = "https: / /www.facebook.com/ "target =" _ blank "> Facebook </a> ';
$txt['optimus_tw_cards'] = '<a href="https://twitter.com/" target="_blank"> Twitter </a> hesap adı (<a href = "https: // dev etkinleştirmeyi belirtin) .twitter.com / cards / overview "target =" _ blank "> Twitter Kartları </a>) ';
$txt['optimus_json_ld'] = '<a href="https://json-ld.org/" target="_blank"> JSON-LD </a> "<a href =" https: // için biçimlendirme developers.google.com/search/docs/data-types/breadcrumbs?hl= '. $txt['lang_dictionary']. '"target =" _ blank "> ekmek kırıntıları </a>"';

$txt['optimus_meta_title'] = 'Meta etiketler';
$txt['optimus_meta_desc'] = 'Bu sayfada aşağıdaki listeden herhangi bir normal / doğrulama kodu ekleyebilirsiniz.';

$txt['optimus_meta_addtag'] = 'Yeni bir etiket eklemek için buraya tıklayın';
$txt['optimus_meta_customtag'] = 'Özel meta etiketi';
$txt['optimus_meta_tools'] = 'Arama motoru (Araçlar)';
$txt['optimus_meta_name'] = 'İsim';
$txt['optimus_meta_content'] = 'İçerik';
$txt['optimus_meta_info'] = ' Lütfen sadece <strong>content</strong> meta etiketinin parametre değerlerini kullanın.<br />Örneğin: <span class="smalltext">&lt;meta name="google-site-verification" content="<strong>SAĞ SÜTUNA YAPIŞTIRMANIZ GEREKEN DEĞER</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
'Google' => array('google-site-verification', '<a href="https://www.google.com/webmasters/tools/" target="_blank"> Google Search Console </a>' ),
'Yandex' => array('yandex-doğrulama', '<a href="https://webmaster.yandex.com/" target="_blank"> Yandex.Webmaster </a>'),
'Bing' => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank"> Bing Web Yöneticisi </a>')
);

$txt['optimus_counters'] = 'Sayaçlar';
$txt['optimus_counters_desc'] = 'Forumunuzun ziyaretlerini günlüğe kaydetmek için bu bölümdeki sayaçları ekleyebilir ve değiştirebilirsiniz.';

$txt['optimus_head_code'] = '<strong> head </ strong> bölümünde görünmez sayaçlar yükleniyor (<a href="https://www.google.com/analytics/sign_up.html" target="_blank"> Google Analytics </a>) ';
$txt['optimus_stat_code'] = 'Diğer görünmez sayıcılar (<a href="https://matomo.org/" target="_blank"> Matomo </a> vs.)';
$txt['optimus_count_code'] = 'Görünür sayaçlar (<a href="http://www.freestats.com/" target="_blank"> FreeStats </a>, <a href = "http: // www .superstats.com / "target =" _ blank "> SuperStats </a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank"> PRTracker </a> vb.) ';
$txt['optimus_counters_css'] = 'Görünür sayıcılar (CSS) için görünüm';
$txt['optimus_ignored_actions'] = 'Göz ardı edilen eylemler';

$txt['optimus_robots_title'] = 'Editör robots.txt';
$txt['optimus_robots_desc'] = 'Bu sayfada forum haritası oluşturmanın bazı seçeneklerini değiştirebilirsiniz, bunun yanı sıra özel üreteci kullanarak bir robots.txt dosyası değiştirebilirsiniz.';

$txt['optimus_manage'] = 'Robots.txt\'yi yönetin';
$txt['optimus_rules'] = 'Robots.txt Üreteci';
$txt['optimus_rules_hint'] = 'Bu kuralları sağdaki alana kopyalayabilirsiniz:';
$txt['optimus_robots_hint'] = 'Burada kendi kurallarınızı ekleyebilir veya mevcut olanları değiştirebilirsiniz:';
$txt['optimus_useful'] = '';
$txt['optimus_links_title'] = 'Faydalı Linkler';
$txt['optimus_links'] = array(
'Bir robots.txt dosyası oluştur' => 'https://support.google.com/webmasters/answer/6062596?hl=tr',
'Robots.txt kullanarak' => 'https://help.yandex.com/webmaster/?id=1113851',
'Tüm web sitesinin teknik denetimi' => 'https://netpeaksoftware.com/ucp?invite=94cdaf6a'
);

$txt['optimus_sitemap_title'] = 'Optimus Site Haritası';
$txt['optimus_sitemap_desc'] = 'Basit bir site haritası mı istiyorsunuz? Optimus, küçük forumlar için sitemap.xml oluşturabilir. Sadece bu seçeneği aşağıda etkinleştirin. Bu site haritası, <a href="%1$s"> Görev Yöneticisi </a> \'ndeki ayarlara bağlı olarak güncellenecektir.';

$txt['optimus_sitemap_enable'] = 'Site Haritası XML dosyasını oluşturun ve periyodik olarak güncelleyin';
$txt['optimus_sitemap_link'] = 'Altbilgideki Site Haritası XML bağlantısını göster';
$txt['optimus_sitemap_boards'] = 'Site haritasına bağlantılar ekle <br /> <span class = "smalltext error"> Konuklara kapatılan panolar eklenmeyecek. </ span>';
$txt['optimus_sitemap_topics'] = 'Site haritasına ekle, sadece cevap sayısına sahip olan konular daha fazla';

$txt['optimus_sitemap_rec'] = 'Optimus, dosyaları birkaç parçaya bölemez.';
$txt['optimus_sitemap_url_limit'] = 'Site Haritası dosyası 50.000\'den fazla URL içermemelidir!';
$txt['optimus_sitemap_size_limit'] = '%1$s dosyası 10MB\'den büyük olmamalıdır!';
$txt['optimus_sitemap_xml_link'] = 'Site Haritası XML';

// Görev Yöneticisi
$txt['scheduled_task_optimus_sitemap'] = 'Site Haritası XML Gererasyonu';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Site haritasının sıklığını ayarlayabilirsiniz.';
