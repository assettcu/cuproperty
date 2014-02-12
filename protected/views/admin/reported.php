<?php
$system = new System;
$listings = $system->get_all_reported();
require_once "admin_header.php";
?>
<div id="administration">
    <ul class="admin-nav">
        <li id="feedback"><div class="message-icon"><?php echo StdLib::load_image("star","19px"); ?></div> Feedback</li>
        <li id="users"><div class="message-icon"><?php echo StdLib::load_image("friends_group","19px"); ?></div> Manage Users</li>
        <li id="reported" class="selected"><div class="message-icon"><?php echo StdLib::load_image("flag_mark_red","19px"); ?></div> Listings Reported</li>
        <li id="emails"><div class="message-icon"><?php echo StdLib::load_image("mail_next","19px"); ?></div> Email Logs</li>
        <li id="removed"><div class="message-icon"><?php echo StdLib::load_image("close_delete","19px"); ?></div> Listings Removed</li>
    </ul>
    
    <div class="admin-panel">
        <table id="issues-table" class="fancy-table" width="100%">
            <thead>
                <tr>
                    <th class="calign" width="30px">ID</th>
                    <th width="250px">Contact</th>
                    <th>Property Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($listings) > 0): ?>
                    <?php $count =0; foreach($listings as $property): $count++; ?>
                        <?php $poster = $property->get_poster(); ?>
                    <tr <?php echo ($count%2==0) ? "class='odd'" : "class='even'"; ?>>
                        <td class="calign"><?php echo $property->propertyid; ?></td>
                        <td class="lalign mvalign">
                            <span class="hint"><i><?php echo $property->department; ?></i></span><br/>
                            (posted by <i><?php echo $poster->username;?></i>)<br/>
                            <?php echo $property->contactname; ?><br/>
                            <?php echo $property->contactemail; ?>
                        </td>
                        
                        <td>
                        <?php echo $property->description; ?>
                        
                        <?php
                            if($property->has_images()) {
                                echo "<hr style=\"margin:5px 0px;\"/>";
                                echo "<div class='property-images' propertyid='".$property->propertyid."'>";
                                # echo "<div style='float:left;margin-right:5px;margin-top:15px;'>Images: </div>";
                                foreach($property->images as $image) {
                                    // echo '<script>jQuery(document).ready(function($){$(".colorbox-group-'.$property->propertyid.'").colorbox();});</script>';
                                    $imager = new Imager(_LOCAL_ROOT_."/".$image->location);
                                    echo "<div style='float:left;margin:3px;width:50px;min-height:30px;'><a href='".$image->location."' class='colorbox-image colorbox-group-".$property->propertyid."'>";
                                    $imager->resize(50);
                                    $imager->render();
                                    echo "</a></div>";
                                }
                                echo "</div>";
                            }
                        ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty">There are no reported listings.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br style="clear:both;"/>
</div>