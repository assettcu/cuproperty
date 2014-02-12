<?php
$user = new UserObj();
if(!Yii::app()->user->isGuest) {
    $user->username = Yii::app()->user->name;
    $user->load();
}
?>
<h1>Contact Developers</h1>

<div class="ui-widget-content ui-corner-all notice">
    Have an issue or comment? Contact our developers! 
</div>

<form method="post">
    <input type="hidden" name="issuesform" />
    <table id="post-form-table">
        <?php if(!Yii::app()->user->isGuest): ?>
        <tr>
            <th><div>Username</div></th>
            <td><i><?php echo $user->username; ?></i></td>
        </tr>
        <tr>
            <th><div>Name</div></th>
            <td><i><?php echo $user->name; ?></i></td>
        </tr>
        <tr>
            <th><div>Email</div></th>
            <td><i><?php echo $user->email; ?></i></td>
        </tr>
        <?php else: ?>
        <tr>
            <th><div <?php echo ($error == "contactname") ? 'class="error"' : ''; ?>>Name</div></th>
            <td><input type="text" name="contactname" id="contactname" value="<?php echo @$_REQUEST["contactname"]; ?>" /></td>
        </tr>
        <tr>
            <th><div <?php echo ($error == "contactemail") ? 'class="error"' : ''; ?>>Email</div></th>
            <td><input type="text" name="contactemail" id="contactemail" value="<?php echo @$_REQUEST["contactemail"]; ?>" /></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><div <?php echo ($error == "category") ? 'class="error"' : ''; ?>>Type of Issue/Comment</div></th>
            <td>
                <select name="category">
                    <option value="website-problem" <?php echo (isset($_REQUEST["category"]) and $_REQUEST["category"] == "website-problem") ? 'checked="checked"' : ''; ?>>Problem with website</option>
                    <option value="incorrect-content" <?php echo (isset($_REQUEST["category"]) and $_REQUEST["category"] == "incorrect-content") ? 'checked="checked"' : ''; ?>>Incorrect content</option>
                    <option value="report-posting" <?php echo (isset($_REQUEST["category"]) and $_REQUEST["category"] == "report-posting") ? 'checked="checked"' : ''; ?>>Report a posting</option>
                    <option value="suggestion" <?php echo (isset($_REQUEST["category"]) and $_REQUEST["category"] == "suggestion") ? 'checked="checked"' : ''; ?>>Suggest a feature/change</option>
                    <option value="other" <?php echo (isset($_REQUEST["category"]) and $_REQUEST["category"] == "other") ? 'checked="checked"' : ''; ?>>Other</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><div <?php echo ($error == "description") ? 'class="error"' : ''; ?>>Issue/Comment</div></th>
            <td>
                <textarea name="description" id="description" rows="9" cols="100" ><?php echo @$_REQUEST["description"]; ?></textarea>
            </td>
        </tr>
    </table>
    <br/>
    <div class="hint">* All fields are required</div>
    <hr style="margin-top:25px;"/>
    
    <div class="button-container">
        <button class="cancel"><span class="message-icon"><?php echo StdLib::load_image("close_delete","16px"); ?></span> Cancel</button>
        <button class="submit"><span class="message-icon"><?php echo StdLib::load_image("forward","16px"); ?></span> Submit Issue/Comment</button>
    </div>
</form>

<script>
jQuery(document).ready(function(){
    
    // Button to return home
    $("button.cancel").click(function(){
        $("button").button({"disabled":"disabled"});
        window.location = "<?php echo Yii::app()->createUrl('index'); ?>";
        return false; 
    });
});
</script>