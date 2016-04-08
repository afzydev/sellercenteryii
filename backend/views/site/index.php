<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use common\components\Helpers as Helper;
use kartik\date\DatePicker;
use miloschuman\highcharts\Highcharts;

if(Helper::isSeller())
{
   $this->title=  'Dashboard | '.Helper::getSessionFullName();    
}
   else
{
    $this->title = 'Dashboard | CarDekho Seller OMS';
}
?>
<style type="text/css">
    thead th {
        background-color: #4d4d4d;
        color:#fff;
        font-size: 12px!important;

    }

  </style>

<section class="content-header">
    <section class="content">

        <div class="row">
            <div class="col-md-6">
                <table class="table table-striped table-bordered detail-view" id="_orders">
                    <?php echo $this->render("_orders", array('data'=>$data,'orderState'=>$orderState)); ?>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-striped table-bordered detail-view" id="_stockReport">
                    <?php echo $this->render("_stockReport", array('data'=>$data)); ?>
                </table>
            </div>
        </div>        
        <?php $form = ActiveForm::begin(['id'=>'filtersalesorders']); ?>
        <div class="row" style="margin-top: 20px; margin-bottom: 5px;">
            <div class="col-md-8">
                <button type="button" id="day_filter" onclick="javascript:filterbybtn('day');" class="btn btn-success">Last 15 Days</button>
                <button type="button" id="month_filter" onclick="javascript:filterbybtn('month');" class="btn btn-primary">Last 30 Days</button>
                <!-- <button type="button" id="year_filter" onclick="javascript:filterbybtn('year');" class="btn btn-primary">Year</button>   -->              
            </div>

            <div class="col-md-3">
                <?php
                $days_ago = date('d-m-Y', strtotime('-15 days', strtotime(date('d-m-Y   '))));

                echo DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'from_date_add',
                    'attribute2' => 'to_date_add',
                    'options' => ['value' =>$days_ago],
                    'options2' => ['value' => date('d-m-Y')],
                    'type' => DatePicker::TYPE_RANGE,
                    'form' => $form,
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy',
                        'autoclose' => true,
                        'disabled' => true,
                    ]
                ]);
                ?>          

            </div>
            <div class="col-md-1 pull-right">
                <button type="button" class="btn btn-primary" onclick="filterSalesOrders()">Search</button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered detail-view" >
                <div id="salesOrdersView">
                <?php
                //print_r($data);die;
                 echo $this->render("_salesOrdersView", array('data'=>$data)); ?>
                </div>
                </table>
               
            </div>
       </div>
        <div class="row">
                <div id="salesOrdersChart">
                    <?php echo $this->render("_salesOrdersChart", array('data'=>$data)); ?>                    
                </div>
          </div>
         <div class="row">
        <div class="col-md-6">
                <div class="best-selling-pro">
                    <table class="table table-striped table-bordered detail-view" id="_bestSellingProduct">
                        <?php echo $this->render("_bestSellingProduct", array('data'=>$data)); ?>
                    </table>
                </div>
            </div>
         </div>

    </section>
</section>

