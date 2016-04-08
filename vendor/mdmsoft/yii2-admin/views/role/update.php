<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\AuthItem $model
 */
$this->title = Yii::t('rbac-admin', 'Update Role').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = Yii::t('rbac-admin', 'Update');
?>
<div class="auth-item-update">
<a class="btn btn-success btn-sm" href="<?php echo Yii::$app->params['WEB_URL'];?>index.php?r=admin/role" title="Back" style="float:right;width:70px;">Back</a>
	<h1>&nbsp;</h1>
	<?php
    echo $this->render('_form', [
        'model' => $model,
    ]);
    ?>
</div>
