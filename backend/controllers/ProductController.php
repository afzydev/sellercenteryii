<?php

namespace backend\controllers;

use Yii;
use backend\models\Product;
use common\controllers\AppController;
use common\components\Session as ShopSession;
use common\components\Helpers as Helper;
use backend\models\AssociateSeller;
use backend\models\SaveSearches;
use common\components\Curl;
use backend\models\StockLog;
use backend\models\PriceLog;
use backend\models\StatusLog;
use common\components\ShopdevConfiguration;
use common\components\Export;

/**
 * Class name    : ProductController
 * Description      : Listing of all the Products.
 * Created By       : Preet Saxena
 * Created Date     : 08-02-2015
 * Modified By      : 00-00-0000
 * Modified Date    : 00-00-0000
 */
class ProductController extends AppController {

    protected $product;
    protected $productCategoryModel;
    protected $saveSearches;
    protected $searchData;
    protected $basePrice;
    protected $sellPrice;
    public $sellerInfo;

    public $sellers;

    public function init() {
        parent::init();
        $this->saveSearches = new SaveSearches;
        $this->product = new Product;
        $margin_service_tax_value= ShopdevConfiguration::getConfigValue('_MARGIN_SERVICE_TAX_PRECENTAGE_',null, ShopSession::shopSessionId());
        Yii::$app->params['margin_service_tax']=$margin_service_tax_value['value'];
        //$this->product->id_shop = ShopSession::shopSessionId();
        // return true;
    }

