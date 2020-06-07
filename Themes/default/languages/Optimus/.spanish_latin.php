<?php

/**
 * Spanish translation by Rock Lee (https://www.bombercode.net) Copyright 2014-2019
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_title'] = 'Optimizaci&oacute;n de motores de b&uacute;squeda';

$txt['optimus_base_title'] = 'Base de ceonfiguraci&oacute;n';

$txt['optimus_main_page']   = 'P&aacute;gina principal';
$txt['optimus_base_info']   = 'Bueno, el contenido de la etiqueta de descripci&oacute;n puede tenerse en cuenta cuando el robot determina si una p&aacute;gina coincide con una consulta de b&uacute;squeda.';
$txt['optimus_forum_index'] = 'T&iacute;tulo de la p&aacute;gina del foro';
$txt['optimus_description'] = 'La anotaci&oacute;n del foro<br /><span class="smalltext">Se usar&aacute; como contenido de la meta-etiqueta <strong>descripci&oacute;n</strong>.</span>';

$txt['optimus_all_pages'] = 'P&aacute;gina(s) de tema(s) y foro(s)';
$txt['optimus_tpl_info']  = 'Posibles variables:<br/><strong>{board_name}</strong> &mdash; nombre del foro, <strong>{topic_name}</strong> &mdash; asunto del tema,<br/><strong>{#}</strong> &mdash; n&uacute;mero de p&aacute;gina actual, <strong>{cat_name}</strong> &mdash; nombre de la categor&iacute;a, <strong>{forum_name}</strong> &mdash; el nombre de tu foro.';
$txt['optimus_board_tpl'] = 'Plantilla del t&iacute;tulo de las p&aacute;ginas del foro';
$txt['optimus_topic_tpl'] = 'Plantilla de t&iacute;tulo de p&aacute;ginas de temas';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - p&aacute;gina {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - p&aacute;gina {#} - ', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number']   = 'No mostrar n&uacute;mero para una primera p&aacute;gina';
$txt['optimus_board_description'] = 'Descripci&oacute;n de la pantalla como la meta-etiqueta <strong>descripci&oacute;n</strong>';
$txt['optimus_topic_description'] = 'Mostrar la descripci&oacute;n del tema como la meta-etiqueta <strong>descripci&oacute;n</strong><br /><span class="smalltext">Use <a class="bbc_link" href="https://custom.simplemachines.org/mods/index.php?mod=3012" target="_blank">Descripciones de temas mod</a> para crear descripciones cortas para los temas.</span>';
$txt['optimus_404_status']        = 'Retorno <a class="bbc_link" href="https://es.wikipedia.org/wiki/Anexo:C%C3%B3digos_de_estado_HTTP" target="_blank">C&oacute;digo 403/404</a> dependiendo del estado de la p&aacute;gina solicitada';
$txt['optimus_404_page_title']    = '404 - P&aacute;gina no encontrada';
$txt['optimus_404_h2']            = 'Error 404';
$txt['optimus_404_h3']            = 'Lo sentimos, pero la p&aacute;gina solicitada no existe.';
$txt['optimus_403_page_title']    = '403 - Acceso Prohibido';
$txt['optimus_403_h2']            = 'Error 403';
$txt['optimus_403_h3']            = 'Lo sentimos, pero no tiene acceso a esta p&aacute;gina.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc']  = 'Crea tu propio &iacute;cono de foro. Se mostrar&aacute; en el navegador en la pesta&ntilde;a antes del nombre de la p&aacute;gina, as&iacute; como en una imagen junto a la pesta&ntilde;a abierta y otros elementos de la interfaz.';

$txt['optimus_favicon_create']  = 'Crear el favicon';
$txt['optimus_favicon_api_key'] = 'Clave API para trabajar con Favicon Generator (<a class="bbc_link" href="https://realfavicongenerator.net/api/#register_key" target="_blank">Obtener la clave API</a>)';
$txt['optimus_favicon_text']    = 'El c&oacute;digo del favicon';
$txt['optimus_favicon_help']    = 'Genera tu propio favicon <a class="bbc_link" href="http://www.favicomatic.com/" target="_blank">Aqu&iacute;</a>, o use un generador especial (necesita ingresar la clave API en el campo de arriba).<br />A continuaci&oacute;n, cargue los archivos de favicon en la ra&iacute;z del foro y guarde el c&oacute;digo del sitio del generador en el campo de la derecha.<br />Este c&oacute;digo se cargar&aacute; en la parte superior de las p&aacute;ginas del sitio, entre las etiquetas &lt;head&gt;&lt;/head&gt;.';

$txt['optimus_extra_title'] = 'Metadata';
$txt['optimus_extra_desc']  = 'Aqu&iacute; puedes encontrar algunas soluciones para tu foro. Adem&aacute;s, puede habilitar la compatibilidad con Open Graph y JSON-LD. &iexcl;Disfrutar!';

$txt['optimus_open_graph'] = '<a class="bbc_link" href="http://ogp.me/" target="_blank">Open Graph</a> meta etiquetas para p&aacute;ginas del foro';
$txt['optimus_og_image']   = 'Enlace a su imagen predeterminada de Open Graph<br /><span class="smalltext">Ser&aacute; reemplazado por la inserci&oacute;n del primer mensaje en los temas (si existe).</span>';
$txt['optimus_fb_appid']   = '<a class="bbc_link" href="https://developers.facebook.com/apps" target="_blank">APP ID</a> (ID de aplicaci&oacute;n) <a class="bbc_link" href="https://www.facebook.com/" target="_blank">Facebook</a> (si hay)';
$txt['optimus_tw_cards']   = '<a class="bbc_link" href="https://twitter.com/" target="_blank">Twitter</a> nombre de cuenta (especifique para habilitar <a class="bbc_link" href="https://dev.twitter.com/cards/overview" target="_blank">Tarjetas de Twitter</a>)';

$txt['optimus_meta_title'] = 'Meta-etiquetas';
$txt['optimus_meta_desc']  = 'En esta p&aacute;gina puede agregar cualquier c&oacute;digo regular/de verificaci&oacute;n de la lista a continuaci&oacute;n.';

$txt['optimus_meta_addtag']    = 'Haga clic aqu&iacute; para agregar una nueva etiqueta';
$txt['optimus_meta_customtag'] = 'Etiqueta meta personalizada';
$txt['optimus_meta_tools']     = 'Motor de b&uacute;squeda (Herramientas)';
$txt['optimus_meta_name']      = 'Nombre';
$txt['optimus_meta_content']   = 'Contenido';
$txt['optimus_meta_info']      = 'Por favor, use s&oacute;lo los valores de <strong>contenido</strong> en los par&aacute;metro de las etiquetas meta.<br />Ejemplo: <span class="smalltext">&lt;meta name=&quot;<strong>NOMBRE</strong>&quot; content=&quot;<strong>VALOR</strong>&quot; /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a class="bbc_link" href="https://www.google.com/webmasters/tools/" target="_blank">Google Search Console</a>'),
	'Yandex' => array('yandex-verification', '<a class="bbc_link" href="https://webmaster.yandex.com/" target="_blank">Yandex.Webmaster</a>'),
	'Bing'   => array('msvalidate.01', '<a class="bbc_link" href="https://www.bing.com/toolbox/webmaster/" target="_blank">Bing Webmaster</a>')
);

$txt['optimus_counters']      = 'Contadores';
$txt['optimus_counters_desc'] = 'Puede agregar y cambiar los contadores en esta secci&oacute;n para registrar las visitas de su foro.';

$txt['optimus_head_code']       = 'Contadores invisibles cargando en la secci&oacute;n de <strong>cabecera</strong> (<a class="bbc_link" href="https://www.google.com/analytics/sign_up.html" target="_blank">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Otros contadores invisibles';
$txt['optimus_count_code']      = 'Contadores visibles';
$txt['optimus_counters_css']    = 'Apariencia para contadores visibles (CSS)';
$txt['optimus_ignored_actions'] = 'Acciones ignoradas';

$txt['optimus_robots_title'] = 'Editor robots.txt';
$txt['optimus_robots_desc']  = 'En esta p&aacute;gina puede cambiar algunas opciones de la creaci&oacute;n del mapa del foro, as&iacute; como modificar un archivo robots.txt mediante el uso de un generador especial.';

$txt['optimus_manage']      = 'Administrar robots.txt';
$txt['optimus_rules']       = 'Robots.txt generador';
$txt['optimus_rules_hint']  = 'Puede copiar estas reglas en el campo de la derecha:';
$txt['optimus_robots_hint'] = 'Aqu&iacute; puede insertar sus propias reglas o modificar las existentes:';
$txt['optimus_useful']      = '';

$txt['optimus_sitemap_title'] = 'Optimus Sitemap';
$txt['optimus_sitemap_desc']  = '&iquest;Quieres un mapa del sitio simple? Optimus puede generar sitemap.xml para peque&ntilde;os foros.';

$txt['optimus_sitemap_enable'] = 'Crear y actualizar peri&oacute;dicamente el archivo XML de Sitemap';
$txt['optimus_sitemap_link']   = 'Mostrar enlace XML de Sitemap en el pie de p&aacute;gina';
$txt['optimus_sitemap_boards'] = 'Agregar enlaces a los foros en el mapa del sitio<br /><span class="smalltext error">No se agregar&aacute;n paneles cerrados a los invitados.</span>';
$txt['optimus_sitemap_topics'] = 'Agregue al sitemap solo aquellos temas que tienen el n&uacute;mero de respuestas es m&aacute;s que';
