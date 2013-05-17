<?php
/*
Plugin Name: Zayso
Plugin URI: http://wordpress.org/extend/plugins/zayso/
Description: Zayso Referee Scheduling
Author: Art Hundiak
Version: 1.6
Author URI: http://ma.tt/
*/
require_once 'zayso_curl.php';

// Fire up the session handler
add_action('init','zayso_session_start',1);

/* ===========================
* Add a zayso admin page
*/
function zayso_admin_actions()
{
    add_options_page("Zayso Admin", "Zayso Admin", 'manage_options', "zayso-admin", "zayso_admin");
}
function zayso_admin()
{
    include 'zayso_admin_options.php';
}
add_action('admin_menu', 'zayso_admin_actions');

/* ==========================
* Just call this directly from a page?
* [zayso_project]
*/
function zayso_project($atts, $content = null)
{
    return sprintf('<h2>Zayso Project %s</h2>',get_option('zayso_project_key'));
}
add_shortcode('zayso_project','zayso_project');

/* ===========================================================
 * Generic short code processor
 */
function zayso_action_fragment($args)
{
    $path = $args['path'];
    if (isset($args['id'])) $id = $args['id'];
    else                    $id = null;
    
    return zayso_curl_get($path,$id);
}
add_shortcode('zayso_fragment','zayso_action_fragment');

/* ========================================================
 * Still not sure if it is better to action or filter
 * function zayso_filter_parse_request($bool,$wp,$extra)
 */
function zayso_action_parse_request($wp)
{
    $bool = true;
    
    // Maybe this should be on option page?
    $zayso_routes = array(
        // Zayso URL              // Wordpress page
        '/volunteer/plan/form' => '/referee-page/',
        '/signin-check'        => '/volunteer-home/',
        '/signout'             => '/volunteer-signin/',
        '/search'              => '/search/',
        '/register'            => '/volunteer-home/',
        
        '/a5bgames/register'       => '/volunteer-home/',
        '/a5bgames/home'           => '/volunteer-home/',
        
        '/natgames/signout'        => '/sample-page/',
        '/natgames/search'         => '/sample-page/',
        '/natgames/signin-check'   => '/sample-page/',
        '/natgames/volunteer/plan' => '/sample-page/',
   );
    
    // Is it a zayso route
    $uri = $_SERVER['REQUEST_URI'];
    if (!isset($zayso_routes[$uri])) return $bool;
    $wp_uri  = $zayso_routes[$uri];
    
    // Is it a post?
    if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST') 
    {
        // Redirect on GET
        wp_redirect($wp_uri); exit();
    }
    // Hack for sub domain
    if (strpos($uri,'/natgames') === 0) $uri = substr($uri,9);
    if (strpos($uri,'/a5bgames') === 0) $uri = substr($uri,9);
    
    // Send the post on
    $body = zayso_curl_post($uri,$_POST);
    echo $body;die("XXX");
    // For now just redirect but want to check for errors
    wp_redirect($wp_uri); exit();
}
add_action('parse_request','zayso_action_parse_request');

?>