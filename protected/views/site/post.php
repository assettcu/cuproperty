<?php
$this->pageTitle=Yii::app()->name . ' - Post CU Equipment';

# Load the user (must exist, cannot get to this page without logging in)
$user = new UserObj(Yii::app()->user->name);

# Load success/warning/error messages
$flashes = new Flashes();
$flashes->render();
?>

<!-- Load Queue widget CSS and jQuery -->
<style type="text/css">@import url(<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>

<h1>Post CU Property</h1>

<div class="ui-widget-content ui-corner-all" style="padding:10px;margin-bottom:15px;">
    You can post anything your department has to give away.
</div>

<form method="post">
    <input type="hidden" name="propertyform" />
    <table id="post-form-table">
        <?php if($property->loaded): ?>
        <tr>
            <th><div>Post ID</div></th>
            <td><?php echo $property->propertyid; ?>
                <input type="hidden" value="<?php echo $property->propertyid; ?>" name="propertyid" />
                <input type="hidden" value="0" name="remove-property" id="remove-property" />
            </td>
        </tr>
        <tr>
            <th><div>Date Posted</div></th>
            <td><?php echo StdLib::format_date($property->date_added,"normal"); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><div>Department/Program</div></th>
            <td><input type="text" name="department" id="department" value="<?php echo @$property->department; ?>" /></td>
        </tr>
        <tr>
            <th><div>Use me as contact</div></th>
            <td>
                <input type="checkbox" name="useme" id="useme" style="float:left;margin-right:15px;" />
                <div class="hint">Check this to auto-fill contact name and email.</div>
            </td>
        </tr>
        <tr>
            <th><div>Contact Name</div></th>
            <td><input type="text" name="contactname" id="contactname" value="<?php echo @$property->contactname; ?>" /></td>
        </tr>
        <tr>
            <th><div>Contact Email</div></th>
            <td><input type="text" name="contactemail" id="contactemail" value="<?php echo @$property->contactemail; ?>" /></td>
        </tr>
        <tr>
            <th><div>Description of CU Property</div></th>
            <td>
                <textarea name="description" id="description" rows="8" cols="100" ><?php echo @$property->description; ?></textarea>
            </td>
        </tr>
        <?php if($property->loaded): ?>
        <tr>
            <th><div>Current Images</div></th>
            <td>
                <?php if($property->has_images()): ?>
                    <table>
                    <?php $count = 0; foreach($property->images as $image): $count++; ?>
                    <?php if($count%6==1) echo "<tr>"; ?>
                        <td class="calign">
                            <?php 
                                $imager = new Imager($image->location);
                                $imager->resize(100);
                                $imager->render();
                            ?><br/>
                            <button class="remove-image" imageid="<?php echo $image->imageid; ?>">Remove</button>
                        </td>
                    <?php if($count%6==0 or $count==count($property->images)) echo "</tr>"; ?>
                    <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    No images for current property
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <th><div>Image Attachments</div></th>
            <td><div id="html5_uploader">You browser doesn't support native upload. Try Firefox 3 or Safari 4.</div></td>
        </tr>
        <?php if(!$property->loaded): ?>
        <tr>
            <th><div>Terms of Agreement</div></th>
            <td>
                <input type="checkbox" name="agree" id="agree" style="float:left;margin-right:15px;" <?php echo (isset($_POST["agree"])) ? "checked='checked'" : ""; ?> />
                <div class="hint">I have read and agree to the <a href="#" class="toa-button">terms of agreement</a>.</div>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    
    <hr style="margin-top:25px;"/>
    
    <div class="button-container">
        <button class="cancel">Cancel</button>
        <?php if($property->loaded) : ?>
            <button class="remove">Remove Post</button>
            <button class="submit">Update Post</button>
        <?php else: ?>
            <button class="submit" disabled="disabled">Post Equipment</button>
            <span class="hint" id="post-stop-hint"> You must agree to the terms of agreement.</span>
        <?php endif; ?>
    </div>
</form>

<!-- Terms of Agreement -->
<div id="toa-dialog" title="Terms of Agreement">
    You agree that by posting surplus CU Equipment you have legal equipment belonging to the University of Colorado Boulder and are willing to give this equipment to another
    department/program that is a part of the University of Colorado Boulder.<br/>
    <br/>
    You are not legally obligated to fulfill requests. By posting you are allowing other departments/programs to contact you for collecting your surplus equipment.
    Use of this application is for communication between departments/programs, use accordingly.<br/>
    <br/>
    The images attached should be of the surplus equipment only.
</div>
    
<script>

var contactname = "<?php echo $user->name; ?>";
var contactemail = "<?php echo $user->email; ?>";
var uploader;

jQuery(document).ready(function($){
	// All buttons do not submit forms
    $("button").click(function(){
        return false;
    });
    
    // Terms of agreement check on page load
    if($("#agree").is(":checked")) {
      $("span#post-stop-hint").hide('fade');
      $("button.submit").button({
          "disabled": ""
      });
    }
    
    // File uploader
    uploader = $('#html5_uploader').pluploadQueue({
       runtimes:        'html5',
       container:       'html5_uploader',
       url:             '<?php echo Yii::app()->createUrl('_upload_images'); ?>',
       max_file_size:   '20mb',
       chunk_size:      '1mb',
       unique_names:    true,
       browse_button:   'Select Images',
       filters:         [{
            title:          "Image Files",
            extensions:     "jpg,gif,png,jpeg,bmp"
       }]
    });
    
    // Remove image from property
    $("button.remove-image").click(function(){
       var imagecontainer = $(this).parent();
       var imageid = $(this).attr("imageid");
       var r = confirm("Are you sure you wish to remove this image?");
       if(r) {
           $.ajax({
              url:      "<?php echo Yii::app()->createUrl('_remove_image'); ?>",
              data:     "imageid="+imageid,
              success:  function(){
                  imagecontainer.fadeOut(1000,function(){
                    imagecontainer.remove();
                  });
              }
           });
       }
       return false; 
    });
    
    // Client side form validation
    $('form').submit(function(e) {
       	$("button").button({"disabled":"disabled"});
        var uploader = $('#html5_uploader').pluploadQueue();

        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form')[0].submit();
                }
            });
                
            uploader.start();
        } else {
            $('form')[0].submit();
        }
        return false;
    });
	
	// Hide the upload button, the files will upload when the form is submitted
    $("a.plupload_start").hide();
   
	// Show/hide warning of agreeing to terms based on checkbox
	$("#agree").change(function(){
		if($("#agree").is(":checked")) {
			$("span#post-stop-hint").hide('fade');
			$("button.submit").button({
				"disabled": ""
			});
		} else {
			$("span#post-stop-hint").show('fade');
			$("button.submit").button({
				"disabled": "disabled"
			});
		}
	});

	// Init dialog box for terms of agreement
	$("#toa-dialog").dialog({
		"autoOpen":       false, 
		"modal":          true,
		"width":          500,
		"height":         300,
		"draggable":      false,
		"resizable":      false,
	});
	
	// Link for the Terms of Agreement
	$(".toa-button").click(function(){
		$("#toa-dialog").dialog("open");
		return false; 
	});
	
	// Checkbox for autofill name/email input fields
	$("#useme").change(function(){
		if($(this).is(":checked")) {
			$("#contactname").val(contactname);
			$("#contactemail").val(contactemail);
		} else {
			$("#contactname").val("");
			$("#contactemail").val("");
		}
	});

	// Submit the new Property post
	$("button.submit").click(function(){	
        $("button").button({"disabled":"disabled"});
		// If property is loaded (editing post), special conditions apply
		<?php if(!$property->loaded): ?>
		if(!$("#agree").is(":checked")) {
			alert("You must agree to the terms of agreement before posting.");
			return false;
		}
		<?php endif; ?>

		// Let's submit the form
		$('form').submit();
		return false;
	});
	
	// Button to return home
	$("button.cancel").click(function(){
		$("button").button({"disabled":"disabled"});
		window.location = "<?php echo Yii::app()->createUrl('index'); ?>";
		return false; 
	});

	// Button to remove image currently attached to the property (used when editing property)
	$("button.remove").click(function(){
		var r = confirm("Are you sure you wish to remove this post?");
		if(r) {
            $("button").button({"disabled":"disabled"});
			$("#remove-property").val(1);
			$("form").submit();
		}
		return false;
	});
});
</script>