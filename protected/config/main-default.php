<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'CU Property',

    // preloading 'log' component
    'preload'=>array('log'),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
    ),

    'modules'=>array(
    ),

    // application components
    'components'=>array(
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
        ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'rules'=>array(
                '<id:\d+>'=>'site/view',
                '<action:\w+>/<id:\d+>'=>'site/<action>',
                '<action:\w+>'=>'site/<action>',
            ),
        ),
        'db'=>array(
            'connectionString'  => 'mysql:host=localhost;dbname=cuproperty',
            'emulatePrepare'    => true,
            'username'          => '',
            'password'          => '',
            'charset'           => 'utf8',
            'tablePrefix'       => ''
        ),
        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                */
            ),
        ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        'LOCALAPP_JQUERY_VER'       => '1.10.2',
        'LOCALAPP_JQUERYUI_VER'     => '1.10.3',
        'LOCALAPP_SERVER'           => 'compass.colorado.edu',
    ),
    // If install file still exists, redirect to install page
    'catchAllRequest'=>file_exists(dirname(__FILE__).'../../../.install') ? array('site/install') : null,
);