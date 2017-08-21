<?php

/**
 * .russian-utf8 language file
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_main']  = 'Optimus';
$txt['optimus_title'] = 'Поисковая оптимизация';

$txt['optimus_common_title'] = 'Общие настройки';
$txt['optimus_common_desc']  = 'Изменение описания форума, настройка шаблонов заголовков страниц разделов и тем, а также включение/отключение генерации карты сайта.';

$txt['optimus_main_page']         = 'Главная страница';
$txt['optimus_common_info']       = 'Содержание мета-тега description может использоваться в сниппетах на странице результатов поиска.';
$txt['optimus_portal_compat']     = 'Интеграция с порталом';
$txt['optimus_portal_compat_set'] = array('Нет', 'PortaMx', 'SimplePortal', 'TinyPortal');
$txt['optimus_portal_index']      = 'Заголовок главной страницы портала';
$txt['optimus_forum_index']       = 'Заголовок главной страницы форума';
$txt['optimus_description']       = 'Краткое, но интересное описание форума<br /><span class="smalltext">Будет выведено в мета-теге <strong>description</strong>.</span>';

$txt['optimus_all_pages'] = 'Страницы тем и разделов';
$txt['optimus_tpl_info']  = 'Доступные переменные:<br/><strong>{board_name}</strong> &mdash; название раздела, <strong>{topic_name}</strong> &mdash; название темы,<br/><strong>{#}</strong> &mdash; номер текущей страницы, <strong>{cat_name}</strong> &mdash; название категории, <strong>{forum_name}</strong> &mdash; название форума.';
$txt['optimus_board_tpl'] = 'Шаблон заголовка страниц разделов';
$txt['optimus_topic_tpl'] = 'Шаблон заголовка страниц тем';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - стр. {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - стр. {#} - ', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number']   = 'Не выводить номер на первой странице';
$txt['optimus_board_description'] = 'Выводить описание раздела в мета-теге <strong>description</strong>';
$txt['optimus_topic_description'] = 'Выводить описание темы в мета-теге <strong>description</strong><br /><span class="smalltext">Для создания описаний к темам используйте мод <a href="https://dragomano.ru/translations/topic-descriptions" target="_blank">Topic Descriptions</a>.</span>';
$txt['optimus_404_status']        = 'Возвращать <a href="https://ru.wikipedia.org/wiki/HTTP#.D0.9A.D0.BE.D0.B4.D1.8B_.D1.81.D0.BE.D1.81.D1.82.D0.BE.D1.8F.D0.BD.D0.B8.D1.8F" target="_blank">код 403/404</a>, в зависимости от статуса запрашиваемой страницы';
$txt['optimus_404_page_title']    = '404 - Страница не найдена';
$txt['optimus_404_h2']            = 'Ошибка 404';
$txt['optimus_404_h3']            = 'Извините, но такой страницы здесь нет.';
$txt['optimus_403_page_title']    = '403 - Доступ запрещён';
$txt['optimus_403_h2']            = 'Ошибка 403';
$txt['optimus_403_h3']            = 'Извините, но у вас нет доступа к этой странице.';

$txt['optimus_extra_title'] = 'Дополнительно';
$txt['optimus_extra_desc']  = 'Некоторые экспериментальные фиксы, а также активация поддержки Open Graph. Наслаждайтесь!';

$txt['optimus_remove_last_bc_item'] = 'Правильные «хлебные крошки» (последний пункт не будет ссылкой)';
$txt['optimus_correct_prevnext']    = '<a href="http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html" target="_blank">Корректные rel="next" и rel="prev"</a> (постраничная навигация в темах)';
$txt['optimus_open_graph']          = 'Мета-теги <a href="http://ruogp.me/" target="_blank">Open Graph</a> для страниц форума';
$txt['optimus_og_image']            = 'Ссылка на изображение по умолчанию для Open Graph<br /><span class="smalltext">В темах будет использоваться вложение из первого сообщения (если есть).</span>';
$txt['optimus_fb_appid']            = '<a href="https://developers.facebook.com/apps" target="_blank">APP ID</a> (ID приложения) <a href="https://www.facebook.com/" target="_blank">Facebook</a> (если есть)';
$txt['optimus_tw_cards']            = 'Имя аккаунта в <a href="https://twitter.com/" target="_blank">Twitter</a> (укажите, чтобы включить поддержку <a href="https://dev.twitter.com/cards/overview" target="_blank">карточек</a>)';
$txt['optimus_json_ld']             = 'Разметка <a href="https://json-ld.org/" target="_blank">JSON-LD</a> для «<a href="https://developers.google.com/search/docs/data-types/breadcrumbs?hl=' . $txt['lang_dictionary'] . '" target="_blank">хлебных крошек</a>»';

$txt['optimus_verification_title'] = 'Проверочные мета-теги';
$txt['optimus_verification_desc']  = 'Добавление в код страниц форума специальных проверочных кодов, для подтверждения права собственности на сайт.';

$txt['optimus_codes']          = 'Проверочные мета-теги';
$txt['optimus_titles']         = 'Поисковик (Сервис)';
$txt['optimus_name']           = 'Имя тега';
$txt['optimus_content']        = 'Значение';
$txt['optimus_meta_info']      = 'Пожалуйста, указывайте только значения, содержащиеся в добавляемых мета-тегах (а не теги целиком).<br />Например: <span class="smalltext">&lt;meta name="<strong>ИМЯ ТЕГА</strong>" content="<strong>ЗНАЧЕНИЕ</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="https://www.google.com/webmasters/tools/" target="_blank">Инструменты веб-мастера</a>'),
	'Yandex' => array('yandex-verification', '<a href="https://webmaster.yandex.ru/" target="_blank">Яндекс.Вебмастер</a>'),
	'Mail'   => array('wmail-verification', '<a href="https://webmaster.mail.ru" target="_blank">Поиск Mail.Ru - Кабинет вебмастера</a>'),
	'Bing'   => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank">Bing веб-мастер</a>')
);

$txt['optimus_counters']      = 'Счётчики';
$txt['optimus_counters_desc'] = 'Добавляйте и изменяйте всевозможные счетчики для подсчета посещений форума.';

$txt['optimus_head_code']       = 'Невидимые счётчики с загрузкой в секции <strong>head</strong> (<a href="https://google.ru/analytics/sign_up.html" target="_blank">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Другие невидимые счётчики (например, <a href="https://metrika.yandex.ru/" target="_blank">Яндекс.Метрика</a> без информера)';
$txt['optimus_count_code']      = 'Обычные счётчики (<a href="https://www.liveinternet.ru/add" target="_blank">LiveInternet</a>, <a href="https://top100.rambler.ru/top100/rules.shtml.ru" target="_blank">Rambler\'s Top100</a> и т. п.)';
$txt['optimus_counters_css']    = 'Оформление блока со счётчиками (CSS)';
$txt['optimus_ignored_actions'] = 'Игнорируемые области (actions) &mdash; на этих страницах счетчики подгружаться не будут!';
$txt['optimus_ga_note']         = 'На заметку: <a href="https://www.simplemachines.ru/index.php?topic=12304.0" target="_blank">Реальный показатель отказов в Google Analytics</a>';

$txt['optimus_robots_title'] = 'Редактор robots.txt';
$txt['optimus_robots_desc']  = 'К вашим услугам генератор правил для создания robots.txt.';

$txt['optimus_manage']      = 'Настройка robots.txt';
$txt['optimus_rules']       = 'Генератор правил';
$txt['optimus_rules_hint']  = 'Можете воспользоваться этими заготовками для создания своих правил в области справа:';
$txt['optimus_robots_hint'] = 'Сюда можно вставить собственные правила или изменить существующие:';
$txt['optimus_useful']      = '<a href="https://dragomano.ru/articles/pravilnyj-robotstxt-dlja-smf" target="_blank">Правильный robots.txt для SMF</a>';
$txt['optimus_links_title'] = 'Полезные ссылки';
$txt['optimus_links']       = array(
	'Как настроить редирект'                             => 'https://beget.com/p1361/ru/articles/htaccess',
	'Использование robots.txt (справка Яндекса)'         => 'https://help.yandex.ru/webmaster/?id=996567',
	'Проверка robots.txt'                                => 'https://webmaster.yandex.ru/robots.xml',
	'Блокировка и удаление страниц с помощью robots.txt' => 'https://support.google.com/webmasters/topic/6061961?hl=ru&ref_topic=4598466',
	'Частые ошибки в robots.txt'                         => 'https://robotstxt.org.ru/robotstxterrors',
	'Авторегистрация форума в каталогах Рунета'          => 'https://1ps.ru/info/?p=383933',
	'Автоматическое продвижение вашего сайта'            => 'https://www.webeffector.ru/?invitation=f1d58982cd75dbe8e19be3d54a6b25fe'
);

$txt['optimus_sitemap_title'] = 'Карта форума';
$txt['optimus_sitemap_desc']  = 'Optimus предоставляет возможность создать простую xml-карту, для небольших форумов. Обновляться такая карта будет в зависимости от настроек в <a href="%1$s">Диспетчере задач</a>.';

$txt['optimus_sitemap_enable']      = 'Создать и периодически обновлять xml-карту форума';
$txt['optimus_sitemap_link']        = 'Показывать ссылку на xml-карту в подвале форума';
$txt['optimus_sitemap_boards']      = 'Добавлять в карту ссылки на разделы форума<br /><span class="smalltext error">Разделы, закрытые для гостей, добавлены НЕ будут.</span>';
$txt['optimus_sitemap_topics']      = 'Добавлять в карту темы с количеством сообщений больше';

$txt['optimus_sitemap_rec']        = ' Optimus пока не умеет разбивать файлы на несколько частей.';
$txt['optimus_sitemap_url_limit']  = 'В файле sitemap должно быть не более 50 тысяч ссылок!';
$txt['optimus_sitemap_size_limit'] = 'Размер файла %1$s не должен превышать 10 МБ!';
$txt['optimus_sitemap_xml_link']   = 'Sitemap XML';

// Диспетчер задач
$txt['scheduled_task_optimus_sitemap']      = 'Генерация XML-карты форума';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Настройте периодичность создания карты, если хотите.';

// Реклама
$txt['optimus_1ps_ads'] = '<h4>Онлайн-курс «SEO оптимизация и продвижение сайта самостоятельно»</h4>
<p>Научись продвигать сайт за пять занятий! Специально для новичков мы разработали курс по базовым правилам SEO. Вы получите подробную пошаговую инструкцию, как самостоятельно провести SEO оптимизацию сайта и продвинуть его в ТОП.</p>
<br/>
<p>По окончании курса Вы:
<ul>
<li>Разберетесь в принципах работы поисковых систем.</li>
<li>Научитесь планировать продвижение своего сайта и анализировать результат.</li>
<li>Узнаете основные технические требования, которые поисковики предъявляют к сайтам, и сможете корректно настроить свой сайт, а также устранить причины, мешающие продвижению.</li>
<li>Разберетесь, как оптимизировать контент на сайте.</li>
<li>Сможете анализировать сайты и объективно оценивать их качество.</li>
</ul>
<br/>
<p><a href="https://go.1ps.ru/promo/?p=383933&fm_promocode=949796R252" target="_blank">Ссылка для активации промокода</a></p>';
