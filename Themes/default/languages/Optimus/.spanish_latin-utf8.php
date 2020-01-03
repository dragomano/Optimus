<?php

/**
 * Spanish translation by Rock Lee (https://www.bombercode.net) Copyright 2014-2019
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_main']  = 'Optimus';
$txt['optimus_title'] = 'Optimización de motores de búsqueda';

$txt['optimus_base_title'] = 'Base de ceonfiguración';
$txt['optimus_base_desc']  = 'En esta página puede cambiar la descripción de un foro, el administrador de plantillas de títulos de páginas, activar/desactivar la generación XML de Sitemap.';

$txt['optimus_main_page']         = 'Página principal';
$txt['optimus_base_info']         = 'Bueno, el contenido de la etiqueta de descripción puede tenerse en cuenta cuando el robot determina si una página coincide con una consulta de búsqueda.';
$txt['optimus_portal_compat']     = 'Compatibilidad con Portal';
$txt['optimus_portal_compat_set'] = array('None', 'PortaMx', 'SimplePortal', 'TinyPortal');
$txt['optimus_portal_index']      = 'Título de la página Portal';
$txt['optimus_forum_index']       = 'Título de la página del foro';
$txt['optimus_description']       = 'La anotación del foro<br /><span class="smalltext">Se usará como contenido de la meta-etiqueta <strong>descripción</strong>.</span>';

$txt['optimus_all_pages'] = 'Página(s) de tema(s) y foro(s)';
$txt['optimus_tpl_info']  = 'Posibles variables:<br/><strong>{board_name}</strong> &mdash; nombre del foro, <strong>{topic_name}</strong> &mdash; asunto del tema,<br/><strong>{#}</strong> &mdash; número de página actual, <strong>{cat_name}</strong> &mdash; nombre de la categoría, <strong>{forum_name}</strong> &mdash; el nombre de tu foro.';
$txt['optimus_board_tpl'] = 'Plantilla del título de las páginas del foro';
$txt['optimus_topic_tpl'] = 'Plantilla de título de páginas de temas';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - página {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - página {#} - ', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number']   = 'No mostrar número para una primera página';
$txt['optimus_board_description'] = 'Descripción de la pantalla como la meta-etiqueta <strong>descripción</strong>';
$txt['optimus_topic_description'] = 'Mostrar la descripción del tema como la meta-etiqueta <strong>descripción</strong><br /><span class="smalltext">Use <a href="https://custom.simplemachines.org/mods/index.php?mod=3012" target="_blank">Descripciones de temas mod</a> para crear descripciones cortas para los temas.</span>';
$txt['optimus_404_status']        = 'Retorno <a href="https://es.wikipedia.org/wiki/Anexo:C%C3%B3digos_de_estado_HTTP" target="_blank">Código 403/404</a> dependiendo del estado de la página solicitada';
$txt['optimus_404_page_title']    = '404 - Página no encontrada';
$txt['optimus_404_h2']            = 'Error 404';
$txt['optimus_404_h3']            = 'Lo sentimos, pero la página solicitada no existe.';
$txt['optimus_403_page_title']    = '403 - Acceso Prohibido';
$txt['optimus_403_h2']            = 'Error 403';
$txt['optimus_403_h3']            = 'Lo sentimos, pero no tiene acceso a esta página.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc']  = 'Crea tu propio ícono de foro. Se mostrará en el navegador en la pesta&ntilde;a antes del nombre de la página, así como en una imagen junto a la pesta&ntilde;a abierta y otros elementos de la interfaz.';

$txt['optimus_favicon_create']  = 'Crear el favicon';
$txt['optimus_favicon_api_key'] = 'Clave API para trabajar con Favicon Generator (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank">Obtener la clave API</a>)';
$txt['optimus_favicon_text']    = 'El código del favicon';
$txt['optimus_favicon_help']    = 'Genera tu propio favicon <a href="http://www.favicomatic.com/" target="_blank">Aquí</a>, o use un generador especial (necesita ingresar la clave API en el campo de arriba).<br />A continuación, cargue los archivos de favicon en la raíz del foro y guarde el código del sitio del generador en el campo de la derecha.<br />Este código se cargará en la parte superior de las páginas del sitio, entre las etiquetas &lt;head&gt;&lt;/head&gt;.';

$txt['optimus_extra_title'] = 'Metadata';
$txt['optimus_extra_desc']  = 'Aquí puedes encontrar algunas soluciones para tu foro. Además, puede habilitar la compatibilidad con Open Graph y JSON-LD. ¡Disfrutar!';

$txt['optimus_open_graph'] = '<a href="http://ogp.me/" target="_blank">Open Graph</a> meta etiquetas para páginas del foro';
$txt['optimus_og_image']   = 'Enlace a su imagen predeterminada de Open Graph<br /><span class="smalltext">Será reemplazado por la inserción del primer mensaje en los temas (si existe).</span>';
$txt['optimus_fb_appid']   = '<a href="https://developers.facebook.com/apps" target="_blank">APP ID</a> (ID de aplicación) <a href="https://www.facebook.com/" target="_blank">Facebook</a> (si hay)';
$txt['optimus_tw_cards']   = '<a href="https://twitter.com/" target="_blank">Twitter</a> nombre de cuenta (especifique para habilitar <a href="https://dev.twitter.com/cards/overview" target="_blank">Tarjetas de Twitter</a>)';
$txt['optimus_json_ld']    = '<a href="https://json-ld.org/" target="_blank">JSON-LD</a> marcado para "<a href="https://developers.google.com/search/docs/data-types/breadcrumbs?hl=' . $txt['lang_dictionary'] . '" target="_blank">Migas de pan</a>"'; // No encontré otro significado - I did not find another meaning

$txt['optimus_meta_title'] = 'Meta-etiquetas';
$txt['optimus_meta_desc']  = 'En esta página puede agregar cualquier código regular/de verificación de la lista a continuación.';

$txt['optimus_meta_addtag']    = 'Haga clic aquí para agregar una nueva etiqueta';
$txt['optimus_meta_customtag'] = 'Etiqueta meta personalizada';
$txt['optimus_meta_tools']     = 'Motor de búsqueda (Herramientas)';
$txt['optimus_meta_name']      = 'Nombre';
$txt['optimus_meta_content']   = 'Contenido';
$txt['optimus_meta_info']      = 'Por favor, use sólo los valores de <strong>contenido</strong> en los parámetro de las etiquetas meta.<br />Ejemplo: <span class="smalltext">&lt;meta name=&quot;<strong>NOMBRE</strong>&quot; content=&quot;<strong>VALOR</strong>&quot; /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="https://www.google.com/webmasters/tools/" target="_blank">Google Search Console</a>'),
	'Yandex' => array('yandex-verification', '<a href="https://webmaster.yandex.com/" target="_blank">Yandex.Webmaster</a>'),
	'Bing'   => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank">Bing Webmaster</a>')
);

$txt['optimus_counters']      = 'Contadores';
$txt['optimus_counters_desc'] = 'Puede agregar y cambiar los contadores en esta sección para registrar las visitas de su foro.';

$txt['optimus_head_code']       = 'Contadores invisibles cargando en la sección de <strong>cabecera</strong> (<a href="https://www.google.com/analytics/sign_up.html" target="_blank">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Otros contadores invisibles (<a href="https://matomo.org/" target="_blank">Matomo</a> etc)';
$txt['optimus_count_code']      = 'Contadores visibles (<a href="http://www.freestats.com/" target="_blank">FreeStats</a>, <a href="http://www.superstats.com/" target="_blank">SuperStats</a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank">PRTracker</a> etc)';
$txt['optimus_counters_css']    = 'Apariencia para contadores visibles (CSS)';
$txt['optimus_ignored_actions'] = 'Acciones ignoradas';

$txt['optimus_robots_title'] = 'Editor robots.txt';
$txt['optimus_robots_desc']  = 'En esta página puede cambiar algunas opciones de la creación del mapa del foro, así como modificar un archivo robots.txt mediante el uso de un generador especial.';

$txt['optimus_manage']      = 'Administrar robots.txt';
$txt['optimus_rules']       = 'Robots.txt generador';
$txt['optimus_rules_hint']  = 'Puede copiar estas reglas en el campo de la derecha:';
$txt['optimus_robots_hint'] = 'Aquí puede insertar sus propias reglas o modificar las existentes:';
$txt['optimus_useful']      = '';
$txt['optimus_links_title'] = 'Enlaces útiles';
$txt['optimus_links']       = array(
	'Crea un archivo robots.txt'              => 'https://support.google.com/webmasters/answer/6062596?hl=es',
	'Usando robots.txt'                      => 'https://help.yandex.com/webmaster/?id=1113851',
	'Auditoría técnica de todo el sitio web' => 'https://netpeaksoftware.com/ucp?invite=94cdaf6a'
);

$txt['optimus_sitemap_title'] = 'Optimus Sitemap';
$txt['optimus_sitemap_desc']  = '¿Quieres un mapa del sitio simple? Optimus puede generar sitemap.xml para peque&ntilde;os foros. Simplemente active esta opción a continuación. Este mapa del sitio se actualizará según la configuración en <a href="%1$s">Administrador de tareas</a>.';

$txt['optimus_sitemap_enable']      = 'Crear y actualizar periódicamente el archivo XML de Sitemap';
$txt['optimus_sitemap_link']        = 'Mostrar enlace XML de Sitemap en el pie de página';
$txt['optimus_sitemap_boards']      = 'Agregar enlaces a los foros en el mapa del sitio<br /><span class="smalltext error">No se agregarán paneles cerrados a los invitados.</span>';
$txt['optimus_sitemap_topics']      = 'Agregue al sitemap solo aquellos temas que tienen el número de respuestas es más que';

$txt['optimus_sitemap_xml_link']   = 'Sitemap XML';

// Task Manager
$txt['scheduled_task_optimus_sitemap']      = 'Sitemap XML Gereration';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Puede establecer la frecuencia de la creación del mapa del sitio.';
