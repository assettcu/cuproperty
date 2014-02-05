<?php
// Load various settings set for the application and display them.
$flashes = new Flashes;
$flashes->render();

try {
    Yii::app()->db;
    $connection = "yes";
    $conn_name = Yii::app()->db->connectionString;
} catch(Exception $e) {
    $connection = "no";
    $dbname = "";
}

$infotable = array(
    "Yii Version"           			=> Yii::getVersion(),
    "Application Name"      			=> Yii::app()->name,
    "JQuery Version"        			=> @Yii::app()->params["LOCALAPP_JQUERY_VER"],
    "JQuery UI Version"     			=> @Yii::app()->params["LOCALAPP_JQUERYUI_VER"],  
    "Database Connection String"        => $conn_name,
    "Database Connection"   			=> $connection,
    "Database Error"					=> @$e
);

?>

<style>
table#site-info {
    border-spacing:3px;
}
table#site-info tr th {
    border:2px solid #ccc;
    padding:5px;
    color:#333;
    background-color:#f0f0f0;
}
table#site-info tr td {
    padding:5px;
    color:#09a;
    padding-left:15px;
}
</style>

<table id="site-info">
    <?php foreach($infotable as $label=>$value) : ?>
    <tr>
        <th><?php echo $label; ?></th>
        <td><?php echo $value;?></td>
    </tr>
    <?php endforeach; ?>
</table>