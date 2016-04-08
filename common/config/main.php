<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'helpers' => [
            'class' => 'common\components\Helpers',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\DbManager'
            'defaultRoles' => ['Guest'],
        ],
        'db' => require_once 'database.php',		
        'dbshop' => require_once 'shop_database.php'
    ],
];
