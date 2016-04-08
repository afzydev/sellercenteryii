<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var mdm\admin\models\AuthItemSearch $searchModel
 */
//echo "<pre>";print_r($dataProvider);die;
$this->title = Yii::t('rbac-admin', 'Roles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-index">


    <h1>&nbsp;</h1>

    <p>
        <?= Html::a(Yii::t('rbac-admin', 'Create Role'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    Pjax::begin([
        'enablePushState'=>false,
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => Yii::t('rbac-admin', 'Name'),
            ],
            [
                'attribute' => 'description',
                'label' => Yii::t('rbac-admin', 'Description'),
            ],
            [
                'label' =>'',
                'value' => function($model){
                    if($model->ruleName == 'Admin')
                        return Html::a("<i class='fa fa-eye'></i>",['role/view','id'=>$model->name]);
                    if($model->ruleName == 'Seller')
                        return Html::a("<i class='fa fa-eye'></i>",['role/view','id'=>$model->name]);
                    if($model->ruleName == 'Super Admin')
                        return Html::a("<i class='fa fa-eye'></i>",['role/view','id'=>$model->name]);
                },
                'format' =>'raw'              
            ],
            [
                'label' =>'',
                'value' => function($model){
                    if($model->ruleName == 'Admin')
                        return Html::a("<i class='fa fa-pencil'></i>",['role/update','id'=>$model->name]);
                    if($model->ruleName == 'Seller')
                        return Html::a("<i class='fa fa-pencil'></i>",['role/update','id'=>$model->name]);
                    if($model->ruleName == 'Super Admin')
                        return Html::a("<i class='fa fa-pencil'></i>",['role/update','id'=>$model->name]);
                },
                'format' =>'raw'              
            ],
             [
                'label' =>'',
                'value' => function($model){
                    if($model->ruleName == 'Admin')
                        return Html::a("<i class='fa fa-trash'></i>",['role/delete','id'=>$model->name]);
                    if($model->ruleName == 'Seller')
                        return Html::a("<i class='fa fa-trash'></i>",['role/delete','id'=>$model->name]);
                    
                },
                'format' =>'raw'              
            ],
            
        ],
    ]);
    Pjax::end();
    ?>

</div>
