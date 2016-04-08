<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/login']);
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p><h4>Welcome </h4>, Your Password <?= Html::encode($user->password) ?></p>

    <p>Login <?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
