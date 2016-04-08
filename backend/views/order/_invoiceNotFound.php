<?php

use yii\helpers\Html;
use common\components\Helpers as Helper;

if (class_exists('backend\assets\AppAsset')) {
    backend\assets\AppAsset::register($this);
} else {
    app\assets\AppAsset::register($this);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <h4><?php echo isset($recordNotFound) ? $recordNotFound : 'Tempalate not found';  ?></h4>
    <?php $this->endBody() ?>
    </body>
<?php echo $breakPage ? '<pagebreak />' : ''; ?>
</html>
<?php $this->endPage() ?>

