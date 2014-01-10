<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

// define local library paths (see StdLib class)
defined('LOCAL_LIBRARY_PATH') or define('LOCAL_LIBRARY_PATH','c:\\web\\compass.colorado.edu\\cuproperty\\library\\');
defined('LOCAL_IMAGE_LIBRARY') or define('LOCAL_IMAGE_LIBRARY',LOCAL_LIBRARY_PATH."images\\");

// define web library paths (see StdLib class)
defined('WEB_LIBRARY_PATH') or define('WEB_LIBRARY_PATH','//compass.colorado.edu/cuproperty/library/');
defined('WEB_IMAGE_LIBRARY') or define('WEB_IMAGE_LIBRARY',WEB_LIBRARY_PATH."images\\");

require_once($yii);
Yii::createWebApplication($config)->run();