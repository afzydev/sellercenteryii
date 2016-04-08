<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SeachEmployee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_employee') ?>

    <?= $form->field($model, 'id_profile') ?>

    <?= $form->field($model, 'id_lang') ?>

    <?= $form->field($model, 'lastname') ?>

    <?= $form->field($model, 'firstname') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'passwd') ?>

    <?php // echo $form->field($model, 'last_passwd_gen') ?>

    <?php // echo $form->field($model, 'stats_date_from') ?>

    <?php // echo $form->field($model, 'stats_date_to') ?>

    <?php // echo $form->field($model, 'stats_compare_from') ?>

    <?php // echo $form->field($model, 'stats_compare_to') ?>

    <?php // echo $form->field($model, 'stats_compare_option') ?>

    <?php // echo $form->field($model, 'preselect_date_range') ?>

    <?php // echo $form->field($model, 'bo_color') ?>

    <?php // echo $form->field($model, 'bo_theme') ?>

    <?php // echo $form->field($model, 'bo_css') ?>

    <?php // echo $form->field($model, 'default_tab') ?>

    <?php // echo $form->field($model, 'bo_width') ?>

    <?php // echo $form->field($model, 'bo_menu') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'optin') ?>

    <?php // echo $form->field($model, 'id_last_order') ?>

    <?php // echo $form->field($model, 'id_last_customer_message') ?>

    <?php // echo $form->field($model, 'id_last_customer') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
