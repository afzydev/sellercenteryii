<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Employee */

$this->title = $model->id_employee;
$this->params['breadcrumbs'][] = ['label' => 'Employees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_employee], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id_employee], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_employee',
            'id_profile',
            'id_lang',
            'lastname',
            'firstname',
            'email:email',
            'passwd',
            'last_passwd_gen',
            'stats_date_from',
            'stats_date_to',
            'stats_compare_from',
            'stats_compare_to',
            'stats_compare_option',
            'preselect_date_range',
            'bo_color',
            'bo_theme',
            'bo_css',
            'default_tab',
            'bo_width',
            'bo_menu',
            'active',
            'optin',
            'id_last_order',
            'id_last_customer_message',
            'id_last_customer',
        ],
    ]) ?>

</div>
