<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AjaxLoaderAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/loader.js',
        'js/ajaxloader.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
