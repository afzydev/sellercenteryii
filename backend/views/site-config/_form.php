<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SiteConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="site-config-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php
        if (isset($model->id)) {
            echo $model->config_key;
        }
        else{
            echo $form->field($model, 'config_key')->textInput(['maxlength' => 255]);
        }            
    ?>
    <?= $form->field($model, 'value')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => 60]) ?>

    <?= $form->field($model, 'type')->dropDownList([ 'API_URL' => 'API URL', 'System' => 'System','Custom' => 'Custom' ], ['prompt' => '']) ?>
    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
