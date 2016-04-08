<style type="text/css">table, tr, td,th { text-align: center; }</style>
<?php
use yii\helpers\Html;
use yii\helpers\Url as currentURL;
//use yii\grid\GridView;
use common\models\User;
use common\models\Shop;
use yii\helpers\ArrayHelper;
use common\extensions\CommonHelper;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\date\DatePicker;
use yii\widgets\Pjax;
use common\components\Helpers as Helper;
use yii\bootstrap\Modal as Modal;
use common\components\Configuration;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use backend\assets\OrderAsset;
use branchonline\lightbox\Lightbox;
use backend\models\Order;
use common\components\ShopdevConfiguration;
use common\components\Session as ShopSession;
use backend\models\Product;

$currentUrl = currentURL::current(['lg'=>NULL], TRUE);

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
OrderAsset::register($this);
$orderStatusIds=[];
foreach ($filterOrderStatuses as $value) {
    $orderStatusIds[$value['id_order_state']]=$value['name'];

}
// Export Filter  
    $gridExportColumns = [
        [
            'attribute' => 'date_add',
            'label' => 'Order Date',
        ],
        [
            'attribute' => 'id_order',
            'label' => 'Order Id',
        ],
        [
            'attribute' => 'invoice_ref_key',
            'label' => 'Order Number',
        ],
        [
            'attribute' => 'reference',
            'label' => 'Sub-Order Number',
        ],
        [
            'attribute' => 'osname',
            'label' => 'Order Status',
        ],
        [
            'attribute' => 'product_name',
            'label' => 'Product Name',
        ],
        [
            'attribute' => 'product_quantity',
            'label' => 'Stock',
        ],
        [
            'attribute' => 'total_paid_tax_incl',
            'label' => 'Total Price',
        ],
        [
            'attribute' =>'vendor_payout',
            'label'     =>  'Vendor Payout'
        ],
        [
            'label' => 'Package Slip No ',
            'attribute'=>'slip_number',
        ],
        [
            'attribute' => 'count_days',
            'label' => 'Ageing(In days)'
        ],
        [
            'attribute' => 'response_waywill',
            'label' => 'Waybill Number',
        ],
        [
            'attribute' => 'payment',
            'label' => 'Mode of Payment',
        ],
        [
            'attribute' => 'osname',
            'label' => 'Order Status',
        ],
        [
            'attribute' => 'carrier_name',
            'label' => 'Courier Name',
        ]];
        if (isset($sellerDetails) && count($sellerDetails)>0){
                        $gridExportColumns = array_merge_recursive($gridExportColumns,[[
                        'attribute' => 'company_name',
                        'label' => 'Seller Name',
                    ]]);
                    }
        $gridExportColumns = array_merge_recursive($gridExportColumns,[
            [
            'attribute' =>  'product_reference',
            'label'     =>  'Product SKU',
        ],
        [
            'attribute' => 'customer',
            'label' => 'Customer Name',
        ],
        
        [
            'attribute' => 'unit_price_tax_incl',
            'label' => 'Product Price',
        ],
        [
            'attribute' => 'cod_charge',
            'label' => 'COD Fee',
        ],
        [
            'attribute' => 'total_shipping',
            'label' => 'Shipping Fee',
        ],
        [
            'attribute' => 'confirmed_by',
            'label' => 'Confirmed By',
        ],
        'slip_number',
        [
            'attribute' => 'total_paid_tax_incl',
            'label' => 'Total Paid',
        ],
        [
            'attribute' => 'mobile11_number',
            'label'     => 'Mobile Number 1',
        ],
        [
            'attribute' => 'contact_number',
            'label'     => 'Mobile Number 2',
        ],
        [
            'attribute' => 'address1',
            'label' => 'Customer Address 1',
        ],
        [
            'attribute' => 'address2',
            'label' => 'Customer Address 2',
        ],
        [
            'attribute' => 'state_name',
            'label' => 'State',
        ],
        [
            'attribute' => 'city',
            'label' => 'City',
        ],
        [
            'attribute' => 'postcode',
            'label' => 'Postcode',
        ],
        [
            'label' => 'Img',
            'value' =>
            function ($model) {
                return Helper::getImagePath($model['id_image'], 'thickbox', 'jpg', 'default');
            },
            'format' => 'raw',
        ]
    ]);
