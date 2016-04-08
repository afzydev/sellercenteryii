<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\components\Helpers as Helper;
use yii\bootstrap\Modal as Modal;
use backend\assets\ProductAsset;
use branchonline\lightbox\Lightbox;

ProductAsset::register($this);

$this->title = 'View Product Details';
?>
<style type="text/css">
    table {
        border-collapse: separate;
        border-spacing: 0 5px;
    }

    thead th {
        background-color: #5C97BF;
        color:#fff;
    }

</style>
<?php
    if($model['active']==1)
    {
        $status='Active';
    }
    else if($model['active']==0)
    {
        $status='Inactive';
    }
$mrp=0;
$sellingPrice=0;
$shop_margin=0;
$pg_fee=0;
$shipping_charge=0;
$totalMargin=0;
$discount=0;

$mrp = str_replace(",", "",$model['base_price']);
$sellingPrice=str_replace(",", "",$model['sell_price']);
$shop_margin=$model['shop_margin'];
$pg_fee=$model['pg_fee'];
$shipping_charge=$model['shipping_charge'];
$quantity=1;
//$serviceTax is stored in configuration table
$serviceTax = Yii::$app->params['margin_service_tax'];

/*for shop margin*/
$a = ($sellingPrice * $shop_margin) / 100 ;
$A = number_format(($a + ( $a * $serviceTax / 100 )),2);
/*for payment gateway fee*/        
$b = ($sellingPrice * $pg_fee) / 100 ;
$B = number_format(($b + ( $b * $serviceTax / 100 )),2);
/*for shipping cost*/
$c = $shipping_charge * $quantity;
$C = number_format(($c + ( $c * $serviceTax / 100 )),2);
/*shop total margin*/
$totalMargin = $A + $B + $C;
/*total vendor payout*/
$vendorPayout = $sellingPrice - $totalMargin;
$vendorPayout=number_format($vendorPayout,2);
$discountRupee = ($mrp - $sellingPrice);
$discount=(($mrp - $sellingPrice) / $mrp)*100;
$discount=number_format($discount,2);
?>
<style type="text/css">
    thead th { 
  font-size: 12px!important;
  background: #4d4d4d;
  color:#fff;
}
</style>
<div class="row">
    <?php $form = ActiveForm::begin();
    ?>

    <div class="col-md-12">
        <div class="" id="messageBox" style="display:none;"></div>
    </div>
    <div class="col-md-2">
        <a data-toggle="modal" data-target="#viewStockLog" class="btn btn-success" > View Stock Log</a>
    </div>
    <div class="col-md-2">
        <a data-toggle="modal" data-target="#viewPriceLog" class="btn btn-success" > View Price Log</a>
    </div>
</div>

<?php ActiveForm::end(); ?>

