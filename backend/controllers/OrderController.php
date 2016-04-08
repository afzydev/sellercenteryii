<?php
namespace backend\controllers;
use Yii;
use backend\models\Order;
use backend\models\SearchOrder;
use backend\models\OrderState;
use backend\models\OrderStateLang;
use backend\models\OrderStateReason;
use backend\models\OrderHistory;
use backend\models\StateMachine;
use backend\models\AssociateSeller;
use backend\models\OrderDetail;
use backend\models\Employee;
use backend\models\Carrier;
use backend\models\SaveSearches;
use backend\models\OrderStatusApiLog;
use yii\web\NotFoundHttpException;
use yii\db\ActiveQuery;
use yii\web\Session;
use common\components\Curl;
use common\components\Export;
use common\controllers\AppController;
use common\components\Helpers as Helper;
use common\components\Configuration;
use common\components\Session as ShopSession;
use common\components\Message;
use yii\helpers\Url;
use common\components\ShopdevConfiguration;
use common\components\Barcode;

class OrderController extends AppController
{
  public $enableCsrfValidation = false;
  public $orderStatus;
  public $employeeIds=0;
  public $sellers;
  public $checkedStatus;
  public $getAllCarrier;
  public $searchData;
  public $sellerInfo;

  protected $shops;
  protected $order;
  protected $orderStatelangModel;
  protected $orderHistoryModel;
  protected $orderDetailModel;
  protected $carrier;
  protected $saveSearches;
  protected $orderStateModel;

  const VIEW_TYPE = 'view';
  const TEMPLATE = 'PDF_TEMPLATE';
  const INVOICE_PREFIX = 'PS_INVOICE_PREFIX_OMS';
  const FILTER_ORDER_FIRST='filter_order_first';

  public function init() {
    parent::init();
    $this->order=new Order;
    $this->orderStatelangModel=new OrderStateLang;      
    $this->orderHistoryModel=new OrderHistory;
    $this->orderDetailModel=new OrderDetail;
    $this->carrier=new Carrier;
    $this->saveSearches=new SaveSearches;
    $this->orderStateModel = new OrderState;
	  Yii::$app->params['ps_configuration']=ShopdevConfiguration::getConfigValue('_NOT_SHOW_SELLER_STATUS_', null, ShopSession::shopSessionId());
    $margin_service_tax_value= ShopdevConfiguration::getConfigValue('_MARGIN_SERVICE_TAX_PRECENTAGE_',null, ShopSession::shopSessionId());
        Yii::$app->params['margin_service_tax']=$margin_service_tax_value['value'];
    $this->order->id_shop = ShopSession::shopSessionId();
    
   // return true;
  }

     /**
     * Function name    : actionIndex
     * Description      : Listing of all the orders.
     * @param           : @int
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 22-12-2015
     * Modified By      : 16-01-2016
     * Modified Date    : 00-00-0000
     */

