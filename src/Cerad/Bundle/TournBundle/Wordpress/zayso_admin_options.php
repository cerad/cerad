<?php
/* ===================================
 * TODO: 
 * zayso_enviroment
 * zayso_debug
 * zayso_cookies
 */
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST')
    {
        $zaysoProjectKey = $_POST['zayso_project_key'];
        update_option('zayso_project_key', $zaysoProjectKey);
        
        $zaysoDirectory = $_POST['zayso_directory'];
        update_option('zayso_directory', $zaysoDirectory);
        
        $zaysoHost = $_POST['zayso_host'];
        update_option('zayso_host', $zaysoHost);
        
        $zaysoSessionPath = $_POST['zayso_session_path'];
        update_option('zayso_session_path', $zaysoSessionPath);

    }
    else
    {
        $zaysoProjectKey = get_option('zayso_project_key'); // AYSONatGames2014
        $zaysoDirectory  = get_option('zayso_directory');   // /../../../../zayso
        $zaysoHost       = get_option('zayso_host');        // http://local.zayso.org
        $zaysoSessionPath= get_option('zayso_session_path');// /var//tmp/sessions
    }
    
    /* ================================================
     * Does not seem to be a table oriented form class
     * Just us a table for now
     */
?>
<div class="wrap">
<h2>Zayso Admin Options <?php echo $_SERVER['REQUEST_METHOD']; ?> </h2>
<form name="zayso_admin_options" method="post" action="">
    <table>
        <tr>
            <td><label>Project Key</label></td>
            <td><input type="text" size="30" name="zayso_project_key" value="<?php echo $zaysoProjectKey; ?>"/></td>
        </tr><tr>
            <td><label>Directory</label></td>
            <td><input type="text" size="30" name="zayso_directory" value="<?php echo $zaysoDirectory; ?>"/></td>
        </tr><tr>
            <td><label>Host</label></td>
            <td><input type="text" size="30" name="zayso_host" value="<?php echo $zaysoHost; ?>"/></td>
        </tr><tr>
            <td><label>Session Path</label></td>
            <td><input type="text" size="30" name="zayso_session_path" value="<?php echo $zaysoSessionPath; ?>"/></td>
        </tr><tr>
            <td colspan=2"><input type="submit" name="update" value="Update"/></td>
        </tr>
    </table>
</form>
</div>