?>


<div class="row">
    
    <?php
    if (Yii::$app->session->has('invoiceDuplError')) {
        ?>
        <div class="col-md-12 pdlr5">
            <div class="alert alert-danger"><i class="icon fa fa-ban"></i><?php
                echo Yii::$app->session->get('invoiceDuplError');
                Yii::$app->session->remove('invoiceDuplError');
                ?></div>
        </div>
    <?php } ?>
    <div class="col-md-12 pdlr5">
        <div class="" id="messageBox" style="display:none;"></div>
    </div>
        
        <?php
            if (isset($sellers) && count($sellers)>0) {
        ?>
        <div class="col-md-2 pdlr5">
            <select class="form-control" onchange="filterSellers(this.value)" >
                <option value="">All Sellers</option>
                <?php
                if (isset($sellers)) {
                    foreach ($sellers as $sellerDetail) {
                        ?>
                        <option value="<?php echo $sellerDetail['id_seller']; ?>" <?php
                        if (isset($getParam['sellers']) && ($getParam['sellers'] == $sellerDetail['id_seller'])) {
                            echo 'selected';
                        }
                        ?>><?php echo $sellerDetail['company']; ?></option>
                                <?php
                            }
                        } else {
                            ?>
                        <?php } ?>
            </select>
        </div>
        <?php
            }
        ?>

   
    <div class="col-md-2 pdlr5">
        <select class="form-control" onchange="filterStatus(this.value)">
            <option value="">Filter by Status</option>
            <?php
            if ($filterOrderStatuses) {
                foreach ($filterOrderStatuses as $row) {
                    ?>
                    <option value="<?php echo $row['id_order_state'] ?>" <?php
                    if (!empty($getParam['status']) && $getParam['status'] == $row['id_order_state']) {
                        echo 'selected';
                    }
                    ?> ><?php echo $row['name'] ?></option>
                            <?php
                        }
                    }
                    ?>
        </select>
    </div>
    
    <!-- Export Button -->
    <div class="">
           

            <?php $singleExportForm = ActiveForm::begin(['action' => 'index.php?r=order/export-selected-rows', 'id' => 'singleExport']); ?>    
            <div class="col-md-2 pdlr5" >
                <div class="form-group">
                    <div class="form-group">
                        <?= Html::input('hidden', 'multipleOrderIds', '', ['id' => 'multipleOrderIds']) ?>
                                          
                    <?= Html::submitButton('Export Selected Orders', ['class' => 'btn btn-primary btn-block']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            
        </div>
    
    <!-- End Export Button -->
 




   <?php

    // if ($getOrderStatuses) {
    //     $displayUpdateStatusColumn = true;
    // } else {
    //     $displayUpdateStatusColumn = false;
    // }

    $gridColumns = [
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'expandTitle' => 'See Customer Details',
            'value' => function ($model, $key, $index, $column) {

        return GridView::ROW_COLLAPSED;
    },
            'expandIcon' => '<i class="fa fa-plus"></i>',
            'collapseIcon' => '<i class="fa fa-minus"></i>',
            'allowBatchToggle' => true,
            'detail' => function ($model) {
        return Yii::$app->controller->renderPartial('_rows-expand', ['model' => $model]);
    },
            'detailOptions' => [
                'class' => 'kv-state-enable',
            ],
        ],
        [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function ($searchModel, $key, $index, $column) {
                 $key = array('PS_ON_BACKORDER');
                $configValues = ShopdevConfiguration::getConfigValue($key);
                $setConfigValue=[];
                foreach($configValues as $configValue){
                 $setConfigValue[$configValue['name']] = $configValue['value'];
                }
        $searchModel['id_order_state'] = $setConfigValue['PS_ON_BACKORDER'] ? $disabled = 'none' : $disabled = 'block';
        if ($searchModel['id_order_state'] == $setConfigValue['PS_ON_BACKORDER']) {
            $disabled = 'none';
            $value = 0;
        } else {
            $disabled = 'block';
            $value = $searchModel['id_order'];
        }
        return ['name' => 'selection[]', 'class' => 'check-select-highlight', 'value' => $value, 'style' => 'display:' . $disabled];
    }
        ],
        [
            'attribute' => 'date_add',
            'label' => 'Order Date',
        ],
        [
            'label' => 'Img',
            'value' => function ($model) {
                    $product=new Product;
                    $productImages=$product->getProductImages($model['id_product']);
                    $i=0;
                    $tmp=[];
                    if(!empty($productImages))
                    {
                        foreach ($productImages as $value) {
                            
                            $tmp[$i]['thumb']= Helper::getImagePath($value['id_image'], 'small', 'jpg', 'default');
                            $tmp[$i]['original'] = Helper::getImagePath($value['id_image'], 'large', 'jpg', 'default');
                            $tmp[$i]['group'] = $model['id_product'];
                            $tmp[$i]['pageType'] = 'listingPage';
                            $i++;
                        }
                        return Lightbox::widget([
                            'files' => $tmp
                        ]);

                    }
            },
            'format' => 'raw',
        ],
        [
            'attribute' => 'id_order',
            'label' => 'Order Id',
             'value' =>function($model){
                return Html::a($model['id_order'], ['view', 'id' => $model['id_order']]);
            },
            'format' => 'raw',
            'filter' => AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'id_order',
                'clientOptions' =>
                [
                    'source' => [],
                ],
                'options' => array('class' => 'form-control')
            ])
        ],
        [
            'attribute' => 'invoice_ref_key',
            'label' => 'Order No',
            'value' =>function($model){
                return Html::a($model['invoice_ref_key'], ['view', 'id' => $model['id_order']]);
            },
            'format' => 'raw',
            'filter' => AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'invoice_ref_key',
                'clientOptions' =>
                [
                    'source' => [],
                ],
                'options' => array('class' => 'form-control')
            ])
        ],
        [
            'attribute' => 'reference',
            'label' => 'Sub-Order No',
             'value' =>function($model){
                return Html::a($model['reference'], ['view', 'id' => $model['id_order']]);
            },
            'format'=>'raw',
            'filter' => AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'reference',
                'clientOptions' =>
                [
                    'source' => [],
                ],
                'options' => array('class' => 'form-control')
            ])
        ],
            
        [
            'attribute' => 'osname',
            'label' => 'Status',
            'contentOptions' => ['style'=>'font-weight:bold'],
            'filter' => Html::activeDropDownList($searchModel, 'orderStatus', $orderStatusIds,['class'=>'form-control','style'=>'width: 135px;','prompt' => 'Select Status']),
        ],
        [
            'attribute' => 'product_name',
            'label' => 'Product Name',
            'format' => 'raw',
             'value' => function ($model) {
                return Html::a(Html::encode($model['product_name']),'index.php?r=product/view&id='.$model['id_product']);
                //return $data['day2']==0?0:Html::a('index.php?r=product/view&id=');
                //return '<a href='.Yii::$app->params['WEB_URL'].'index.php?r=product/view&id='.$model['id_product'].' target="_blank">".'$model['product_name'].'</a>';
            },
            'filter' => AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'product_name',
                'clientOptions' =>
                [
                    'source' => [],
                ],
                'options' => array('class' => 'form-control')
            ])
        ],
            
        [
            'attribute' => 'product_quantity',
            'label' => 'Qty',
        ],
        [
        'attribute'=>'total_paid_tax_incl',
        'value' => function($model) {
        return Html::a(Yii::t('app', ' {modelClass}', [
                        'modelClass' => $model['total_paid_tax_incl'],
                    ]), ['order/view-payment-details', 'id_order' => $model['id_order'], 'invoice_ref_key' => $model['invoice_ref_key'], 'price' => $model['unit_price_tax_incl'], 'discount' => $model['voucher_discount'], 'cod' => $model['cod_charge'], 'shipping' => $model['total_shipping'], 'paid' => $model['total_paid_tax_incl'], 'product_quantity' => $model['product_quantity'],'payment'=>$model['payment']], ['class' => 'btn btn-primary btn-xs upate-status-btn', 'id' => 'popupModal_' . $model['id_order']]);
        },
        'format' => 'raw',
        'label' => 'Total Price',
        ],
        [
            'attribute' =>'vendor_payout',
            'value' => function($model){
                return $model['vendor_payout'];
            },
            'format'    =>  'raw',
            'label'     =>  'Vendor Payout'
        ],
        [
            'label' => 'Package Slip No ',
            'attribute'=>'slip_number',
            'value' => function($model) {
             if($model['slip_number']!= '') {
                $model['slip_number'] = 'PS'.$model['slip_number']; 
             }
             $orders=new Order;
             if(!is_null($orders->getDownloadStatus($model['id_order'],'check')))
             {
                $className='btn btn-success btn-xs';
                $altText='Already-Downloaded';
             }
             else
             {
                $className='btn btn-primary btn-xs';
                $altText='Download Packing Slip';

             }
             if($altText!='Download Packing Slip')
                return "<button title='".$altText."' type='button' class='".$className."' onclick='downloadPackageSlip(".$model['id_order'].");'>".$model["slip_number"]."</button><span style='font-size: 10px;'>Already-Downloaded</span>";
            else
                return "<button title='".$altText."' type='button' class='".$className."' onclick='downloadPackageSlip(".$model['id_order'].");'>".$model["slip_number"]."</button>";            
            },
            'format'=>'raw', 
            'filter' => AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'slip_number',
                'clientOptions' =>
                [
                    'source' => [],
                ],
                'options' => array('class' => 'form-control')
            ])
        ],
        [
            'attribute' => 'count_days',
            'label' => 'Ageing (In Days)',
            'value' => function($model) {

/*                $key = array('PS_OS_ORDERCONFIRMATION');
                $configValues = ShopdevConfiguration::getConfigValue($key);

                foreach($configValues as $configValue){
                 $setConfigValue[$configValue['name']] = $configValue['value'];
                }
                 if($model['current_state']==$setConfigValue['PS_OS_ORDERCONFIRMATION'] && $model['count_days']>2 )
                 {
                    $background='#E51212';
                    $color='#ECEFEE';
                    $padding='5px';
                    $style='background-color:'.$background.';color:'.$color.';'.$padding.'';
                 }
                 else
                 {
                    $background='none';
                    $color='#222d32';
                    $padding='0px';
                    $style='background-color:'.$background.';color:'.$color.';'.$padding.'';
                 }*/
                 //$style .='text-align:center;' 
                 return Html::a("<span>"  . $model['count_days']. " </span>");
                 
            },
            'format'=>'raw',
            'filter' => Html::activeDropDownList($searchModel, 'count_days', ['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5+'],['class'=>'form-control','prompt' => 'Select Days']),

        ],
        [
            'attribute' => 'response_waywill',
            'label' => 'Waybill No',
            'filter' => AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'response_waywill',
                'clientOptions' =>
                [
                    'source' => [],
                ],
                'options' => array('class' => 'form-control')
            ]),
            'value' => function($model) {
                if($model['response_waywill']!='')
                    return Html::a(Yii::t('app', ' {modelClass}', [
                            'modelClass' => $model['response_waywill'],
                        ]), ['order/view-shipping-details', 'id_order' => $model['id_order'], 'delivery_type' => $model['delivery_type'], 'carrier_name' => $model['carrier_name'], 'response_waywill' => $model['response_waywill']], ['class' => 'btn btn-primary btn-xs upate-status-btn', 'id' => 'popupModal_' . $model['id_order']]);
                else
                    return " ";
            },
            'format' => 'raw',

        ],
        [
            'attribute' => 'payment',
            'value' => function($model) {
                 if($model['payment']=="Cash on delivery (COD)")       
                     return "COD";
                 else
                     return "Prepaid";
            },
       
            'label' => 'Payment Method',
            

            'filter' => Html::activeDropDownList($searchModel, 'payment', ['Cash on delivery (COD)'=>'COD','Prepaid'=>'Prepaid'],['class'=>'form-control','prompt' => 'Select Method']),
        ],
        [
            'attribute' => 'carrier_name',
            'label' => 'Courier Name',
            'filter' => AutoComplete::widget([
                'model' => $searchModel,
                'attribute' => 'carrier_name',
                'clientOptions' =>
                [
                    'source' => [],
                ],
                'options' => array('class' => 'form-control')
            ])
        ],

    ];
    if (isset($sellerDetails) && count($sellerDetails)>0){
       $gridColumns = array_merge_recursive($gridColumns,[ [
            'attribute' => 'company_name',
            'label' => 'Seller Name',
        ]]);
}
    ?>
    <div class="col-md-2 pdlr5 ">
                <?php
                $fileName='order_'.date('Y-m-d').'.csv';
                echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridExportColumns,
                    'filename' => $fileName,
                    'fontAwesome' => true,
                    'target' => ExportMenu::TARGET_SELF,
                    'dropdownOptions' => [
                        'label' => 'Export All',
                        'class' => 'btn btn-primary btn-block'
                    ],
                    'exportConfig' => [
                        ExportMenu::FORMAT_HTML => false,
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_EXCEL => false,
                        ExportMenu::FORMAT_PDF => false
                    ]
                ]);
                ?>
            </div>

