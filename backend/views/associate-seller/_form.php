<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AssociateSeller */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="associate-seller-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_profile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_lang')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'passwd')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_passwd_gen')->textInput() ?>

    <?= $form->field($model, 'stats_date_from')->textInput() ?>

    <?= $form->field($model, 'stats_date_to')->textInput() ?>

    <?= $form->field($model, 'stats_compare_from')->textInput() ?>

    <?= $form->field($model, 'stats_compare_to')->textInput() ?>

    <?= $form->field($model, 'stats_compare_option')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'preselect_date_range')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bo_color')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bo_theme')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bo_css')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'default_tab')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bo_width')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bo_menu')->textInput() ?>

    <?= $form->field($model, 'active')->textInput() ?>

    <?= $form->field($model, 'optin')->textInput() ?>

    <?= $form->field($model, 'id_last_order')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_last_customer_message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_last_customer')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
