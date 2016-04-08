<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\date\DatePicker;
use common\components\Configuration;
use dosamigos\datepicker\DateRangePicker;

$this->title = 'Aging Report';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin(['method'=>'get', 'action'=>'index.php?r=report/aging-report']); ?>

<div class="row">
    <div class="col-md-12">
            <span class="col-md-4">
            <label>Search Between Dates</label>
                <?php
                $from_date_add='';
                $to_date_add='';
                if(!empty($getValues['from_date_add']) || !empty($getValues['to_date_add']))
                {
                    $from_date_add=$getValues['from_date_add'];
                    $to_date_add=$getValues['to_date_add'];
                }
                //echo $from_date_add;
                ?>
                <?= DateRangePicker::widget([
                'name' => 'from_date_add',
                'value' => $from_date_add,
                'nameTo' => 'to_date_add',
                'valueTo' => $to_date_add,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
                ]);?>

             
            </span>   
    <?php if (isset($sellerDetails) && count($sellerDetails)>0) { ?>
        <span class="col-md-4">
            <label>Seller</label>
            <select class="form-control" name="sellerFilter[]" >
            <option value="">All Sellers</option>
                <?php
                    foreach ($sellerDetails as $sellerDetail) {
                        ?>
                        <option value="<?php echo $sellerDetail['id_seller']; ?>" <?php if(isset($getValues['sellerFilter']) && in_array($sellerDetail['id_seller'], $getValues['sellerFilter'])){echo 'selected';} ?>><?php echo $sellerDetail['company']; ?></option>
                <?php } ?>
            </select>

        </span>
    <?php } ?>

    </div>
    
    <div class="col-md-12" style="margin-top:5px;">
        <span class="col-md-1">
            <?= Html::submitButton('Search', ['class' => 'btn btn-success']) ?>
        </span>
        <span class="col-md-1">
            <a href="<?= Yii::$app->params['WEB_URL']; ?>index.php?r=report/aging-report" class='btn btn-primary' >Reset </a>
        </span>

    </div>

</div>
<?php ActiveForm::end(); ?>

<?php
$exportColumns = [
    [
        'attribute' => 'stage',
        'label' => 'Stage',
    ],
    [
        'attribute' => 'day',
        'label' => '0',
    ],
    [
        'attribute' => 'day1',
        'label' => '1',
    ],
    [
        'attribute' => 'day2',
        'label' => '2',
    ],
    [
        'attribute' => 'day3',
        'label' => '3',
    ],
    [
        'attribute' => 'day4',
        'label' => '4',
    ],
    [
        'attribute' => 'day5',
        'label' => '5+',
    ],
    
];
if (isset($sellerDetails) && count($sellerDetails)>0){
                    $exportColumns = array_merge([

    [
        'attribute' => 'seller_name',
        'label' => 'Seller Name',
    ]],$exportColumns);   
    }
?>

<div class="row" style="margin-top:10px;">
    <div class="col-md-12">
        <?php
            echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $exportColumns,
                'filename'    => 'aging-report',
                'fontAwesome' => true,
                'target' => ExportMenu::TARGET_SELF,
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-primary'
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ]
            ]);
            ?>
     </div>
</div>


