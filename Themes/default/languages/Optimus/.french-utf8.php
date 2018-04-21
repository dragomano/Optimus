<?php

/**
 * .french UTF-8 language file
 * @Translation by alexetgus - https://chez-oim.org
 *
 * @package Optimus
 * @author Bugo https://dragomano.ru/mods/optimus
 */

$txt['optimus_main']  = 'Optimus';
$txt['optimus_title'] = 'SEO';

$txt['optimus_base_title'] = 'Réglages de base';
$txt['optimus_base_desc']  = 'Sur cette page, vous pouvez changer la description du forum, gérer les modèles de titres de vos pages, activer/désactiver la génération d\'un sitemap XML.';

$txt['optimus_main_page']         = 'page d\'accueil';
$txt['optimus_base_info']         = 'La balise "description" peut être prise en compte par les moteurs de recherche si une page correspond à la requête de la recherche.';
$txt['optimus_portal_compat']     = 'Compatibilité du portail';
$txt['optimus_portal_compat_set'] = array('None', 'PortaMx', 'SimplePortal', 'TinyPortal');
$txt['optimus_portal_index']      = 'Titre de la page d\'accueil du portail';
$txt['optimus_forum_index']       = 'Titre de la page d\'accueil du forum';
$txt['optimus_description']       = 'Description du forum<br /><span class="smalltext">Sera le contenu de la balise meta "description"</strong>.</span>';

$txt['optimus_all_pages'] = 'Page des sujets & sections';
$txt['optimus_tpl_info']  = 'Variables utilisables :<br/><strong>{board_name}</strong> &mdash; nom de la section, <strong>{topic_name}</strong> &mdash; titre du sujet,<br/><strong>{#}</strong> &mdash; numéro de la page, <strong>{cat_name}</strong> &mdash; nom de la catégorie, <strong>{forum_name}</strong> &mdash; nom de votre forum.';
$txt['optimus_board_tpl'] = 'Modèle des titres des pages des sections';
$txt['optimus_topic_tpl'] = 'Modèle des titres des pages des sujets';
$txt['optimus_templates'] = array(
	'board' => array('{board_name}', ' - page {#} - ', '{forum_name}'),
	'topic' => array('{topic_name}', ' - page {#} - ', '{board_name} - {forum_name}')
);
$txt['optimus_no_first_number']   = 'Ne pas afficher le numéro de la première page';
$txt['optimus_board_description'] = 'Utiliser le titre du sujet dans la balise meta "<strong>description</strong>"';
$txt['optimus_topic_description'] = 'Utiliser la description du sujet dans la balise meta "<strong>description</strong>"<br /><span class="smalltext">Utiliser <a href="https://custom.simplemachines.org/mods/index.php?mod=3012" target="_blank">le mod "Topic Descriptions"</a> pour créer des descriptions courtes de vos sujets.</span>';
$txt['optimus_404_status']        = 'Retourner le <a href="https://en.wikipedia.org/wiki/List_of_HTTP_status_codes" target="_blank">code de statut</a> 403/404 en fonction de l\'état de la page demandée';
$txt['optimus_404_page_title']    = '404 - Page inexistante';
$txt['optimus_404_h2']            = 'Erreur 404';
$txt['optimus_404_h3']            = 'Désolé, mais la page demandée n\'existe pas.';
$txt['optimus_403_page_title']    = '403 - Accès interdit';
$txt['optimus_403_h2']            = 'Erreur 403';
$txt['optimus_403_h3']            = 'Désolé, mais vous pouvez pas accéder à la page demandée.';

$txt['optimus_favicon_title'] = 'Favicon';
$txt['optimus_favicon_desc']  = 'Créez votre propre icône de forum. Elle sera affichée par le navigateur dans l\'onglet avant le nom de la page, comme une image à gauche de l\'onglet ouvert et des autres éléments.';

