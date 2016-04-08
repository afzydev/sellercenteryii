<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AssociateSeller */

$this->title = 'Update Associate Seller: ' . ' ' . $model->id_employee;
$this->params['breadcrumbs'][] = ['label' => 'Associate Sellers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_employee, 'url' => ['view', 'id' => $model->id_employee]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="associate-seller-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
