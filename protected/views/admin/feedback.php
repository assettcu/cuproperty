<?php
$system = new System;
$issues = $system->get_all_issues();
require_once "admin_header.php";
?>

<div id="administration">
    <ul class="admin-nav">
        <li id="feedback" class="selected"><div class="message-icon"><?php echo StdLib::load_image("star","19px"); ?></div> Feedback</li>
        <li id="users"><div class="message-icon"><?php echo StdLib::load_image("friends_group","19px"); ?></div> Manage Users</li>
        <li id="reported"><div class="message-icon"><?php echo StdLib::load_image("flag_mark_red","19px"); ?></div> Listings Reported</li>
        <li id="emails"><div class="message-icon"><?php echo StdLib::load_image("mail_next","19px"); ?></div> Email Logs</li>
        <li id="removed"><div class="message-icon"><?php echo StdLib::load_image("close_delete","19px"); ?></div> Listings Removed</li>
    </ul>
    
    <div class="admin-panel">
        <table id="issues-table" class="fancy-table" width="100%">
            <thead>
                <tr>
                    <th width="25px" class="calign">ID</th>
                    <th width="250px">Contact</th>
                    <th>Issue/Comment Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($issues) > 0): ?>
                    <?php $count =0; foreach($issues as $issue): $count++; ?>
                    <tr <?php echo ($count%2==0) ? "class='odd'" : "class='even'"; ?>>
                        <td class="calign"><?php echo $issue->issueid; ?></td>
                        <td class="lalign mvalign">
                            <?php echo $issue->name; ?><br/>
                            <?php echo $issue->email; ?>
                        </td>
                        <td class="lalign" title="<?php echo StdLib::format_date($issue->date_submitted, "nice"); ?>">
                            <span class="hint"><i><?php echo StdLib::time_ago($issue->date_submitted, "round"); ?></i></span><br/>
                            <?php echo $issue->description; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="empty">There are currently no issues/comments. Got a <a href="<?php echo Yii::app()->createUrl('site/feedback'); ?>">comment</a> about that?</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br style="clear:both;"/>
</div>