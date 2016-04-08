<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\AutoComplete;
use backend\assets\AjaxLoaderAsset;
use backend\assets\AssociateSellerAsset;
use common\components\Configuration;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SearchAssociateSeller */
/* @var $dataProvider yii\data\ActiveDataProvider */
 AjaxLoaderAsset::register($this);
 AssociateSellerAsset::register($this);
$this->title = $searchModel->employee_details['firstname'].' '. $searchModel->employee_details['lastname'];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php
   $user_status = array('1'=>'Active','0'=>'Deactive'); 
?>
<div id="alertMessage-modal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id='alert-title'><strong>Error!</strong></h4>
            </div>
            <div class="modal-body">
                <div id="alertMessage">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="associate-seller-index">
	
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <!--    <p>
        <?= Html::a('Create Associate Seller', ['create'], ['class' => 'btn btn-success']) ?>
    </p>-->
    <?php Pjax::begin(['id'=>'user-grid-pjax', 'formSelector' => 'form', 'enablePushState' => false]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id_employee',
            [
                'attribute' => 'company',
				'format'    => 'raw',
                'value' =>function ($model) { 
	   		         return $model['company'];
				},
				'filter' => AutoComplete::widget([
					 'model' => $searchModel,
					 'attribute' => 'company',
					 'clientOptions' =>
					 [
						 'source' => [],
					 ],
					'options' => array('class' => 'form-control')
			     ])       
            ],
            //'id_profile',
            //'id_lang',
            'firstname',
            'lastname',
            'email',
            [
                'attribute' => 'city',
				'format'    => 'raw',
                'value' =>function ($model) { 
	   		         return $model['city'];
				},
                'filter' => AutoComplete::widget([
					 'model' => $searchModel,
					 'attribute' => 'city',
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
                
                'label' => 'Assign',
		          'format'    => 'raw',
                  'attribute'   => 'assign',
                'value' =>function ($model)  use ($searchModel) {
                              return isset($searchModel->arr_id_seller) ? (in_array($model['id_employee'], $searchModel->arr_id_seller) ? Html::tag('i', '', ['class' => 'fa fa-check text-success']):Html::tag('i', '', ['class' => 'fa fa-close text-danger'])) : Html::tag('i', '', ['class' => 'fa fa-close text-danger']);
				    },
                'filter' => false,
                'contentOptions'=>['align'=>'center']				
            ],
            
           
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
            [
                
                'attribute' => 'assign',
		        'format'    => 'raw',
                'header' => '',
                'value' =>function ($model)  use ($searchModel) {
                              if( $model['id_employee'] != Yii::$app->getRequest()->getQueryParam('employee_id') )
                              {
                                return Html::a(isset($searchModel->arr_id_seller) ? (in_array($model['id_employee'], $searchModel->arr_id_seller) ? 'Remove':'Assign') : 'Assign',null, ['class' => isset($searchModel->arr_id_seller) ? (in_array($model['id_employee'], $searchModel->arr_id_seller) ? 'btn btn-success btn-sm':'btn btn-primary btn-sm') : 'btn btn-primary btn-sm','href'=>'javascript:void(0);','title'=>'Update','data-pjax'=>$model['id_employee'],'data-seller-assignment-model'=>$model['id_employee'],'data-seller-assignment-admin'=>Yii::$app->getRequest()->getQueryParam('employee_id')]);
                            }
                            else
                            {
                                return null;
				            }
                    },
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>

