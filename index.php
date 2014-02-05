<?php
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
if(YII_DEBUG) {
    ini_set("display_errors",1);
    error_reporting(E_ALL);
}
// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii-master/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

# If Yii framework does not exist, then let's update it
if(!is_file($yii)) {
    header("Location: yii-update.php");
    exit;
}

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

// define the root of this application
defined('HTTP_HOST')    or define('HTTP_HOST',$_SERVER["HTTP_HOST"]);
defined('_ROOT_')       or define('_ROOT_',dirname($_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]));
defined('_LOCAL_ROOT_') or define('_LOCAL_ROOT_',dirname(__FILE__));

// define local library paths (see StdLib class)
defined('LOCAL_LIBRARY_PATH') or define('LOCAL_LIBRARY_PATH',_LOCAL_ROOT_.'\\library\\');
defined('LOCAL_IMAGE_LIBRARY') or define('LOCAL_IMAGE_LIBRARY',LOCAL_LIBRARY_PATH."images\\");

// define web library paths (see StdLib class)
defined('WEB_LIBRARY_PATH') or define('WEB_LIBRARY_PATH','//'._ROOT_.'/library/');
defined('WEB_IMAGE_LIBRARY') or define('WEB_IMAGE_LIBRARY',WEB_LIBRARY_PATH."images\\");

require_once($yii);
Yii::createWebApplication($config)->run();