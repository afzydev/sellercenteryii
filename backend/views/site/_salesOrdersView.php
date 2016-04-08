<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Helpers as Helper;
?>

<div class="row">
    <div class="alert alert-success" style="padding: 10px;margin-left: 15px;margin-right: 15px;background: #4d4d4d!important;border-color: #201D1D!important;">
    <b>SALES AND ORDERS QUICK VIEW</b>
    </div>
</div>

<div class="row">
       <div class="col-md-4 col-sm-6 col-xs-12">
         <div class="info-box">
           <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

           <div class="info-box-content">
             <span class="info-box-text">SALES</span>
             <span class="info-box-number"><?php echo isset($data['sale']['getOverAllSales']) ? Helper::getFormattedNumber($data['sale']['getOverAllSales']) : 0; ?></span>
           </div>
           <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
       </div>
       <!-- /.col -->
       <div class="col-md-4 col-sm-6 col-xs-12">
         <div class="info-box">
           <span class="info-box-icon bg-red"><i class="fa fa-bars"></i></span>

           <div class="info-box-content">
             <span class="info-box-text">ORDERS</span>
             <span class="info-box-number"><?php echo isset($data['order']['getOverallOrders']) ? Helper::getFormattedNumber($data['order']['getOverallOrders']) : 0; ?></span>
           </div>
           <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
       </div>
       <!-- /.col -->

       <!-- fix for small devices only -->
       <div class="clearfix visible-sm-block"></div>

       <div class="col-md-4 col-sm-6 col-xs-12">
         <div class="info-box">
           <span class="info-box-icon bg-green"><i class="fa fa-inr"></i></span>

           <div class="info-box-content">
             <span class="info-box-text">AVG. ORDER VALUE</span>
             <span class="info-box-number"><?php echo isset($data['avgCartValue']['avg_order_value']) ? Helper::getFormattedNumber($data['avgCartValue']['avg_order_value']) : 0; ?></span>
           </div>
           <!-- /.info-box-content -->
         </div>
         <!-- /.info-box -->
       </div>
       <!-- /.col -->
       </div>
	
        


	
        