  public function actionIndex() {

    try{
          $request = Yii::$app->request;
          $getParam=$request->get();
          $searchpage='order';
          /* checking whether the session id is seller*/
          if(Helper::isSeller()){
              $this->employeeIds = AssociateSeller::getAssociatedSeller();
              $this->orderStatelangModel->employeeIds=$this->order->employeeIds=$this->employeeIds;
              //$this->orderStatelangModel->employeeIds=$this->employeeIds;
          }
          /*Fetching All Sellers for Super Admin*/
          $this->sellers = AssociateSeller::getAllSellerList();
          if(!empty($getParam['sellers'])) // Get Sellers Id from URL
          {
              $this->orderStatelangModel->employeeIds=$this->order->employeeIds=$getParam['sellers'];
          }
         

          if(isset($getParam['search-delete']) && !empty($getParam['search-delete'])) // Deleting Search Item
          {
            $this->saveSearches->deleteSearchData($getParam['search-delete']);
          }

          // Searching Starts here
          if($this->saveSearches->getSearchData($searchpage)) /*Saving Search Data*/
          {
            $this->searchData=$this->saveSearches->getSearchData($searchpage);
          }


          if((isset($getParam['search']) && $getParam['search']=="true") || (isset($getParam['filter']) && $getParam['filter']=="true") )
          {

            $this->order->setSearchAttributeValue($getParam); // setting properties of Order models
            if(!empty($getParam['search_status_param'])) /*Filter by multiple order statuses */
            {
                $this->checkedStatus=join(',',$getParam['search_status_param']);
                if(!empty($this->checkedStatus) && !empty($getParam['status']))
                {
                  $this->order->orderStatus=$this->checkedStatus.','.$getParam['status'];
                }
                else
                {
                  $this->order->orderStatus=$this->checkedStatus;                  
                }
            }
          }
          if(isset($getParam['Order']) && is_array($getParam['Order']))
          {
              $this->order = Helper::setModelByParams($this->order, $getParam['Order']);
          }
           if (Helper::isSeller()) {
                $this->sellerInfo = AssociateSeller::getAssociatedSellerList();
            } else {
                $this->sellerInfo = AssociateSeller::getAllSellerList();
            }
          
          $dataProvider=$this->order->search(Yii::$app->request->queryParams); // getting all the orders for listing
          $filterOrderStatuses=$this->orderStatelangModel->getAllOrderStatus(); // populate dropdown all orderStatus in filter 
          $this->getAllCarrier=$this->carrier->getCarrier();
          return $this->render('index', [
            'searchModel'               =>  $this->order,
            'dataProvider'              =>  $dataProvider,
            'shopModel'                 =>  $this->shops,
            'getOrderStatuses'          =>  $this->getAllOrderStatus($this->orderStatus),
            'filterOrderStatuses'       =>  $filterOrderStatuses,
            'sellers'                   =>  $this->sellers,
            'checkedStatus'             =>  $this->checkedStatus,
            'getAllCarrier'             =>  $this->getAllCarrier,
            'searchData'                =>  $this->searchData,
            'getParam'                  =>  $getParam,
            'sellerDetails' => $this->sellerInfo,
          ]);
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

  }

     /**
     * Function name    : getAllOrderStatus
     * Description      : Getting all orders status name and id according to the user logged in.
     * @param           : @int
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 06-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

    public function getAllOrderStatus($order_state_id) {
      try{
          $request = Yii::$app->request;
          $getParam=$request->get();
          $getOrderStatuses=[];
          if(Helper::isSeller()) // if loggedin user is Seller
          {
              $StateMachineModel=new StateMachine; // populate dropdown all orderStatus according to possible state machine while updating status 
              $StateMachineModel->id_order_state=$order_state_id;
              if($StateMachineModel->getOrderStatusAccToStateMachine())
                $getOrderStatuses=$StateMachineModel->getOrderStatusAccToStateMachine();

          }
          else if(!Helper::isSeller()) // if loggedin user is SuperAdmin
          {
            if(!empty($getParam['sellers']))
              $this->orderStatelangModel->employeeIds=$getParam['sellers'];
            
            if($this->orderStatelangModel->getAllOrderStatus())
                $getOrderStatuses=$this->orderStatelangModel->getAllOrderStatus(); // populate dropdown all orderStatus while updating status
          }
          
          if(count($getOrderStatuses)!=0)
          {
            return $getOrderStatuses;
          }
          else
          {
            return false;
          }
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

    }

   /**
     * Function name    : actionFilterstatus
     * Description      : Checking if logged in user is updating orders with same status or not.
     * @param           : @int
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 07-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */


    public function actionFilterstatus() {
    try{      
        $error=true;
        $success=false;
        $message="";
        $data=null;        
         if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $allOrderIds  =  $data['orderIds'];
            $this->order->id_order=$allOrderIds;
            $orderStateId=$this->order->checkOrderStatus();
            //print_r($orderState);die;
            if(!$orderStateId) // checking whether the user is updating same status or not
            {
                $error=true;
                $success=false;
                $message='Please select order of same status';
            }
            else
            {
                $orderStateId=array_unique($orderStateId);
                if(!empty($orderStateId[0]))
                {
                  $orderStateId=$orderStateId[0];
                }
                $getAllOrderStatus=$this->getAllOrderStatus($orderStateId);
                $getAllOrderStatus['current_state']=$orderStateId;
                //print_r($getAllOrderStatus);die;
                $error=false;
                $success=true;
                $message='';
                $data=$getAllOrderStatus;
            }

            return Helper::formatJson($error,$success,$message,$data);

          }
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

    }

  /**
     * Function name    : actionGetorderreason
     * Description      : Getting status change reason according to status selected.
     * @param           : @int
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 07-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

    public function actionGetorderreason() {
      try{
          $error=true;
          $success=false;
          $message="";
          $data=null;        

           if (Yii::$app->request->isAjax) {
              $data = Yii::$app->request->post();
              $OrderStateReason=new OrderStateReason;
              $OrderStateReason->id_order_state=$data['id_order_state'];
              $getAllStateReason=$OrderStateReason->getAllOrderStateReason();
              if(count($getAllStateReason)>0)
              {
                  $error=false;
                  $success=true;
                  $data=$getAllStateReason;       
              }
              else
              {
                $error=true;
                $success=false;
              }

              return Helper::formatJson($error,$success,$message,$data);
          }
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

   }

  /**
     * Function name    : actionChangestatus
     * Description      : This function used to update status of bulk orders.
     * @param           : @int
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 07-01-2016
     * Modified By      : 16-01-2016
     * Modified Date    : 00-00-0000
     */

   public function actionChangestatus() {
    try{
            $error=true;
            $success=false;
            $message="";
            $data=null;
            $id_order_state_reason=0;

             if (Yii::$app->request->isAjax){

              $postData = Yii::$app->request->post();
              $orderStatus=$postData['id_order_state'];
              $orderIds=$postData['orderIds'];
              if(empty($orderStatus)|| empty($orderIds)) {
                  $error=true;
                  $success=false;
                  $message="Please select all the fields required to update the satus";
                  $data=null;
                  return Helper::formatJson($error,$success,$message,$data);
              }

              if(array_key_exists('id_order_state_reason', $postData)) // check whether the reason dropdown exists or not
              {
                $OrderStateReason=new OrderStateReason;
                $OrderStateReason->id_order_state=$orderStatus;
                $getAllStateReason=$OrderStateReason->getAllOrderStateReason();

                $id_order_state_reason=$postData['id_order_state_reason'];
                if(!empty($orderStatus) && count($getAllStateReason)>0 && empty($id_order_state_reason) )
                {
                    $error=true;
                    $success=false;
                    $message="Please select reason also to update the status";
                    $data=null;
                    return Helper::formatJson($error,$success,$message,$data);
                }
              }

              /*checking whether the package slip is downloaded or not*/
              if(Helper::isSeller()){
                if(!$this->order->checkPackageSlipDonwload($orderIds)){
                      $error=true;
                      $success=false;
                      $message="Change statuses for those orders whose package slip is downloaded";
                      $data=null;
                      return Helper::formatJson($error,$success,$message,$data);
                }
              }

              // $this->order->id_order=$orderIds;
              // if(!$this->order->checkOrderStatus()) // checking whether the user is updating same status or not
              // {
              //     $error=true;
              //     $success=false;
              //     $message='Please select order of same status';
              //     $data=null;
              //     return Helper::formatJson($error,$success,$message,$data);
              // }

              $OrderStateModel=new OrderState;
              $OrderStateModel->id_order_state=$orderStatus;
              $sendemail=$OrderStateModel->findSendEmail();

              $explodeOrderIds=explode(',',$orderIds);
              $k=0;
              $apiData=[];
              foreach($explodeOrderIds as $orderId)
              {
                 $k++;
                 $apiData[$k]['id_order']=$orderId;
                 $apiData[$k]['id_order_state']=$orderStatus;
                 $apiData[$k]['id_employee']=Helper::getSessionId();
                 $apiData[$k]['id_reason']=$id_order_state_reason;
                 $apiData[$k]['waybill_number']='';
                 $apiData[$k]['date_add']=date('Y-m-d H:i:s', time());
                 $apiData[$k]['sendemail']=$sendemail->send_email;
              }

              $api_url=Yii::$app->params['API_URL'].'bulk_update_order_status.php?resource=order_histories&action=add'; // update order status api
              $_POST['data'] = json_encode($apiData);
              $curlObject = new Curl;
              $responseResult=$curlObject->executeCurl($api_url,$_POST['data'],'POST');

              $resultData=json_decode($responseResult,true);
            //print_r($responseResult);die;
              if(!empty($resultData)) // if no response coming from API
              {
                if($resultData['status']) // if overall status is true
                {
                    $OrderHistory=new OrderHistory; // maintaining LOG for status update in SHOP OMS DB
                    $OrderHistory->id_order=$orderIds;
                    $OrderHistory->id_order_state=$orderStatus;
                    $OrderHistory->id_employee=Helper::getSessionId();
                    $OrderHistory->id_cancellation_reason=$id_order_state_reason;
                    $OrderHistory->waybill='';
                    $OrderHistory->sendemail=$sendemail->send_email;
                    $OrderHistory->date_add=date('Y-m-d H:i:s', time());
                    if($OrderHistory->updateStatus()) // if order status update log in oms db is inserting updated status or not
                    {
                      $error=false;
                      $success=true;
                      $message="Status Updated Successfully";
                      $data=null;
                    }
                    else
                    {
                      $error=true;
                      $success=false;
                      $message="Invalid Status";
                      $data=null;
                    }

                }
                else
                {
                      $ids='';
                      $finalMesage='Unable to update the order status.Please try again later';
                      if(is_array($resultData))
                      {
                        $resultValues=array_values($resultData);
                        foreach($resultData as $key=>$Value){
                          if(!empty($Value))
                          {
                            if(!$resultData[$key]['status'])
                            {
                              $ids.=','.$key; 
                            }
                          }
                        }
                        $finalMesage='For Order Ids '.substr($ids,1).' '.$resultValues[0]['msg'];
                      }
                      
                      $error=!$resultData['status'];
                      $success=false;
                      $message=$finalMesage;
                      $data=null;
                }
                // Maintaining API hit log
                if($resultData['status'])
                {
                  $overall_return_status="true";
                }
                else
                {
                  $overall_return_status="false";
                }
                OrderStatusApiLog::saveOrderStatusApiLog(Helper::getSessionId(),$orderIds,$orderStatus,$overall_return_status,date("Y-m-d H:i:s"),$resultData);

                return Helper::formatJson($error,$success,$message,$data);
              }
              else
              { 
                OrderStatusApiLog::saveOrderStatusApiLog(Helper::getSessionId(),$orderIds,$orderStatus,$overall_return_status="false",date("Y-m-d H:i:s"),$resultData=null);

                $error=true;
                $success=false;
                $message='Unable to update the order status.Please try again later';
                $data=null;
                return Helper::formatJson($error,$success,$message,$data);
              }
          }
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }
   }

  /**
     * Function name    : actionView
     * Description      : This function used to view order details page.
     * @param           : @int
     * @return          : order-details view page
     * Created By       : Mohd Afzal
     * Created Date     : 13-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

  public function actionView($id) {
    try{
        if(empty($id))
          throw new \yii\web\HttpException(400, 'Wrong method', 405);
        if(Helper::isSeller())
        {
          if(!$this->order->validateOrderId($id))
            return $this->redirect(Yii::$app->params['WEB_URL'].'index.php?r=order/index');
        }
        $this->order->id_order=$id;
        $this->orderDetailModel->id_order=$id;
        $this->orderHistoryModel->id_order=$id;
        $this->order->type=self::VIEW_TYPE;
        $getOrderDetails=$this->order->search(Yii::$app->request->queryParams);
        if($getOrderDetails)
        {
          $this->orderStatus=$getOrderDetails[0]['id_order_state'];
          $orderHistory=$this->orderHistoryModel->getAllOrderHistory();
          $getAllOrderDetail=$this->orderDetailModel->getFullOrderDetails();

         
          
            return $this->render('view', [
              'model'                 =>  $getOrderDetails,
              'getOrderStatuses'      =>  $this->getAllOrderStatus($this->orderStatus),
              'orderHistory'          =>  $orderHistory,
              'getAllOrderDetail'     =>  $getAllOrderDetail,
              'color'                 =>  $this->orderStateModel->getColorById($this->orderStatus),
           ]);
        }
        else
            return $this->redirect(Yii::$app->params['WEB_URL'].'index.php?r=order/index');

      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

   }

  /**
     * Function name    : actionExportSelectedRows
     * Description      : This function used to export selected orders.
     * @param           : none
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 14-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

 
     public function actionExportSelectedRows(){
      try{
          $data = Yii::$app->request->post();
          $sellerDetails = '';
          $multipleOrderIds=$data['multipleOrderIds'];
          if(empty($multipleOrderIds))
          {
            Yii::$app->session->setFlash('error','Please select orders to export');
            return $this->redirect(Yii::$app->params['WEB_URL'].'index.php?r=order/index');
          }        

          $this->order->id_order=$multipleOrderIds;
          $exportedData=$this->order->singleExport();
          if($exportedData)
          {
            $exportColumnsValues=[['Order Date','Order ID','Order Number','Sub-Order Number','Order Status','Product Name','Stock','Total Price','Vendor Payout','Package Slip No ','Ageing(In days)','Waybill Number','Mode of Payment','Courier Name']];

            if (Helper::isSeller()) {
                $sellerDetails = AssociateSeller::getAssociatedSellerList();
            } else {
                $sellerDetails = AssociateSeller::getAllSellerList();
            }
            if (isset($sellerDetails) && count($sellerDetails)>0){
                 $exportColumnsValues[0][] = "Seller Name";
            }
             $header_arr = [
              'Product SKU','Customer Name','Product Price','COD Fee','Shipping Fee','Confirmed By','Mobile Number 1','Mobile Number 2','Customer Address 1','Customer Address 2','State','City','Postcode','Img'
              ];
              foreach ($header_arr as $label) {
                $exportColumnsValues[0][] = $label;
              }
         
              $i=0;
            foreach ($exportedData as $keys=>$value) {
                $newArr = array();
                if (isset($sellerDetails) && count($sellerDetails)>0){
                   foreach ($value as $key => $value1) {
                        if($key == 'id_image'){
                           $i++;
                           $newArr[] = Helper::getImagePath($value1, 'small', 'jpg', 'default');
                        }else{
                          $i++;
                        $newArr[]=$value1;
                      }
                      }
                }
                else{
                    foreach ($value as $key => $value1) {
                      if($key != 'company_name'){
                        if($key == 'id_image'){
                           $i++;
                           $newArr[] = Helper::getImagePath($value1, 'small', 'jpg', 'default');
                        }else{
                          $i++;
                        $newArr[]=$value1;
                      }
                      }

                    }
            }
            $exportColumnsValues[$i]=$newArr;
            $fileName='order_'.date('Y-m-d').'.csv';
            
          }
          Export::exportCsv($exportColumnsValues, $fileName);

        }
          else
          {
            Yii::$app->session->setFlash('error','Error in exporting orders.Try after some time');
            return $this->redirect(Yii::$app->params['WEB_URL'].'index.php?r=order/index');
          }
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

  }

  /**
     * Function name    : actionChangeStatusOnPopup
     * Description      : This function used to change order status on popup.
     * @param           : none
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 16-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

  public function actionChangeStatusOnPopup(){
    try{
        $data=Yii::$app->request->queryParams;
        $id_order_state=$data['id_order_state'];
        $id_order=$data['id_order'];
        $passData=[
              'id_order'              =>  $id_order,
              'getOrderStatuses'      =>  $this->getAllOrderStatus($id_order_state)
        ];
        return $this->renderPartial('single-status-update-popup.php',$passData);
    }
    catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
    }

  }
 /**
     * Function name    : actionViewPaymentDetails
     * Description      : This function used to view payment details on modal popup.
     * @param           : 
     * @return          : @json
     * Created By       : Mohd Afzal
     * Created Date     : 21-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
  public function actionViewPaymentDetails(){
    try{
        $data=Yii::$app->request->queryParams;
        $price='';
        $discount='';
        $product_quantity='';
        $cod='';
        $shipping='';
        $paid='';
        $id_order='';
        $invoice_ref_key='';
        $payment='';

        if(isset($data['price']) && !empty($data['price']))
          $price=$data['price'];
        if(isset($data['discount']) && !empty($data['discount']))
          $discount=$data['discount'];
        if(isset($data['cod']) && !empty($data['cod']))
          $cod=$data['cod'];
        if(isset($data['shipping']) && !empty($data['shipping']))
          $shipping=$data['shipping'];
        if(isset($data['paid']) && !empty($data['paid']))        
          $paid=$data['paid'];
        if(isset($data['id_order']) && !empty($data['id_order']))        
          $id_order=$data['id_order'];
        if(isset($data['invoice_ref_key']) && !empty($data['invoice_ref_key']))        
          $invoice_ref_key=$data['invoice_ref_key'];
        if(isset($data['product_quantity']) && !empty($data['product_quantity']))        
          $product_quantity=$data['product_quantity'];
        if(isset($data['payment']) && !empty($data['payment']))
          $payment=$data['payment'];

        $passData=[
              'price'           =>  $price,
              'discount'        =>  $discount,
              'cod'             =>  $cod,
              'shipping'        =>  $shipping,
              'paid'            =>  $paid,
              'id_order'        =>  $id_order,
              'invoice_ref_key' => $invoice_ref_key,
              'product_quantity' => $product_quantity,
              'payment'         =>  $payment
        ];
        return $this->renderPartial('view-payment-details.php',$passData);

    }
    catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
    }
  }

 /**
     * Function name    : actionViewShippingDetails
     * Description      : This function used to view shipping details on modal popup.
     * @param           : 
     * @return          : @json
     * Created By       : Mohd Afzal
     * Created Date     : 21-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
  public function actionViewShippingDetails(){
    try{
        $data=Yii::$app->request->queryParams;
        $id_order='';
        $delivery_type='';
        $carrier_name='';
        $response_waywill='';

        if(isset($data['id_order']) && !empty($data['id_order']))
          $id_order=$data['id_order'];
        if(isset($data['delivery_type']) && !empty($data['delivery_type']))
          $delivery_type=$data['delivery_type'];
        if(isset($data['carrier_name']) && !empty($data['carrier_name']))
          $carrier_name=$data['carrier_name'];
        if(isset($data['response_waywill']) && !empty($data['response_waywill']))
          $response_waywill=$data['response_waywill'];

        $passData=[
              'id_order'             =>  $id_order,
              'delivery_type'        =>  $delivery_type,
              'carrier_name'         =>  $carrier_name,
              'response_waywill'     =>  $response_waywill
        ];
        return $this->renderPartial('view-shipping-details.php',$passData);

    }
    catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
    }
  }


    /**
     * Function name    : actionSaveSearch
     * Description      : This function used to save the search string.
     * @param           : 
     * @return          : @json
     * Created By       : Mohd Afzal
     * Created Date     : 21-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function actionSaveSearch(){
    try{
          $error=true;
          $success=false;
          $message='';
          $data=null;

          if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $name=$data['name'];
            if(empty($name)) {
                $error=true;
                $success=false;
                $message='Please enter name of the search';
            }
            if(!empty($name))
            {
              $data=$data['query_string'];
              if(isset($data) && !empty($data))
              {
                $serializeQuery=Helper::urlSerialize($data);
                $this->saveSearches->query_string=$serializeQuery;
                $this->saveSearches->name=$name;
                $this->saveSearches->page='order';
                if($this->saveSearches->saveData()){
                  $error=false;
                  $success=true;
                  $message='Searches Saved Successfully';
                }
                else
                {
                  $error=true;
                  $success=false;
                  $message='Present Search already exists.Please save different searching';
                }
              }
              else
              {
                  $error=true;
                  $success=false;
                  $message='Sorry! There are no search to save.';
              }
            }
            return Helper::formatJson($error,$success,$message,$data);
          }
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
  
    }


  /**
     * Function name    : actionDonwloadPackageSlip
     * Description      : This function used to pass data on popup to create all invoice.
     * @param           : @string
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 04-04-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

   public function actionDonwloadPackageSlip(){
        $data=Yii::$app->request->queryParams;
        $countSlip=$this->order->packageSlipCounter();
        $idOrders="";
        $packageslipcreatable=0;
        $orderIdsArr=[];
        if(!empty($countSlip['id_order']))
        {
          $idOrders=$countSlip['id_order'];
          $orderIdsArr=explode(',',$idOrders);
          $packageslipcreatable=count($orderIdsArr);
        }
        
        $configValues = ShopdevConfiguration::getConfigValue('_INVOICE_DOWNLOAD_LIMIT_');
        $invoiceLimit = $configValues['value'];

        $passData=[
              'idOrders'                        =>   $idOrders,
              'packageslipcreatable'            =>   $packageslipcreatable,
              'invoiceLimit'                    =>   $invoiceLimit
        ];
        return $this->renderPartial('create-package-slip.php',$passData);

   } 

  /**
     * Function name    : actionInvoices
     * Description      : This function used to generate the signle and multiple invoices of orders.
     * @param           : @string
     * @return          : none
     * Created By       : Ravi kumar
     * Created Date     : 15-01-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 00-00-0000
     */
    public function actionInvoices() {
      try{
          $type="";
          $data=null;
          $error=true;
          $success=false;
          $message='No data available';
          $orderIds = array();
          $content = '';
          $status='';
          $search='true';
          $sellers='';
          if(!Yii::$app->request->isAjax|| Yii::$app->request->isAjax)
          {
            $data = Yii::$app->request->post();
            $getParam = Yii::$app->request->get();
            if(!empty($getParam['status']))
            {
              $status=$getParam['status'];
            }
            if(!empty($getParam['search']))
            {
              $search=$getParam['search'];
            }
            if(!empty($getParam['sellers']))
            {
              $sellers=$getParam['sellers'];
            }

          }

          if(Yii::$app->request->isAjax && empty($data['invoiceMultipleOrderIds']))/*Checking whether the orderIds are present to download or not on popup*/
          {
              $error=true;
              $success=false;
              $message='There are no orders to process';
              return Helper::formatJson($error,$success,$message,$data);
          }
          else if(!Yii::$app->request->isAjax && empty($data['invoiceMultipleOrderIds']))/*Checking whether the orderIds are present to download or not from INDEX page for superadmin and admin*/
          {
                Yii::$app->session->setFlash('error', 'Please select orders to create invoice');
                return $this->redirect(Yii::$app->params['WEB_URL'] . 'index.php?r=order/index&sellers='.$sellers.'&status='.$status.'&search='.$search);
          }
          else if(Yii::$app->request->isAjax && isset($data['invoiceMultipleOrderIds']) && !empty($data['invoiceMultipleOrderIds']))/*download PDF if everything is right using AJAX*/
          {
              $error=false;
              $success=true;
              $message='';
              return Helper::formatJson($error,$success,$message,$data);
          }

          if(!empty($data['invoiceMultipleOrderIds']))
          {
            $getOrderIds = $data['invoiceMultipleOrderIds'];

            $orderIds = explode(',', $getOrderIds);
            $len = count($orderIds);
            $i = 0;
            $packageSlipName = 'Package Slip';

            foreach ($orderIds as $key => $orderId) {
                if ($i != $len - 1) {
                    $getInvoiceResult = $this->generateInvoices($orderId, true);
                } else {
                    $getInvoiceResult = $this->generateInvoices($orderId, false);
                }
                if($getInvoiceResult['createPdf'])
                {
                  $content .= $getInvoiceResult['setContent'];
                  $packageSlipName = $getInvoiceResult['packageSlipName'];
                  $i++;
                }
            }
            if(!empty($content)){
              $pdf = Helper::downloadPdf($content, $packageSlipName, 'Package Slip');
              //return the pdf output
              $pdf->render();
              return $this->redirect(Yii::$app->params['WEB_URL'] . 'index.php?r=order/index');
            }
            else
            {
                Yii::$app->session->setFlash('error','Unable to download as waybill or package slip number is not generated');
                return $this->redirect(Yii::$app->params['WEB_URL'] . 'index.php?r=order/index&sellers='.$sellers.'&status='.$status.'&search='.$search);
            }
          }
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }


    /**
     * Function name    : actionSingleInvoice
     * Description      : This function used to generate the signle and multiple invoices of orders.
     * @param           : @string
     * @return          : none
     * Created By       : Ravi kumar
     * Created Date     : 15-01-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 00-00-0000
     */
    public function actionSingleInvoice() {
      try{

          $content = '';
          if(Yii::$app->request->isAjax || !Yii::$app->request->isAjax)
          {
              $data = Yii::$app->request->post();
              if(empty($data['single_id_order']))
              {
                $error=true;
                $success=false;
                $message='There are no orders to process';
                return Helper::formatJson($error,$success,$message,$data);
              }
              else if(Yii::$app->request->isAjax)
              {
                $error=false;
                $success=true;
                $message='';
                return Helper::formatJson($error,$success,$message,$data);
              }
          }
          $orderId = $data['single_id_order'];
          $getInvoiceResult = $this->generateInvoices($orderId, true);
          $packageSlipName = 'Package Slip';
          if($getInvoiceResult['createPdf'])
              {
                $content = $getInvoiceResult['setContent'];
                $packageSlipName = $getInvoiceResult['packageSlipName'];
              }
          if(!empty($content)){
            $pdf = Helper::downloadPdf($content, $packageSlipName, 'Package Slip');

            //return the pdf output
            return $pdf->render();
          }
          else
          {
              Yii::$app->session->setFlash('error','Unable to download as waybill or package slip number is not generated');
              return $this->redirect(Yii::$app->params['WEB_URL'] . 'index.php?r=order/index');
          }
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    /**
     * Function name    : generateInvoices
     * Description      : This function used to generate the invoice pdf html.
     * @param           : @int, @bool
     * @return          : @string
     * Created By       : Ravi kumar
     * Created Date     : 12-01-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 00-00-0000
     */
    public function generateInvoices($orderId, $breakPage) {
      try{
          $recordNotFound = 'Package Slip details not found for order id:' . $orderId;
          $setContent = '';
          $orders = $this->order->getOrderInvoiceDetails($orderId);
          $key = self::TEMPLATE;
          $getTemplateName = $this->order->getTemplateForPdf($orders['id_shop'], $key);
          $pdfName = 'Package Slip';
          $setDownloadStatus = '';
          
          if (is_array($orders) && count($orders)) {
              $orderDetails = $this->order->getOrderDetails($orders['id_order'], $orders['order_number']);
              $orderInvoice = $this->order->getOrderInvoice($orders['id_order']);
              $sellerInfo = $this->order->getSellerInfo($orders['id_order']);
              $taxExcludedDisplay = $this->order->taxExculdedDisplay($orders['id_default_group']);
              $logoPath = Yii::$app->params['WEB_URL'].'images/pdf-logo.png';
              $title = $this->getPdfTitle('', $orderInvoice['number']);
              $pdfName = $this->getPdfName('', $orderInvoice['number']);
              
              //check download staus for downloading duplicate package slip for seller only
              if(Helper::isSeller() && !Helper::isAdmin() && !Helper::isSuperAdmin()){

                $getDownloadStatus = $this->checkDownloadStatus($orders);
                $configValues = ShopdevConfiguration::getConfigValue('_INVOICE_DOWNLOAD_LIMIT_');
                $invoiceLimit = $configValues['value'];
                if ($getDownloadStatus >= $invoiceLimit) {
                    Yii::$app->session->set('invoiceDuplError','Packing Slip download limit exceeded');
                    //return $this->redirect(Yii::$app->params['WEB_URL'] . 'index.php?r=order/index');
                    header('Location: index.php?r=order/index');
                }
              }
              $setInvoiceDetails = array(
                  'orders' => $orders,
                  'order_details' => $orderDetails,
                  'order_invoice' => $orderInvoice,
                  'seller_info' => $sellerInfo,
                  'title' => $title,
                  'tax_excluded_display' => $taxExcludedDisplay,
                  'logoPath' => $logoPath,
                  'breakPage' => $breakPage,
                  'duplicate' => $setDownloadStatus
              );
              $setContent = $this->renderPartial($getTemplateName, $setInvoiceDetails);
              $create=true;
          } else {
                
//                    Yii::$app->session->set('invoiceDuplError','You cannot create package slip for order id '.$orders['id_order'].'');
//                    header('Location: index.php?r=order/index');
         
               $setInvoiceDetails = array(
                   'recordNotFound' => $recordNotFound,
                   'breakPage' => $breakPage
               );
               $setContent = $this->renderPartial('_invoiceNotFound', $setInvoiceDetails);
               $create=false;
          }
          return array('setContent' => $setContent, 'packageSlipName' => $pdfName,'createPdf'=>$create);
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

    }

    /**
     * Function name    : checkDownloadStatus
     * Description      : This function used to check the status or downloaded Package slip for seller only.
     * @param           : @Array
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 16-01-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 00-00-0000
     */
    public function checkDownloadStatus($orderInfo = null) {
      try{
          $setDownloadStatus = false;
          // $getDownloadStatusShopDev = $orderInfo['is_invoice_download'];
          
          // if ($getDownloadStatusShopDev) {
          //     $setDownloadStatus = true;
          // } else {
          //     $setDownloadStatus = $this->order->getDownloadStatus($orderInfo['id_order']);
          // }
          $setDownloadStatus = $this->order->getDownloadStatus($orderInfo['id_order'],'update');

          return $setDownloadStatus;
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

    }


    /**
     * Function name    : getPdfTitle
     * Description      : This function used to generate the Invoice number for PDF.
     * @param           : @int, @int, @int;
     * @return          : @string
     * Created By       : Ravi kumar
     * Created Date     : 12-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getPdfTitle($idShopGroup = null, $orderInvoiceNum = null) {
      try{
          $shopId = ShopSession::shopSessionId();
          $key = self::INVOICE_PREFIX;
          $invoiceTitle = 'Packing Slip: ';

          $configVal = ShopdevConfiguration::getConfigValue($key);
          if (is_array($configVal) && count($configVal)) {
              $invoiceTitle = 'Packing Slip: ' . '#' . $configVal['value'] . sprintf('%06d', $orderInvoiceNum);
          }
          return $invoiceTitle;
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

    }


    
    /**
     * Function name    : checkDownloadStatus
     * Description      : This function used to check the status or downloaded Package slip.
     * @param           : @Array
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 16-01-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 00-00-0000
     */
    public function actionCheckDownloadStatusAjax() {
      try{
          Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
          
          if (Yii::$app->request->isAjax) {
              $data = Yii::$app->request->post();
              $orderInfo = $this->order->getDownloadStatasShopdevAjax($data['orderIds']);
              
              if(count($orderInfo) && is_array($orderInfo)){
                  $setDownloadStatus = false;
                  $getDownloadStatusShopDev = $orderInfo['is_invoice_download'];
                  
                  if ($getDownloadStatusShopDev) {
                      $setDownloadStatus = true;
                  } else {
                      $setDownloadStatus = $this->order->getDownloadStatasAjax($data['orderIds']);
                  }

                return Helper::formatJson(['success' => true, 'status' => $setDownloadStatus]);

              }
          }
          return ['error' => true];
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }

    }

    /**
     * Function name    : getPdfName
     * Description      : This function used to generate the name for PDF.
     * @param           : @int, @int;
     * @return          : @string
     * Created By       : Ravi kumar
     * Created Date     : 21-01-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 00-00-0000
     */
    public function getPdfName($idShopGroup = null, $orderInvoiceNum = null) {
      try{
        $shopId = ShopSession::shopSessionId();
        $key = self::INVOICE_PREFIX;
        $invoiceName = 'Package Slip';

        $configVal = ShopdevConfiguration::getConfigValue($key, NULL, NULL);
        if (is_array($configVal) && count($configVal)) {
            $invoiceName = $configVal['value'] . sprintf('%06d', $orderInvoiceNum);
        }
        return $invoiceName;
      }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }      
    }
    /**
     * Function name    : getPdfName
     * Description      : This function used to generate the name for PDF.
     * @param           : @int, @int;
     * @return          : @string
     * Created By       : Mohd Afzal
     * Created Date     : 02-03-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 00-00-0000
     */

    public function actionCreateManifest(){
      try{
          $error=true;
          $success=false;
          $message='';
          $data=null;
          $type='';
          if(!Yii::$app->request->isAjax)
          {
            $request = Yii::$app->request;
            $getParam=$request->get();
            $type=$getParam['type']=='export';
          }

          if (Yii::$app->request->isAjax || !empty($type)) {
              $postData = Yii::$app->request->post();
              $idOrders=0;
              if(!empty($postData['manifestMultipleOrderIds']))
                  $idOrders=$postData['manifestMultipleOrderIds'];

              $returnData=$this->order->createManifest($idOrders,$postData['id_carrier_pop'],$postData['id_seller_pop']);
              if($returnData!="not_ready_to_be_shipped" && !empty($returnData))
              {
                $error=false;
                $success=true;
                $message='';
                $data=null;
                if(!empty($type))
                {
                  if(!empty($postData['id_carrier_pop']))
                    $this->getAllCarrier=$this->carrier->getCarrier($postData['id_carrier_pop']);
                  
                  if(!empty($postData['id_seller_pop']))
                    $this->sellers=Helper::getSellerInfo($postData['id_seller_pop']);

                   $setData=[
                    'setData' =>$returnData,
                    'carrier' =>$this->getAllCarrier,
                    'sellerInfo'=>$this->sellers
                  ];
                  $setContent = $this->renderPartial('_manifest_creation', $setData);
                  $pdf = Helper::downloadPdf($setContent, 'manifest_'.date("Y-m-d").'', 'Manifest Sheet');
                  return $pdf->render();
                }
                //$this->exportmanifestPdf($setData);
              }
              else if($returnData=="not_ready_to_be_shipped")
              {
                $error=true;
                $success=false;
                $message='Please select orders that are READY TO BE SHIPPED only to create manifest';
                $data=null;
                // Yii::$app->session->setFlash('error', 'Please select orders that are READY TO SHIPPED only to create manifest');
                // return $this->redirect(Yii::$app->params['WEB_URL'] . 'index.php?r=order/index');
              }
              else 
              {
                $error=true;
                $success=false;
                $messageVar='and seller';
                $message='There are no orders for a selected carrier '.$messageVar.' to create manifest';
                $data=null;
              }

              return Helper::formatJson($error,$success,$message,$data);
            }
          }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }
    }

    /**
     * Function name    : getPdfName
     * Description      : This function used to generate the name for PDF.
     * @param           : @int, @int;
     * @return          : @string
     * Created By       : Mohd Afzal
     * Created Date     : 08-03-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 00-00-0000
     */

  public function actionCreatePicklist(){
      try{
          $error=true;
          $success=false;
          $message='';
          $data=null;
          $type='';
          if(!Yii::$app->request->isAjax)
          {
            $request = Yii::$app->request;
            $getParam=$request->get();
            $type=$getParam['type']=='export';
          }

          if (Yii::$app->request->isAjax || !empty($type)) {
              $postData = Yii::$app->request->post();
              $idOrders=0;
              if(!empty($postData['picklistMultipleOrderIds']))
                  $idOrders=$postData['picklistMultipleOrderIds'];

              $returnData=$this->order->createPicklist($idOrders,$postData['id_carrier_picklist'],$postData['id_seller_picklist']);
              if($returnData!="not_orders_confirmed" && !empty($returnData))
              {
                $error=false;
                $success=true;
                $message='';
                $data=null;
                if(!empty($type))
                {
                  if(!empty($postData['id_carrier_picklist']))
                    $this->getAllCarrier=$this->carrier->getCarrier($postData['id_carrier_picklist']);
                  
                  if(!empty($postData['id_seller_picklist']))
                    $this->sellers=Helper::getSellerInfo($postData['id_seller_picklist']);

                   $setData=[
                    'setData' =>$returnData,
                    'carrier' =>$this->getAllCarrier,
                    'sellerInfo'=>$this->sellers
                  ];
                  $setContent = $this->renderPartial('_picklist_creation', $setData);
                  $pdf = Helper::downloadPdf($setContent, 'picklist_'.date("Y-m-d").'', 'Picklist Sheet');
                  return $pdf->render();
                }
                //$this->exportmanifestPdf($setData);
              }
              else if($returnData=="not_orders_confirmed")
              {
                $error=true;
                $success=false;
                $message='Please select orders that are ORDERS CONFIRMED only to create picklist';
                $data=null;
                // Yii::$app->session->setFlash('error', 'Please select orders that are READY TO SHIPPED only to create manifest');
                // return $this->redirect(Yii::$app->params['WEB_URL'] . 'index.php?r=order/index');
              }
              else 
              {
                $error=true;
                $success=false;
                $messageVar='and seller';
                $message='There are no orders for a selected carrier '.$messageVar.' to create picklist';
                $data=null;
              }

              return Helper::formatJson($error,$success,$message,$data);
            }
          }
      catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }
    }

    /**
     * Function name    : actionSaveSearch
     * Description      : This function used to create product picklist on orders listing page.
     * @param           : 
     * @return          : Export PDF
     * Created By       : Mohd Afzal
     * Created Date     : 04-04-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

   public function actionGenerateProductPicklist(){
      $type="";
      $data=null;
      $error=true;
      $success=false;
      $message='No data available';
      if(!Yii::$app->request->isAjax)
      {
        $request = Yii::$app->request;
        $getParam=$request->get();
        $type=$getParam['type'];
      }
      if(Yii::$app->request->isAjax || !empty($type))
      {
        $data = Yii::$app->request->post();
        $id_seller=$data['id_seller_product_picklist'];
        $productData=$this->order->getProductPicklist($id_seller);

        if($type=="export")
        {
          if(Helper::isSeller()) {
            $this->sellers=Helper::getSellerInfo(Helper::getSessionId());
          }
          else {
            $this->sellers=Helper::getSellerInfo($data['id_seller_product_picklist']);
          }
          $passData=[
          'productData' =>  $productData,
          'sellerInfo'     =>  $this->sellers
          ];
          $setContent = $this->renderPartial('_product_picklist_creation', $passData);
          $pdf = Helper::downloadPdf($setContent, 'product_picklist_'.date("Y-m-d").'', 'Product Picklist Sheet');
          return $pdf->render();
        }
        if($productData)
        {
          $data=null;
          $error=false;
          $success=true;
          $message='';
        }
        return Helper::formatJson($error,$success,$message,$data);

      }
   }


}

?>