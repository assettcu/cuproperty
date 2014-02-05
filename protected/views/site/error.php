<?php
$this->pageTitle=Yii::app()->name . ' - Error';
?>
<h1>There was an Error that occured!</h1>
<div class="ui-widget-content ui-corner-all notice">
    If there are no errors then please go back to the <a href="<?php echo Yii::app()->baseUrl; ?>" style="color:#09f;text-decoration:underline;">Home Page</a>.
</div>
<?php
$flashes = new Flashes;
$flashes->render();
?>