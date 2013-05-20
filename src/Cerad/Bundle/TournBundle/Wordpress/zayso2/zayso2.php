<?php
/*
Plugin Name: Zayso2
Plugin URI: http://wordpress.org/extend/plugins/zayso2/
Description: Zayso 2 Referee Scheduling
Author: Art Hundiak
Version: 2.0
Author URI: http://zayso.org/
*/
class Zayso2
{
    /* =============================================
     * Builds and process the admin options form
     */
    public function adminActions()
    {
        add_options_page("Zayso 2 Admin", "Zayso 2 Admin", 10, "zayso2-admin", array($this,'adminOptions')); 
    }
    public function adminOptions()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $props  = array
        (
            'zayso2_project_key'  => 'Project Key',  // = AYSONationalGames2014
            'zayso2_install_path' => 'Install Path', // = /../../../../zayso
            'zayso2_web_host'     => 'Web Host',     // = http://local.zayso.org
            'zayso2_web_path'     => 'Web Path',     // = /natgames2014
            'zayso2_session_path' => 'Session Path', // = /var//tmp/sessions
        );
        if ($method == 'POST')
        {
            foreach($props as $key => $label)
            {
                // Might be good to sanitize a bit?
                update_option($key,trim($_POST[$key]));
            }
        }
        include 'zayso2_admin_options.tpl.php';        
    }
    function showProjectKey($atts, $content = null)
    {
        return sprintf('<h2>Zayso2 Project %s</h2>',get_option('zayso2_project_key'));
    }
}
$zayso2 = new Zayso2();

add_action('admin_menu', array($zayso2,'adminActions'));

add_shortcode('zayso2_project',array($zayso2,'showProjectKey'));
?>
