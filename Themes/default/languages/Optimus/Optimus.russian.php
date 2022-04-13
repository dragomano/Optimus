<?php

/**
 * russian language file
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_title'] = 'Поисковая оптимизация';

$txt['optimus_basic_title'] = 'Общие настройки';
$txt['optimus_basic_desc'] = 'Версия мода: <strong>%1$s</strong>, версия PHP: <strong>%2$s</strong>, версия %3$s: <strong>%4$s</strong>.<br>Обсудить баги и фичи мода можно на <a class="bbc_link" href="https://www.simplemachines.org/community/index.php?topic=422210.0">simplemachines.org</a>.<br>Вы также можете угостить разработчика <a class="bbc_link" href="https://qiwi.com/n/DRAGOMANO">кусочком киви</a>.';

$txt['optimus_main_page'] = 'Главная страница';
$txt['optimus_forum_index'] = 'Заголовок главной страницы форума';
$txt['optimus_description'] = 'Описание форума';
$txt['optimus_description_subtext'] = 'Будет выведено в мета-теге <strong>description</strong> главной страницы.';

$txt['optimus_all_pages'] = 'Страницы тем и разделов';
$txt['optimus_board_extend_title'] = 'Добавлять название форума к заголовкам разделов';
$txt['optimus_board_extend_title_set'] = array('Нет', 'Перед названием раздела', 'После названия раздела');
$txt['optimus_topic_extend_title'] = 'Добавлять название раздела и форума к заголовкам тем';
$txt['optimus_topic_extend_title_set'] = array('Нет', 'Перед названием темы', 'После названия темы');
$txt['optimus_topic_description'] = 'Использовать отрывок первого сообщения темы в качестве мета-тега <strong>description</strong>';
$txt['optimus_allow_change_topic_desc'] = 'Разрешить отдельное поле для описания темы';
$txt['optimus_allow_change_topic_desc_subtext'] = 'Отображается при редактировании темы.';
$txt['optimus_allow_change_topic_keywords'] = 'Разрешить отдельное поле для ключевых слов темы';
$txt['optimus_allow_change_topic_keywords_subtext'] = 'Отображается при редактировании темы.';
$txt['optimus_show_keywords_block'] = 'Отображать блок с ключевыми словами над первым сообщением темы';
$txt['optimus_correct_http_status'] = 'Возвращать <a href="https://goo.gl/1UHxeB" target="_blank" rel="noopener" class="bbc_link">код 403/404</a>, в зависимости от статуса запрашиваемой страницы';

$txt['optimus_extra_settings'] = 'Дополнительные настройки';
$txt['optimus_log_search'] = 'Вести статистику поисковых запросов';
$txt['optimus_disable_syntax_highlighting'] = 'Отключить подсветку синтаксиса в текстовых областях';

$txt['optimus_extra_title'] = 'Микроразметка';
$txt['optimus_extra_desc'] = 'Добавление дополнительной <a href="https://ruogp.me/" target="_blank" rel="noopener" class="bbc_link">разметки</a> для страниц форума. <a href="https://developers.facebook.com/docs/sharing/webmasters" target="_blank" rel="noopener" class="bbc_link">Руководство по публикации для веб-мастеров</a>.';
$txt['optimus_extra_info'] = 'Используйте <a href="https://webmaster.yandex.ru/tools/microtest/" target="_blank" rel="noopener" class="bbc_link">валидатор микроразметки</a> (Яндекс.Вебмастер) или <a href="https://developers.facebook.com/tools/debug" target="_blank" rel="noopener" class="bbc_link">отладчик репостов Facebook</a> для проверки микроразметки вашего сайта.<hr><strong>Примечание</strong>: Facebook кэширует картинки и другие OG-данные. Для сброса кэша нужно в отладчике репостов набрать адрес страницы с параметром <em>fbrefresh</em>, например: %1$s?fbrefresh=reset.';

$txt['optimus_og_image'] = 'Использовать изображение из первого сообщения темы в мета-теге <strong>og:image</strong>';
$txt['optimus_og_image_subtext'] = 'По умолчанию используется изображение, заданное в <a href="%s" class="bbc_link">настройках текущей темы оформления</a>.';
$txt['optimus_og_image_help'] = 'Если включено, в мета-тег <strong>og:image</strong> подставляется ссылка на первое изображение, приложенное к первому сообщению темы. Если вложения нет, а в тексте сообщения будет найдено изображение внутри тега <strong>img</strong>, используется оно.';
$txt['optimus_allow_change_board_og_image'] = 'Разрешить отдельное поле для картинки раздела (<strong>og:image</strong>)';
$txt['optimus_allow_change_board_og_image_subtext'] = 'Отображается при редактировании раздела.';
$txt['optimus_fb_appid'] = 'ID приложения Facebook (если есть)';
$txt['optimus_fb_appid_help'] = 'Создайте приложение <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener" class="bbc_link"><strong>здесь</strong></a>, скопируйте его ID и укажите в этом поле.';
$txt['optimus_tw_cards'] = 'Имя аккаунта в Twitter (если есть)';
$txt['optimus_tw_cards_help'] = 'Подробнее о карточках Twitter можно почитать <a href="https://dev.twitter.com/cards/overview" target="_blank" rel="noopener" class="bbc_link"><strong>здесь</strong></a>.';

$txt['optimus_favicon_title'] = 'Иконка сайта';
$txt['optimus_favicon_desc'] = 'Создайте свой значок для форума.<br>Он будет отображаться браузером в качестве картинки рядом с закладкой, во вкладках и в других элементах интерфейса.';

$txt['optimus_current_icon'] = 'Отображение иконки вашего сайта для поисковых ботов';
$txt['optimus_favicon_text'] = 'Код для вставки favicon';
$txt['optimus_favicon_help'] = 'Сгенерируйте свою уникальную иконку, например, <a href="https://www.favicomatic.com/" target="_blank" rel="noopener" class="bbc_link">здесь</a>, или <a href="https://digitalagencyrankings.com/iconogen/" target="_blank" rel="noopener" class="bbc_link">здесь</a>. Затем загрузите файлы иконки в корень форума, а предложенный на сайте код сохраните в поле справа. Этот код будет загружаться в верхней части страниц, между тегами &lt;<strong>head</strong>&gt;&lt;/<strong>head</strong>&gt;.';

$txt['optimus_meta_title'] = 'Мета-теги';
$txt['optimus_meta_desc'] = 'Добавление в код страниц форума дополнительных мета-тегов, например, для подтверждения права собственности на сайт.';

$txt['optimus_meta_addtag'] = 'Добавить тег';
$txt['optimus_meta_customtag'] = 'Пользовательский мета-тег';
$txt['optimus_meta_tools'] = 'Поисковик (Сервис)';
$txt['optimus_meta_name'] = 'Имя тега';
$txt['optimus_meta_content'] = 'Значение';
$txt['optimus_meta_info'] = 'Пожалуйста, указывайте только значения, содержащиеся в добавляемых мета-тегах (а не теги целиком).<br>Например: <span class="smalltext">&lt;meta name="<strong>ИМЯ ТЕГА</strong>" content="<strong>ЗНАЧЕНИЕ</strong>"&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Yandex' => array('yandex-verification', 'https://webmaster.yandex.ru/', 'Яндекс.Вебмастер'),
	'Google' => array('google-site-verification','https://www.google.com/webmasters/tools/', 'Google Search Console'),
	'Mail' => array('wmail-verification', 'https://webmaster.mail.ru', 'Поиск Mail.Ru - Кабинет вебмастера'),
	'Bing' => array('msvalidate.01', 'https://www.bing.com/toolbox/webmaster/', 'Bing - Средства веб-мастера')
);

$txt['optimus_counters'] = 'Счётчики';
$txt['optimus_counters_desc'] = 'Добавляйте и изменяйте всевозможные счетчики для подсчета посещений форума.';

$txt['optimus_head_code'] = 'Невидимые счётчики с загрузкой в секции <strong>head</strong>';
$txt['optimus_head_code_subtext'] = 'Например, <a href="https://www.google.ru/analytics/" target="_blank" rel="noopener" class="bbc_link">Google Analytics</a>';
$txt['optimus_stat_code'] = 'Невидимые счётчики с загрузкой в секции <strong>body</strong>';
$txt['optimus_stat_code_subtext'] = 'Например, <a href="https://metrika.yandex.ru/" target="_blank" rel="noopener" class="bbc_link">Яндекс.Метрика</a> без информера';
$txt['optimus_count_code'] = 'Обычные счётчики';
$txt['optimus_counters_css'] = 'Оформление блока с обычными счётчиками (CSS)';
$txt['optimus_ignored_actions'] = 'Игнорируемые области';
$txt['optimus_ignored_actions_subtext'] = 'На этих страницах счетчики подгружаться не будут!';

$txt['optimus_robots_title'] = 'Редактор robots.txt';
$txt['optimus_robots_desc'] = 'Генератор правил обновляется в зависимости от установленных модов и некоторых настроек SMF.';

$txt['optimus_manage'] = 'Настройка robots.txt';
$txt['optimus_rules'] = 'Генератор правил';
$txt['optimus_rules_hint'] = 'Можете воспользоваться этими заготовками для создания своих правил (в области справа):';
$txt['optimus_links_title'] = 'Полезные ссылки';
$txt['optimus_links'] = array(
	'Правильный robots.txt для SMF' => 'https://dragomano.ru/articles/pravilnyj-robotstxt-dlja-smf',
	'Проверка robots.txt' => 'https://webmaster.yandex.ru/robots.xml'
);

$txt['optimus_htaccess_title'] = 'Настройка .htaccess';
$txt['optimus_htaccess_desc'] = 'Здесь можно изменить файл .htaccess для вашего форума. Будьте осторожны!';

$txt['optimus_sitemap_title'] = 'Карта форума';
$txt['optimus_sitemap_desc'] = '%1$s умеет создавать простую XML-карту, в соответствии с расположенными ниже настройками.';

$txt['optimus_sitemap_enable'] = 'Активировать карту форума';
$txt['optimus_sitemap_enable_subtext'] = 'Карта будет создана/обновлена после сохранения настроек.';
$txt['optimus_sitemap_link'] = 'Показывать ссылку на карту в подвале';
$txt['optimus_remove_previous_xml_files'] = 'Удалять ранее созданные файлы sitemap*.xml';
$txt['optimus_main_page_frequency'] = 'Частота изменения главной страницы';
$txt['optimus_main_page_frequency_set'] = array('Постоянная (always)', 'В зависимости от даты последнего сообщения');
$txt['optimus_sitemap_boards'] = 'Добавлять в карту ссылки на разделы форума';
$txt['optimus_sitemap_boards_subtext'] = 'Разделы, закрытые для гостей, добавлены НЕ будут.';
$txt['optimus_sitemap_topics_num_replies'] = 'Добавлять в карту только темы с количеством ответов >=';
$txt['optimus_sitemap_items_display'] = 'Максимальное количество элементов на странице';
$txt['optimus_sitemap_all_topic_pages'] = 'Добавлять в карту ВСЕ страницы тем';
$txt['optimus_sitemap_all_topic_pages_subtext'] = 'Если не отмечено, в карту будут добавляться только первые страницы тем.';
$txt['optimus_start_year'] = 'В карту должны попадать записи, начиная с указанного года';
$txt['optimus_update_frequency'] = 'Периодичность обновления карты';
$txt['optimus_update_frequency_set'] = array('Раз в день', 'Раз в 3 дня', 'Раз в неделю', 'Раз в 2 недели', 'Раз в месяц');

$txt['optimus_mobile'] = 'Страницы для мобильных устройств';
$txt['optimus_images'] = 'Изображения';
$txt['optimus_news'] = 'Новости';
$txt['optimus_video'] = 'Видео';
$txt['optimus_index'] = 'Индекс';
$txt['optimus_total_files'] = 'Всего файлов';
$txt['optimus_total_urls'] = 'Всего ссылок';
$txt['optimus_last_modified'] = 'Изменено';
$txt['optimus_frequency'] = 'Периодичность';
$txt['optimus_priority'] = 'Приоритет';
$txt['optimus_direct_link'] = 'Прямая ссылка';
$txt['optimus_caption'] = 'Заголовок';
$txt['optimus_thumbnail'] = 'Миниатюра';

$txt['permissionname_optimus_add_descriptions'] = $txt['group_perms_name_optimus_add_descriptions'] = 'Добавление описаний для тем';
$txt['permissionhelp_optimus_add_descriptions'] = 'Возможность добавлять описание при создании/редактировании темы.';
$txt['permissionname_optimus_add_keywords'] = $txt['group_perms_name_optimus_add_keywords'] = 'Добавление ключевых слов для тем';
$txt['permissionhelp_optimus_add_keywords'] = 'Возможность добавлять ключевые слова при создании/редактировании темы.';
$txt['permissionname_optimus_add_descriptions_own'] = $txt['permissionname_optimus_add_keywords_own'] = 'Собственная тема';
$txt['permissionname_optimus_add_descriptions_any'] = $txt['permissionname_optimus_add_keywords_any'] = 'Любая тема';
$txt['group_perms_name_optimus_add_descriptions_own'] = 'Добавление описаний для собственных тем';
$txt['group_perms_name_optimus_add_descriptions_any'] = 'Добавление описаний для любых тем';
$txt['permissionname_optimus_view_search_terms'] = $txt['group_perms_name_optimus_view_search_terms'] = 'Просмотр статистики поисковых запросов';
$txt['permissionhelp_optimus_view_search_terms'] = 'Возможность просматривать статистику поиска на форуме.';

$txt['optimus_404_page_title'] = '404 - Страница не найдена';
$txt['optimus_404_h2'] = 'Ошибка 404';
$txt['optimus_404_h3'] = 'Извините, но такой страницы здесь нет.';
$txt['optimus_403_page_title'] = '403 - Доступ запрещён';
$txt['optimus_403_h2'] = 'Ошибка 403';
$txt['optimus_403_h3'] = 'Извините, но у вас нет доступа к этой странице.';
$txt['optimus_goto_main_page'] = 'Перейти на <a class="bbc_link" href="%1$s">главную страницу</a>.';
$txt['optimus_seo_description'] = 'Описание темы [SEO]';
$txt['optimus_seo_keywords'] = 'Ключевые слова [SEO]';
$txt['optimus_enter_keywords'] = 'Введите одно или несколько ключевых слов';
$txt['optimus_topics_with_keyword'] = 'Темы форума с ключевым словом «%s»';
$txt['optimus_keyword_id_not_found'] = 'Указанный идентификатор ключевого слова не найден.';
$txt['optimus_no_keywords'] = 'Информация по данному идентификатору ключевого слова отсутствует.';
$txt['optimus_all_keywords'] = 'Все ключевые слова в темах форума';
$txt['optimus_keyword_column'] = 'Ключевое слово';
$txt['optimus_frequency_column'] = 'Частотность';
$txt['optimus_top_queries'] = 'Популярные запросы';
$txt['optimus_chart_title'] = 'Топ-%1$s';
$txt['optimus_no_search_terms'] = 'Статистики пока нет.';
