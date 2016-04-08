<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use backend\models\Order;
use backend\models\Product;
use yii\filters\VerbFilter;
use common\controllers\AppController;
use common\components\ShopdevConfiguration;
use backend\models\AssociateSeller;
use common\components\Helpers as Helper;
use common\components\Session as ShopSession;

/**
 * Site controller
 */
class SiteController extends AppController {

    public $setConfigValue;

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'update','filtersalesorders'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function init(){

        $key = array('PS_OS_READYTOSHIPPED', 'PS_OS_ORDERCONFIRMATION',  'PS_OS_RETURNED','RECEIVED_AT_COURIER_HUB','PS_SHIPPED','PS_OS_RTO_INITIATED_DELIVERED');
        $configValues = ShopdevConfiguration::getConfigValue($key);
        foreach($configValues as $configValue){
            $this->setConfigValue[$configValue['name']] = $configValue['value'];
        }
    }

    public function actionIndex() {
        $data = array();
        $dateFrom = date('Y-m-d');
        $dateTo = '';

        $model = new Order();
        $productModel = new Product();

        if (Helper::isSeller()) {
            $model->employeeIds =$productModel->employeeIds= AssociateSeller::getAssociatedSeller();
        }
        $shopId = ShopSession::shopSessionId();
        $model->id_shop=$shopId;
        $data['ordersInfo'] = $model->getOrdersInfo();
        $data['order'] = $model->getOrder();
        $data['sale'] = $model->getSale();
        
        $data['avgCartValue'] = $model->getAvgCartValue();
        $data['productInfo'] = $productModel->getProductInfo();
        $data['bestSellingProduct'] = $productModel->getBestSellingProduct();
        return $this->render('index', [
                    'data' => $data,
                    'searchModel' => $model,
                    'orderState' => $this->setConfigValue
        ]);
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    public function actionUpdate()
    {
        $model = new Order();
        $productModel = new Product();
        $error=true;
        $success=false;
        $message="";
        $data=null;        
        if (Yii::$app->request->isAjax) {
           $data = Yii::$app->request->post();
           $updateSection =  $data['panelId'];
           
            if (Helper::isSeller()) {
                $model->employeeIds = $productModel->employeeIds = AssociateSeller::getAssociatedSeller();
            }

            $shopId = ShopSession::shopSessionId();
            $model->id_shop=$shopId;

            switch($updateSection){
                case '_orders':
                    $data['ordersInfo'] = $model->getOrdersInfo();
                    break;
                case '_stockReport':
                    $data['productInfo'] = $productModel->getProductInfo();
                    break;
                case '_bestSellingProduct':
                    $data['bestSellingProduct'] = $productModel->getBestSellingProduct();
                    break;
            }

            $data = $this->renderPartial($updateSection, ['data' => $data,'orderState'=>$this->setConfigValue], false, true);
            return Helper::formatJson($error=0,$success=0,$message=0,$data);
        }
    }

    public function actionFiltersalesorders(){
        $model = new Order();
        $productModel = new Product();

        $error=true;
        $success=false;
        $message="";
        $data=null;
        $ordersDate=[];
        $orderTotal=[];
        $salesDate=[];
        $saleTotal=[];
        
        if (Helper::isSeller()) {
            $model->employeeIds = $productModel->employeeIds = AssociateSeller::getAssociatedSeller();
        }
        $shopId = ShopSession::shopSessionId();
        $model->id_shop=$shopId;

        if (Yii::$app->request->isAjax) {
        $data = Yii::$app->request->post();
        $from_date_add=$data['Order']['from_date_add'];
        $to_date_add=$data['Order']['to_date_add'];
        if(empty($from_date_add)|| empty($to_date_add) )
        {
            $error=true;
            $success=false;
            $message="Please enter both the date";
        }
        else
        {
            $model->from_date_add=$from_date_add;
            $model->to_date_add=$to_date_add;
            $data['order'] = $model->getOrder();
            $data['sale'] = $model->getSale();
            $data['avgCartValue'] = $model->getAvgCartValue();

            if(isset($data['order']['ordersDate'])) 
            { 
                $ordersDate = $data['order']['ordersDate']; 
                $orderTotal=$data['order']['orderTotal'];
            }
            if(isset($data['sale']['salesDate'])) 
            { 
                $salesDate = $data['sale']['salesDate']; 
                $saleTotal=$data['sale']['saleTotal'];
            }
            $pageData['countOrdersAndMap'] = $this->renderPartial('_salesOrdersView', ['data' => $data], false, true);

            $data['bestSellingProduct'] = $productModel->getBestSellingProduct();
            $pageData['bestSellingProduct'] = $this->renderPartial('_bestSellingProduct', ['data' => $data], false, true);

            $finalUpdatedPageData=$pageData;

            $error=false;
            $success=true;
            $message="";
           // print_r($data);die;
            $pdata = ['orderdate'=>$ordersDate,'ordertotal'=>$orderTotal,'salesdate'=>$salesDate,'saleTotal'=>$saleTotal,'pageData'=>$finalUpdatedPageData];
           // print_r($pdata);exit;
            return Helper::formatJson($error,$success,$message,$pdata);

        }
       }
    }
}
