<?php
$flashes = new Flashes();
$flashes->render();

ini_set("display_errors",1);
error_reporting(E_ALL);
?>
<h1>Need CU Property?</h1>
<div class="ui-widget-content ui-corner-all notice">
    Simply add <i>keywords</i> for the system to look out for and we will notify you when a piece of CU property becomes available that has those keywords.<br/>
    For example, add <strong><i>printer</i></strong> to your keyword's list and you will be notified when a CU Property posting has <i>printer</i> in its description.
</div>

<link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/tags/jquery.tagsinput.css" type="text/css" />
<script src="<?php echo WEB_LIBRARY_PATH; ?>/jquery/modules/tags/jquery.tagsinput.min.js" type="text/javascript"></script>

<p><label>Separate tags by commas or by pressing enter:</label>
<input id="watchlist" type="text" class="tags" value="<?php echo $user->watchlist; ?>" /></p>
<div class="hint"><i>This list is automatically saved as you add/remove tags.</i></div>

<script>
jQuery(document).ready(function($){
    $(function() {
        $('#watchlist').tagsInput({
            width:'auto',
            defaultText: '',
            onAddTag: function(elem, elemtags) {
                updateTags($("#watchlist").val());
            },
            onRemoveTag: function(elem, elemtags) {
                updateTags($("#watchlist").val());
            }
        });
    });
    
    function updateTags(elemtags) {
        $.ajax({
           url:     "<?php echo Yii::app()->createUrl('ajax/updateWatchList'); ?>",
           data:    "tags="+escape(elemtags),
        });
    }
});
</script>