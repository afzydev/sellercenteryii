<style type="text/css">table, tr, td,th { text-align: center; }</style>
<?php
use yii\helpers\Html;
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
use backend\assets\ProductAsset;
use branchonline\lightbox\Lightbox;
use dosamigos\datepicker\DateRangePicker;
use backend\models\Product;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$gridColumns = array();
$catagory = [];
$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
ProductAsset::register($this);

        $gridColumns = [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function ($searchModel, $key, $index, $column) {
                   
                    return ['name' => 'selection[]', 'class' => 'check-select-highlight', 'value' => $searchModel['id_product'], 'style' => 'display: block' ];
                }
            ],
            [
                'label' =>'Status', 
                'attribute'=>'active',
                'value' => function ($model)
                { 
                    if($model['active']==1){
                        return '<span class="glyphicon glyphicon-ok" style="color:green"></span>';
                    }else{
                        return '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
                    }
                },
                'format'=>'raw',
                'filter' => Html::activeDropDownList($searchModel, 'active', ['0'=>'Inactive','1'=>'Active'],['class'=>'form-control','style'=>'width: 90px','prompt' => 'Select Status']),
            ],    
            [
                'label' => 'Img',
                'value' =>
                function ($model){
               
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
                'attribute' => 'name',
                'label' => 'Product Name',
                'value' => function ($model) { 
                    return Html::a($model['name'], ['view', 'id' => $model['id_product']]);
                },
                'format'=>'raw',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'name',
                    'clientOptions' =>
                    [
                        'source' => [],
                    ],
                    'options' => array('class' => 'form-control')
                ])
            ],
            [
                'attribute' => 'id_product',
                'label' => 'Product ID',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'id_product',
                    'clientOptions' =>
                    [
                        'source' => [],
                    ],
                    'options' => array('class' => 'form-control')
                ])
            ],
            [
                'attribute' => 'name_category',
                'label' => 'Category name',
                'filter' => Html::activeDropDownList($searchModel, 'name_category', ArrayHelper::map($l3cats, 'id_category', 'name'),['class'=>'form-control','prompt' => 'Select Category']),
            ],
            [
                'attribute' => 'shop_reference',
                'label' => 'Seller SKU',
                 'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'shop_reference',
                    'clientOptions' =>
                    [
                        'source' => [],
                    ],
                    'options' => array('class' => 'form-control')
                ])
                
            ],
            [
                'attribute' => 'date_upd',
                'label' => 'Modified Date',
            ],
            [
                'attribute' => 'sav_quantity',
                'label' => 'Stock',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'quantity',
                    'clientOptions' =>
                    [
                        'source' => [],
                    ],
                    'options' => array('class' => 'form-control')
                ])
            ],
            
            [
                'attribute' => 'base_price',
                'label' => 'Base Price',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'base_price',
                    'clientOptions' =>
                    [
                        'source' => [],
                    ],
                    'options' => array('class' => 'form-control')
                ])
            ],
            [
                'attribute' => 'sell_price',
                'label' => 'Selling Price',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'sell_price',
                    'clientOptions' =>
                    [
                        'source' => [],
                    ],
                    'options' => array('class' => 'form-control')
                ])
            ],
            [
                'label' =>  'Vendor Payout',
                'value' =>  function($model){

                $mrp=0;
                $sellingPrice=0;
                $shop_margin=0;
                $pg_fee=0;
                $shipping_charge=0;
                $totalMargin=0;
                $discount=0;
                $total_deductions=0;
                $mrp=$model['base_price'];
                $sellingPrice=$model['sell_price'];
                $shop_margin=$model['shop_margin'];
                $pg_fee=$model['pg_fee'];
                $shipping_charge=$model['shipping_charge'];
                $quantity=1;
                //$serviceTax is stored in configuration table
                $finalResult=Helper::getSellerPayoutDetails(Yii::$app->params['margin_service_tax'],$mrp,$sellingPrice,$shop_margin,$pg_fee,$shipping_charge,$quantity);
                 return Html::a(Yii::t('app', ' {modelClass}', [
                        'modelClass' => $finalResult['vendorPayout'],
                    ]), ['product/view-vendor-payout', 'sellingPrice' => $sellingPrice, 'shop_margin' => $finalResult['shop_margin'], 'payment_gateway_fee' => $finalResult['pg_fee'], 'shipping_cost' => $finalResult['shipping_charge'],'vendor_payout' => $finalResult['vendorPayout'],'total_deductions'=>$finalResult['total_deductions']], ['class' => 'btn btn-primary btn-xs upate-status-btn', 'id' => 'popupModal_' . $model['id_product']]);
             },
             'format' => 'raw'
 
            ],
            
];
if (!empty($sellerDetails) && count($sellerDetails)>0){
       $gridColumns = array_merge_recursive($gridColumns,[[
                'attribute' => 'vendor',
                'label' => 'Seller Name',
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'vendor',
                    'clientOptions' =>
                    [
                        'source' => [],
                    ],
                    'options' => array('class' => 'form-control')
                ])
            ]]);
}
            
