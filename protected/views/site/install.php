<?php
$flashes = new Flashes;
$flashes->render();
?>
<h1>Installing the CU Property Application</h1>

<form method="post">
    <input type="hidden" name="stage" value="init" />
    <table class="fancy-table">
        <thead>
            <tr>
               <th colspan="2">Setup New Installation</th> 
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label">Database Host</td>
                <td class="form-input">
                    <input type="text" name="db-host" value="<?php echo (isset($_REQUEST["db-host"]) and !empty($_REQUEST["db-host"])) ? $_REQUEST["db-host"] : "localhost"; ?>" />
                    <span class="hint">Usually set to <i>localhost</i>.</span>
                </td>
            </tr>
            <tr class="odd">
                <td class="label">Database Name</td>
                <td class="form-input"><input type="text" name="db-name" value="<?php echo @$_REQUEST["db-name"]; ?>" /></td>
            </tr>
            <tr>
                <td class="label">Database Username</td>
                <td class="form-input"><input type="text" name="db-username" value="<?php echo @$_REQUEST["db-username"]; ?>" /></td>
            </tr>
            <tr class="odd">
                <td class="label">Database Password</td>
                <td class="form-input">
                    <input type="password" name="db-password" id="db-password" value="<?php echo @$_REQUEST["db-password"]; ?>" class="showpassword" /> 
                    <a href="#" class="changepasswordtype">show password</a>
                </td>
            </tr>
            <tr>
                <td class="label">Table Prefix</td>
                <td class="form-input">
                    <input type="text" name="table-prefix" value="<?php echo @$_REQUEST["table-prefix"]; ?>" />
                    <span class="hint">Set to none by default.</span>
                </td>
            </tr>
            <tr class="odd">
                <td class="label">Overwrite?</td>
                <td class="form-input mvalign">
                    <input type="checkbox" name="overwrite" <?php echo (isset($_REQUEST["overwrite"])) ? "checked='checked'" : ""; ?> />
                    <span>Check this box to overwrite existing database connections if they exist.</span>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="ralign" style="padding-top:10px;">
                    <button style="">Start Installation &gt;</button>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<script>
jQuery(document).ready(function($){
    $(".changepasswordtype").click(function(){
        var input = $(this).parent().find("input"); 
        if(input.attr("type") == "password") {
            input.attr("type","text");
            $(this).text("hide password");
        } else if(input.attr("type") == "text") {
            input.attr("type","password");
            $(this).text("show password");
        }
        return false;
    });
});
</script>
