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
        ['pattern' => 'myindex', 'route' => 'site/myindex'],


        ['pattern' => '<controller:\w+>/\/{0,1}', 'route' => 'site/index'],

        ['pattern' => 'auth/<action:[\w\-]+>', 'route' => 'auth/<action>'],

        ['pattern' => 'goods/<action:[\w\-]+>', 'route' => 'goods/<action>'],

        ['pattern' => '<controller:\w+>/<action:[\w\-]+>', 'route' => '<controller>/<action>'],

    ]
];
