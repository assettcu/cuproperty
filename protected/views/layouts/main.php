<?php
# Theme name from Jquery UI themes
$theme = "bluebird";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/<?php echo  Yii::app()->params["LOCALAPP_JQUERY_VER"]; ?>/jquery.min.js" type="text/javascript"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/<?php echo Yii::app()->params["LOCALAPP_JQUERYUI_VER"]; ?>/jquery-ui.min.js" type="text/javascript"></script>

    <script src="<?php echo WEB_LIBRARY_PATH; ?>jquery/modules/cookie/jquery.cookie.js" type="text/javascript"></script>

    <link rel="stylesheet" href="<?php echo WEB_LIBRARY_PATH; ?>jquery/themes/<?=$theme?>/jquery-ui.css" type="text/css" />

	<script>
	// Button Script for all buttons
	jQuery(document).ready(function($){
		$("button").button();
	});
	</script>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo">
			<div id="logo-text" style="position:relative;">
                <div id="logo-image" style="position:absolute;top:5px;left:15px;">
                    <?=StdLib::load_image('aperture',"48px");?>
                </div>
				<?php echo CHtml::encode(Yii::app()->name); ?>
			</div>
			<div id="mainmenu">
				<?php if(Yii::app()->user->isGuest): ?>
				<a href="<?=Yii::app()->createUrl('login')?>">Login</a>
				<?php else: ?>
				<a href="<?=Yii::app()->createUrl('logout')?>">Logout (<?=Yii::app()->user->name?>)</a>
				<?php endif; ?>
                <a href="<?=Yii::app()->createUrl('post');?>">Add CU Equipment</a>
				<a href="<?=Yii::app()->baseUrl;?>/">Home</a>
			</div>
		</div>

	</div><!-- header -->

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		<a id="assettlogo" href="http://assett.colorado.edu"></a>
		<div style="padding-top:10px;">
			Copyright &copy; <?php echo date('Y'); ?> by the University of Colorado Boulder.<br/>
			Developed by the <a href="http://assett.colorado.edu">ASSETT program</a><br/>
		</div>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
