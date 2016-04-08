<?php
 $params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
 );

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
           // 'layout' => 'left-menu',
        ],
        'gridview' =>  [
        'class' => '\kartik\grid\Module',
        // enter optional module parameters below - only if you need to  
        // use your own export download action or custom translation 
        // message source
        'downloadAction' => 'gridview/export/download',
        // 'i18n' => []
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\DbManager'
            'defaultRoles' => ['Guest'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
         'view' => [
         'theme' => [
             'pathMap' => [
                '@app/views' => '@app/views/theme',
                '@vendor/mdmsoft/yii2-admin/views' => '@common/extensions/yii2-admin/views',
             ],
         ],
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-car-dekho',
                ],
            ],
        ],
        'formatter' => [
                'class' => 'yii\i18n\Formatter',
                'nullDisplay' => '',
        ],
    ],
     'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
          //  'admin/*', // add or remove allowed actions to this list
        ],
    ], 
    'params' => $params,
];
