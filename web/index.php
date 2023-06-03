<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

try {
    (new yii\web\Application($config))->run();
} catch (\yii\base\InvalidConfigException $e) {

    print_r([
        'data' => $e,
        'message' => 'Ошибка'
    ]);
//    Yii::$app->mailer->send(
//
//    );
}
