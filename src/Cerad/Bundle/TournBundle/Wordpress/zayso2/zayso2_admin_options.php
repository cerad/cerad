<?php
/* ===================================
 * TODO: 
 * zayso_enviroment
 * zayso_debug
 * zayso_cookies
 *//*
    $zayso2Method = $_SERVER['REQUEST_METHOD'];
    $zayso2AdminFields = array
    (
        'zayso2_project_key'  => 'Project Key',  // = AYSONationalGames2014
        'zayso2_install_path' => 'Install Path', // = /../../../../zayso
        'zayso2_web_host'     => 'Web Host',     // = http://local.zayso.org
        'zayso2_web_path'     => 'Web Path',     // = /natgames2014
        'zayso2_session_path' => 'Session Path', // = /var//tmp/sessions

    );
    if ($zayso2Method == 'POST')
    {
        foreach($zayso2AdminFields as $zayso2Key => $zayso2Label)
        {
            update_option($zayso2Key,$_POST[$zayso2Key]);
        }
    }
    */
    /* ================================================
     * Does not seem to be a table oriented form class
     * Just us a table for now
     */
?>
<div class="wrap">
<h2>Zayso 2 Admin Options <?php echo $zayso2Method; ?> </h2>
<form name="zayso_admin_options" method="post" action="">
    <table>
    <?php foreach ($zayso2AdminFields as $zayso2Key => $zayso2Label) { ?>
        <tr>
            <td><label><?php echo $zayso2Label; ?></label></td>
            <td><input type="text" size="30" name="<?php echo $zayso2Key; ?>" value="<?php echo get_option($zayso2Key); ?>"/></td>
        </tr>
    <?php } ?>    
        <tr>
            <td colspan=2"><input type="submit" name="update" value="Update"/></td>
        </tr>
    </table>
</form>
</div>

