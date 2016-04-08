<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AssociateSeller */

$this->title = 'Create Associate Seller';
$this->params['breadcrumbs'][] = ['label' => 'Associate Sellers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="associate-seller-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