<div class="user-index">

    <?php
    $daysArray=array();
   for ($i=0;$i<11;$i++){
       $daysArray[$i]=date('d-m-Y', strtotime(' -'.$i.' day'));
   }
   $total = 0;
   $gridColumns = [

        [
            'attribute' => 'stage',     
            'label' => 'Order Status',
        ],
        [
            'attribute' => 'day',
            'label' => '0',
            'format' => 'raw',
            'value'=>function ($data) use ($daysArray) {
                    return $data['day']==0?0:Html::a(Html::encode($data['day']),'index.php?r=order/index&filter=true&from_date_add='.$daysArray[0].'&to_date_add='.$daysArray[0].'&sellers='.$data['id_seller'].'&status='.$data['current_state']);
                },
        ],
        [
            'attribute' => 'day1',
            'label' => '1',
            'format' => 'raw',
            'value'=>function ($data) use ($daysArray){
                    return $data['day1']==0?0:Html::a(Html::encode($data['day1']),'index.php?r=order/index&filter=true&from_date_add='.$daysArray[1].'&to_date_add='.$daysArray[1].'&sellers='.$data['id_seller'].'&status='.$data['current_state']);
                },
        ],
        [
            'attribute' => 'day2',
            'label' => '2',
            'format' => 'raw',
            'value'=>function ($data) use ($daysArray){
                    return $data['day2']==0?0:Html::a(Html::encode($data['day2']),'index.php?r=order/index&filter=true&from_date_add='.$daysArray[2].'&to_date_add='.$daysArray[2].'&sellers='.$data['id_seller'].'&status='.$data['current_state']);
                },
        ],
        [
            'attribute' => 'day3',
            'label' => '3',
            'format' => 'raw',
            'value'=>function ($data) use ($daysArray){
                    return $data['day3']==0?0:Html::a(Html::encode($data['day3']),'index.php?r=order/index&filter=true&from_date_add='.$daysArray[3].'&to_date_add='.$daysArray[3].'&sellers='.$data['id_seller'].'&status='.$data['current_state']);
                },
        ],
        [
            'attribute' => 'day4',
            'label' => '4',
            'format' => 'raw',
            'value'=>function ($data) use ($daysArray){
                    return $data['day4']==0?0:Html::a(Html::encode($data['day4']),'index.php?r=order/index&filter=true&from_date_add='.$daysArray[4].'&to_date_add='.$daysArray[4].'&sellers='.$data['id_seller'].'&status='.$data['current_state']);
                },
        ],
        [
            'attribute' => 'day5',
            'label' => '5+',
            'format' => 'raw',
            'value'=>function ($data) use ($daysArray){
                     return $data['day5']==0?0:Html::a(Html::encode($data['day5']),'index.php?r=order/index&filter=true&from_date_add=24-04-2015&to_date_add='.$daysArray[5].'&sellers='.$data['id_seller'].'&status='.$data['current_state']);
                },
        ], 
         [
            'attribute' => 'Total',
            'label' => 'Total',
            'format' => 'raw',
            'value'=>function ($data){
                     return  $data['day'] + $data['day1'] + $data['day2'] + $data['day3'] + $data['day4'] + $data['day5'];
                },
        ]
    ];

    if (isset($sellerDetails) && count($sellerDetails)>0){
                    $gridColumns = array_merge([[
                        'attribute' => 'seller_name',
                        'label' => 'Seller Name',
                    ]],$gridColumns);   
    }
    ?>


    <?php
    $old_value = 0;
    $colour = '#dcdcdc';
    $rowassocolor = array();
    foreach ($dataProvider->getModels() as $selleridArr) {
        if(isset($selleridArr['id_seller']) && $selleridArr['id_seller']>0) {
            if ($old_value == $selleridArr['id_seller'])
            {
                //colour stays the same
            }
            else
            {
                if($colour == '#dcdcdc')
                {
                    $colour = '#f0f0f0';
                }
                else
                {
                    $colour = '#dcdcdc';
                }
                $old_value = $selleridArr['id_seller'];
            }
        }
        $rowassocolor[$selleridArr['id_seller']] = $colour;
    }

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'bootstrap' => true,
        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
        'headerRowOptions' => ['class' => 'kartik-sheet-style text-center'],
        'responsiveWrap' => false,
        'layout' => "{pager}\n{items}\n{pager}",
        'rowOptions' => function ($model) use ($rowassocolor){
           return ['style'=> 'background-color:'.$rowassocolor[$model['id_seller']].';']; 
        
        },
        'beforeHeader'=>[
            [
                'columns'=>[
                    ['content'=>'', 'options'=>['colspan'=>2, 'class'=>'text-center kartik-sheet-style']],
                    ['content'=>'Aging (In Days)', 'options'=>['colspan'=>11, 'class'=>'text-center kartik-sheet-style']], 
                ],
                'options'=>['class'=>'skip-export'] // remove this row from export
            ]
        ]
    ]);
    ?>


</div>
