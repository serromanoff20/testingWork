<?php
return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => true,
    'suffix' => '',
    'rules' => [
        ['pattern' => '', 'route' => 'site/index'],
        ['pattern' => 'index', 'route' => 'site/index'],
        ['pattern' => 'about', 'route' => 'site/about'],
        ['pattern' => 'find', 'route' => 'site/find'],
        ['pattern' => 'check', 'route' => 'site/check'],
//        ['pattern' => 'test', 'route' => 'test'],
//        ['pattern' => 'new', 'route' => 'site/newDirectory/'],
//        ['pattern' => 'angular', 'route' => 'app/index'],
//        ['pattern' => 'login', 'route' => 'site/login'],
//        ['pattern' => 'about', 'route' => 'site/logout'],
//        ['pattern' => 'csp-report', 'route' => 'site/csp-report'],

        ['pattern' => '<controller:\w+>/\/{0,1}', 'route' => 'site/index'],
        ['pattern' => '<controller:\w+>/<action:[\w\-]+>/<id:\d+>', 'route' => '<controller>/<action>'],
        ['pattern' => '<controller:\w+>/<action:[\w\-]+>', 'route' => '<controller>/<action>'],

    ]
];