<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped table-bordered detail-view">
            <thead>
                <tr>
                    <th>
                        <?php echo $model['name'];?>
                    </th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="col-lg-8">
        <table class="table table-striped table-bordered detail-view">
            <thead>
                <tr>
                    <th colspan ="2">Information</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>Product Name    </td>
                <td><?php echo $model['name']; ?></td>
            </tr>
            <tr>
                <td>Shop SKU Id </td>
                <td><?php echo $model['shop_reference']; ?></td>
            </tr>
            <tr>
                <td>Seller SKU Id</td>
                <td><?php echo $model['seller_reference']; ?></td>
            </tr>
            <tr>
                <td>Catagory</td>
                <td><?php echo $model['name_category']; ?></td>
            </tr>
            <tr>
                <td>Short Description</td>
                <td><?php echo $model['short_description']; ?></td>
            </tr>
            <tr>
                <td>Description</td>
                <td ><div style="overflow-y:auto;height:200px;"><?php echo $model['description']; ?></div></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-lg-4">
        <table class="table table-striped table-bordered detail-view">
            <thead>
                <tr>
                    <th colspan ="3">Update Product</th>
                </tr>
            </thead>
            <tr>
                <td>Status</td>
                <td><?php echo $status; ?></td>
                <input type="hidden" name="employee" id="employee" value="<?php if(!empty($model['id_employee'])) echo $id_employee = $model['id_employee'];?>" />
                <input type="hidden" name="id_product" id="id_product" value="<?php if(!empty($model['id_product'])) echo $id_product = $model['id_product'];?>" />

                <td><button type="button" class="btn btn-success" onclick="changeProductStatus('<?php echo $model['active'];?>')"><?php echo $status=='Active'?'Deactivate':'Activate'; ?></button></td>
            </tr>
            <tr>
                <td>Quantity in Stock</td>
                <td><?php echo $model['sav_quantity']; ?></td>
                <td><a data-toggle="modal" data-target="#updateStock"  class="btn btn-success"> Update</a></td>
            </tr>
            <tr>
                <td>Selling Price</td>
                <td><?php echo str_replace(",", "",$model['sell_price']); ?></td>
                <td> <a data-toggle="modal" data-target="#updatePrice"  class="btn btn-success"> Update</a></td>
            </tr>
            
        </table>
    </div>
    <div class="col-lg-4">
        <table class="table table-striped table-bordered detail-view">
            <thead>
                <tr>
                    <th colspan ="3">Prices</th>
                </tr>
            </thead>
            <tr>
                <td>MRP</td>
                <td><?php echo str_replace(",", "",$model['base_price']); ?></td>
            </tr>
            <tr>
                <td>Discount (<?=$discount;?>%)</td>
                <td><?=$discountRupee?></td>
            </tr>
            <tr>
                <td>Selling Price</td>
                <td><?php echo str_replace(",", "",$model['sell_price']); ?></td>
            </tr>
            <tr>
                <td>Shipping Charges</td>
                <td><?php echo $shipping_charge;?></td>
            </tr>
            <tr>
                <td>Payment Gateway Fee (<?=$pg_fee;?>%)</td>
                <td><?=$B?></td>
            </tr>
            <tr>
                <td>Shop Margin (<?=$shop_margin;?>%)</td>
                <td><?=$A?></td>
            </tr>
            <tr>
                <td>Vendor Payout</td>
                <td><?php echo $vendorPayout;?></td>
            </tr>
            
        </table>
    </div>
    <div class="col-lg-8">
        <table class="table table-striped table-bordered detail-view">
            <thead>
                <tr>
                    <th colspan ="3">Features</th>
                </tr>
            </thead>
            <tr>
                <td>Brand</td>
                <td><?php echo $productFeatureDetails['Brand'];?></td>
            </tr>
            <tr>
                <td>Model Number</td>
                <td></td>
            </tr>
            <tr>
                <td>Vehicle<br>Brand/Model/Year</td>
                <td></td>
            </tr>
            <tr>
                <td>Universal Fit</td>
                <td></td>
            </tr>
            <tr>
                <td>Color</td>
                <td><?php echo $productFeatureDetails['Color'];?></td>
            </tr>
            <tr>
                <td>Size</td>
                <td><?php echo $productFeatureDetails['Size'];?></td>
            </tr>
            <tr>
                <td>Material</td>
                <td></td>
            </tr>
            <tr>
                <td>EAN</td>
                <td></td>
            </tr>
            <tr>
                <td>Key Spec 1</td>
                <td><?php echo $productFeatureDetails['Key Feature 1'];?></td>
            </tr>
            <tr>
                <td>Key Spec 2</td>
                <td><?php echo $productFeatureDetails['Key Feature 2'];?></td>
            </tr>
            <tr>
                <td>Key Spec 3</td>
                <td><?php echo $productFeatureDetails['Key Feature 3'];?></td>
            </tr>
            <tr>
                <td>Key Spec 4</td>
                <td><?php echo $productFeatureDetails['Key Feature 4'];?></td>
            </tr>
            <tr>
                <td>Width(cm)</td>
                <td></td>
            </tr>
            <tr>
                <td>Height(cm)</td>
                <td></td>
            </tr>
            <tr>
                <td>Length(cm)</td>
                <td></td>
            </tr>
            <tr>
                <td>Charged Weight (Kg)</td>
                <td></td>
            </tr>
            <tr>
                <td>Warranty</td>
                <td></td>
            </tr>
            <tr>
                <td>Product Type</td>
                <td><?php echo $productFeatureDetails['Product Type'];?></td>
            </tr>
        </table>
    </div>
    <?php 
    $items=[];
    $i=0;
    if(!empty($productImages)){
        foreach ($productImages as $value) {
            
            $items[$i]['thumb']= Helper::getImagePath($value['id_image'], 'large', 'jpg', 'default');
            $items[$i]['original'] = Helper::getImagePath($value['id_image'], 'large', 'jpg', 'default');
            $items[$i]['group'] = $model['id_product'];
            $i++;
        }
    }
    ?>
    <div class="col-lg-4">
        <table class="table table-striped table-bordered detail-view">
        <thead>
                <tr>
                    <th>Images</th>
                </tr>
        </thead>
        <tbody>
                <tr>
                    <td>
                    <?php echo Lightbox::widget([
                            'files' => $items
                        ]); ?>
                    </td>
                </tr>
        </tbody>
        </table>
    </div>
</div>

<?php
Modal::begin(['id' => 'updatePrice',
    'closeButton' => [true],
    'header' => 'Update Price',
    'footer' => ''
        ]
);
$price = 0;
$sell_price =0;
$id_employee=0;
if (isset($model['base_price']) && isset($model['sell_price'] ))
{
    $price = str_replace(",", "",$model['base_price']);
    $sell_price = str_replace(",", "",$model['sell_price']);

    if(!empty($model['id_employee']))
        $id_employee = $model['id_employee'];
}
echo yii\base\View::render('update-price', array('price' => $price,'sell_price'=>$sell_price,'id_employee'=>$id_employee));



Modal::end();

Modal::begin(['id' => 'updateStock',
    'closeButton' => [true],
    'header' => 'Update Stock',
    'footer' => ''
        ]
);
$quantity = 0;
if (isset($model['sav_quantity']))
    $quantity = $model['sav_quantity'];
echo yii\base\View::render('update-stock', array('stock' => $quantity));


Modal::end();

Modal::begin(['id' => 'viewStockLog',
    'closeButton' => [true],
    'header' => 'View Stock Log',
    'footer' => ''
        ]
);

echo yii\base\View::render('_view-stock-log', array('viewLog' => $viewLog));


Modal::end();


Modal::begin(['id' => 'viewPriceLog',
    'closeButton' => [true],
    'header' => 'View Price Log',
    'footer' => ''
        ]
);

echo yii\base\View::render('_view-price-log', array('viewPriceLog' => $viewPriceLog));


Modal::end();
?>