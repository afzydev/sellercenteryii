<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<style type="text/css">
.cardekho-logo{
    width: 220px;
    text-align: center;
    margin-left: 50px;
}
.login-logo{
    font-size: 26px;
    text-align: center;
    margin-bottom: 25px;
    font-weight: 300;
}
.btn-primary{
  background-color:#EF5A28;
  border-color: #C4390A;
  font-family: "Roboto",sans-serif;
  font-size: 12px;
}
.btn-primary:hover{
  background-color:#C4390A;
  border-color: #C4390A;
}


.login-page, .register-page{
  background: rgb(235, 235, 235)!important;
}
.btn-primary:focus, .btn-primary.focus{
  background-color:#C4390A!important;
  border-color: #C4390A!important;
}

</style>
<div class="login-box">
    <!-- /.login-logo -->
    <div class="login-box-body">

        <div class="row">
            <!-- /.col -->
            <div class="col-md-12">
                <img src="<?=Yii::$app->params['WEB_URL'] ?>images/seller-dashboard.png" title="CarDekho Seller Dashboard" class="cardekho-logo" >
            </div>
            <div class="col-md-12">
                <div class="login-logo">
                    <!-- <a href="#"><b>Seller</b> Dashboard</a> -->
                </div>
            </div>
            <!-- /.col -->
        </div>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'email', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        <?= $form
            ->field($model, 'passwd', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <!-- /.col -->
            <div class="col-xs-12">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
