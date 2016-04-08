<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\components\Helpers as Helper;
use backend\assets\OrderAsset;


/* @var $this yii\web\View */
/* @var $model backend\models\User */
$this->title='View Order Details'." (".$model[0]['company_name'].")";
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
OrderAsset::register($this);

?>
<style type="text/css">
    table {
  border-collapse: separate;
  border-spacing: 0 5px;
}

thead th { 
  font-size: 12px!important;
  background: #4d4d4d;
  color:#fff;
}

</style>
<?php
$cod_charge=0;
$total_shippment=0;
$discountCoupon=0;
$discountVoucherName='';
$address1='';
$city='';
$postcode='';
$mobile_number='';
$shipping_address='';
$shipping_city='';
$shipping_postcode='';
$shipping_mobile_number='';

// Shipping Address
if(!empty($model[0]['address1']))
    $address1=$model[0]['address1'];
if(!empty($model[0]['city']))
    $city=$model[0]['city'];
if(!empty($model[0]['postcode']))
    $postcode=$model[0]['postcode'];
if(!empty($model[0]['mobile_number']))
    $mobile_number=$model[0]['mobile_number'];
// Invoice Address
if(!empty($model[0]['shipping_address']))
    $shipping_address=$model[0]['shipping_address'];
if(!empty($model[0]['shipping_city']))
    $shipping_city=$model[0]['shipping_city'];
if(!empty($model[0]['shipping_postcode']))
    $shipping_postcode=$model[0]['shipping_postcode'];
if(!empty($model[0]['shipping_mobile_number']))
    $shipping_mobile_number=$model[0]['shipping_mobile_number'];

// Payment Details
$unit_price=Helper::formatNumberByType($getAllOrderDetail['unit_price_tax_incl'],Yii::$app->params['formatNumberType']);
$productQuantity=$getAllOrderDetail['product_quantity'];
$totalAmount=Helper::formatNumberByType($getAllOrderDetail['unit_price_tax_incl'],Yii::$app->params['formatNumberType'])*$productQuantity; // total amount paid 

if(!empty($model[0]['cod_charge']))
    $cod_charge=Helper::formatNumberByType($model[0]['cod_charge'],Yii::$app->params['formatNumberType']);

if(!empty($model[0]['total_shipping']))
    $total_shippment=Helper::formatNumberByType($model[0]['total_shipping'],Yii::$app->params['formatNumberType']);                 

// $shippingAmount=$total_shippment+$cod_charge;

$finalAmount=Helper::formatNumberByType($model[0]['total_paid_tax_incl'],Yii::$app->params['formatNumberType']); // total amount paid 

if(!empty($model[0]['voucher_discount']))
{
    $discountCoupon=Helper::formatNumberByType($model[0]['voucher_discount'],Yii::$app->params['formatNumberType']);
    $discountVoucherName=$model[0]['voucher_name'];
}

// if(!empty($discountCoupon))
//     $finalAmount=$finalAmount-$discountCoupon;
?>

<div class="row">
<p style="font-weight: bold;margin-left: 1%;"><?php echo  "(".$model[0]['reference'].")"." (#".$model[0]['id_order'].")"; ?></p>
<?php $form = ActiveForm::begin(['id'=>'updateStateViewForm']); ?>
     <div class="col-md-12">
        <div class="" id="messageBox" style="display:none;"></div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
        <button type="button" class="btn btn-success" onclick="changeStatus('view')">Change Order Status</button>
        </div>
    </div>
    <div class="col-md-2 pdlr5" id="showStatus" style="display:none;">
            <div class="form-group">
                <select  class="form-control" name="id_order_state" id="statusDropdown" style="width:175px;" onchange="selectReason('indexpage', this.value)">

                </select>
            </div>
    </div>

    <div class="col-md-2" id="showReasonDropdwon" style="display:none;">
        <div class="form-group">
            <select id="reasonDropDown" class="form-control" name="id_order_state_reason" style="width:175px;">
                
            </select>
        </div>

    </div>

    <div class="col-md-2" id="showUpdateButton" style="display:none;">
        <div class="form-group">
            <button type="button" class="btn btn-success" onclick="updateOrderStatus('viewpage','singleUpdate')">Update</button>
        </div>
    </div>

    <input type="hidden" name="orderIds" id="orderIds" value="<?php echo $_GET['id'];?>"  />
   <?php ActiveForm::end(); ?>

    <?php $createInvoiceForm = ActiveForm::begin(['action' => 'index.php?r=order/invoices','id'=>'createInvoice']); ?>  
    <div class="col-md-2" >
        <div class="form-group">
            <div class="form-group">
                <input type="hidden" name="invoiceMultipleOrderIds" id="invoiceMultipleOrderIds" value="<?php echo $_GET['id'];?>">

                <button type="submit" class="btn btn-primary" id="chck-dwnld-stats-btn">Download Packing  Slip</button>
            </div>
        </div>
    </div>    
<?php ActiveForm::end(); ?>
</div>

