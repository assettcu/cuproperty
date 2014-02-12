<?php
$system = new System;
require_once "admin_header.php";
?>

<div id="administration">
    <ul class="admin-nav">
        <li id="feedback"><div class="message-icon"><?php echo StdLib::load_image("star","19px"); ?></div> Feedback</li>
        <li id="users"><div class="message-icon"><?php echo StdLib::load_image("friends_group","19px"); ?></div> Manage Users</li>
        <li id="reported"><div class="message-icon"><?php echo StdLib::load_image("flag_mark_red","19px"); ?></div> Listings Reported</li>
        <li id="emails" class="selected"><div class="message-icon"><?php echo StdLib::load_image("mail_next","19px"); ?></div> Email Logs</li>
        <li id="removed"><div class="message-icon"><?php echo StdLib::load_image("close_delete","19px"); ?></div> Listings Removed</li>
    </ul>
    
    <div class="admin-panel">
    </div>
    <br style="clear:both;"/>
</div>