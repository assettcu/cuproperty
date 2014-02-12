<?php
$system = new System;
$users = $system->get_all_users();
require_once "admin_header.php";
?>

<div id="administration">
    <ul class="admin-nav">
        <li id="feedback"><div class="message-icon"><?php echo StdLib::load_image("star","19px"); ?></div> Feedback</li>
        <li id="users" class="selected"><div class="message-icon"><?php echo StdLib::load_image("friends_group","19px"); ?></div> Manage Users</li>
        <li id="reported"><div class="message-icon"><?php echo StdLib::load_image("flag_mark_red","19px"); ?></div> Listings Reported</li>
        <li id="emails"><div class="message-icon"><?php echo StdLib::load_image("mail_next","19px"); ?></div> Email Logs</li>
        <li id="removed"><div class="message-icon"><?php echo StdLib::load_image("close_delete","19px"); ?></div> Listings Removed</li>
    </ul>
    
    <div class="admin-panel">
        <table id="issues-table" class="fancy-table" width="100%">
            <thead>
                <tr>
                    <th width="250px">Contact</th>
                    <th>Watchlist</th>
                    <th class="calign">Last Login</th>
                    <th class="calign" width="20px" title="Number of postings posted"><?php echo StdLib::load_image("post","20px"); ?></th>
                    <th class="calign" width="20px" title="Number of emails sent"><?php echo StdLib::load_image("message","20px"); ?></th>
                    <th class="calign" width="20px" title="Permission Level"><?php echo StdLib::load_image("permission","20px"); ?></th>
                    <th class="calign" width="20px" title="Edit User">Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($users) > 0): ?>
                    <?php $count =0; foreach($users as $user): $count++; ?>
                    <tr <?php echo ($count%2==0) ? "class='odd'" : "class='even'"; ?>>
                        <td class="lalign mvalign">
                            <?php echo $user->name; ?> (<i><?php echo $user->username; ?></i>) 
                            <div class="message-icon" title="User is <?php echo $user->active(); ?>">
                                <?php echo ($user->active==1) ? StdLib::load_image("active","20px") : StdLib::load_image("inactive","20px"); ?>
                            </div>
                            <?php echo $user->email; ?>
                        </td>
                        <td>
                            <?php if(isset($user->watchlist) and !empty($user->watchlist)): ?>
                                <?php echo implode(", ",explode(",",$user->watchlist)); ?>
                            <?php else: ?>
                                <i>This user has no items in their watchlist</i>
                            <?php endif; ?>
                        </td>
                        <td class="calign" title="<?php echo StdLib::format_date($user->last_login, "nice"); ?>">
                            <span class="hint"><i><?php echo StdLib::time_ago($user->last_login, "round"); ?></i></span>
                        </td>
                        <td class="calign"><?php echo $user->num_postings(); ?></td>
                        <td class="calign"><?php echo $user->num_emails(); ?></td>
                        <td class="calign"><?php echo $user->permission(); ?> (<?php echo $user->permission; ?>)</td>
                        <td class="calign" title="Edit User">
                            <a href="#"><?php echo StdLib::load_image("edit","16px"); ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else:
                    $this->redirect(Yii::app()->createUrl('site/logout'));
                    exit;
                endif; ?>
            </tbody>
        </table>
    </div>
    <br style="clear:both;"/>
</div>