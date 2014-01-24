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
    <link rel="stylesheet" type="text/css" href="css/screen.css" media="screen, projection" />
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print" />

    <link rel="stylesheet" type="text/css" href="css/main.css" />
    <link rel="stylesheet" type="text/css" href="css/form.css" />
    <link rel="stylesheet" type="text/css" href="css/table.css" />

    <title>Site Maintenance</title>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js" type="text/javascript"></script>

    <link rel="stylesheet" href="library/jquery/themes/<?php echo $theme; ?>/jquery-ui.css" type="text/css" />
    <style>
        table.fancy-table {
            border-collapse:separate;
            border-spacing:2px;
        }
        table.fancy-table tbody tr td {
            border:2px solid #ccc;
            padding:5px;
        }
    </style>
</head>

<body>
    
<div class="container" id="page">

    <div id="header">
        <div id="logo">
            <div id="logo-text" style="position:relative;">
                <div id="logo-image" style="position:absolute;top:5px;left:15px;">
                    <img src="library/images/aperture.png" width="48px" height="48px" />
                </div>
                CU Property
            </div>
        </div>

    </div><!-- header -->

    <div id="content">
        <h1>Site Currently Under Maintenance</h1>
        <div class="ui-widget-content ui-corner-all notice">
            The system is currently being upgraded and will return shortly. Thank you for your patience.
        </div>
    </div>

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
