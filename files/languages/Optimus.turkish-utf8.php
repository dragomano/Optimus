<?php

$txt['optimus_main'] = 'Optimus Brave';
$txt['optimus_title'] = 'Arama Motoru Optimizasyonu';

$txt['optimus_common_title'] = 'Genel ayarlar';
$txt['optimus_common_desc'] = 'Bu sayfada forum açıklamasını değiştirebilir, sayfa başlıklarının şablonlarını yönetebilirsiniz.';
$txt['optimus_verification_title'] = 'Meta etiketleri doğrulaması';
$txt['optimus_verification_desc'] = 'Bu sayfada aşağıdaki listeden herhangi bir genel veya doğrulama kodu ekleyebilirsiniz.';
$txt['optimus_robots_title'] = 'robots.txt';
$txt['optimus_robots_desc'] = 'Bu sayfada forum haritası oluşturmanın bazı seçeneklerini değiştirebilirsiniz, bunun yanı sıra özel üreteci kullanarak bir robots.txt dosyası değiştirebilirsiniz.';
$txt['optimus_terms_title'] = 'Arama terimleri';
$txt['optimus_terms_desc'] = 'Arama terimleri insanların forumunuzu bulmak için arama motorlarının arama formlarına yazdığı kelime ve ifadelerdir.';

$txt['optimus_main_page'] = 'Anasayfa';
$txt['optimus_common_info'] = 'Peki, eğer robot bir sayfa ile bir arama sorgusunun eşleştiğini belirlerse açıklama etiketi içeriği dikkate alınabilir.';
$txt['optimus_portal_index'] = 'Portal anasayfa başlığı';
$txt['optimus_forum_index'] = 'Forum anasayfa başlığı';
$txt['optimus_description'] = 'Kısa ama ilginç bir forum yorumu<br /><span class="smalltext"><em>description</em> etiketinin içeriği olarak kullanılacaktır.</span>';
$txt['optimus_all_pages'] = 'Konu/bölüm sayfalarının ayarları';
$txt['optimus_tpl_info'] = 'Olası değişkenler:<br/><strong>{board_name}</strong> &mdash; bölüm adı, <strong>{topic_name}</strong> &mdash; konu başlığı,<br/><strong>{#}</strong> &mdash; geçerli sayfa numarası, <strong>{cat_name}</strong> &mdash; kategori adı, <strong>{forum_name}</strong> &mdash; forumunuzun adı.';
$txt['optimus_board_tpl'] = 'Bölüm sayfalarının başlık şablonu';
$txt['optimus_topic_tpl'] = 'Konu sayfalarının başlık şablonu';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - sayfa {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - sayfa {#} - ', '{board_name} - {forum_name}')
);

$txt['optimus_board_description'] = 'Bölüm açıklamasını meta-etikete gönder <em>description</em>';
$txt['optimus_topic_description'] = 'Konunun ilk mesajının ilk cümlesini meta-etikete gönder <em>description</em>';
$txt['optimus_404_status'] = 'İstenen sayfa durumuna göre <a href="http://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">403/404 kodunu</a> getir';
$txt['optimus_404_page_title'] = '404 - Sayfa bulunamadı';
$txt['optimus_404_h2'] = 'Hata 404';
$txt['optimus_404_h3'] = 'Üzgünüm, ama istenilen sayfa bulunamadı.';
$txt['optimus_403_page_title'] = '403 - Erişim Yasak';
$txt['optimus_403_h2'] = 'Hata 403';
$txt['optimus_403_h3'] = 'Üzgünüm, ancak bu sayfaya erişim hakkınız yok.';

$txt['optimus_codes'] = 'Doğrulama meta etiketleri';
$txt['optimus_titles'] = 'Arama Motoru (Araçları)';
$txt['optimus_name'] = 'Adı';
$txt['optimus_content'] = 'İçerik';
$txt['optimus_meta_info'] = ' Lütfen sadece <strong>content</strong> meta etiketinin parametre değerlerini kullanın.<br />Örneğin: <span class="smalltext">&lt;meta name="google-site-verification" content="<strong>SAĞ SÜTUNA YAPIŞTIRMANIZ GEREKEN DEĞER</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="http://www.google.com/webmasters/tools" target="_blank">Web Yöneticisi Araçları</a>'),
	'Yandex' => array('yandex-verification','<a href="http://webmaster.yandex.com/" target="_blank">Yandex.Web Yöneticisi</a>'),
	'MSN' => array('msvalidate.01','<a href="http://www.bing.com/webmaster" target="_blank">MSN Web Yöneticisi Araçları</a>'),
	'Yahoo' => array('y_key','<a href="https://siteexplorer.search.yahoo.com/" target="_blank">Yahoo Site Tarayıcısı</a>'),
	'Alexa' => array('alexaVerifyID','<a href="http://www.alexa.com/siteowners" target="_blank">Alexa Site Araçları</a>')
);

