<?php

namespace backend\controllers;

use Yii;
use backend\models\Order;
use common\controllers\AppController;
use common\components\Helpers as Helper;
use backend\models\AssociateSeller;
use common\components\Session as ShopSession;

class ReportController extends AppController {

    public $enableCsrfValidation = false;
    public $shopId;
    public $order;
    public $employeeIds;
    public $sellerInfo;

    public function beforeAction($action) {
        $this->shopId = ShopSession::shopSessionId();
        return true;
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
        
    }

    /**
     * Function name    : actionAgingReport
     * Description      : Listing of all the Aging details as per seller.
     * @param           : @int
     * @return          : none
     * Created By       : Mohd Afzal
     * Created Date     : 22-12-2015
     * Modified By      : 16-01-2016
     * Modified Date    : 00-00-0000
     */
    public function actionAgingReport() {
        $this->order = new Order();
        $request = Yii::$app->request;
        $getValues = $request->get();
        if(isset($getValues['Order']) && is_array($getValues['Order']))
        {
          $this->order = Helper::setModelByParams($this->order, $getValues['Order']);
        }
        if (isset($getValues['from_date_add'])) {
             $this->order->from_date_add = isset($getValues['from_date_add']) ? $getValues['from_date_add'] : null;
        }
        if (isset($getValues['to_date_add'])) {
             $this->order->to_date_add = isset($getValues['to_date_add']) ? $getValues['to_date_add'] : null;
        }
        if (isset($getValues['sellerFilter'])) {
             $this->order->sellerInfo = isset($getValues['sellerFilter'][0]) ? $getValues['sellerFilter'][0] : null;
        }
        if (isset($getValues['sellerFilter']) && count($getValues['sellerFilter'])) {
                $this->order->sellerInput = implode(',', $getValues['sellerFilter']);
            }
        if(ShopSession::shopSessionId())
            $this->order->id_shop = ShopSession::shopSessionId();

            
        /* checking whether the session id is seller */
        if (Helper::isSeller()) {
            $this->employeeIds = AssociateSeller::getAssociatedSeller();
            $this->order->employeeIds = !empty($this->employeeIds) ? $this->employeeIds : null;
            $this->sellerInfo = AssociateSeller::getAssociatedSellerList();
        } else {
            $this->sellerInfo = AssociateSeller::getAllSellerList();
        }

        

        $dataProvider = $this->order->getAgingReport();
        return $this->render('aging-report', [
                    'searchModel' => $this->order,
                    'dataProvider' => $dataProvider,
                    'sellerDetails' => $this->sellerInfo,
                    'getValues' => $getValues,
                    'sellerDetails' => $this->sellerInfo,
        ]);
    }

}