    /**
     * Function name    : actionIndex
     * Description      : Listing of all the products.
     * @param           : none
     * @return          : none
     * Created By       : Preet Saxena
     * Created Date     : 08-02-2015
     * Modified By      : 00-00-0000
     * Modified Date    : 00-00-0000
     */
    public function actionIndex() {

        try {

            $request = Yii::$app->request;
            $getParam = $request->get();
            $searchpage = 'product';
            if(isset($getParam['search-delete']) && !empty($getParam['search-delete'])) // Deleting Search Item
            {
                $this->saveSearches->deleteSearchData($getParam['search-delete']);
            }
            if ($this->saveSearches->getSearchData($searchpage)) {
                $this->searchData = $this->saveSearches->getSearchData($searchpage);
            }
            if ((isset($getParam['search']) && $getParam['search'] == "true") || (isset($getParam['filter']))) {
                $this->product->setSearchAttributeValue($getParam); // setting properties of Product models
            }
            if (isset($getParam['Product']) && is_array($getParam['Product'])) {
                $this->product = Helper::setModelByParams($this->product, $getParam['Product']);
            }
            if (Helper::isSeller()) {
                $this->product->employeeIds = AssociateSeller::getAssociatedSeller();
                $this->sellerInfo = AssociateSeller::getAssociatedSellerList();
                
            } else {
                $this->sellerInfo = AssociateSeller::getAllSellerList();
            }
            if(!empty($getParam['sellers']))
            {
                $this->product->employeeIds = $getParam['sellers'];
            }
            
            $this->sellers = AssociateSeller::getAllSellerList();
            
            // // getting all the Products for listing by 
            $dataProvider = $this->product->search($getParam);
            $l3cats = $this->product->getL3category();

            return $this->render('index', [
                        'searchModel' => $this->product,
                        'dataProvider' => $dataProvider,
                        'l3cats' => $l3cats,
                        'getParam' => $getParam,
                        'searchData' => $this->searchData,
                        'sellers'   => $this->sellers,
                        'sellerDetails' => $this->sellerInfo,
            ]);
        } catch (Exception $e) {
            CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    public function actionSaveSearch() {
        try {
            $error = true;
            $success = false;
            $message = '';
            $data = null;

            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();
                $name = $data['name'];
                if (empty($name)) {
                    $error = true;
                    $success = false;
                    $message = 'Please enter name of the search';
                }
                if (!empty($name)) {
                    $data = $data['query_string'];
                    if (isset($data) && !empty($data)) {
                        $serializeQuery = Helper::urlSerialize($data);
                        $this->saveSearches->query_string = $serializeQuery;
                        $this->saveSearches->name = $name;
                        $this->saveSearches->page = 'product';
                        if ($this->saveSearches->saveData()) {
                            $error = false;
                            $success = true;
                            $message = 'Searches Saved Successfully';
                        } else {
                            $error = true;
                            $success = false;
                            $message = 'Search name already exists.Please enter different name';
                        }
                    } else {
                        $error = true;
                        $success = false;
                        $message = 'Sorry! There are no search to save.';
                    }
                }
                return Helper::formatJson($error, $success, $message, $data);
            }
        } catch (Exception $e) {
            CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    /* Function Name : To Update Price 
     * Author   Name : Liyakat Ali
     * Date          : 15-02-2016
     */

    public function actionUpdatePrice() {
        try {
            $error = true;
            $success = false;
            $message = '';
            $data = null;
            $shop_id=1;
            $id_employee=Helper::getSessionId();
        
            if(ShopSession::shopSessionId())
                $shop_id =ShopSession::shopSessionId();

            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();
                                
                $mrp_price = $data['mrp_price'];
                $current_selling_price =$data['current_selling_price'];         

                $sell_price =$data['selling_price'];         
                $id_product = $data['id_product'];

                $thirty_percent = $current_selling_price * .3;
                $lowest_val = $current_selling_price - $thirty_percent;
                $highest_val = $current_selling_price + $thirty_percent;
                if($highest_val > $mrp_price){
                    $highest_val = $mrp_price;
                }
                
                if (empty($mrp_price) || empty($id_product) || empty($sell_price)) {
                    $error = true;
                    $success = false;
                    if(empty($price) || empty($sell_price))
                    {
                         
                        $message = 'Please enter a selling price between Rs.'.$lowest_val.' and Rs.'.$highest_val.'';

                    }
                    else 
                    {
                        $message ='Produdt Id Missing';
                    }
                }
                else 
                {
                    if (preg_match('/[\'^£$%&!*()}{@#~?><>,|=_+¬-]/', $sell_price) || preg_match('/[\'^£$%&!*()}{@#~?><>,|=_+¬-]/', $mrp_price))
                    {
                        $message = ' Please enter the integer value';
                        $error = true;
                        $success = false;
                        return Helper::formatJson($error, $success, $message, $data);
                    }
                    if(!empty($sell_price) && !is_numeric($mrp_price))
                    {
                         
                        $message = 'Please enter the integer value';
                        $error = true;
                        $success = false;
                        return Helper::formatJson($error, $success, $message, $data);

                    }



                    $checkSellingPriceEnter=(30 / 100) * $current_selling_price;
                    $lesserValue=$current_selling_price-$checkSellingPriceEnter;
                    $greaterValue=$current_selling_price+$checkSellingPriceEnter;
                    if($sell_price < $lesserValue || $sell_price > $greaterValue)
                    {
                        $error = true;
                        $success = false;
                        $message ='Please enter a selling price between Rs.'.$lowest_val.' and Rs.'.$highest_val.'';
                        return Helper::formatJson($error, $success, $message, $data);
                    }


                    if(!empty($mrp_price) && is_numeric($mrp_price) && !empty($sell_price) && is_numeric($sell_price) && $mrp_price< $sell_price)
                    {
                         
                        $message = 'MRP should be greater than the selling price and selling price should be between Rs.'.$lesserValue.' and Rs.'.$greaterValue.'';
                        $error = true;
                        $success = false;
                        return Helper::formatJson($error, $success, $message, $data);

                    }
                    if(!Helper::isSeller())
                    {
                        $id_employee=$data['id_employee'];
                    }

                    $result[0]['id_product'] = $id_product;
                    $result[0]['price'] = $sell_price;
                    $result[0]['id_employee'] = $id_employee;
                    $result[0]['id_shop'] = $shop_id;
                    $result[0]['mrp'] = $mrp_price;
                    //print_r($result);die;             
                    $api_url =Yii::$app->params['API_URL'].'update_product_price.php?resource=products&action=modify';                  

                    $_POST['data'] = json_encode($result);
                    //print_r($_POST['data'] );die();
                    $curlObject = new Curl;
                    $responseResult=$curlObject->executeCurl($api_url,$_POST['data'],'POST');

                    $resultData=json_decode($responseResult,true);
                    //print_r($responseResult);die();
                    if($resultData['status'])
                    {   
                        $PriceLog=new PriceLog;
                        $PriceLog->basePrice =$mrp_price;
                        $PriceLog->sellPrice =$sell_price;
                        $PriceLog->id_product =$id_product;
                        $PriceLog->id_employee=Helper::getSessionId();
                        $PriceLog->id_shop=$shop_id;              
                        $PriceLog->date_add=date('Y-m-d H:i:s', time());
                        
                        if($PriceLog->updatePrice())
                        {
                          $error=false;
                          $success=true;
                          $message="Price Updated Successfully";
                          $data=null;
                        }
                        else
                        {
                          $error=true;
                          $success=false;
                          $message="Invalid Price";
                          $data=null;
                        }

                    }
                    else
                    {  
                          $error=!$resultData['status'];
                          $success=false;
                          $message='Error Updating Price.Please check inputs and try again';
                          $data=null;
                    }
                    
                }
                return Helper::formatJson($error, $success, $message, $data);
            }
        } catch (Exception $e) {
            CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    /* Function Name : To Update Stock 
     * Author   Name : Liyakat Ali
     * Date          : 15-02-2016
     */

    public function actionUpdateStock() {
        
        try {
            $error = true;
            $success = false;
            $message = '';
            $data = null;
            $shop_id=1;
           
   
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();
                if(isset($data['outofstock']))
                {
                    $quantity = $data['outofstock'];
                }
                else
                {
                    $quantity = $data['quantity'];
                }
                $id_product = $data['id_product'];

                if ((empty($quantity) && !isset($data['outofstock'])) || (empty($id_product) && !isset($data['outofstock']))) {
                    $error = true;
                    $success = false;
                    if(empty($quantity))
                    {
                        $message = 'Please enter Quantity';
                    }
                    else
                    {
                        $message ='Product Id Missing';
                    }
                }
                else 
                {
                    if (preg_match('/[\'^£$%&!*()}{@#~?><>,|=_+¬-]/', $quantity))
                    {
                        $message = ' Please enter the integer value';
                        $error = true;
                        $success = false;
                        return Helper::formatJson($error, $success, $message, $data);
                    }

                     if(ShopSession::shopSessionId())
                        $shop_id =ShopSession::shopSessionId();

                    $result[0]['id_product'] = $id_product;
                    $result[0]['quantity'] = $quantity;
                    $result[0]['id_employee'] = Helper::getSessionId();;
                    $result[0]['id_shop'] = $shop_id;
                    
                    $api_url =Yii::$app->params['API_URL'].'update_product_quantity.php?resource=stock_availables&action=modify';                  
                    
                    $_POST['data'] = json_encode($result);
                     //print_r($_POST['data']);die();
                    $curlObject = new Curl;
                    $responseResult=$curlObject->executeCurl($api_url,$_POST['data'],'POST');
                    
                    $resultData=json_decode($responseResult,true);
                    //print_r($resultData);die();
                   
                    if($resultData['status'])
                    {   
                        $StockLog=new StockLog;
                        $StockLog->quantity =$quantity;
                        $StockLog->id_product =$id_product;
                        $StockLog->id_employee=Helper::getSessionId();
                        $StockLog->id_shop=$shop_id;              
                        $StockLog->date_generated=date('Y-m-d H:i:s', time());
                        if($StockLog->updateStock())
                        {
                          $error=false;
                          $success=true;
                          $message="Stock Updated Successfully";
                          $data=null;
                        }
                        else
                        {
                          $error=true;
                          $success=false;
                          $message="Invalid Stock";
                          $data=null;
                        }

                    }
                    else
                    {  
                          $error=!$resultData['status'];
                          $success=false;
                          $message='Error Updating Stock.Please try again later';
                          $data=null;
                    }
                    
                }
                return Helper::formatJson($error, $success, $message, $data);
            }
        } catch (Exception $e) {
            CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    /* Function Name : To Update Stock 
     * Author   Name : Liyakat Ali
     * Date          : 15-02-2016
     */

    public function actionUpdateProductStatus() {
        
        try {
            $error = true;
            $success = false;
            $message = '';
            $data = null;
            $id_employee=Helper::getSessionId();
            $shop_id=1;

            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();
                $status=$data['status'];
                $id_product=$data['id_product'];
                if (!isset($status) || empty($id_product)) {
                    $error = true;
                    $success = false;
                    $message = 'Please select status';
                }
                else 
                {
                     if(ShopSession::shopSessionId())
                        $shop_id =ShopSession::shopSessionId();

                    if($status==1)
                    {
                        $updateStatus=0;
                    }
                    else
                    {
                        $updateStatus=1;
                    }

                    if(!Helper::isSeller())
                    {
                        $id_employee=$data['id_employee'];
                    }

                   // echo $updateStatus;die;
                    $result[0]['id_product']    = $id_product;
                    $result[0]['active']        = $updateStatus;
                    $result[0]['id_employee']   = $id_employee;
                    $result[0]['id_shop']       = $shop_id;
                    
                    $api_url =Yii::$app->params['API_URL'].'update_product_status.php?resource=products&action=modify';                  
                    
                    $_POST['data'] = json_encode($result);
                     //print_r($_POST['data']);die();
                    $curlObject = new Curl;
                    $responseResult=$curlObject->executeCurl($api_url,$_POST['data'],'POST');
                    //print_r($responseResult);die;
                    $resultData=json_decode($responseResult,true);
                   
                    if($resultData['status'])
                    {   
                        $StatusLog=new StatusLog;
                        $StatusLog->id_employee=Helper::getSessionId();
                        $StatusLog->id_shop=$shop_id;  
                        $StatusLog->id_product=$id_product;  
                        $StatusLog->status=$updateStatus;
                        $StatusLog->date_add=date('Y-m-d H:i:s', time());  
                        if($StatusLog->updateStatus())
                        {
                          $error=false;
                          $success=true;
                          $message="Stock Updated Successfully";
                          $data=null;
                        }
                        else
                        {
                          $error=true;
                          $success=false;
                          $message="Invalid Stock";
                          $data=null;
                        }

                    }
                    else
                    {  
                          $error=!$resultData['status'];
                          $success=false;
                          $message='Error Updating Status.Please try again later';
                          $data=null;
                    }
                    
                }
                return Helper::formatJson($error, $success, $message, $data);
            }
        } catch (Exception $e) {
            CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }


    public function actionView() {
        try {
            $request = Yii::$app->request;
            $getParam = $request->get();
            $getParam ['view'] = TRUE;
            $viewLog=0;
            $viewPriceLog=0;
            $productFeatureDetails = array();
            $productImages=[];

            if(empty($getParam['id']))
                return $this->redirect(Yii::$app->params['WEB_URL'].'index.php?r=product/index');


            $productDetails = $this->product->productDetails($getParam['id']);// get product data
            //print_r($productDetails);die;
            $productImages=$this->product->getProductImages($getParam['id']);// get product images
            $productFeatureData = $this->product->getProductFeature($getParam);// get product features
            for ($i = 0; $i < count($productFeatureData);$i++)
            { 
                $productFeatureDetails[$productFeatureData[$i]['name']] = $productFeatureData[$i]['values1'];
            }   
                        
            $viewStockLog=new StockLog();
            if($viewStockLog->getStockLog($getParam['id']))
                $viewLog=$viewStockLog->getStockLog($getParam['id']);
            
            $priceLog=new PriceLog;
            if($priceLog->getPriceLog($getParam['id']))
                $viewPriceLog=$priceLog->getPriceLog($getParam['id']);

            if(!$productDetails)
            {
               return $this->redirect(Yii::$app->params['WEB_URL'].'index.php?r=product/index');
            }
            

            return $this->render('view', [
                    'model' => $productDetails,
                    'viewLog'=>$viewLog,
                    'viewPriceLog'=>$viewPriceLog,
                    'productFeatureDetails' => $productFeatureDetails,
                    'productImages'     =>  $productImages
                ]);
            
        } catch (Exception $e) {
            CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }


    /**
     * Function name    : actionViewVendorPayout
     * Description      : This function used to view Vendor payout.
     * @param           : 
     * @return          : @json
     * Created By       : Preet Saxena
     * Created Date     : 12-03-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
  public function actionViewVendorPayout(){
    try{
        $data=Yii::$app->request->queryParams;
        $sellingPrice='';
        $shop_margin='';
        $payment_gateway_fee ='';
        $shipping_cost='';
        $vendor_payout = '';
        $sellingPrice = $data['sellingPrice'];
        $shop_margin = $data['shop_margin'] ;

        $payment_gateway_fee = $data['payment_gateway_fee'];
        $shipping_cost = $data['shipping_cost'];
        $vendor_payout = $data['vendor_payout'];
        $total_deductions=$data['total_deductions'];

        //print_r()
        $passData=[
              'sellingPrice'            =>  $sellingPrice,
              'shop_margin'             =>  number_format($shop_margin,2),
              'payment_gateway_fee'     =>  number_format($payment_gateway_fee,2),
              'shipping_cost'           =>  number_format($shipping_cost,2),
              'vendor_payout'           =>  $vendor_payout,
              'total_deductions'        =>  $total_deductions
        ];
        return $this->renderPartial('view-vendor-payout.php',$passData);

    }
    catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
    }
  }


  /**
     * Function name    : actionExportSelectedRows
     * Description      : This function used to export selected Products.
     * @param           : none
     * @return          : none
     * Created By       : Preet Saxena
     * Created Date     : 30-03-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

 
     public function actionExportSelectedRows(){
      try{
         $sellerInfo = AssociateSeller::getAssociatedSellerList();

            //echo "<pre>";print_r($sellerInfo);die;    
          $data = Yii::$app->request->post();
          $multipleProductIds=$data['multipleProductIds'];
          if(empty($multipleProductIds))
            {
                Yii::$app->session->setFlash('error','Please select product to export');
                return $this->redirect(Yii::$app->params['WEB_URL'].'index.php?r=product/index');
            }

            $this->product->id_product=$multipleProductIds;
            $exportedData=$this->product->singleExport();
            $exportedDataRTrim = array();
            $i = 0;
            foreach ($exportedData as $key => $model) {
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
                $serviceTax = Yii::$app->params['margin_service_tax'];

                /*for shop margin*/
                $a = ($sellingPrice * $shop_margin) / 100 ;
                $A = $a + ( $a * $serviceTax / 100 );
                /*for payment gateway fee*/        
                $b = ($sellingPrice * $pg_fee) / 100 ;
                $B = $b + ( $b * $serviceTax / 100 );
                /*for shipping cost*/
                $c = $shipping_charge * $quantity;
                $C = $c + ( $c * $serviceTax / 100 );
                /*shop total margin*/
                $totalMargin = $A + $B + $C;
                /*total vendor payout*/
                $vendorPayout = $sellingPrice - $totalMargin;
                $vendor_payout=number_format($vendorPayout,2);
                $exportedData[$i]['vendor_payout'] = $vendor_payout;

                $imgPath = Helper::getImagePath($model['id_image'], 'thickbox', 'jpg', 'default');
                $exportedData[$i]['id_image'] = $imgPath;
                $i++;
            }
            $j=0;
            foreach ($exportedData as $key => $value) {
                $i=0;
                foreach ($value as $name => $data) {
                    if (isset($sellerInfo) && count($sellerInfo)>0)
                    {   
                        if($i <= 12)
                        {
                            $exportedDataRTrim[$j][$i] = $data;
                            $i++;  
                        }
                        
                    }
                    else
                    {
                     if( $name != 'vendor' || $name != 'date_upd'){
                            if($i <= 10)
                            {
                                $exportedDataRTrim[$j][$i] = $data;
                                $i++;  
                            }else{
                                if($i == 12)
                                {
                                    $exportedDataRTrim[$j][10] = $data;
                                }
                                $i++;
                            }
                        }   
                    }   
                }
                $j++;
            }
            if($exportedDataRTrim){
                if (isset($sellerInfo) && count($sellerInfo)>0){
                    $exportColumnsValues=[['Status','Product Name','Product ID','Category name','Seller SKU','Modified Date','Stock','Base Price','Selling Price','Vendor Payout','Seller Name','Shop Name','Img']];
                }
                else{
                    $exportColumnsValues=[['Status','Product Name','Product ID','Category name','Seller SKU','Stock','Base Price','Selling Price','Vendor Payout','Shop Name','Img']];
                }
        $exportColumnsValues = array_merge($exportColumnsValues,$exportedDataRTrim);
        
        $fileName='order_'.date('Y-m-d').'.csv'; 
       // echo "<hr>";print_r($exportColumnsValues);die;
        Export::exportCsv($exportColumnsValues, $fileName);
      }
          else
          {
            Yii::$app->session->setFlash('error','Error in exporting orders.Try after some time');
            return $this->redirect(Yii::$app->params['WEB_URL'].'index.php?r=product/index');
          } 
          
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

  }

}