$txt['optimus_counters'] = 'Sayaçlar';
$txt['optimus_counters_desc'] = 'Bu bölümde forumunuza ziyaretleri hesaplamak için sayaç çeşitleri ekleyebilir veya değiştirebilirsiniz.';
$txt['optimus_stat_code'] = 'Görünmez sayaçlar (<a href="http://www.google.com/analytics/sign_up.html" target="_blank">Google Analytics</a>, <a href="http://piwik.org/" target="_blank">Piwik</a> etc)';
$txt['optimus_count_code'] = 'Görünür sayaçlar (<a href="http://www.freestats.com/" target="_blank">FreeStats</a>, <a href="http://www.superstats.com/" target="_blank">SuperStats</a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank">PRTracker</a> etc)';
$txt['optimus_ignored_actions'] = 'Yoksayılan eylemler';

$txt['optimus_sitemap_section'] = 'Forum haritası';
$txt['optimus_sitemap_desc'] = 'Basit bir site haritası ister misiniz? Optimus Brave küçük forumlar için sitemap.xml oluşturabilir. Sadece <a href="?action=admin;area=scheduledtasks;sa=tasks" target="_blank">Zamanlanmış Görevler</a>e gidin ve Site Haritası Oluşturma görevini etkinleştirin.';

$txt['optimus_manage'] = 'Robots.txt yönet';
$txt['optimus_robots_old'] = 'Eski (yüklemeden önceki) robots.txt içeriğini <a href="/old_robots.txt" target="_blank">bu bağlantıdan</a> görebilirsiniz.';
$txt['optimus_links_title'] = 'Faydalı bağlantılar';
$txt['optimus_links'] = array(
	'.htaccess düzenleme' => 'http://httpd.apache.org/docs/trunk/howto/htaccess.html',
	'robots.txt kullanımı' => 'http://help.yandex.com/webmaster/?id=1113851',
	'robots.txt dosyası kullanarak sayfaları engelleme veya kaldırma' => 'http://www.google.com/support/webmasters/bin/answer.py?hl=en&amp;answer=156449'
);

$txt['optimus_rules'] = 'Kural oluşturucu';
$txt['optimus_rules_hint'] = 'Siz sağdaki alana bu kuralları kopyalayabilirsiniz:';
$txt['optimus_robots_hint'] = 'Burada kendi kurallarınızı ekleyebilir veya mevcut olanları değiştirebilirsiniz:';
$txt['optimus_other_text'] = 'Lütfen unutmayın';
$txt['optimus_post_scriptum'] = '<span class="alert">Bu değişikliği kendi sorumluluğunuzda kullanın</span>';
$txt['optimus_useful'] = '';

$txt['scheduled_task_optimus_sitemap'] = 'Forum Haritası Oluştur';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Önerilen düzenlilik &mdash; günde bir defa.';
$txt['optimus_sitemap_rec'] = ' Optimus Brave dosyaları birkaç parçaya bölemez.';
$txt['optimus_sitemap_url_limit'] = 'Site Haritası dosyası 50.000\'den fazla URL içermemelidir!';
$txt['optimus_sitemap_size_limit'] = '%1$s dosyası 10 MB\'den büyük olmamalıdır.!';

$txt['optimus_search_stats'] = 'Arama terimleri kaydını etkinleştir';
$txt['optimus_chart_title'] = 'Arama terimleri - En İyi %1$s';
$txt['optimus_terms_none'] = 'İstatistikler mevcut değil. Belki forumunuz henüz indekslenmemiştir.';
$txt['optimus_terms'] = array(
	'google' => 'q',
	'yahoo' => 'p',
	'bing' => 'q',
	'alexa' => 'q'
);

?>