<div class="row">

    <div class="col-md-8" style="overflow-y:auto;height:200px;">
      
        <table class="table table-striped table-bordered detail-view">
            
            <thead>
            <tr>
                <th colspan="4" style="background: <?php echo $color; ?>;color: #fff;"><b>Current Status :</b> <?php echo $model[0]['osname'];?></th>
            </tr>
            <tr> 
                <th colspan="4"><i class="fa fa-clock-o"></i> Status(<?php echo count($orderHistory);?>)</th> 
            </tr>
            </thead>

            <tbody>
                <?php
                $status='';
                foreach($orderHistory as $history) {
                    if(!empty($history['reason']))
                        $status=$history['name'].'('.$history['reason'].')';
                    else
                        $status=$history['name'];
                ?>
                <tr>
                    <th><?php echo $history['id_order'];?></th>
                    <th><?php echo $status;?></th>
                    <td><?php echo ucwords($history['firstname'].' '.$history['lastname']);?></td>
                    <td><?php echo $history['date_add'];?></td>
                </tr>
                <?php } ?>
                <?php if(count($orderHistory)==0) {?>
                <tr>
                    <th colspan="4">No result found</th>
                </tr>
                <?php } ?>
            </tbody>
            
        </table>

    </div>

    <div class="col-md-4">
        <table class="table table-striped table-bordered detail-view">

            <thead> 
                <tr> 
                <th colspan="4"><i class="fa fa-user"></i> Customer</th> 
            </tr> 
            </thead>
            <tbody>
                <tr>
                    <td colspan="2"><b>Name</b></td>
                    <td colspan="2"><?php echo $model[0]['customer'];?></td>
                </tr>
                <tr>
                    <td colspan="2"><b>Email</b></td>
                    <td colspan="2"><?php echo $model[0]['cust_email'];?></td>
                </tr>
                <tr>
                    <td colspan="2"><b>Account registered</b></td>
                    <td colspan="2"><?php echo $model[0]['account_registered'];?></td>
                </tr>  
                <tr>
                    <td colspan="2"><b>Phone Number</b></td>
                    <td ><?php echo $model[0]['mobile1_number'];?></td>
                    <td ><?php echo $model[0]['contact_number'];?></td>
                </tr> 


            </tbody>
            
        </table>
    </div>

</div>

<div class="row">
     <div class="col-md-8">
         
        <table class="table table-striped table-bordered detail-view">

            <thead> 
                <tr> 
                <th colspan="7"><i class="fa fa-truck"></i> Shipping Details</th> 
            </tr>
            </thead>

            <tbody>
            <tbody>
                <tr>
                    <td><b>Date</b></td>
                    <td><b>Type</b></td>
                    <td><b>Carrier</b></td>
                    <td><b>Weight</b></td>
                    <td><b>Shipping Fees</b></td>
                    <td><b>Shipping Number</b></td>
                    <td><b>Payment Mode</b></td>
                </tr>
                <tr>
                    <td><?php if(!empty($model[0]['date_add'])) { echo $model[0]['date_add'];}?></td>
                    <td><?php if(!empty($model[0]['delivery_type'])) { echo $model[0]['delivery_type'];}?></td>
                    <td><?php if(!empty($model[0]['carrier_name'])) { echo $model[0]['carrier_name']; }?></td>
                    <td><?php if(!empty($getAllOrderDetail['product_weight'])) { echo Helper::getFormattedNumber($getAllOrderDetail['product_weight'],2); } ?>kg</td>
                    <td><?php echo $total_shippment; ?></td>
                    <td><?php if(!empty($model[0]['response_waywill'])) { echo $model[0]['response_waywill']; } ?></td>
                    <td><?php if(!empty($model[0]['payment'])) { echo $model[0]['payment']; } ?></td>
                </tr>
            </tbody>
            </tbody>
           
        </table>

     </div>
     <div class="col-md-4">
         <table class="table table-striped table-bordered detail-view">

            <thead> 
            <tr> 
                <th colspan="2" style="width:50%"><i class="fa fa-truck"></i> Shipping Address</th>
                <th colspan="2" style="width:50%"><i class="fa fa-file-text-o"></i> Invoice Address</th> 
            </tr>
            </thead>

            <tbody>
            <tbody>
                <tr>
                    <td colspan="2"><?php echo $address1.',<br/>'.$city.',<br/>'.$postcode.'<br>Ph-'.$mobile_number;?></td>
                    <td colspan="2"><?php echo $shipping_address.',<br/>'.$shipping_city.',<br/>'.$shipping_postcode.'<br>Ph-'.$shipping_mobile_number;?>
                    </td>
                </tr>
            </tbody>
            </tbody>
           
        </table>
     </div>
</div>

    <div class="row">
        <div class="col-md-12">
         
            <table class="table table-striped table-bordered detail-view">

                <thead> 
                <tr> 
                    <th colspan="7"><i class="fa fa-shopping-cart"></i>Products</th> 
                </tr>
                <tr> 
                    <td>Product</th>
                    <td>Unit Price</th>
                    <td>Qty</th>
                    <td>Quantity in Stock</th>
                    <td>Total</th>
                </tr>

                </thead>

                <tbody>
                <tr>
                    <td><a href="<?=Yii::$app->params['WEB_URL'] ?>index.php?r=product/view&id=<?php echo $model[0]['id_product'];?>" target="_blank"><?php echo $getAllOrderDetail['product_name'];?></a></td>
                    <td><?php echo $unit_price;?></td>
                    <td><?php echo $getAllOrderDetail['product_quantity'];?></td>
                    <td><?php echo $getAllOrderDetail['quantity'];?></td>
                    <td><?php echo $totalAmount;?></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:right">COD Fees</td>
                    <td><?php echo $cod_charge; ?></td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:right">Shipping Fees</td>
                    <td><?php echo $total_shippment;?></td>
                </tr>
                <?php if(!empty($discountCoupon)){?>
                <tr>
                    <td colspan="4" style="text-align:right">Discount</td>
                    <td><?php echo $discountCoupon;?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan="4" style="text-align:right;font-weight:bold;">Total</td>
                    <td style="font-weight:bold;"><?php echo $finalAmount;?></td>
                </tr>

                </tbody>
           
            </table>

        </div>

    </div>

 