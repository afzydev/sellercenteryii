<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\AutoComplete;
use common\components\Configuration;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SeachEmployee */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Employees';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    
    
    <?php
       $user_status = array('1'=>'Active','0'=>'Deactive'); 
    ?>

<!--    <p>
        <?= Html::a('Create Employee', ['create'], ['class' => 'btn btn-success']) ?>
    </p>-->
    <?php Pjax::begin(['formSelector' => 'form', 'enablePushState' => false]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id_employee',
            //'id_profile',
            //'id_lang',
            'email:email',
            'firstname',
            'lastname',
            [
                'attribute' => 'profile',
				'format'    => 'raw',
                'value' =>function ($model) { 
	   		         return $model['profile'];
				},
                'filter' => AutoComplete::widget([
					 'model' => $searchModel,
					 'attribute' => 'profile',
					 'clientOptions' =>
					 [
						 'source' => [],
					 ],
					'options' => array('class' => 'form-control')
			     ])       
            ],
            [
                
                'attribute' => 'active',
				'format'    => 'raw',
                'value' =>function ($model) { 
				   return $model['active'] == '1' ? Html::tag('i', '', ['class' => 'fa fa-check text-success']) : Html::tag('i', '', ['class' => 'fa fa-close text-danger']);
				},
                'filter' => $user_status,
                'contentOptions'=>['align'=>'center']
                                        
            ],
			[
                
                'attribute' => 'associate',
				'format'    => 'raw',
                'value' =>function ($model) { 
				    //return $model['id_profile'] == Yii::$app->params['ps_seller_profile_id'] ? '' : Html::a('Seller',null, ['class' => 'btn btn-success btn-sm','href'=>Yii::$app->params['web_url']."index.php?r=associate-seller&employee_id=".$model['id_employee']]);
				    return Html::a('Seller',null, ['class' => 'btn btn-primary btn-sm','href'=>Yii::$app->params['WEB_URL']."index.php?r=associate-seller&employee_id=".$model['id_employee']]);
				},
            ],
             // 'email:email',
            // 'passwd',
            // 'last_passwd_gen',
            // 'stats_date_from',
            // 'stats_date_to',
            // 'stats_compare_from',
            // 'stats_compare_to',
            // 'stats_compare_option',
            // 'preselect_date_range',
            // 'bo_color',
            // 'bo_theme',
            // 'bo_css',
            // 'default_tab',
            // 'bo_width',
            // 'bo_menu',
            // 'active',
            // 'optin',
            // 'id_last_order',
            // 'id_last_customer_message',
            // 'id_last_customer',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