$txt['optimus_favicon_create']  = 'Créer une favicon';
$txt['optimus_favicon_api_key'] = 'Clé API pour travailler avec Favicon Generator (<a href="https://realfavicongenerator.net/api/#register_key" target="_blank">Obtenir une clé API</a>)';
$txt['optimus_favicon_text']    = 'Code de la favicon';
$txt['optimus_favicon_help']    = 'Générez votre propre favicon <a href="http://www.favicomatic.com/" target="_blank">ICI</a>, ou utilisez un générateur spécial (vous devez entrer la clé API dans le champ ci-dessus).<br />Ensuite, téléchargez les fichiers favicon à la racine du forum, et sauvegardez le code du générateur dans le champ de droite.<br />Ce code sera chargé en tête des pages du site, entre les balises &lt;head&gt;&lt;/head&gt;.';

$txt['optimus_extra_title'] = 'Meta données';
$txt['optimus_extra_desc']  = 'Ici vous trouverez quelques corrections pour votre forum. De plus, vous pouvez activer le support d\'<strong>Open Graph</strong> et <strong>JSON-LD</strong>. profitez-en !';

$txt['optimus_remove_last_bc_item'] = 'Fil d\'Ariane correct (le dernier élément ne sera pas un lien)';
$txt['optimus_correct_prevnext']    = 'Balises <a href="http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html" target="_blank">rel="next"</a> et <a href="http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html" target="_blank">rel="prev"</a> correctes (pagination des sujets)';
$txt['optimus_open_graph']          = 'Meta <a href="http://ogp.me/" target="_blank">Open Graph</a> pour les pages du forum';
$txt['optimus_og_image']            = 'Lien vers votre image par défaut Open Graph<br /><span class="smalltext">Est remplacée par la pièce jointe du premier message dans les sujets (si existante).</span>';
$txt['optimus_fb_appid']            = '<a href="https://developers.facebook.com/apps" target="_blank">APP ID</a> (ID d\'application) <a href="https://www.facebook.com/" target="_blank">Facebook</a> (si vous en avez un)';
$txt['optimus_tw_cards']            = '<a href="https://twitter.com/" target="_blank">Twitter</a> : Nom de compte (à spécifier pour activer les "<a href="https://dev.twitter.com/cards/overview" target="_blank">Twitter Cards</a>")';
$txt['optimus_json_ld']             = 'Marquage <a href="https://json-ld.org/" target="_blank">JSON-LD</a> pour "<a href="https://developers.google.com/search/docs/data-types/breadcrumbs?hl=' . $txt['lang_dictionary'] . '" target="_blank">fil d\'Ariane</a>"';

$txt['optimus_meta_title'] = 'Balises Meta';
$txt['optimus_meta_desc']  = 'Sur cette page, vous pouvez ajouter n\'importe quel code régulier ou de vérification de la liste ci-dessous.';

$txt['optimus_meta_addtag']    = 'Cliquez ici pour ajouter une nouvelle balise';
$txt['optimus_meta_customtag'] = 'Balise Meta customisée';
$txt['optimus_meta_tools']     = 'Moteurs de recherches (Outils)';
$txt['optimus_meta_name']      = 'Nom';
$txt['optimus_meta_content']   = 'Contenu';
$txt['optimus_meta_info']      = 'Veuillez n\'utiliser que les valeurs du paramètre "<strong>contenu</strong>" des balises META.<br />Exemple : <span class="smalltext">&lt;meta name="<strong>NOM</strong>" content="<strong>VALEUR</strong>" /&gt;</span>';
$txt['optimus_search_engines'] = array(
	'Google' => array('google-site-verification','<a href="https://www.google.com/webmasters/tools/" target="_blank">Search Console</a>'),
	'Yandex' => array('yandex-verification', '<a href="https://webmaster.yandex.com/" target="_blank">Yandex.Webmaster</a>'),
	'Bing'   => array('msvalidate.01', '<a href="https://www.bing.com/toolbox/webmaster/" target="_blank">Bing Webmaster</a>')
);

