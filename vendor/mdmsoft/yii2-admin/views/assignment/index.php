<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\AutoComplete;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Assignment */
/* @var $usernameField string */
/* @var $extraColumns string[] */

$this->title = Yii::t('rbac-admin', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
    $user_status = array('1'=>'Active','0'=>'Deactive'); 
 ?>
<div class="assignment-index">

    <h1>&nbsp;</h1>

	<?php
    Pjax::begin(['enablePushState'=>false]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
  
            'email:email',
           /* ['attribute' => $usernameField,
	     'format'    => 'raw',
             'filter' => AutoComplete::widget([
                             'model' => $searchModel,
                             'attribute' =>  'username',
                             'clientOptions' =>
                             [
                                     'source' => [],
                             ],
                            'options' => array('class' => 'form-control')
			     ])       
            ],*/
            'firstname',
            'lastname',
        //    ['attribute' => $firstname],
        //    ['attribute' => $lastname],
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
            //['class' => 'yii\grid\ActionColumn',
                //'template'=>'{view}'],
              [
                'label' =>'Action', 
                'value' => function ($model)
                { 
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model['id_employee']]);
                },
                'format'=>'raw', 
                ],
        ],
    ]);
    Pjax::end();
    ?>

</div>
