<script>
jQuery(document).ready(function($){
   $("ul.admin-nav li:not('.selected')").hover(
     function() {
         $(this).stop().animate({
            "backgroundColor":  "#ffffff",
            "color":            "#000000",
         },100);
     },
     function() {
         $(this).stop().animate({
            "backgroundColor":  "#f0f0f0",
            "color":            "#555555", 
         },100);
     }
   ); 
   
   $("ul.admin-nav li:not('.selected')").click(function(){
       window.location = "<?php echo Yii::app()->baseUrl; ?>/admin/"+$(this).attr("id");
       return false;
   });
});
</script>

<h1>Administration Control Panel</h1>
<div class="ui-widget-content ui-corner-all notice">
    This is the administrative panel. From here you can view all submitted feedback, property postings (including removed postings), and submitted email logs. You may also manage users from this panel.
</div>
