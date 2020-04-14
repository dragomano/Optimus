<?php

/**
 * .russian language file
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_main']  = 'Optimus';
$txt['optimus_title'] = 'Поисковая оптимизация';

$txt['optimus_base_title'] = 'Общие настройки';
$txt['optimus_base_desc']  = 'Изменение описания форума, настройка шаблонов заголовков страниц разделов и тем, а также включение/отключение генерации карты сайта.';

$txt['optimus_main_page']           = 'Главная страница';
$txt['optimus_forum_index']         = 'Заголовок главной страницы форума';
$txt['optimus_description']         = 'Описание форума';
$txt['optimus_description_subtext'] = 'Будет выведено в мета-теге <strong>description</strong> главной страницы.';

$txt['optimus_all_pages']                           = 'Страницы тем и разделов';
$txt['optimus_board_extend_title']                  = 'Добавлять название форума к заголовкам разделов';
$txt['optimus_board_extend_title_set']              = array('Нет', 'Перед названием раздела', 'После названия раздела');
$txt['optimus_topic_extend_title']                  = 'Добавлять название раздела и форума к заголовкам тем';
$txt['optimus_topic_extend_title_set']              = array('Нет', 'Перед названием темы', 'После названия темы');
$txt['optimus_topic_description']                   = 'Использовать отрывок первого сообщения темы в качестве мета-тега <strong>description</strong>';
$txt['optimus_allow_change_board_og_image']         = 'Разрешить отдельное поле для картинки раздела (<strong>og:image</strong>)';
$txt['optimus_allow_change_board_og_image_subtext'] = 'Отображается при редактировании раздела.';
$txt['optimus_allow_change_topic_desc']             = 'Разрешить отдельное поле для описания темы';
$txt['optimus_allow_change_topic_desc_subtext']     = 'Отображается при редактировании темы.';
$txt['optimus_allow_change_topic_keywords']         = 'Разрешить отдельное поле для ключевых слов темы';
$txt['optimus_allow_change_topic_keywords_subtext'] = 'Отображается при редактировании темы.';
$txt['optimus_show_keywords_block']                 = 'Отображать блок с ключевыми словами над первым сообщением темы';
$txt['optimus_correct_http_status']                 = 'Возвращать <a href="https://goo.gl/1UHxeB" target="_blank" rel="noopener" class="bbc_link">код 403/404</a>, в зависимости от статуса запрашиваемой страницы';

$txt['optimus_extra_settings']        = 'Дополнительно';
$txt['optimus_use_only_cookies']      = 'Использовать куки для хранения идентификатора сессии на стороне клиента';
$txt['optimus_use_only_cookies_help'] = 'Включение параметра <a href="https://www.php.net/manual/ru/session.configuration.php#ini.session.use-only-cookies" target="_blank" rel="noopener" class="bbc_link">session.use_only_cookies</a> предотвращает атаки с использованием идентификатора сессии, размещенного в URL.<br>Кроме того, вы сможете избавиться от идентификатора сессии в канонических адресах страниц форума.';
$txt['optimus_remove_index_php']      = 'Убрать «index.php» из адресов форума';
$txt['optimus_extend_h1']             = 'Добавлять заголовок страницы к тегу <strong>H1</strong>';

$txt['optimus_extra_title'] = 'Микроразметка';
$txt['optimus_extra_desc']  = 'Добавление дополнительной <a href="https://ruogp.me/" target="_blank" rel="noopener" class="bbc_link">разметки</a> для страниц форума.';

$txt['optimus_og_image']         = 'Использовать изображение из первого сообщения темы в мета-теге <strong>og:image</strong>';
$txt['optimus_og_image_subtext'] = 'По умолчанию используется изображение, заданное в <a href="%s" class="bbc_link">настройках текущей темы оформления</a>.';
$txt['optimus_og_image_help']    = 'Если включено, в мета-тег <strong>og:image</strong> подставляется ссылка на первое изображение, приложенное к первому сообщению темы. Если вложения нет, а в тексте сообщения будет найдено изображение внутри тега <strong>img</strong>, используется оно.';
$txt['optimus_fb_appid']         = 'ID приложения Facebook (если есть)';
$txt['optimus_fb_appid_help']    = 'Создайте приложение <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener" class="bbc_link"><strong>здесь</strong></a>, скопируйте его ID и укажите в этом поле.';
$txt['optimus_tw_cards']         = 'Имя аккаунта в Twitter (если есть)';
$txt['optimus_tw_cards_help']    = 'Подробнее о карточках Twitter можно почитать <a href="https://dev.twitter.com/cards/overview" target="_blank" rel="noopener" class="bbc_link"><strong>здесь</strong></a>.';

$txt['optimus_favicon_title'] = 'Иконка сайта';
$txt['optimus_favicon_desc']  = 'Создайте свой значок для форума. Он будет отображаться браузером во вкладке перед названием страниц, а также в качестве картинки рядом с закладкой, во вкладках и в других элементах интерфейса.';

$txt['optimus_favicon_create']  = 'Создать иконку для сайта';
$txt['optimus_favicon_api_key'] = 'Ключ API для работы с генератором иконки (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank" rel="noopener" class="bbc_link">Получить</a>)';
$txt['optimus_favicon_text']    = 'Код для вставки favicon';
$txt['optimus_favicon_help']    = 'Сгенерируйте свою уникальную иконку, например, <a href="https://www.favicomatic.com/" target="_blank" rel="noopener" class="bbc_link">здесь</a>, или <a href="https://digitalagencyrankings.com/iconogen/" target="_blank" rel="noopener" class="bbc_link">здесь</a>, или воспользуйтесь генератором, указав ключ API в поле выше. Затем загрузите файлы иконки в корень форума, а предложенный на сайте код сохраните в поле справа. Этот код будет загружаться в верхней части страниц, между тегами &lt;<strong>head</strong>&gt;&lt;/<strong>head</strong>&gt;.';

$txt['optimus_meta_title'] = 'Мета-теги';
$txt['optimus_meta_desc']  = 'Добавление в код страниц форума дополнительных мета-тегов, например, для подтверждения права собственности на сайт.';

$txt['optimus_meta_addtag']    = 'Добавить тег';
$txt['optimus_meta_customtag'] = 'Пользовательский мета-тег';
$txt['optimus_meta_tools']     = 'Поисковик (Сервис)';
$txt['optimus_meta_name']      = 'Имя тега';
$txt['optimus_meta_content']   = 'Значение';
$txt['optimus_meta_info']      = 'Пожалуйста, указывайте только значения, содержащиеся в добавляемых мета-тегах (а не теги целиком).<br>Например: <span class="smalltext">&lt;meta name="<strong>ИМЯ ТЕГА</strong>" content="<strong>ЗНАЧЕНИЕ</strong>"&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="https://www.google.com/webmasters/tools/" target="_blank" rel="noopener">Google Search Console</a>'),
	'Yandex' => array('yandex-verification', '<a href="https://webmaster.yandex.ru/" target="_blank" rel="noopener">Яндекс.Вебмастер</a>'),
	'Mail'   => array('wmail-verification', '<a href="https://webmaster.mail.ru" target="_blank" rel="noopener">Поиск Mail.Ru - Кабинет вебмастера</a>'),
	'Bing'   => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank" rel="noopener">Bing - Средства веб-мастера</a>')
);

$txt['optimus_counters']      = 'Счётчики';
$txt['optimus_counters_desc'] = 'Добавляйте и изменяйте всевозможные счетчики для подсчета посещений форума.';

$txt['optimus_head_code']       = 'Невидимые счётчики с загрузкой в секции <strong>head</strong> (<a href="https://www.google.ru/analytics/" target="_blank" rel="noopener" class="bbc_link">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Другие невидимые счётчики (например, <a href="https://metrika.yandex.ru/" target="_blank" rel="noopener" class="bbc_link">Яндекс.Метрика</a> без информера)';
$txt['optimus_count_code']      = 'Обычные счётчики (<a href="https://www.liveinternet.ru/add" target="_blank" rel="noopener" class="bbc_link">LiveInternet</a>, <a href="https://top100.rambler.ru/" target="_blank" rel="noopener" class="bbc_link">Rambler\'s Top100</a> и т. п.)';
$txt['optimus_counters_css']    = 'Оформление блока со счётчиками (CSS)';
$txt['optimus_ignored_actions'] = 'Игнорируемые области (actions) &mdash; на этих страницах счетчики подгружаться не будут!';

$txt['optimus_robots_title'] = 'Редактор robots.txt';
$txt['optimus_robots_desc']  = 'Генератор правил обновляется в зависимости от установленных модов и некоторых настроек SMF.';

$txt['optimus_manage']      = 'Настройка robots.txt';
$txt['optimus_root_path']   = 'Путь к корневой директории сайта';
$txt['optimus_rules']       = 'Генератор правил';
$txt['optimus_rules_hint']  = 'Можете воспользоваться этими заготовками для создания своих правил (в области справа):';
$txt['optimus_useful']      = '<a href="https://dragomano.ru/articles/pravilnyj-robotstxt-dlja-smf" target="_blank" rel="noopener" class="bbc_link">Правильный robots.txt для SMF</a>';
$txt['optimus_links_title'] = 'Полезные ссылки';
$txt['optimus_links']       = array(
	'Проверка robots.txt'                       => 'https://webmaster.yandex.ru/robots.xml',
	'Как настроить редирект'                    => 'https://goo.gl/LVPRpr',
	'Комплексный SEO-аудит всего сайта'         => 'https://goo.gl/TBw79p',
	'Авторегистрация форума в каталогах Рунета' => 'https://goo.gl/uAR3CZ',
	'Автоматическое продвижение вашего сайта'   => 'https://goo.gl/RMSDnx'
);

$txt['optimus_sitemap_title'] = 'Карта форума';
$txt['optimus_sitemap_desc']  = 'Optimus умеет создавать xml-карту для форумов любого размера.';

$txt['optimus_sitemap_enable']                  = 'Активировать карту форума';
$txt['optimus_sitemap_link']                    = 'Показывать ссылку на карту форума в подвале';
$txt['optimus_main_page_frequency']             = 'Частота изменения главной страницы';
$txt['optimus_main_page_frequency_set']         = array('Постоянная (always)', 'В зависимости от даты последнего сообщения');
$txt['optimus_sitemap_boards']                  = 'Добавлять в карту ссылки на разделы форума';
$txt['optimus_sitemap_boards_subtext']          = 'Разделы, закрытые для гостей, добавлены НЕ будут.';
$txt['optimus_sitemap_topics_num_replies']      = 'Добавлять в карту только темы с количеством ответов более';
$txt['optimus_sitemap_items_display']           = 'Максимальное количество элементов в одном XML-файле';
$txt['optimus_sitemap_all_topic_pages']         = 'Добавлять в карту ВСЕ страницы тем';
$txt['optimus_sitemap_all_topic_pages_subtext'] = 'Если не отмечено, в карту будут добавляться только первые страницы тем.';

$txt['optimus_404_page_title']       = '404 - Страница не найдена';
$txt['optimus_404_h2']               = 'Ошибка 404';
$txt['optimus_404_h3']               = 'Извините, но такой страницы здесь нет.';
$txt['optimus_403_page_title']       = '403 - Доступ запрещён';
$txt['optimus_403_h2']               = 'Ошибка 403';
$txt['optimus_403_h3']               = 'Извините, но у вас нет доступа к этой странице.';
$txt['optimus_seo_description']      = 'Описание темы [SEO]';
$txt['optimus_seo_keywords']         = 'Ключевые слова [SEO]';
$txt['optimus_enter_keywords']       = 'Введите одно или несколько ключевых слов';
$txt['optimus_topics_with_keyword']  = 'Темы форума с ключевым словом «%s»';
$txt['optimus_keyword_id_not_found'] = 'Указанный идентификатор ключевого слова не найден.';
$txt['optimus_no_keywords']          = 'Информация по данному идентификатору ключевого слова отсутствует.';
$txt['optimus_all_keywords']         = 'Все ключевые слова в темах форума';
$txt['optimus_keyword_column']       = 'Ключевое слово';
$txt['optimus_frequency_column']     = 'Частотность';