$gridExportColumns = [
        
                    [
                        'attribute' => 'active1',
                        'label' => 'Status',
                    ],
                    [
                        'attribute' => 'name',
                        'label' => 'Product Name',
                    ],
                    [
                        'attribute' => 'id_product',
                        'label' => 'Product ID',
                    ],
                    [
                        'attribute' => 'name_category',
                        'label' => 'Category name',
                    ],                 
                    [
                        'attribute' => 'shop_reference',
                        'label' => 'Seller SKU',
                    ],
                    ];
                    if (isset($sellerDetails) && count($sellerDetails)>0){
                        $gridExportColumns = array_merge_recursive($gridExportColumns,[[
                        'attribute' => 'date_upd',
                        'label' => 'Modified Date',
                    ],]);
                    }
                    $gridExportColumns = array_merge($gridExportColumns,[
                    [
                        'attribute' => 'sav_quantity', 
                        'label' => 'Stock',
                    ],
                    [
                        'attribute' => 'base_price',
                        'label' => 'Base Price',
                    ],
                    [
                        'attribute' => 'sell_price',
                        'label' => 'Selling Price',
                    ],
                    [
                        'label' =>  'Vendor Payout',
                        'value' =>  function($model){
                            $mrp=0;
                            $sellingPrice=0;
                            $shop_margin=0;
                            $pg_fee=0;
                            $shipping_charge=0;
                            $totalMargin=0;
                            $discount=0;
                            $mrp=$model['base_price'];
                            $sellingPrice=$model['sell_price'];
                            $shop_margin=$model['shop_margin'];
                            $pg_fee=$model['pg_fee'];
                            $shipping_charge=$model['shipping_charge'];
                            $quantity=1;
                            //$serviceTax is stored in configuration table
                            $finalResult=Helper::getSellerPayoutDetails(Yii::$app->params['margin_service_tax'],$mrp,$sellingPrice,$shop_margin,$pg_fee,$shipping_charge,$quantity);
                            return $finalResult['vendorPayout'];
                        }
                    ]]);
                    if (isset($sellerDetails) && count($sellerDetails)>0){
                        $gridExportColumns = array_merge_recursive($gridExportColumns,[[
                        'attribute' => 'vendor',
                        'label' => 'Seller Name',
                    ]]);
                    }
                    $gridExportColumns = array_merge_recursive($gridExportColumns,[
                        [
                            'attribute' => 'shop_name',
                            'label' => 'Shop Name',
                        ],
                        [
                            'label' => 'Img',
                            'value' =>
                            function ($model) {
                                return Helper::getImagePath($model['id_image'], 'thickbox', 'jpg', 'default');
                            },
                            'format' => 'raw',
                        ],
                    ]);
                    
                    
?>
<div class="user-index">
    <div class="row">
        <?php

         if (isset($sellers) && count($sellers)>0) { ?>
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
    <?php } ?>
    
  <!--  <div class="col-md-2 pdlr5">
        <div class="form-group">
        <?= Html::button('Advanced Search', ['class' => 'btn btn-primary btn-block','id'=>'advancedSearchBtn']) ?>
        </div>
    </div>-->
<?php
    if(Helper::isSeller())
    {
        $divDesign='col-md-12';
    }
    else
    {
        $divDesign='col-md-10';
    }