$txt['optimus_counters']      = 'Compteurs';
$txt['optimus_counters_desc'] = 'Vous pouvez ajouter et modifier des compteurs dans cette partie pour enregistrer les visites de votre forum.';

$txt['optimus_head_code']       = 'Chargement de compteurs invisibles en tête (<strong>head</strong>) de section (<a href="https://www.google.com/analytics/sign_up.html" target="_blank">Google Analytics</a>)';
$txt['optimus_stat_code']       = 'Autres compteurs invisibles (<a href="https://matomo.org/" target="_blank">Matomo</a>, etc)';
$txt['optimus_count_code']      = 'Compteurs visibles (<a href="http://www.freestats.com/" target="_blank">FreeStats</a>, <a href="http://www.superstats.com/" target="_blank">SuperStats</a>, <a href="http://www.prtracker.com/FreeCounter.html" target="_blank">PRTracker</a>, etc)';
$txt['optimus_counters_css']    = 'Apparence des compteurs visibles (CSS)';
$txt['optimus_ignored_actions'] = 'Actions ignorées';

$txt['optimus_robots_title'] = 'Editeur robots.txt';
$txt['optimus_robots_desc']  = 'On this page you can change some options of forum map\'s creating, as well as modify a robots.txt file by using special generator.';

$txt['optimus_manage']      = 'Gestion de robots.txt';
$txt['optimus_rules']       = 'Générateur de robots.txt';
$txt['optimus_rules_hint']  = 'Vous pouvez copier ces règles dans le champ de droite :';
$txt['optimus_robots_hint'] = 'Ici vous pouvez insérer vos propres règles ou modifier celles qui existent déjà :';
$txt['optimus_useful']      = '';
$txt['optimus_links_title'] = 'Liens utiles';
$txt['optimus_links']       = array(
	'Créer un fichier robots.txt'              => 'https://support.google.com/webmasters/answer/6062596?hl=fr',
	'Utiliser robots.txt'                      => 'https://help.yandex.com/webmaster/?id=1113851',
	'Audit technique de l\'ensemble du site' => 'https://netpeaksoftware.com/ucp?invite=94cdaf6a'
);

$txt['optimus_sitemap_title'] = 'Optimus Sitemap';
$txt['optimus_sitemap_desc']  = 'Vous voulez un sitemap simple ? Optimus peut générer un sitemap.xml pour les petits forums. Activez simplement cette option ci-dessous. Ce sitemap sera mis à jour en fonction des paramètres dans les <a href="%1$s">Tâches programmées</a>.';

$txt['optimus_sitemap_enable']      = 'Créer et mettre à jour périodiquement le fichier XML Sitemap';
$txt['optimus_sitemap_link']        = 'Afficher un lien vers le sitemap en pied de page (footer)';
$txt['optimus_sitemap_boards']      = 'Ajouter les liens vers les sections dans le sitemap<br /><span class="smalltext error">Les sections cachées aux invités NE seront PAS ajoutées.</span>';
$txt['optimus_sitemap_topics']      = 'Ajoutez uniquement au sitemap les sujets ayant un nombre de réponses supérieur à';

$txt['optimus_sitemap_rec']        = ' Optimus is not able to split files into several parts.';
$txt['optimus_sitemap_url_limit']  = 'Sitemap file must have no more than 50,000 URLs!';
$txt['optimus_sitemap_size_limit'] = '%1$s file must be no larger than 10MB!';
$txt['optimus_sitemap_xml_link']   = 'Sitemap XML';

$txt['optimus_donate_title'] = 'Don/Participation';
$txt['optimus_donate_desc']  = 'Depuis cet onglet, vous pouvez soutenir financièrement l\'auteur de ce mod.';

// Task Manager
$txt['scheduled_task_optimus_sitemap']      = 'Génération du sitemap XML';
$txt['scheduled_task_desc_optimus_sitemap'] = 'Vous pouvez régler la fréquence de création du sitemap.';
