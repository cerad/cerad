<div class="wrap">
<h2>Zayso 2 Admin Options <?php echo $method; ?> </h2>
<form name="zayso_admin_options" method="post" action="">
    <table>
    <?php foreach ($props as $key => $label) { ?>
        <tr>
            <td><label><?php echo $label; ?></label></td>
            <td><input type="text" size="30" name="<?php echo $key; ?>" value="<?php echo get_option($key); ?>"/></td>
        </tr>
    <?php } ?>    
        <tr>
            <td colspan=2"><input type="submit" name="update" value="Update"/></td>
        </tr>
    </table>
</form>
</div>

