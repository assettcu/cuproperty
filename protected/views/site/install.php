<?php
$this->pageTitle=Yii::app()->name;

# Load success/warning/error messages
$flashes = new Flashes();
$flashes->render();

# Property Manager to load all property
$propmanager = new PropertyManager();
$props = $propmanager->load_property();

# Load user if they are logged in
if(!Yii::app()->user->isGuest) {
    $user = new UserObj(Yii::app()->user->name);
}
?>

<div class="ui-widget-content ui-corner-all notice">
    You can find the surplus of CU Property on this page. To place an advertisement for surplus equipment, please click on "Add CU Equipment" button in the menu.
</div>

<table id="haves-table">
    <thead>
        <tr>
            <th class="calign" width="50px">Post ID</th>
            <th width="200px">Department/Program</th>
            <th width="280px">Contact</th>
            <th>CU Property</th>
            <th class="calign" width="180px">Last Updated</th>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($props)) : ?>
        <?php $a=0; foreach($props as $property): $a++; ?>
            <tr class="<?php echo ($a%2==0) ? 'odd' : 'even'; ?>">
                <td class="calign propid">
                    <?php if(Yii::app()->user->name == $property->postedby) : ?>
                        <span title="You are the one who posted this advertisement">
                            <?php echo StdLib::load_image("star","16px","16px"); ?>
                        </span>
                    <?php endif; ?>
                    <span class="postid"><?php echo $property->propertyid; ?></span>
                </td>
                <td><?php echo $property->department; ?></td>
                <td>
                    <span class="contactname"><?php echo $property->contactname; ?></span><br/>
                    <span class="contactemail"><?php echo $property->contactemail; ?></span>
                    <?php if(!Yii::app()->user->isGuest): ?>
                        <a href="#" class="email-contact" title="Email this contact."><?php echo StdLib::load_image("mail_next","16px","16px"); ?></a>
                    <?php endif; ?>
                </td>
                <td>
                <?php echo $property->description; ?>
                <hr style="margin:5px 0px;"/>
                
                <?php
                    if($property->has_images()) {
                        echo "<div class='property-images' propertyid='".$property->propertyid."'>";
                        # echo "<div style='float:left;margin-right:5px;margin-top:15px;'>Images: </div>";
                        foreach($property->images as $image) {
                            // echo '<script>jQuery(document).ready(function($){$(".colorbox-group-'.$property->propertyid.'").colorbox();});</script>';
                            $imager = new Imager(getcwd()."/".$image->location);
                            echo "<div style='float:left;margin:3px;width:50px;min-height:30px;'><a href='".$image->location."' class='colorbox-image colorbox-group-".$property->propertyid."'>";
                            $imager->resize(50);
                            $imager->render();
                            echo "</a></div>";
                        }
                        echo "</div>";
                    }
                ?></td>
                <td class="calign">
                    <?php echo StdLib::format_date($property->date_updated,"short-normal"); ?>
                    <?php if(Yii::app()->user->name == $property->postedby) : ?>
                        <a href="<?=Yii::app()->createUrl('post');?>?id=<?php echo $property->propertyid;?>">edit this post</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="calign mvalign" style="padding:20px;">
                There are currently no postings. Check back later.
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<?php 
# Load the email dialog box if the user is logged in
if(isset($user)) : 
?>
<div id="email-dialog" title="Email contact for CU Property">
    <table class="modal-table">
        <tr>
            <th><div>Post ID:</div></th>
            <td><span id="postid"></span></td>
        </tr>
        <tr>
            <th><div>From:</div></th>
            <td><?php echo $user->name; ?> (<?php echo $user->email; ?>)</td>
        </tr>
        <tr>
            <th><div>To:</div></th>
            <td>
                <span id="email-to-name"></span>
                <input type="hidden" name="contactemail" id="contactemail" />
            </td>
        </tr>
        <tr>
            <th><div>Subject:</div></th>
            <td>CU Property Surplus Response</td>
        </tr>
        <tr>
            <th><div>Message:</div></th>
            <td></td>
        </tr>
        <tr>
            <td class="calign" colspan="2" style="padding:5px;">
                <textarea name="message" id="message" style="width:640px;height:250px;"></textarea>
            </td>
        </tr>
    </table>
</div>
<?php endif; ?>

<?php # Include the colorbox script to display the images ?>
<link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/colorbox/colorbox.css" type="text/css" />
<script src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/colorbox/jquery.colorbox-min.js"></script>

<script>
jQuery(function($) {
    
    // Init colorbox for each of the loaded images
    $(".property-images").each(function(){
        var propertyid = $(this).attr("propertyid");
        $(".colorbox-group-"+propertyid).colorbox({rel:".colorbox-group-"+propertyid, transition:"none", width:"65%", height:"75%"});
    });
    
    // Init Email dialog box
    $("#email-dialog").dialog({
       "autoOpen":      false,
       "modal":         true,
       "width":         700,
       "height":        600, 
       "buttons":       {
           "Cancel":                function() {
             $("#email-dialog").dialog("close");  
           },
           "Email Contact":         function() {
               if($("#message").val().trim()=="") {
                   alert("Your message is empty. Please provide a message.");
               } else {
                   $("button").button({"disabled":"disabled"});
                   var postid = $("#postid").text();
                   $.ajax({
                      "url":        "<?=Yii::app()->createUrl('_email_contact');?>",
                      "data":       "contact="+$("#contactemail").val()+"&postid="+postid+"&message="+escape($("#message").val()),
                      "success":    function(data) {
                          window.location.reload();
                          return false;
                      }
                   });
               }
           }
       }
    });
    
    // On email open, set the input fields and open dialog
    $(".email-contact").click(function(){
       var contactname      = $(this).parent().parent().find(".contactname").text();
       var contactemail     = $(this).parent().parent().find(".contactemail").text();
       var postid           = $(this).parent().parent().find("span.postid").text();
       $("#contactemail").val(contactemail);
       $("#postid").text(postid);
       $("#email-to-name").text(contactname+" ("+contactemail+")");
       $("#email-dialog").dialog("open");
       return false; 
    });
    
});
</script>