<?php

/**
 * .ukrainian language file
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_main']  = 'Optimus';
$txt['optimus_title'] = 'Пошукова оптимізація';

$txt['optimus_base_title'] = 'Загальні налаштування';
$txt['optimus_base_desc']  = 'Зміна опису форуму, налаштування шаблонів заголовків сторінок розділів і тем, а також включення/відключення генерації карти сайту.';

$txt['optimus_main_page']           = 'Головна сторінка';
$txt['optimus_forum_index']         = 'Заголовок головної сторінки форуму';
$txt['optimus_description']         = 'Опис форуму';
$txt['optimus_description_subtext'] = 'Буде виведене в мета-тегу <strong>description</strong> головної сторінки.';

$txt['optimus_all_pages']                           = 'Сторінки тем і разділів';
$txt['optimus_board_extend_title']                  = 'Додавати назву форуму до заголовків разділів';
$txt['optimus_board_extend_title_set']              = array('Ні', 'Перед назвою разділу', 'Після назви розділу');
$txt['optimus_topic_extend_title']                  = 'Додавати назву разділу і форуму до заголовків тем';
$txt['optimus_topic_extend_title_set']              = array('Ні', 'Перед назвою теми', 'Після назви теми');
$txt['optimus_topic_description']                   = 'Використовувати уривок першого повідомлення теми в якості мета-тегу <strong>description</strong>';
$txt['optimus_allow_change_board_og_image']         = 'Дозволити окреме поле для додавання посилання на <strong>OG Image</strong> зображення для розділу';
$txt['optimus_allow_change_board_og_image_subtext'] = 'Відображається при редагуванні розділу.';
$txt['optimus_allow_change_topic_desc']             = 'Дозволити окреме поле для опису теми';
$txt['optimus_allow_change_topic_desc_subtext']     = 'Відображається при редагуванні теми.';
$txt['optimus_allow_change_topic_keywords']         = 'Дозволити окреме поле для ключових слів теми';
$txt['optimus_allow_change_topic_keywords_subtext'] = 'Відображається при редагуванні теми.';
$txt['optimus_show_keywords_block']                 = 'Відображати блок з ключовими словами над першим повідомленням теми';
$txt['optimus_correct_http_status']                 = 'Повертати <a href="https://goo.gl/1UHxeB" target="_blank" rel="noopener" class="bbc_link">код 403/404</a>, в залежності від статусу запитуваної сторінки';

$txt['optimus_extra_settings']        = 'Додатково';
$txt['optimus_use_only_cookies']      = 'Використовувати кукі для зберігання ідентифікатора сесії на стороні клієнта';
$txt['optimus_use_only_cookies_help'] = 'Увімкнення параметру <a href="https://www.php.net/manual/ru/session.configuration.php#ini.session.use-only-cookies" target="_blank" rel="noopener" class="bbc_link">session.use_only_cookies</a> попереджає атаки з використанням ідентифікатора сесії, розміщеного в URL.<br>Окрім того, вы зможете позбутися ідентифікатора сесії в канонічних адресах сторінок форуму.';
$txt['optimus_remove_index_php']      = 'Прибрати "index.php" з адрес форуму';
$txt['optimus_extend_h1']             = 'Додати заголовок сторінки до тегу <strong>H1</strong>';

$txt['optimus_extra_title'] = 'Мікророзмітка';
$txt['optimus_extra_desc']  = 'Додавання додаткової <a href="https://ruogp.me/" target="_blank" rel="noopener" class="bbc_link">розмітки</a> для сторінок форуму.';

$txt['optimus_og_image']         = 'Використання зображення з першого повідомлення теми в мета-тегу <strong>og:image</strong>';
$txt['optimus_og_image_subtext'] = 'За замовчуванням використовуєтся зображення, задане в <a href="%s" class="bbc_link">налаштуваннях поточної теми оформлення</a>.';
$txt['optimus_og_image_help']    = 'Якщо включено, в мета-тег <strong>og:image</strong> підставляється посилання на перше зображення, прикладене до першого повідомлення теми. Якщо вкладення нема, а в тексті повідомлення буде знайдене зображення всередині тегу <strong>img</strong>, використовується воно.';
$txt['optimus_fb_appid']         = 'ID додатка Facebook (якщо є)';
$txt['optimus_fb_appid_help']    = 'Створіть додаток <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener" class="bbc_link"><strong>здесь</strong></a>, скопіюйте його ID і вкажіть в цьому полі.';
$txt['optimus_tw_cards']         = 'Ім\'я акаунту в Twitter (якщо є)';
$txt['optimus_tw_cards_help']    = 'Детальніше про картки Twitter можна почитати <a href="https://dev.twitter.com/cards/overview" target="_blank" rel="noopener" class="bbc_link"><strong>тут</strong></a>.';

$txt['optimus_favicon_title'] = 'Іконка сайту';
$txt['optimus_favicon_desc']  = 'Створіть свій значок для форуму. Він буде відображатись браузером у вкладці під назвою сторінок, а також в якості картинки поруч з закладкою, у вкладках та в інших елементах інтерфейсу.';

$txt['optimus_favicon_create']  = 'Створити іконку для сайту';
$txt['optimus_favicon_api_key'] = 'Ключ API для роботи з генератором іконки (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank" rel="noopener" class="bbc_link">Отримати</a>)';
$txt['optimus_favicon_text']    = 'Код для вставки favicon';
$txt['optimus_favicon_help']    = 'Згенеруйте свою унікальную іконку, наприклад, <a href="https://www.favicomatic.com/" target="_blank" rel="noopener" class="bbc_link">здесь</a>, або <a href="https://digitalagencyrankings.com/iconogen/" target="_blank" rel="noopener" class="bbc_link">здесь</a>, або скористайтесь генератором, вказавши ключ API в полі вище. Потім завантажте файли іконки в корінь форуму, а запропонований на сайті код збережіть в полі праворуч. Цей код буде завантажуватись в верхній частині сторінок, між тегами &lt;<strong>head</strong>&gt;&lt;/<strong>head</strong>&gt;.';

$txt['optimus_meta_title'] = 'Мета-теги';
$txt['optimus_meta_desc']  = 'Додавання в код сторінок форуму додаткових мета-тегів, наприклад, для підтвердження права власності на сайт.';

$txt['optimus_meta_addtag']    = 'Додати тег';
$txt['optimus_meta_customtag'] = 'Мета-тег для користувачів';
$txt['optimus_meta_tools']     = 'Пошуковик (Сервіс)';
$txt['optimus_meta_name']      = 'Ім\'я тегу';
$txt['optimus_meta_content']   = 'Значення';
$txt['optimus_meta_info']      = 'Будь ласка, вказуйте лише значення, що міститься в мета-тегах, які додаються (а не теги повністю).<br>Наприклад: <span class="smalltext">&lt;meta name="<strong>ІМ\'Я ТЕГУ</strong>" content="<strong>ЗНАЧЕННЯ</strong>"&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="https://www.google.com/webmasters/tools/" target="_blank" rel="noopener">Google Search Console</a>'),
	'Yandex' => array('yandex-verification', '<a href="https://webmaster.yandex.ru/" target="_blank" rel="noopener">Яндекс.Вебмайстер</a>'),
	'Mail'   => array('wmail-verification', '<a href="https://webmaster.mail.ru" target="_blank" rel="noopener">Поиск Mail.Ru - Кабінет вебмайстра</a>'),
	'Bing'   => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank" rel="noopener">Bing - Засоби веб-майстра</a>')
);

$txt['optimus_counters']      = 'Лічильники';
$txt['optimus_counters_desc'] = 'Додавайте і змінюйте найрізноманітніші лічильники для підрахунку відвідувань форуму.';

$txt['optimus_head_code']       = 'Невидимі лічильники із завантаженням в секції <strong>head</strong> (<a href="https://www.google.ru/analytics/" target="_blank" rel="noopener" class="bbc_link">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Інші невидимі лічильники (наприклад, <a href="https://metrika.yandex.ru/" target="_blank" rel="noopener" class="bbc_link">Яндекс.Метрика</a> без інформера)';
$txt['optimus_count_code']      = 'Звичайні лічильники (<a href="https://www.liveinternet.ru/add" target="_blank" rel="noopener" class="bbc_link">LiveInternet</a>, <a href="https://top100.rambler.ru/" target="_blank" rel="noopener" class="bbc_link">Rambler\'s Top100</a> і т. п.)';
$txt['optimus_counters_css']    = 'Оформлення блоку з лічильниками (CSS)';
$txt['optimus_ignored_actions'] = 'Області, що ігноруються (actions) &mdash; на цих сторінках лічильники підвантажуватись не будуть!';

$txt['optimus_robots_title'] = 'Редактор robots.txt';
$txt['optimus_robots_desc']  = 'Генератор правил оновлюється в залежності від встановлених модів і деяких налаштувань SMF.';

$txt['optimus_manage']      = 'Налаштування robots.txt';
$txt['optimus_root_path']   = 'Шлях до кореневої директорії сайту';
$txt['optimus_rules']       = 'Генератор правил';
$txt['optimus_rules_hint']  = 'Можете скористатись цими заготовками для створення своїх правил (в області, що знаходиться праворуч):';
$txt['optimus_useful']      = '<a href="https://dragomano.ru/articles/pravilnyj-robotstxt-dlja-smf" target="_blank" rel="noopener" class="bbc_link">Правильний robots.txt для SMF</a>';
$txt['optimus_links_title'] = 'Корисні посилання';
$txt['optimus_links']       = array(
	'Перевірка robots.txt'                     => 'https://webmaster.yandex.ru/robots.xml',
	'Як налаштувати редирект'                  => 'https://goo.gl/LVPRpr',
	'Комплексний SEO-аудит всього сайту'       => 'https://goo.gl/TBw79p',
	'Автореєстрація форуму в каталогах Рунету' => 'https://goo.gl/uAR3CZ',
	'Автоматичне просування вашего сайту'      => 'https://goo.gl/RMSDnx'
);

$txt['optimus_sitemap_title'] = 'Карта форуму';
$txt['optimus_sitemap_desc']  = 'Оптимус може створити xml-карту для форумів будь-якого розміру.';

$txt['optimus_sitemap_enable']                  = 'Активувати карту форуму';
$txt['optimus_sitemap_link']                    = 'Показувати посилання на карту форуму в підвалі';
$txt['optimus_main_page_frequency']             = 'Частота зміни головної сторінки';
$txt['optimus_main_page_frequency_set']         = array('Постійна (always)', 'Залежно від дати останнього повідомлення');
$txt['optimus_sitemap_boards']                  = 'Додавати в карту посилання на розділи форуму';
$txt['optimus_sitemap_boards_subtext']          = 'Розділи, закриті для гостей, додані НЕ будуть.';
$txt['optimus_sitemap_topics_num_replies']      = 'Додавати в карту лише теми з кількістю відповідей понад';
$txt['optimus_sitemap_items_display']           = 'Максимальна кількість елементів в одному XML-файлі';
$txt['optimus_sitemap_all_topic_pages']         = 'Додавати в карту ВСІ сторінки тем';
$txt['optimus_sitemap_all_topic_pages_subtext'] = 'Якщо не зазначено, в карту будуть додаватися тільки перші сторінки тем.';

$txt['optimus_404_page_title']       = '404 - Сторінка не знайдена';
$txt['optimus_404_h2']               = 'Помилка 404';
$txt['optimus_404_h3']               = 'Вибачте, але такої сторінки тут нема.';
$txt['optimus_403_page_title']       = '403 - Доступ заборонено';
$txt['optimus_403_h2']               = 'Помилка 403';
$txt['optimus_403_h3']               = 'Вибачте, але у вас нема доступу до цієї сторінки.';
$txt['optimus_seo_description']      = 'Опис теми [SEO]';
$txt['optimus_seo_keywords']         = 'Ключові слова [SEO]';
$txt['optimus_enter_keywords']       = 'Введіть одне або кілька ключових слів';
$txt['optimus_topics_with_keyword']  = 'Теми форуму з ключовим словом «%s»';
$txt['optimus_keyword_id_not_found'] = 'Вказаний ідентифікатор ключового слова не знайдений.';
$txt['optimus_no_keywords']          = 'Інформація з даного ідентифікатора ключового слова відсутня.';
$txt['optimus_all_keywords']         = 'Всі ключові слова в темах форуму';
$txt['optimus_keyword_column']       = 'Ключове слово';
$txt['optimus_frequency_column']     = 'Частотність';