</div>
    <div class="row">
        <?php $form = ActiveForm::begin(['id'=>'updateStatusForm']); ?>
        <div class="col-md-2 pdlr5" style="margin-right: -85px;">
            <div class="form-group">
                <a id="advancedSearchBtn" class="btn btn-primary btn-md">Advanced Search </a>
            </div>
        </div>

        <div class="col-md-2 pdlr5" style="margin-right: -100px;"> 
            <div class="form-group">
                <?= Html::button('Change Status', ['class' => 'btn btn-success btn-md', 'onclick' => 'changeStatus("index")']) ?>
            </div>
        </div>

        <div class="col-md-2 pdlr5" id="showStatus" style="display:none;margin-right: -75px;">
            <div class="form-group">
                <select  class="form-control" name="id_order_state" style="width:130px;border: 1px solid #EF5A28;height: 32px;" id="statusDropdown" onchange="selectReason('indexpage', this.value)">

                </select>
            </div>
        </div>

        <div class="col-md-2 pdlr5" id="showReasonDropdwon" style="display:none;margin-right: -75px;">
            <div class="form-group">
                <select id="reasonDropDown" class="form-control" name="id_order_state_reason" style="width:130px;border: 1px solid #EF5A28;height: 32px;">

                </select>
            </div>
        </div>

        <div class="col-md-1 pdlr5" id="showUpdateButton" style="display:none;margin-right: -35px;">
            <div class="form-group">
                <?= Html::button('Update', ['class' => 'btn btn-success btn-md', 'onclick' => 'updateOrderStatus("indexpage","bulkUpdate")']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
        
         <?php $createInvoiceForm = ActiveForm::begin(['action' => 'index.php?r=order/invoices', 'id' => 'createInvoice']); ?>   
            <div class="col-md-2 pdlr5" style="margin-right: -57px;" >
                <div class="form-group">
                    <div class="form-group">
                    <?php
                    if(Helper::isSuperAdmin() || Helper::isAdmin()){
                    ?>
                        <?= Html::input('hidden', 'invoiceMultipleOrderIds', '', ['id' => 'invoiceMultipleOrderIds']) ?>
                        <?= Html::submitButton('Download Packing Slip', ['class' => 'btn btn-primary btn-md']) ?>
                    <?php } else { ?>
                    <?= Html::input('hidden', 'invoiceMultipleOrderIds', '', ['id' => 'invoiceMultipleOrderIds']) ?>
                    <a onclick="openPopupUrl('downloadpackageslip')" class="btn btn-primary btn-md">Download Packing Slip</a>
                    <?php } ?>
                    </div>
                </div>
            </div>    
            <?php ActiveForm::end(); ?> 
            <!-- <?php $createManifest = ActiveForm::begin(['action' => 'index.php?r=order/create-manifest', 'id' => 'createManifest']); ?> -->  
            <div class="col-md-2 pdlr5" style="margin-right: -81px;" >
                <div class="form-group">
                    <div class="form-group">
                        <!-- <?= Html::input('text', 'manifestMultipleOrderIds', '', ['id' => 'manifestMultipleOrderIds']) ?> -->
                        <!-- <?= Html::submitButton('Create Manifest', ['class' => 'btn btn-success btn-block']) ?> -->
                        <a data-toggle="modal" data-target="#createManifest" class="btn btn-primary btn-md" > Generate Manifest</a>

                    </div>
                </div>
            </div>
            <div class="col-md-2 pdlr5" style="margin-right: -88px;"  >
                <div class="form-group">
                    <div class="form-group">
                        <a data-toggle="modal" data-target="#pickList" class="btn btn-primary btn-md" > Generate Picklist</a>
                    </div>
                </div>
            </div>  
            <div class="col-md-2 pdlr5" >
                <div class="form-group">
                    <div class="form-group">
                        <a data-toggle="modal" data-target="#productpickList" class="btn btn-primary btn-md" > Generate Product Picklist</a>                    
                    </div>
                </div>
            </div>  
        
        
        
        
        
    </div>

    <?php
    if (isset($getParam['sellers']) && !empty($getParam['sellers']))
        $searchUrl = 'index.php?r=order/index&sellers=' . $getParam['sellers'];
    else
        $searchUrl = 'index.php?r=order/index';

    // else if (isset($getParam['status']) && !empty($getParam['status']))
    //     $searchUrl = 'index.php?r=order/index&status=' . $getParam['status'];


$searchform = ActiveForm::begin(['action'=>$searchUrl,'method' => 'get']); 
    $displayNone='none';
    if(!empty($getParam['filter']))
        $displayNone='block';
?>
<div class="user-index">
    
<div class="row" id="advancedSearchForm" style="display: <?php echo $displayNone;?>">
    <div class="col-md-10"> 
        <input type="hidden" name="filter" value="true">

        <span class="col-md-2">
            <div class="form-group">
            <label>Select Type</label>
                <select name="search_type" id="search_type" class="form-control">
                    <option value="">Select Type</option>
                    <option value="id_order" <?php if(!empty($getParam['search_type']) && ($getParam['search_type']=="id_order")) {echo 'selected';} ?> >Order Id</option>
                    <option value="invoice_ref_key" <?php if(!empty($getParam['search_type']) && ($getParam['search_type']=="invoice_ref_key")) {echo 'selected';} ?>>Order Number</option>
                    <option value="reference" <?php if(!empty($getParam['search_type']) && ($getParam['search_type']=="reference")) {echo 'selected';} ?>>Sub-Order Number</option>
                    <option value="id_product" <?php if(!empty($getParam['search_type']) && ($getParam['search_type']=="id_product")) {echo 'selected';} ?>>Product Id</option>
                    <option value="response_waywill" <?php if(!empty($getParam['search_type']) && ($getParam['search_type']=="response_waywill")) {echo 'selected';} ?>>WayBill Number</option>
                    <option value="payment" <?php if(!empty($getParam['search_type']) && ($getParam['search_type']=="payment")) {echo 'selected';} ?>>Mode of Payment</option>
                </select>
            </div>
        </span>
        <span class="col-md-2">
            <div class="form-group">
                <label>&nbsp</label>
                <input type="text" name="search_box" id="search_box" class="form-control" value="<?php if(!empty($getParam['search_box'])) {echo $getParam['search_box'];} ?>" />
            </div>        
        </span>
        <?php
        if(count($filterOrderStatuses)>0)
        {
            $showStatusOnSearch=explode(',',Configuration::get('SHOW_STATUS_ON_SEARCH'));
            $explodeCheckedIds=[];
            if(!empty($checkedStatus))
            {
                $explodeCheckedIds=explode(',',$checkedStatus);
            }
        ?>
        <div class="col-md-8" style="padding-top: 16px;" > 
            <?php 
            $k=0;
            foreach ($filterOrderStatuses as $statusDetails) {
                if(in_array($statusDetails['id_order_state'],$showStatusOnSearch))
                {
                    $k++;
                    if($k>5)
                    {
                        echo '<br>';
                    }
                ?>
                    <input type="checkbox" name="search_status_param[]" value="<?php echo $statusDetails['id_order_state']; ?>" <?php if(in_array($statusDetails['id_order_state'],$explodeCheckedIds)) { echo 'checked'; } ?> >
                <label><?php echo $statusDetails['name']; ?>(<?php echo $statusDetails['count_orders']; ?>) </label>
                
        <?php   
                    
                } 
                
            }?>
            
        </div>
        <?php } ?>
        <div class="" style="clear:both;">
            <span class="col-md-2">
                <div class="form-group">
                <label>Select Carrier</label>
                    <select name="id_carrier" id="id_carrier" class="form-control">
                        <option value="">Select Carrier</option>
                        <?php foreach($getAllCarrier as $carrierDetail) { ?>
                            <option value="<?php echo $carrierDetail['id_reference'];?>" <?php if(isset($getParam['id_carrier']) && $getParam['id_carrier']==$carrierDetail['id_reference']) {echo 'selected';}?>><?php echo $carrierDetail['name'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </span>

       </div>
        <div class="pdlr5">
            
            <span class="col-md-4">
            <label>Search Between Dates</label>
                <?php
                echo DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'from_date_add',
                'attribute2' => 'to_date_add',
                'options' => ['placeholder' =>'From Date'],
                'options2' => ['value' => date('d-m-Y')],
                'type' => DatePicker::TYPE_RANGE,
                'form' => $searchform,
                'pluginOptions' => [
                'todayHighlight' => true,
                'format' => 'dd-mm-yyyy',
                'autoclose' => true,
                'rowOptions' =>['class' => 'center-table']
                ]
            ]);
                ?>
            </span>             
        </div>
    </div>
    <span class="col-md-2">
            <a data-toggle="modal" data-target="#searchModal" style="display:<?php echo $displayNone; ?>"  class="btn btn-success btn-block" href="<?=Yii::$app->params['WEB_URL'] ?>index.php?r=order/save-search"> Save Search</a>
    </span>

    <div class="col-md-2">
        <label>Saved Searches</label>
        <ul class="list-group" id="search-box">
        <?php 
        
        if(isset($searchData)){
        $i=0;
        foreach ($searchData as $searchDetails) {
            $query_string=Helper::unserializeHtmlBuildQuery($searchDetails['query_string']);
            $url='index.php?'.$query_string;
            $i++;
            $activeClass='';
            if($_SERVER['QUERY_STRING']==$query_string)
            { 
                $activeClass='active'; 
            }

            if($i==1){
          ?>
            <li class="list-group-item">
                <a href="<?=Yii::$app->params['WEB_URL'] ?>index.php?r=order/index">Reset Search</a>
            </li>
             <?php } ?>
            <li class="list-group-item <?php echo $activeClass;?> ">
                <span class="label label-default label-pill pull-xs-right" onclick="searchData('<?php echo Url::current(['search-delete' => null]).'&search-delete='.$searchDetails['id'];?>','delete')" style="cursor: pointer;" title="Delete Search" ><i class="fa fa-times"></i></span>
                <span onclick="searchData('<?php echo $url;?>','search')" style="cursor: pointer;"><?php echo $searchDetails['name'];?></span>
            </li>
        <?php } } else { ?>
            <li class="list-group-item">
                No Searches Found
            </li>
            <?php } ?>

        </ul>

    </div>

    <div class="col-md-10">
            <span class="col-md-4">
                <?= Html::submitButton('Search', ['class' =>'btn btn-success btn-block']) ?>
            </span>
            <span class="col-md-4">
            <!-- <?= Html::resetButton('Reset', ['class' => 'btn btn-primary btn-block']) ?> -->
                <a class="btn btn-primary btn-block" href="<?=Yii::$app->params['WEB_URL'] ?>index.php?r=order/index">Reset</a>
            </span>
    </div>

</div>

<?php ActiveForm::end(); ?>
    <?php
    $classTwentyActive='';
    $classFiftyActive='';
    $classHundredActive='';
    $classTwoHundredActive='';
    $classThousandActive='';
    if(!empty($_REQUEST['page-count']))
    {
        switch($_REQUEST['page-count'])
        {
            case 25:
            $classTwentyActive='active';
            break;
            case 50:
            $classFiftyActive='active';
            break;
            case 100:
            $classHundredActive='active';
            break;
            case 200:
            $classTwoHundredActive='active';
            break;
            case 1000:
            $classThousandActive='active';
            break;

        }
    }
        else{
            $classFiftyActive='active';
        }
    ?>
    <div class="text-right">
        <ul class="pagination pagination-sm">
            <li class="<?php echo $classTwentyActive;?>"><a href="<?php echo Url::current(['page-count' => null]).'&page-count=25&search=true';?>">25</a></li>
            <li class="<?php echo $classFiftyActive;?>"><a href="<?php echo Url::current(['page-count' => null]).'&page-count=50&search=true';?>">50</a></li>
            <li class="<?php echo $classHundredActive;?>"><a href="<?php echo Url::current(['page-count' => null]).'&page-count=100&search=true';?>">100</a></li>
            <li class="<?php echo $classTwoHundredActive;?>"><a href="<?php echo Url::current(['page-count' => null]).'&page-count=200&search=true';?>">200</a></li>
            <li class="<?php echo $classThousandActive;?>"><a href="<?php echo Url::current(['page-count' => null]).'&page-count=1000&search=true';?>">1000</a></li>
        </ul>
    </div>
    <?php
        Modal::begin(['id' =>'modal',
        ]);
        Modal::end();

        Modal::begin(['id' => 'searchModal', 
        'closeButton'=>[true],
        'header'=> 'Enter the name of the search',
        'footer'=>''
        ]
        ); 
        ?>
        <?php echo yii\base\View::render('save-search', array()); ?>
        <?php
        Modal::end();

        Modal::begin(['id' => 'createManifest', 
        'closeButton'=>[true],
        'header'=> 'Generate Manifest',
        'footer'=>''
        ]
        );
        ?>
        <?php echo yii\base\View::render('_create-manifest-view', array('getAllCarrier'=>$getAllCarrier,'sellers'=>$sellers)); ?>
        <?php
        Modal::end();

        Modal::begin(['id' => 'pickList', 
        'closeButton'=>[true],
        'header'=> 'Generate Picklist',
        'footer'=>''
        ]
        );
        ?>
        <?php echo yii\base\View::render('_create-picklist-view', array('getAllCarrier'=>$getAllCarrier,'sellers'=>$sellers)); ?>
        <?php
        Modal::end();

        Modal::begin(['id' => 'productpickList', 
        'closeButton'=>[true],
        'header'=> 'Generate Product Picklist',
        'footer'=>''
        ]
        );
        ?>
        <?php echo yii\base\View::render('create-product-picklist', array('sellers'=>$sellers)); ?>
        <?php
        Modal::end();
    
    

    ?>




    <?= GridView::widget([
       'dataProvider'       => $dataProvider,
       'filterModel'        => $searchModel,
       'columns'            => $gridColumns,
       'bootstrap'          => true,
       'responsive'         => false,
       'containerOptions'   => ['style'=>'overflow: auto'], // only set when $responsive = false
       'headerRowOptions'   => ['class'=>'kartik-sheet-style'],
       'responsiveWrap'     => false,
       'id'                 => 'gridViewId',
       'rowOptions' => function ($model){
            $key = array('PS_OS_ORDERCONFIRMATION');
            $configValues = ShopdevConfiguration::getConfigValue($key);

            foreach($configValues as $configValue){
             $setConfigValue[$configValue['name']] = $configValue['value'];
            }
            if(($model['current_state']==$setConfigValue['PS_OS_ORDERCONFIRMATION'] && $model['count_order_conf_days']>24 && $model['count_order_conf_days']<48 )){
                return ['style'=> 'background-color:rgba(229, 229, 0, 0.75);']; 
               //return ['class' => 'order-index-grid','style'=> 'background-color:#DD4B39;'];
            }
            if(($model['current_state']==$setConfigValue['PS_OS_ORDERCONFIRMATION'] && $model['count_order_conf_days']>48))
               return ['class' => 'order-index-grid','style'=> 'background-color:#DD4B39;'];
        },
        //'floatHeader'=>true,
       ////'floatOverflowContainer'=>true,
       //'floatHeaderOptions'=>['scrollingLeft'=>'50']
   ]); ?>

</div>
<?php $downloadSingleInvoice = ActiveForm::begin(['action' => 'index.php?r=order/single-invoice', 'id' => 'downloadSingleInvoice']); ?>   
    <div class="col-md-2 pdlr5" style="margin-right: -57px;" >
        <div class="form-group">
            <div class="form-group">
            <?= Html::input('hidden', 'single_id_order', '', ['id' => 'single_id_order']) ?>
            </div>
        </div>
    </div>    
<?php ActiveForm::end(); ?> 
<style type="text/css">
    .kv-grid-wrapper {
    position: relative;
    overflow: auto;
    height: 500px;
</style>
 