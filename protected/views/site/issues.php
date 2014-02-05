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
            <th><div>Name</div></th>
            <td><input type="text" name="contactname" id="contactname" value="<?php echo @$user->name; ?>" /></td>
        </tr>
        <tr>
            <th><div>Email</div></th>
            <td><input type="text" name="contactemail" id="contactemail" value="<?php echo @$user->email; ?>" /></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><div>Type of Issue/Comment</div></th>
            <td>
                <select>
                    <option value="1">Problem with website</option>
                    <option value="2">Incorrect content</option>
                    <option value="3">Report a posting</option>
                    <option value="4">Suggest a feature/change</option>
                    <option value="5">Other</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><div>Issue/Comment</div></th>
            <td>
                <textarea name="description" id="description" rows="9" cols="100" ></textarea>
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
</form>