?>
    <div class="<?=$divDesign?>" style="text-align: right;">
        
        <?php $singleExportForm = ActiveForm::begin(['action' => 'index.php?r=product/export-selected-rows', 'id' => 'singleExport']); ?>    
            <div class="col-md-2 pull-right pdlr5" >
                <div class="form-group">
                    <div class="form-group">
                        <?= Html::input('hidden', 'multipleProductIds', '', ['id' => 'multipleProductIds']) ?>
                                          
                    <?= Html::submitButton('Export Selected Products', ['class' => 'btn btn-primary btn-block']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
    
            <?php
            echo ExportMenu::widget([
                'dataProvider'      => $dataProvider,
                'columns'           => $gridExportColumns   ,
                'filename'          => 'product-export',
                'fontAwesome'       => true,
                'target'            => ExportMenu::TARGET_SELF,
                'dropdownOptions'   => [
                'label'             => 'Export All',
                'class'             => 'btn btn-primary btn-block'
            ],
            'exportConfig'          => [
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_EXCEL => false,
                ExportMenu::FORMAT_PDF => false,
                
            ]
            ]);
            ?>
        </div>

   
    </div>
<?php
  $searchform = ActiveForm::begin(['method' => 'get','action'=>'index.php?r=product/index']); 
    $displayNone='none';
        $from_date_add='';
        $to_date_add='';
        $from_date_upd='';
        $to_date_upd='';
    if(!empty($getParam['filter']))
        $displayNone='block';
        if(!empty($getParam['from_date_add']))
        $from_date_add=$getParam['from_date_add'];
        if(!empty($getParam['to_date_add']))
        $to_date_add=$getParam['to_date_add'];
        if(!empty($getParam['from_date_upd']))
        $from_date_upd=$getParam['from_date_upd'];
        if(!empty($getParam['to_date_upd']))
        $to_date_upd=$getParam['to_date_upd'];
        
        ?>
 <div class="row" id="advancedSearchForm" style="display:<?php echo $displayNone ?>;margin-bottom:4%">
   <div class="col-md-12">
            <span class="col-md-3">
            <label> Search Between(Created Date)</label>
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
                  
        <div class="pdlr5">
            <span class="col-md-3">
            <label> Search Between(Updated Date)</label>
                <?= DateRangePicker::widget([
                'name' => 'from_date_upd',
                'value' => $from_date_upd,
                'nameTo' => 'to_date_upd',
                'valueTo' => $to_date_upd,
                'clientOptions' => [
                'autoclose' => true,
                'format' => 'dd-mm-yyyy'
                ]
                ]);?>
                
            </span>
    </div>
         
<div class="col-md-10"> 
        <input type="hidden" name="filter" value="true">
            
            <span class="col-md-1" style="margin-top:30px; margin-right: 10px;">           
                  <label>SEARCH:</label>                   
          </span>  
            <span class="col-md-2" style="width: 160px;">           
                <div class="form-group" style="margin-top:25px;">
                <select name="search_type" id="search_type" class="form-control">
                    <option value="">Select Type</option>
                    <option value="id_product" <?php if(!empty($getParam['search_type']) && ($getParam['search_type']=="id_product")) {echo 'selected';} ?> >Product Id</option>
                    <option value="name" <?php if(!empty($getParam['search_type']) && ($getParam['search_type']=="name")) {echo 'selected';} ?>>Product Name</option>                   
                </select>
        </div>
                    
          </span>
           <span class="col-md-3">          
                <label>&nbsp</label>
            <input type="text" name="search_box" id="search_box" class="form-control" value="<?php if(!empty($getParam['search_box'])) {echo $getParam['search_box'];} ?>" />               
        </span>
                <span class="col-md-5">
                   <div class="form-group" style="margin-top:25px;">
                <label>&nbsp;Product Status</label>
                        <input type="radio" name="search_radio" <?php if(empty($getParam['search_radio']) || !isset($getParam['search_radio'])) {echo 'checked';} ?> value="" /><label>All</label>
                        <input type="radio" name="search_radio" <?php if(isset($getParam['search_radio']) && $getParam['search_radio']=='active') {echo 'checked';} ?> value="active" /><label>Active</label>
                        <input type="radio" name="search_radio" <?php if(isset($getParam['search_radio']) && $getParam['search_radio']=='inactive') {echo 'checked';} ?> value="inactive" /><label>Inactive</label> 
                   </div>                
        </span>
    </div>
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
                <a href="<?=Yii::$app->params['WEB_URL'] ?>index.php?r=product/index">Clear Search</a>
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
    </div>
    <div class="col-md-10">
            <span class="col-md-4">
                <?= Html::submitButton('Search', ['class' =>'btn btn-primary btn-block']) ?>
            </span>
            <span class="col-md-4">
                <a class="btn btn-warning btn-block" href="<?=Yii::$app->params['WEB_URL'] ?>index.php?r=product/index">Reset</a>
            </span>
                <span class="col-md-4">
                <a data-toggle="modal" data-target="#searchModal" style="display:<?php echo $displayNone; ?>"  class="btn btn-success btn-block" href="<?=Yii::$app->params['WEB_URL'] ?>index.php?r=product/save-search"> Save Searching</a>
                </span>      
    </div>

</div>   

<?php ActiveForm::end();
Modal::begin(['id' => 'searchModal', 
'closeButton'=>[true],
'header'=> 'Enter the name of the search',
'footer'=>''
]
); 
echo yii\base\View::render('save-search', array());

Modal::end();
?>
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
    }else{
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
    <!-- <div class="wrapper1">
    <div class="div1"> </div>
</div>
    <div class="wrapper2">
    <div class="div2"> -->


<?php
        Modal::begin(['id' =>'modal',
        ]);
        Modal::end();
    ?>

    
<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
           'bootstrap'          => true,
       'responsive'         => true,
       'containerOptions'   => ['style'=>'overflow: auto'], // only set when $responsive = false
       'headerRowOptions'   => ['class'=>'kartik-sheet-style'],
       'responsiveWrap'     => false,
        'id'                => 'gridViewId',

//    'perfectScrollbar'=>TRUE
]);
?>
         <!-- </div>
</div> -->

</div>
<style type="text/css">
    .kv-grid-wrapper {
    position: relative;
    overflow: auto;
    height: 500px;
</style>
 
  