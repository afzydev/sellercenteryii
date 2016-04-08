<?php

namespace backend\models;

use Yii;
use common\models\MyActiveRecordShop;
use yii\data\SqlDataProvider;
use common\components\Helpers as Helper;
use common\components\Configuration;
use common\components\Session as ShopSession;
use common\components\ShopdevConfiguration;
use backend\models\AssociateSeller;
use backend\models\Carrier;
/**
 * 
 */
class Order extends MyActiveRecordShop {

    public $criteria, $id_order, $invoice_ref_key, $reference, $product_name, $customer, $cust_email, $shipping_number, $unit_price_tax_incl, $total_paid_tax_incl, $payment, $osname, $confirmed_by, $delivery_days, $shipped_date_add, $delivered_date_add, $invoice_number, $address1, $address2, $state_name, $city, $postcode, $product_reference, $id_carrier, $date_add, $perpage, $response_waywill,$carrier_name,$current_state;
    public $id_shop;
    public $orderStatus;
    public $id_order_state;
    public $employeeIds;
    public $from_date_add;
    public $to_date_add;
    public $type;
    public $sellers;
    public $shopId;
    public $sellerInput;
    public $id_product;
    public $sellerInfo;
    public $slip_number;
    public $count_days;
    public $agingFilter;

    const OVERDUE_ORDER_HOURS = '24 Hour';

    public static function tableName() {
        return '{{%ps_orders}}';
    }

    public static function primaryKey() {
        return '{{%id_order}}';
    }

    public function setSearchAttributeValue($searchData) {

        if (!empty($searchData['id_carrier'])) { // Filter by Carrier
            $this->id_carrier = $searchData['id_carrier'];
        }
        if (!empty($searchData['page-count'])) {
            $this->perpage = $searchData['page-count'];
        }
        if (!empty($searchData['from_date_add']) && !empty($searchData['to_date_add'])) {
            $this->from_date_add = $searchData['from_date_add'];
            $this->to_date_add = $searchData['to_date_add'];
        }
        if (!empty($searchData['status'])) {
            $this->orderStatus = $searchData['status'];
        }
        if(!empty($searchData['state_name']))
            $this->state_name=$searchData['state_name'];
    }

    public function search($params) {
        try{
            
            $connection = $this->getDb();
            $configValues = ShopdevConfiguration::getConfigValue('_SHOP_LIVE_DATE_TIME_');
            $date = $configValues['value'];

            $this->criteria="a.date_add>='".$date."'";
            if(empty($this->perpage))
                $this->perpage=Configuration::get('PAGE_SIZE'); /*Display records per page*/

            //print_r($params);die;
            if (!empty($params['search_type']) && !empty($params['search_box'])) { // Filter by Order Id,Order Number,SUb-Order Number,Product Id,Waywill Number and Mode of Payment
                //echo $searchData['search_type'].'===='.$searchData['search_box'];die;
                $this->$params['search_type'] = $params['search_box'];
            }
        
            if (!empty($this->id_order) && is_numeric($this->id_order))
            {
                $this->criteria .= ' and a.`id_order` in (' . $this->id_order . ')';
            }
            if (!empty($this->invoice_ref_key))
                $this->criteria .= " and detail.invoice_ref_key LIKE '%" . $this->invoice_ref_key . "%' ";
            if (!empty($this->reference))
                $this->criteria .= " and a.reference LIKE '%" . $this->reference . "%' ";
            if (!empty($this->product_name))
                $this->criteria .= " and product_lang.name LIKE '%" . $this->product_name . "%' ";
            if (!empty($this->customer))
                $this->criteria .= " and CONCAT(LEFT(c.`firstname`, 1), c.`lastname`) LIKE '%" . $this->customer . "%' ";
            if (!empty($this->cust_email))
                $this->criteria .= " and c.email LIKE '%" . $this->cust_email . "%' ";
            if (!empty($this->response_waywill))
                $this->criteria .= " and a.shipping_number LIKE '%" . $this->response_waywill . "%' ";
            if (!empty($this->total_paid_tax_incl))
                $this->criteria .= " and a.total_paid_tax_incl = '" . $this->total_paid_tax_incl . "' ";
            if (!empty($this->payment)){
                if($this->payment == 'Cash on delivery (COD)')
                    $this->criteria .= " and payment LIKE '%" . $this->payment . "%' ";
                else
                    $this->criteria .= " and payment NOT LIKE '%Cash on delivery (COD)%' ";
            }
            if (!empty($this->osname))
                $this->criteria .= " and osl.`name` LIKE '%" . $this->osname . "%' ";
            if (!empty($this->orderStatus))
                $this->criteria.=' and a.`current_state` in (' . $this->orderStatus . ')';

            if (!empty($this->employeeIds))
                $this->criteria.=' and product.id_employee in (' . $this->employeeIds . ')';

            if (!empty($this->product_reference))
                $this->criteria.=" and detail.product_reference LIKE '%" . $this->product_reference . "%' ";
            if (!empty($this->id_product))
                $this->criteria.=" and product.id_product in ('" . $this->id_product . "') ";
            if (!empty($this->id_carrier))
            {
                $carrier=new Carrier;
                $idCarrier=$carrier->getManifestCarrier($this->id_carrier);
                $this->criteria.=" and a.id_carrier in (" . $idCarrier . ") ";
            }
            if (!empty($this->date_add))
                $this->criteria .= " and a.date_add = '" . $this->date_add . "' ";
            if (!empty($this->shipped_date_add))
                $this->criteria .= " and oh1.date_add = '" . $this->shipped_date_add . "' ";
            if (!empty($this->delivered_date_add))
                $this->criteria .= " and oh1.date_add = '" . $this->delivered_date_add . "' ";

            $conditionalShopJoin = '';

            if (!empty($this->id_shop)) {
                $conditionalShopJoin = 'LEFT JOIN ' . SHOP . ' shop1 ON (shop1.id_shop = ' . $this->id_shop . ')';
                $this->criteria.=' and a.id_shop=' . $this->id_shop . '';
            }
            if ($this->state_name == "overdue") {
                $this->criteria.=" and a.date_add < DATE_SUB( NOW( ) , INTERVAL ".self::OVERDUE_ORDER_HOURS." )";
            }
            if(!empty($this->from_date_add) && !empty($this->to_date_add))
            {
                $from_date_add = date("Y-m-d", strtotime($this->from_date_add));
                $to_date_add = date("Y-m-d", strtotime($this->to_date_add));
                $this->criteria.=" and date(a.`date_upd`) BETWEEN  '".$from_date_add."' AND  '".$to_date_add."' ";
            }
            if(!empty($this->slip_number))
                $this->criteria.=" and oi.number =".$this->slip_number." ";

            if(!empty($this->carrier_name))
                $this->criteria.=" and carrier.name  = '".$this->carrier_name."' ";

            if(isset($this->count_days))
            {
                if($this->count_days==5 && $this->count_days!='')
                    $this->agingFilter='having count_days >= '.$this->count_days.'';
                else if($this->count_days>=0 && $this->count_days<5 && $this->count_days!='')
                    $this->agingFilter='having count_days = '.$this->count_days.'';
                else if($this->count_days=='')
                    $this->agingFilter='';
            }

            if(Helper::isSeller())
            {
                $notShowSellerStatus = ShopdevConfiguration::getConfigValue('_NOT_SHOW_SELLER_FILTER_STATUS_', null, ShopSession::shopSessionId());

                if(!empty($notShowSellerStatus['value']))
                    $this->criteria.=' and a.`current_state` NOT IN  ('.$notShowSellerStatus['value'].') ';
            }

            $command = $connection->createCommand('SELECT COUNT(a.`id_order`) ,DATEDIFF( NOW( ) , MAX(a.date_upd) ) AS count_days
            FROM ' . ORDERS . ' a
            LEFT JOIN ' . CUSTOMERS . ' c ON (c.`id_customer` = a.`id_customer`)
            INNER JOIN ' . ADDRESS . ' address ON address.id_address = a.id_address_delivery
            LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = a.id_order
            LEFT JOIN ' . STATE . ' state ON state.id_state = address.id_state
            LEFT JOIN ' . CARRIER . ' carrier ON a.id_carrier = carrier.id_carrier
            LEFT JOIN ' . PRODUCT . ' product ON product.id_product = detail.product_id
            LEFT JOIN ' . PRODUCT_LANG . ' product_lang ON product_lang.id_product = detail.product_id
            LEFT JOIN ' . ORDER_INVOICE . ' oi ON a.id_order = oi.id_order
            ' . $conditionalShopJoin . '
            LEFT JOIN ' . SELLERINFO . ' sellerinfo ON sellerinfo.id_seller = product.id_employee

            LEFT JOIN '.ORDER_STATE_LANG.' osl ON (a.`current_state` = osl.`id_order_state`) 
            LEFT JOIN '.ORDER_CART_RULE.' as ocr ON ( ocr.id_order = a.id_order)
            LEFT JOIN '.IMAGE.' as image ON ( product.id_product = image.id_product)
            WHERE ' . $this->criteria . ' 
            GROUP BY detail.id_order_detail '.$this->agingFilter.'
            ');
            $reader = $command->query();
            $rowCount = $reader->rowCount;

            $Query = 'SELECT product.id_product,a.`id_order`,DATEDIFF( NOW( ) , MAX(a.date_upd) ) AS count_days,TIMESTAMPDIFF(HOUR, a.date_add, now()) AS count_order_conf_days,a.`current_state` as current_state,a.shipping_number as response_waywill,`unit_price_tax_incl`,`cod_charge`,`total_shipping`,ocr.name as voucher_name, ocr.value as voucher_discount,a.total_paid_tax_incl as total_paid_tax_incl,`payment`,a.date_add as date_add, a.id_currency,product_quantity, a.reference as reference, a.confirmed_by as confirmed_by, product.id_employee as id_employee,product.weight as product_weight, product_lang.name as product_name, detail.invoice_ref_key AS invoice_ref_key, IF(carrier.name!="NULL",carrier.name,"Delhivery") as carrier_name, IF(address.phone="",address.phone_mobile,address.phone) AS mobile_number,address.phone_mobile as mobile1_number,address.phone as contact_number,a.id_order AS id_pdf, oi.number AS slip_number, a.is_invoice_download, c.email as cust_email,c.date_add as account_registered, sellerinfo.company AS company_name, state.name AS state_name, address.address1 AS address1,invoice_address.address1 shipping_address,invoice_address.city shipping_city,invoice_address.postcode shipping_postcode,IF(invoice_address.phone="",invoice_address.phone_mobile,invoice_address.phone) AS shipping_mobile_number, address.address2 AS address2, address.city AS city, address.postcode AS postcode, detail.product_reference AS product_reference, CONCAT(LEFT(c.`firstname`, 1), ".", c.`lastname`) AS `customer`, osl.`name` AS `osname`,osl.`id_order_state`,detail.product_id as id_product,detail.vendor_payout as vendor_payout,a.delivery_type as delivery_type, image.id_image
                FROM '.ORDERS.' a
                LEFT JOIN '.CUSTOMERS.' c ON (c.`id_customer` = a.`id_customer`)
                INNER JOIN '.ADDRESS.' address ON address.id_address = a.id_address_delivery
                INNER JOIN ' . ADDRESS . ' invoice_address ON invoice_address.id_address = a.id_address_invoice
                LEFT JOIN '.ORDER_DETAIL.' detail ON detail.id_order = a.id_order
                LEFT JOIN '.STATE.' state ON state.id_state = address.id_state
                LEFT JOIN '.CARRIER.' carrier ON a.id_carrier = carrier.id_carrier
                LEFT JOIN '.PRODUCT.' product ON product.id_product = detail.product_id
                LEFT JOIN '.PRODUCT_LANG.' product_lang ON product_lang.id_product = detail.product_id
                LEFT JOIN '.ORDER_INVOICE.' oi ON a.id_order = oi.id_order
                '.$conditionalShopJoin.'
                LEFT JOIN '.SELLERINFO.' sellerinfo ON sellerinfo.id_seller = product.id_employee
                LEFT JOIN '.ORDER_STATE_LANG.' osl ON (a.`current_state` = osl.`id_order_state`) 
                LEFT JOIN '.ORDER_CART_RULE.' as ocr ON ( ocr.id_order = a.id_order)
                LEFT JOIN '.IMAGE.' as image ON ( product.id_product = image.id_product)
                WHERE ' . $this->criteria . ' 
                GROUP BY detail.id_order_detail '.$this->agingFilter.' ';
     
            $provider = new SqlDataProvider([
                'db' => $connection,
                'sql' => $Query,
                'totalCount' => $rowCount,
                'pagination' => [
                    'pageSize' =>$this->perpage,
                ],
                    'sort' => [
                        'attributes' => [
                            'date_add',
                            'product_name',
                            'id_order' => [
                                            'asc' => ['id_order' => SORT_ASC],
                                            'desc' => ['id_order' => SORT_DESC],
                                            'default' => SORT_DESC,
                                            'label' => 'Order Id',
                                        ],
                        'osname',
                        'invoice_ref_key',
                        'reference',
                        'invoice_ref_key',
                        'reference',
                        'count_days',
                        'slip_number',
                        'payment',
                        'company_name',
                        'response_waywill',
                        'total_paid_tax_incl',
                        'vendor_payout',
                        'carrier_name'
                        ], 
                   
                    'defaultOrder' => ['id_order' => SORT_DESC],
                     
                ],
            ]);

            if (!empty($this->type) && $this->type == 'view')
                return $provider->getModels(); // returns an array of data rows
            else
                return $provider;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    public function singleExport() {
        try {
            $connection = $this->getDb();
            $query = 'SELECT a.date_add as date_add,a.id_order,detail.invoice_ref_key AS invoice_ref_key,a.reference as reference,
          osl.`name` AS `osname`,product_lang.name as product_name,product_quantity,a.total_paid_tax_incl as total_paid,detail.vendor_payout as vendor_payout,oi.number AS slip_number,DATEDIFF( NOW( ) , MAX(a.date_upd) ) AS count_days,
            a.shipping_number as response_waywill,`payment`,IF(carrier.name!="NULL",carrier.name,"Delhivery") as carrier_name,sellerinfo.company AS company_name, detail.product_reference AS product_reference,
            CONCAT(LEFT(c.`firstname`, 1), ".", c.`lastname`) AS `customer`,`unit_price_tax_incl`,`cod_charge`,`total_shipping`,a.confirmed_by as confirmed_by, address.phone_mobile as mobile1_number, address.phone as contact_number,address.address1 AS address1,address.address2 AS address2,state.name AS state_name,address.city AS city,address.postcode AS postcode,image.id_image as id_image
            FROM ' . ORDERS . ' a 
            LEFT JOIN ' . CUSTOMERS . ' c ON (c.`id_customer` = a.`id_customer`) 
            INNER JOIN ' . ADDRESS . ' address ON address.id_address = a.id_address_delivery
            INNER JOIN ' . SHOP . ' shop1 ON (shop1.id_shop = a.id_shop '.$this->shopId.')
            LEFT JOIN ' . ORDER_STATE . ' os ON (os.`id_order_state` = a.`current_state`) 
            LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = a.id_order 
            LEFT JOIN ' . STATE . ' state ON state.id_state = address.id_state 
            LEFT JOIN ' . CARRIER . ' carrier ON a.id_carrier = carrier.id_carrier 
            LEFT JOIN ' . PRODUCT . ' product ON product.id_product = detail.product_id 
            LEFT JOIN ' . PRODUCT_LANG . ' product_lang ON product_lang.id_product = detail.product_id 
            LEFT JOIN ' . ORDER_INVOICE . ' oi ON a.id_order = oi.id_order 
            LEFT JOIN ' . SELLERINFO . ' sellerinfo ON sellerinfo.id_seller = product.id_employee 
            LEFT JOIN ' . ORDER_STATE_LANG . ' osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = 1) 
            LEFT JOIN '.IMAGE.' as image ON ( product.id_product = image.id_product) 
            WHERE a.`id_order` in ('.$this->id_order.') GROUP BY detail.id_order_detail ORDER BY a.`id_order` DESC';

            return $connection->createCommand($query)->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    public function checkOrderStatus() {
        try {
            $connection = $this->getDb();
            $getAllStatus = $connection->createCommand('SELECT current_state FROM ' . ORDERS . ' where id_order IN (' . $this->id_order . ') ')
                    ->queryAll();
            $values = [];
            foreach ($getAllStatus as $row) {
                if (count($values) == 0 || in_array($row['current_state'], $values)) {
                    $values[] = $row['current_state'];
                } else {
                    return false;
                }
            }
            return $values;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    //get config value
//    public function getConfigValue($key = null, $idShopGroup = null, $idShop = null) {
//        try{
//            $connection = $this->getDb();
//            $WHERE = " WHERE 1=1 ";
//            if (!empty($idShopGroup)) {
//                $WHERE .= " AND id_shop_group=$idShopGroup ";
//            }
//            if (!empty($idShop)) {
//                $WHERE .= " AND id_shop=$idShop ";
//            }
//            if (!empty($key) && !is_array($key)) {
//                $WHERE .= " AND name ='$key' ";
//            }elseif(count($key) && is_array($key)){
//                $setkey = null;
//                foreach ($key as $key1){
//                    if($setkey){
//                        $setkey .=  ','."'$key1'";
//                    }else{
//                        $setkey .=  "'$key1'";
//                    }
//                }
//                $WHERE .= " AND name IN ($setkey)";
//            }
//            $query = "SELECT name, value FROM " . PS_CONFIGURATION . "$WHERE";
//            if (!empty($key) && !is_array($key)) {
//                $setResult = $connection->createCommand($query)->queryOne();
//            }elseif(count($key) && is_array($key)){
//                $setResult = $connection->createCommand($query)->queryAll();
//            }
//            
//            return $setResult;
//        }
//        catch(Exception $e){
//          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
//        }
//
//    }


    /**
     * Function name    : actionInvoices
     * Description      : This function used to generate the invoices of particular order.
     * @param           : @int
     * @return          : @array
     * Created By       : Ravi kumar
     * Created Date     : 12-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getOrderInvoiceDetails($orderId = null) {
        try{
            $connection = $this->getDb();

            $query = "SELECT o.id_order, o.ref_key as order_number, o.id_shop, o.id_customer, cu.id_default_group, o.id_cart, o.is_invoice_download, o.payment, 
                    o.module, o.total_discounts, o.total_discounts_tax_incl, o.total_discounts_tax_excl, o.total_paid, o.total_paid_tax_incl, 
                    o.total_paid_tax_excl, o.total_paid_real, o.total_products, o.total_products_wt, o.cod_charge, o.total_shipping, 
                    o.total_shipping_tax_incl, o.total_shipping_tax_excl, o.invoice_number, o.invoice_date, o.date_add, CONCAT(da.firstname,' ',da.lastname) delivery_name, 
                    CONCAT(ba.firstname,' ',ba.lastname) billing_name, CONCAT(ba.address1,' ',ba.address2) billing_adress, CONCAT(da.address1,' ',da.address2) shipping_adress,da.city as shipping_city,da.postcode as shipping_postcode,bc.name as shipping_country,
                    IF(da.phone_mobile, da.phone_mobile, da.phone) as delivery_phone,  IF(ba.phone_mobile, ba.phone_mobile, ba.phone) as billing_phone, da.city as delivery_city, 
                    ba.city as billing_city, da.postcode as delivery_postcode, ba.postcode as billing_postcode, dc.name as delivery_country, bc.name as billing_country , 
                    ds.name as delivery_state, bs.name as billing_state, IF(ISNULL(c.name),'Delhivery', c.name) as carrier_name, o.shipping_number as waybill, 
                    oi.number AS invoice_number, ocr.name as voucher_name, ocr.value as voucher_discount FROM " . ORDERS . " o LEFT JOIN " . ADDRESS . " as da ON ( da.id_address = o.id_address_delivery)
                    LEFT JOIN " . ADDRESS . " as ba ON ( ba.id_address = o.id_address_invoice)
                    LEFT JOIN " . COUNTRY_LANG . " as bc ON ( bc.id_country = ba.id_country)
                    LEFT JOIN " . COUNTRY_LANG . " as dc ON ( dc.id_country = da.id_country)
                    LEFT JOIN " . STATE . " as bs ON ( bs.id_state = ba.id_state)
                    LEFT JOIN " . STATE . " as ds ON ( ds.id_state = da.id_state)
                    LEFT JOIN " . CARRIER . " as c ON ( c.id_carrier = o.id_carrier)
                    LEFT JOIN " . ORDER_INVOICE . " as oi ON ( oi.id_order = o.id_order)
                    LEFT JOIN " . ORDER_CART_RULE . " as ocr ON ( ocr.id_order = o.id_order)
                    LEFT JOIN " . CUSTOMERS . " as cu ON ( cu.id_customer = o.id_customer)
                    where o.id_order = " . $orderId . " and oi.number > 0 and o.shipping_number!='' ";

            return $connection->createCommand($query)->queryOne();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    /**
     * Function name    : getOrderDetails
     * Description      : This function is used to get the all order details from given order id
     * @param           : @int
     * @return          : @array
     * Created By       : Ravi kumar
     * Created Date     : 12-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getOrderDetails($orderId, $refKey) {
        try {
            $connection = $this->getDb();
            $query = "SELECT product_name, product_supplier_reference, product_price, unit_price_tax_excl, 
            unit_price_tax_incl, reduction_amount, reduction_percent, product_quantity, total_price_tax_excl, 
            total_price_tax_incl, FIND_IN_SET(id_order, (SELECT GROUP_CONCAT(id_order ORDER BY id_order ASC) FROM ps_order_detail where invoice_ref_key = " . "'$refKey'" . ")) AS sub_order_position 
            FROM " . ORDER_DETAIL . " where id_order = " . $orderId;
            return $connection->createCommand($query)->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    public function packageSlipCounter(){
        try{
            $connection = $this->getDb();
            $key = array('PS_OS_ORDERCONFIRMATION');
            $configValues = ShopdevConfiguration::getConfigValue($key);
            $setConfigValue = array();
            
            foreach($configValues as $configValue){
                $setConfigValue[$configValue['name']] = $configValue['value'];
            }
            $configValues = ShopdevConfiguration::getConfigValue('_INVOICE_DOWNLOAD_LIMIT_');
            $invoiceLimit = $configValues['value'];



            $query = "SELECT GROUP_CONCAT(id_order) as id_order
                        FROM (SELECT GROUP_CONCAT(o.id_order ) as id_order , o.shipping_number,oms_orders.is_invoice_download,COUNT(o.id_order) as packageslipcreatable 
                            FROM ".ORDERS." o 
                            LEFT JOIN ".ORDER_DETAIL." detail ON detail.id_order = o.id_order 
                            LEFT JOIN ".PRODUCT." product ON product.id_product = detail.product_id 
                            LEFT JOIN ".OMS_ORDERS." oms_orders ON oms_orders.id_order = o.id_order 
                            LEFT JOIN " . ORDER_INVOICE . " as oi ON ( oi.id_order = o.id_order) 
                            where (o.current_state=".$setConfigValue['PS_OS_ORDERCONFIRMATION']." AND product.id_employee=".Helper::getSessionId()." AND oi.number > 0 AND o.shipping_number!='' ) group by o.id_order  having is_invoice_download<".$invoiceLimit." OR is_invoice_download is Null) as id_order";
            return $connection->createCommand($query)->queryOne();

        }
        catch(Exception $e){

        }
    }

    /**
     * Function name    : getOrderInvoice
     * Description      : This function is used to get the order invoice details from given order id.
     * @param           : @int
     * @return          : @array
     * Created By       : Ravi kumar
     * Created Date     : 12-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getOrderInvoice($orderId) {
        try{
            $connection = $this->getDb();
            $query = "SELECT total_paid_tax_incl, number, total_paid_tax_excl, total_products, total_products_wt, total_discount_tax_incl, total_wrapping_tax_incl, total_wrapping_tax_excl FROM " . ORDER_INVOICE . " where id_order = " . $orderId;
            return $connection->createCommand($query)->queryOne();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    /**
     * Function name    : getSellerInfo
     * Description      : This function is used to get the seller infomartion from given order id.
     * @param           : @int
     * @return          : @array
     * Created By       : Ravi kumar
     * Created Date     : 12-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getSellerInfo($orderId) {
        try{
            $connection = $this->getDb();
            $query = 'select si.company, si.address1, si.address2, si.city, si.postcode, s.name as seller_state from ' . ORDER_DETAIL . ' od 
            LEFT JOIN ' . PRODUCT . ' pr ON pr.id_product = od.product_id 
            LEFT JOIN ' . SELLERINFO . ' si ON si.id_seller = pr.id_employee 
            LEFT JOIN ' . STATE . ' s ON s.id_state = si.id_state 
            WHERE od.id_order = ' . $orderId;
            return $connection->createCommand($query)->queryOne();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    /**
     * Function name    : taxExculdedDisplay
     * Description      : This function is used to get the configration value from configration table.
     * @param           : @int
     * @return          : @Array
     * Created By       : Ravi kumar
     * Created Date     : 14-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function taxExculdedDisplay($id_group = null) {
        try{
            $connection = $this->getDb();
            $sendResult = 0;
            $query = "SELECT price_display_method FROM " . GROUP . " WHERE id_group = " . (int) $id_group;
            $result = $connection->createCommand($query)->queryOne();
            if (count($result) && $result) {
                $sendResult = $result['price_display_method'];
            }
            return $sendResult;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
       
    }

    /**
     * Function name    : getDownloadStatas
     * Description      : This function is used to get the configration value from configration table.
     * @param           : @int
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 16-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getDownloadStatus($orderId = null,$type=null) {
        try{
            $connection = $this->getDb();
            $setResult = null;
            $condition='';
            if($type=='update')
            {
                $condition.='AND id_employee='.Helper::getSessionId().'';
            }
            else
            {
                if(Helper::isSeller() || Helper::isAdmin())
                {
                    $condition.='AND id_employee='.Helper::getSessionId().' ';

                    if(Helper::isAdmin())
                    {
                        $sellers= AssociateSeller::getAssociatedSellerList();
                        $sellerIds='';
                        if(!empty($sellers) && count($sellers)>0)
                        {
                            foreach ($sellers as $key=>$value) {
                                $sellerIds.=','.$value['id_seller'];
                            }
                            $sellerIds=substr($sellerIds, 1);
                            $condition='AND id_employee in ('.$sellerIds.') ';
                        }
                    }
                }
            }

            $query = "SELECT is_invoice_download FROM " . OMS_ORDERS . " WHERE id_order = " . (int) $orderId." ".$condition." ";

            $result = $connection->createCommand($query)->queryOne();

            if (count($result) && $result) {
                $setResult = $result['is_invoice_download'];
            }
            if($type=='update')
                return $this->updateDownloadStaus($orderId, $setResult);
            else
                return $setResult;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    
    }

    /**
     * Function name    : updateDownloadStaus
     * Description      : This function is used to get the configration value from configration table.
     * @param           : @int, @bool
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 16-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function updateDownloadStaus($orderId, $setResult) {
        try{
            $connection = $this->getDb();
            $setStaus = false;
            if ($setResult) {
                $query = "UPDATE " . OMS_ORDERS . " SET is_invoice_download = is_invoice_download+1,id_employee=".Helper::getSessionId()." where id_order = " . (int) $orderId." AND id_employee=".Helper::getSessionId()." ";
                $setStaus = $setResult;
            } else {
                $query = "INSERT INTO " . OMS_ORDERS . "(id_order,id_employee,is_invoice_download) values(" . (int) $orderId . ",".Helper::getSessionId().", 1);";
            }
            $connection->createCommand($query)->execute();
            return $setStaus;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    /**
     * Function name    : getTemplateForPdf
     * Description      : This function is used to get pdf template.
     * @param           : @int, @string
     * @return          : @string
     * Created By       : Ravi kumar
     * Created Date     : 16-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getTemplateForPdf($shopId, $key) {
        try{
            $connection = $this->getDb();
            $setResult = '_invoiceNotFound';
            $query = "SELECT value FROM " . SITE_CONFIG . " WHERE id_shop = " . (int) $shopId . " AND name = " . "'$key'";
            $result = $connection->createCommand($query)->queryOne();
            if (count($result) && $result) {
                $setResult = $result['value'];
            }
            return $setResult;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    /**
     * Function name    : getDownloadStatasShopdevAjax
     * Description      : This function is used to check the download status of the report from order table of shop dev.
     * @param           : @int
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 16-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getDownloadStatasShopdevAjax($orderId = null) {
        try {
            $connection = $this->getDb();
            $query = "SELECT is_invoice_download FROM " . ORDERS . " WHERE id_order = " . (int) $orderId;
            $result = $connection->createCommand($query)->queryOne();

            if (count($result) && $result) {
                return $result;
            } else {
                return false;
            }
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    /**
     * Function name    : getDownloadStatasAjax
     * Description      : This function is used to get download status of the invoice pdf.
     * @param           : @int
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 16-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getDownloadStatasAjax($orderId = null) {
        try{
            $connection = $this->getDb();
            $setResult = false;
            $query = "SELECT is_invoice_download FROM " . OMS_ORDERS . " WHERE id_order = " . (int) $orderId;
            $result = $connection->createCommand($query)->queryOne();

            if (count($result) && $result) {
                $setResult = true;
            }
            return $setResult;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
    
    /**
     * Function name    : getOrdersInfo
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Preet saxena
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public function getOrdersInfo() {
        try{
            $connection = $this->getDb();
            $isSeller = '';
            $conditionSeller = '';
            $key = array('PS_OS_READYTOSHIPPED', 'PS_OS_ORDERCONFIRMATION', 'RECEIVED_AT_COURIER_HUB', 'PS_OS_RETURNED','PS_SHIPPED', '_SHOP_LIVE_DATE_TIME_','PS_OS_RTO_INITIATED_DELIVERED');
            $configValues = ShopdevConfiguration::getConfigValue($key);
            $setConfigValue = array();
            
            foreach($configValues as $configValue){
                $setConfigValue[$configValue['name']] = $configValue['value'];
            }
            
            $new_ordres = isset($setConfigValue['PS_OS_ORDERCONFIRMATION'])?$setConfigValue['PS_OS_ORDERCONFIRMATION']:0;
            $ready_to_shipped =isset($setConfigValue['PS_OS_READYTOSHIPPED'])?$setConfigValue['PS_OS_READYTOSHIPPED']:0;
            $handed_over_courier =isset($setConfigValue['RECEIVED_AT_COURIER_HUB'])?$setConfigValue['RECEIVED_AT_COURIER_HUB']:0;
            $returned =isset($setConfigValue['PS_OS_RETURNED'])?$setConfigValue['PS_OS_RETURNED']:0;
            $shipped =isset($setConfigValue['PS_SHIPPED'])?$setConfigValue['PS_SHIPPED']:0;
            $rto_initiated_delivered =isset($setConfigValue['PS_OS_RTO_INITIATED_DELIVERED'])?$setConfigValue['PS_OS_RTO_INITIATED_DELIVERED']:0;
            $date =isset($setConfigValue['_SHOP_LIVE_DATE_TIME_'])?$setConfigValue['_SHOP_LIVE_DATE_TIME_']:0;
            
            if (Helper::isSeller()){
                $conditionSeller = 'AND id_employee IN(' . $this->employeeIds . ')';
            }
            $searchBy='';
            $subSearchBy='';
            if(!empty($this->id_shop))
            {
                $searchBy='JOIN '.SHOP.' shop ON (shop.id_shop = o.id_shop AND o.id_shop IN ('.$this->id_shop.'))';
                $subSearchBy='JOIN '.SHOP.' shop ON (shop.id_shop = ps_orders.id_shop AND ps_orders.id_shop IN ('.$this->id_shop.'))';
            }

            $Query = 'SELECT  SUM(o.current_state = ' . $new_ordres . ') AS new_orders,
                SUM(o.current_state = ' . $ready_to_shipped . ') AS ready_to_shipped,
                SUM(o.current_state = ' . $handed_over_courier . ') AS handed_over_courier,
                SUM(o.current_state = ' . $returned . ') AS returned,
                SUM(o.current_state = ' .$shipped.') AS shipped,
                SUM(o.current_state = '.$new_ordres.' AND  o.`date_add` < DATE_SUB( NOW( ) , INTERVAL '.self::OVERDUE_ORDER_HOURS.' )) AS overdue,
                SUM(o.current_state in ('.$rto_initiated_delivered.')) as rto_initiated_delivered
                FROM '.ORDERS.' o 
                LEFT JOIN '.ORDER_DETAIL.' od ON( o.id_order = od.id_order)
                LEFT JOIN '.PRODUCT.' p ON (p.id_product = od.product_id )
                '.$searchBy.'
                WHERE ( o.current_state = ' . $new_ordres . ' OR o.current_state = ' . $ready_to_shipped . ' OR o.current_state = ' . $handed_over_courier . ' OR o.current_state = ' . $returned . ' OR o.current_state = ' . $shipped . ' OR o.current_state in ('.$rto_initiated_delivered.') ) 
                '.$conditionSeller.' AND o.date_add >= "' . $date . '"';
                
            return $connection->createCommand($Query)->queryOne();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
        
    }
    
    /**
     * Function name    : getSale
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Preet saxena
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

    public function getSale() {
        try{
            $connection = $this->getDb();
            $configValues = ShopdevConfiguration::getConfigValue('_SHOP_LIVE_DATE_TIME_');
            $date = $configValues['value'];

            $days_ago = date('Y-m-d', strtotime('-15 days', strtotime(date('Y-m-d'))));

            $conditionalJoin='';
            $searchBy='';
            $to_date = date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))));
            $this->criteria='`invoice_date` BETWEEN "'.$days_ago.'" AND "'.$to_date.'"';

            if(!empty($this->id_shop))
                $searchBy='AND o.id_shop IN (' . $this->id_shop . ')';

            if(!empty($this->from_date_add) && !empty($this->to_date_add))
            {
                $this->from_date_add = date("Y-m-d", strtotime($this->from_date_add));
                $this->to_date_add = date("Y-m-d", strtotime($this->to_date_add));
                $this->criteria="`invoice_date` BETWEEN  '".$this->from_date_add."' AND  '".$this->to_date_add."' ";
            }

            if (Helper::isSeller()){

                $conditionalJoin=' LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = o.id_order LEFT JOIN ' . PRODUCT . ' product ON product.id_product = detail.product_id LEFT JOIN ' . SELLERINFO . ' sellerinfo ON sellerinfo.id_seller = product.id_employee';

                $this->criteria = $this->criteria.' AND product.id_employee IN(' . $this->employeeIds . ')';
            }

            $Query = 'SELECT LEFT(`invoice_date`, 10) as sales_date, SUM(total_paid_tax_excl / o.conversion_rate) as sales FROM '.ORDERS.' o LEFT JOIN '.ORDER_STATE.' os ON o.current_state = os.id_order_state '.$conditionalJoin.' WHERE '.$this->criteria.' AND os.logable = 1 '.$searchBy.' GROUP BY LEFT(`invoice_date`, 10)';
            
            $sale = $connection->createCommand($Query)->queryAll();
            
            return $this->getSaleChartData($sale);
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    /**
     * Function name    : getSaleChartData
     * Description      : This function is used to get the sales.
     * @param           : @int
     * @return          : @bool
     * Created By       : Preet saxena
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    protected function getSaleChartData($sales) {
        $data = array();
        if (count($sales)>0) {
            foreach ($sales as $sale) {
                $data['saleTotal'][] = (int) $sale['sales'];
                $data['salesDate'][] = date('d-M-y',strtotime($sale['sales_date']));;
            }

            $data['getOverAllSales'] = array_sum($data['saleTotal']);
        }
        return $data;
    }


    /**
     * Function name    : getSale
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Preet saxena
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
/*    public function getProductInfo() {
        try{
            $connection = $this->getDb();
            $shopId = ShopSession::shopSessionId();
            $conditionSeller = '';
            $where=' 1=1';
            $join1 = '';
            $join2 = '';
            $configValues = ShopdevConfiguration::getConfigValue(['LOW_IN_STOCK_QUANTITY','_SHOP_LIVE_DATE_TIME_']);
            foreach($configValues as $configValue){
                $setConfigValue[$configValue['name']] = $configValue['value'];
            }
            $quantity = $setConfigValue['LOW_IN_STOCK_QUANTITY'] ? $setConfigValue['LOW_IN_STOCK_QUANTITY'] : '5';
            if (Helper::isSeller()){
                $conditionSeller = 'AND id_employee IN(' . $this->employeeIds . ')';
            }
            
            if(Helper::isSeller()){
                $where .= ' and p.id_employee IN (' . $this->employeeIds . ')';
                $join1 = ' LEFT JOIN '.STOCK_AVAILABLE.' sv ON (sv.id_product = p.id_product)';
                $join2 = ' JOIN '.PRODUCT_SHOP.' sa ON (p.`id_product` = sa.`id_product`)';
                
            }else{
                if($shopId){
                    $conditionSeller = 'AND sv.id_shop = '.$shopId.'';
                    $join1 = ' LEFT JOIN '.STOCK_AVAILABLE.' sv ON (sv.id_product = p.id_product '.$conditionSeller.' )';
                    $join2 = ' JOIN '.PRODUCT_SHOP.' sa ON (p.`id_product` = sa.`id_product` AND sa.id_shop = '.$shopId.')';
                }else{
                    $conditionSeller = 'AND sv.id_shop = 1';
                    $join1 = ' LEFT JOIN '.STOCK_AVAILABLE.' sv ON (sv.id_product = p.id_product '.$conditionSeller.' )';
                    $shopId=1;
                    $join2 = ' JOIN '.PRODUCT_SHOP.' sa ON (p.`id_product` = sa.`id_product` AND sa.id_shop = '.$shopId.')';
                }
            }
            $Query='SELECT COUNT(DISTINCT sa.`id_product`) AS total_products, 
                    IF(sv.quantity > 0 AND sv.quantity IS NOT NULL,1,0) AS in_stock, 
                    IF(sv.quantity<=0 OR sv.quantity IS NULL ,1,0) AS out_of_stock, 
                    IF(sv.quantity<=5 OR sv.quantity IS NULL ,1,0) AS low_in_stock, 
                    IF(sa.active=0 ,1,0) AS inactive 
                    FROM '.PRODUCT.' p 
                     JOIN '.EMPLOYEE.' emp ON (emp.id_employee = p.id_employee)'.$join1.$join2.''
                    .' where'.$where.
                    ' GROUP BY sa.id_product';
            $product_inv = $connection->createCommand($Query)->queryAll();
            $total_products = 0;
            $in_stock = 0;
            $out_stock = 0;
            $low_in_stock = 0;
            $inactive = 0;
            foreach($product_inv as $products)
            {
                $total_products += $products['total_products'];
                $in_stock       += $products['in_stock'];
                $out_stock      += $products['out_of_stock'];
                $low_in_stock   += $products['low_in_stock'];
                $inactive       += $products['inactive'];
            }
            
            $sale['total_products'] = $total_products;
            $sale['in_stock']       = $in_stock;
            $sale['out_of_stock']   = $out_stock;
            $sale['low_in_stock']   = $low_in_stock;
            $sale['inactive']       = $inactive;
            return $sale;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }*/
           /**
     * Function name    : getOrderChartData
     * Description      : This function is used to get the sales.
     * @param           : @int
     * @return          : @bool
     * Created By       : Preet saxena
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    protected function getOrderChartData($orders) {

        $data = array();
        if (count($orders)>0) {
            foreach ($orders as $order) {
                $data['orderTotal'][] = (int) $order['orders'];
                $data['ordersDate'][] = date('d-M-y',strtotime($order['order_date']));
            }
            $data['getOverallOrders'] = array_sum($data['orderTotal']);
        }
        return $data;
    }

    /**
     * Function name    : getOrder
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Preet saxena
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000*/
    
    public function getOrder() {
        try{
           $connection = $this->getDb();
           $configValues = ShopdevConfiguration::getConfigValue('_SHOP_LIVE_DATE_TIME_');
           $date = $configValues['value'];

           $days_ago = date('Y-m-d', strtotime('-15 days', strtotime(date('Y-m-d'))));
           $totalOrders = 0;
           $searchBy='';
           $conditionalJoin='';
           $to_date = date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))));
           $this->criteria='`invoice_date` BETWEEN "'.$days_ago.'" AND "'.$to_date.'"';

            if(!empty($this->id_shop))
                $searchBy='AND o.id_shop IN (' . $this->id_shop . ') ';


            if(!empty($this->from_date_add) && !empty($this->to_date_add))
            {
                $this->criteria='';
                $this->from_date_add = date("Y-m-d", strtotime($this->from_date_add));
                $this->to_date_add = date("Y-m-d", strtotime($this->to_date_add));
                $this->criteria= " `invoice_date` BETWEEN  '".$this->from_date_add."' AND  '".$this->to_date_add."' ";
            }

            if (Helper::isSeller()){

                $conditionalJoin=' LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = o.id_order LEFT JOIN ' . PRODUCT . ' product ON product.id_product = detail.product_id LEFT JOIN ' . SELLERINFO . ' sellerinfo ON sellerinfo.id_seller = product.id_employee';

                $this->criteria = $this->criteria.' AND product.id_employee IN(' . $this->employeeIds . ')';
            }

              $Query = 'SELECT LEFT(`invoice_date`, 10) as order_date, COUNT(*) as orders FROM '.ORDERS.' o LEFT JOIN '.ORDER_STATE.' os ON o.current_state = os.id_order_state '.$conditionalJoin.'  WHERE '.$this->criteria.' AND os.logable = 1 '.$searchBy.' GROUP BY LEFT(`invoice_date`, 10)';

            $orders = $connection->createCommand($Query)->queryAll();
            return $this->getOrderChartData($orders);
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

   }
   
    /**
     * Function name    : getAgingReport
     * Description      : This function is used to get aging report of the seller
     * @param           : @int
     * @return          : @bool
     * Created By       : Preet saxena
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000*/
    
    public function getAgingReport() {
        $connection = $this->getDb();
        $conditionSeller = '';
        $setConfigValue = array();
        $key = array('PS_OS_READYTOSHIPPED', 'PS_OS_ORDERCONFIRMATION','_SHOP_LIVE_DATE_TIME_');
        $configValues = ShopdevConfiguration::getConfigValue($key);
        
        foreach($configValues as $configValue){
         $setConfigValue[$configValue['name']] = $configValue['value'];
        }
        $date =isset($setConfigValue['_SHOP_LIVE_DATE_TIME_'])?$setConfigValue['_SHOP_LIVE_DATE_TIME_']:0;

        $order_confirmed = $setConfigValue['PS_OS_ORDERCONFIRMATION'];
        $ready_to_shipped = $setConfigValue['PS_OS_READYTOSHIPPED'];
        

        if (Helper::isSeller()){
            $conditionSeller = 'AND product.id_employee IN(' . $this->employeeIds . ')';
        }
        if ($this->sellerInfo) {
            $conditionSeller .= 'AND product.id_employee IN(' . $this->sellerInfo . ')';
        }

        if (!empty($this->from_date_add) && !empty($this->to_date_add)) {
            $this->from_date_add = date("Y-m-d", strtotime($this->from_date_add));
            $this->to_date_add = date("Y-m-d", strtotime($this->to_date_add));
            $this->criteria.=" and date(oh.`date_add`) BETWEEN  '" . $this->from_date_add . "' AND  '" . $this->to_date_add . "' ";
        } else if (!empty($this->from_date_add) && empty($this->to_date_add)) {
            $this->from_date_add = date("Y-m-d", strtotime($this->from_date_add));
            $this->criteria.=" and date(oh.`date_add`) ='" . $this->from_date_add . "'";
        } else if (empty($this->from_date_add) && !empty($this->to_date_add)) {
            $this->to_date_add = date("Y-m-d", strtotime($this->to_date_add));
            $this->criteria.=" and date(oh.`date_add`) ='" . $this->to_date_add . "'";
        }

        if (!empty($this->id_shop))
            $conditionSeller .= 'AND o.id_shop = ' . $this->id_shop . '';

       $Query = 'SELECT sellerinfo.id_seller, sellerinfo.company as seller_name, osl.name as stage,o.current_state, 
        SUM(IF(DATE_FORMAT(o.date_upd,"%Y-%m-%d") = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 0 DAY),"%Y-%m-%d") , 1,0)) as day,
        SUM(IF(DATE_FORMAT(o.date_upd,"%Y-%m-%d") = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 DAY),"%Y-%m-%d") , 1,0)) as day1,
        SUM(IF(DATE_FORMAT(o.date_upd,"%Y-%m-%d") = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 2 DAY),"%Y-%m-%d") , 1,0)) as day2,
        SUM(IF(DATE_FORMAT(o.date_upd,"%Y-%m-%d") = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 3 DAY),"%Y-%m-%d") , 1,0)) as day3,
        SUM(IF(DATE_FORMAT(o.date_upd,"%Y-%m-%d") = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 4 DAY),"%Y-%m-%d") , 1,0)) as day4,
        SUM(IF(DATE_FORMAT(o.date_upd,"%Y-%m-%d") <= DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 5 DAY),"%Y-%m-%d") , 1,0)) as day5
        
        FROM ' . ORDERS . ' o 
        LEFT JOIN (select max(`date_add`) as `date_add`,`id_order`,`id_order_state` from ps_order_history group by `id_order`, `id_order_state`)
        as oh ON (o.`id_order` = oh.`id_order` and o.`current_state` = oh.`id_order_state`)
        LEFT JOIN ' . ORDER_STATE_LANG . ' osl ON (o.`current_state` = osl.`id_order_state` AND osl.`id_lang` = 1) 
        LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = o.id_order 
        LEFT JOIN ' . PRODUCT . ' product ON (product.id_product = detail.product_id ) 
        LEFT JOIN ' . SELLERINFO . ' sellerinfo ON sellerinfo.id_seller = product.id_employee 
        WHERE o.current_state IN (2,4,13)'.$this->criteria.$conditionSeller.' AND o.date_add >= "' . $date . '"
        GROUP BY sellerinfo.id_seller, o.current_state 
        ORDER BY seller_name';
        return $provider = new SqlDataProvider([
            'db' => $connection,
            'sql' => $Query,
            //'totalCount' => $count,
            'pagination' => [
                    'pageSize' => 100,
            ],
        ]);
    }
    
    
    public function getAgingReportOrder($params) {
        $connection = $this->getDb();        
        if($params['$datediff']<10)
            $where = 'WHERE DATEDIFF(CURDATE(), o.date_add) = '. $params['$datediff'] .' AND o.`current_state`='.$params['current_state'].' AND sellerinfo.id_seller='.$params['id_seller'];
        else
            $where = 'WHERE DATEDIFF(CURDATE(), o.date_add) >= '. $params['$datediff'] .' AND o.`current_state`='.$params['current_state'].' AND sellerinfo.id_seller='.$params['id_seller'];
        
        $command = $connection->createCommand('SELECT o.id_order ,sellerinfo.id_seller,o.current_state
            FROM ps_orders as o 
            INNER JOIN ' . ORDER_STATE_LANG . ' osl ON (o.`current_state` = osl.`id_order_state` AND osl.`id_lang` = 1) 
            INNER JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = o.id_order 
            INNER JOIN ' . PRODUCT . ' product ON (product.id_product = detail.product_id )
            INNER JOIN ' . SELLERINFO . ' sellerinfo ON sellerinfo.id_seller = product.id_employee '.$where);
            $reader = $command->query();
            $rowCount = $reader->rowCount;
        $Query = 'SELECT o.id_order ,sellerinfo.id_seller,o.current_state
            FROM ps_orders as o 
            INNER JOIN ' . ORDER_STATE_LANG . ' osl ON (o.`current_state` = osl.`id_order_state` AND osl.`id_lang` = 1) 
            INNER JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = o.id_order 
            INNER JOIN ' . PRODUCT . ' product ON (product.id_product = detail.product_id )
            INNER JOIN ' . SELLERINFO . ' sellerinfo ON sellerinfo.id_seller = product.id_employee '.$where;
        
        
        return $provider = new SqlDataProvider([
            'db' => $connection,
            'sql' => $Query,
            'totalCount' => $rowCount,
            
            'pagination' => [
                    'pageSize' =>20,
            ],
        ]);
    }
    
    /**
     * Function name    : getTopFiveOrderCity
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000*/
    
    public function getTopFiveOrderCity() {
        try{
           $connection = $this->getDb();
           $searchBy='1=1';
            if(!empty($this->id_shop))
                $searchBy='ps_orders.id_shop IN (' . $this->id_shop . ')';
           $Query = 'SELECT ps_address.city AS city, count(ps_address.city) as orderCount 
                     FROM '.ORDERS.' 
                     INNER JOIN '.ADDRESS.' ON '.ADDRESS.'.id_address = '.ORDERS.'.id_address_delivery
                     WHERE '.$searchBy.'
                     GROUP BY '.ADDRESS.'.city order by orderCount DESC LIMIT 5 ';
           $orders = $connection->createCommand($Query)->queryAll();
           return $orders;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }
    
    /**
     * Function name    : getNewCustomers
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000*/
    
    public function getNewCustomers() {
        try{
           $connection = $this->getDb();
           $Query = 'SELECT count(*) AS new_customer FROM ( SELECT id_customer FROM '.ORDERS.' GROUP BY id_customer HAVING COUNT(id_customer) = 1) as T;';
           $orders = $connection->createCommand($Query)->queryOne();
           return $orders;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }
    
    /**
     * Function name    : getReturningCustomer
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000*/
    
    public function getReturningCustomer() {
        try{
           $connection = $this->getDb();
           $Query = 'SELECT count(*) AS returning_customer FROM ( SELECT id_customer FROM '.ORDERS.' GROUP BY id_customer HAVING COUNT(id_customer) > 1) as T;';
           $orders = $connection->createCommand($Query)->queryOne();
           return $orders;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }
    
    /**
     * Function name    : getAvgCartValue
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000*/
    
    public function getAvgCartValue() {
         try{
           $connection = $this->getDb();
           $configValues = ShopdevConfiguration::getConfigValue('_SHOP_LIVE_DATE_TIME_');
           $date = $configValues['value'];

           $days_ago = date('Y-m-d', strtotime('-15 days', strtotime(date('Y-m-d'))));
           $totalOrders = 0;
           $searchBy='';
           $conditionalJoin='';
           $to_date = date('Y-m-d');
           $this->criteria='`invoice_date` BETWEEN "'.$days_ago.'" AND "'.$to_date.'"';

            if(!empty($this->id_shop))
                $searchBy='AND o.id_shop IN (' . $this->id_shop . ') ';


            if(!empty($this->from_date_add) && !empty($this->to_date_add))
            {
                $this->criteria='';
                $this->from_date_add = date("Y-m-d", strtotime($this->from_date_add));
                $this->to_date_add = date("Y-m-d", strtotime($this->to_date_add));
                $this->criteria= " `invoice_date` BETWEEN  '".$this->from_date_add."' AND  '".$this->to_date_add."' ";
            }

            if (Helper::isSeller()){

                $conditionalJoin=' LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = o.id_order LEFT JOIN ' . PRODUCT . ' product ON product.id_product = detail.product_id LEFT JOIN ' . SELLERINFO . ' sellerinfo ON sellerinfo.id_seller = product.id_employee';

                $this->criteria = $this->criteria.' AND product.id_employee IN(' . $this->employeeIds . ')';
            }

              $Query = 'SELECT AVG( total_paid ) AS avg_order_value FROM '.ORDERS.' o LEFT JOIN '.ORDER_STATE.' os ON o.current_state = os.id_order_state '.$conditionalJoin.'  WHERE '.$this->criteria.' AND os.logable = 1 '.$searchBy.' GROUP BY LEFT(`invoice_date`, 10)';

        //$Query = 'SELECT AVG( total_paid ) AS avg_order_value FROM '.ORDERS.' o '.$conditionalJoin.' WHERE '.$this->criteria;
        $orders = $connection->createCommand($Query)->queryOne();
        
        return $orders;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }
    
    /**
     * Function name    : getBestSellingProduct
     * Description      : This function is used to get the orders from the orders.
     * @param           : @int
     * @return          : @bool
     * Created By       : Ravi kumar
     * Created Date     : 22-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000*/
    
    /*public function getBestSellingProduct($empid=null) {
        try {
            $connection = $this->getDb();
            $days_ago = date('Y-m-d', strtotime('-15 days', strtotime(date('Y-m-d'))));
            $to_date = date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d'))));
            $this->criteria='a.date_add BETWEEN "'.$days_ago.'" AND "'.$to_date.'"';

            if(!empty($this->from_date_add) && !empty($this->to_date_add))
            {
                $this->from_date_add = date("Y-m-d", strtotime($this->from_date_add));
                $this->to_date_add = date("Y-m-d", strtotime($this->to_date_add));
                $this->criteria='a.date_add BETWEEN "'.$this->from_date_add.'" AND "'.$this->to_date_add.'"';
            }
            if (Helper::isSeller()) {
                if($empid) {
                    $this->criteria.=" and p.id_employee in(" . $empid.")";
                }else {
                  $this->criteria.=" and p.id_employee in(" . $this->employeeIds.")";
                }
            }

            if(!empty($this->id_shop))
                $this->criteria.=' AND a.id_shop IN (' . $this->id_shop . ') ';

            $Query = 'select product.name,product.id_product as id_product,  sum(sv.quantity > 0) as available_qty, sum(product_quantity) as sold, sum(total_paid) as revenue '
                    . 'FROM ' . ORDERS . ' a 
                    LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = a.id_order 
                    LEFT JOIN ' . PRODUCT . ' p ON p.id_product = detail.product_id
                    LEFT JOIN ' . PRODUCT_LANG . ' product ON product.id_product = detail.product_id
                    JOIN ' . STOCK_AVAILABLE . ' sv ON (sv.id_product = product.id_product)
                        where ' . $this->criteria . '
                    group by product.id_product order by sold DESC LIMIT 10';
            $orders = $connection->createCommand($Query)->queryAll();
            return $orders;
        } catch (Exception $e) {
            CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }*/

    public function createManifest($orderIds,$idCarrier,$idSeller){
        $key = array('PS_OS_READYTOSHIPPED');
        $configValues = ShopdevConfiguration::getConfigValue($key);

        foreach ($configValues as $configValue) {
            $setConfigValue[$configValue['name']] = $configValue['value'];
        }
        $ready_to_shipped = $setConfigValue['PS_OS_READYTOSHIPPED'];
        $count = 0;
        $connection = $this->getDb();
        if (!empty($orderIds)) {
            $checkQuery = "Select COUNT(*) from " . ORDERS . " WHERE id_order IN (" . $orderIds . ") and current_state=" . $ready_to_shipped . " ";
            $count = $connection->createCommand($checkQuery)->queryScalar();

            if ($count == 0)
                return 'not_ready_to_be_shipped';
        }

        if ($count > 0 || empty($orderIds)) {
            $this->criteria = '';
            if (!empty($orderIds))
                $this->criteria.="o.id_order IN (" . $orderIds . ") and ";
            if (!empty($idCarrier)) {
                $carrier=new Carrier;
                $idCarrier=$carrier->getManifestCarrier($idCarrier);
                
                $this->criteria.="o.id_carrier in (" . $idCarrier . ") and ";
            }
            if (!empty($idSeller))
                $this->criteria.="product.id_employee = " . $idSeller . " and ";

            $Query = "Select o.id_order,DATEDIFF( NOW( ) , MAX(o.date_upd) ) AS aging,o.reference,detail.product_quantity as product_quantity,product_lang.name as product_name,o.shipping_number as response_waywill,sellerinfo.company AS company_name,sellerinfo.city as origin_city,sellerinfo.postcode as origin_pincode,total_paid_tax_incl,payment,address.city AS dest_city, address.postcode AS dest_postcode,IF(carrier.name!='NULL',carrier.name,'Delhivery') as carrier_name
            from " . ORDERS . " o 
            LEFT JOIN " . ORDER_DETAIL . " detail ON detail.id_order = o.id_order
            LEFT JOIN " . PRODUCT . " product ON product.id_product = detail.product_id
            LEFT JOIN " . PRODUCT_LANG . " product_lang ON product_lang.id_product = detail.product_id
            LEFT JOIN " . SELLERINFO . " sellerinfo ON sellerinfo.id_seller = product.id_employee
            LEFT JOIN " . CUSTOMERS . " c ON (c.`id_customer` = o.`id_customer`)
            INNER JOIN " . ADDRESS . " address ON address.id_address = o.id_address_delivery
            LEFT JOIN " . CARRIER . " carrier ON o.id_carrier = carrier.id_carrier
            WHERE " . $this->criteria . " current_state=" . $ready_to_shipped . " and o.shipping_number!=''
            GROUP BY detail.id_order_detail
            ORDER BY o.id_order DESC";
            $result = $connection->createCommand($Query)->queryAll();
            return $result;
        }


    }

    public function createPicklist($orderIds,$idCarrier,$idSeller){
        $configValues= ShopdevConfiguration::getConfigValue('PS_OS_ORDERCONFIRMATION',null, ShopSession::shopSessionId());
        $orders_confirmed = $configValues['value'];
        
        $countOrders = 0;
        $connection = $this->getDb();
        if (!empty($orderIds)) {
            $checkQuery = "Select COUNT(*) from " . ORDERS . " WHERE id_order IN (" . $orderIds . ") and current_state=" . $orders_confirmed . " ";
            $countOrders = $connection->createCommand($checkQuery)->queryScalar();

            if ($countOrders == 0)
                return 'not_orders_confirmed';
        }

        if ($countOrders > 0 || empty($orderIds)) {
            $this->criteria = '';
            if (!empty($orderIds))
                $this->criteria.="o.id_order IN (" . $orderIds . ") and ";
            if (!empty($idCarrier)) {
                $carrier=new Carrier;
                $idCarrier=$carrier->getManifestCarrier($idCarrier);
                $this->criteria.="o.id_carrier in (" . $idCarrier . ") and ";
            }
            if (!empty($idSeller))
                $this->criteria.="product.id_employee = " . $idSeller . " and ";

            $Query = "Select o.id_order,DATEDIFF( NOW( ) , MAX(o.date_upd) ) AS aging,o.reference,detail.product_quantity as product_quantity,product_lang.name as product_name,o.shipping_number as response_waywill,sellerinfo.company AS company_name,sellerinfo.city as origin_city,sellerinfo.postcode as origin_pincode,total_paid_tax_incl,payment,address.city AS dest_city, address.postcode AS dest_postcode,IF(carrier.name!='NULL',carrier.name,'Delhivery') as carrier_name
            from " . ORDERS . " o 
            LEFT JOIN " . ORDER_DETAIL . " detail ON detail.id_order = o.id_order
            LEFT JOIN " . PRODUCT . " product ON product.id_product = detail.product_id
            LEFT JOIN " . PRODUCT_LANG . " product_lang ON product_lang.id_product = detail.product_id
            LEFT JOIN " . SELLERINFO . " sellerinfo ON sellerinfo.id_seller = product.id_employee
            LEFT JOIN " . CUSTOMERS . " c ON (c.`id_customer` = o.`id_customer`)
            INNER JOIN " . ADDRESS . " address ON address.id_address = o.id_address_delivery
            LEFT JOIN " . CARRIER . " carrier ON o.id_carrier = carrier.id_carrier
            WHERE " . $this->criteria . " current_state=" . $orders_confirmed . "
            GROUP BY detail.id_order_detail
            ORDER BY o.id_order DESC
            ";
            $result = $connection->createCommand($Query)->queryAll();
            return $result;
        }


    }

    
    public function validateOrderId($id_order) {
        try {
            $connection = $this->getDb();
            $Query = 'select COUNT(o.id_order) from ' . ORDERS . ' o'
                    . ' LEFT JOIN ' . ORDER_DETAIL . ' od ON o.id_order=od.id_order'
                    . ' LEFT JOIN ' . PRODUCT . ' p ON p.id_product=od.product_id '
                    . 'where o.id_order=' . $id_order . ' and p.id_employee in (' . AssociateSeller::getAssociatedSeller().')';
            $result = $connection->createCommand($Query)->queryScalar();

            if ($result == 1) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }        
    }

    public function checkPackageSlipDonwload($orderIds){
            $connection = $this->getDb();
            $Query = 'select COUNT(o.id_order) from ' . OMS_ORDERS . ' o
                    where o.id_order in ('.$orderIds.') and o.is_invoice_download>=1 and o.id_employee='.Helper::getSessionId().'  ';
            $result = $connection->createCommand($Query)->queryScalar();

            if ($result >= 1) {
                return TRUE;
            } else {
                return FALSE;
            }

    }

        public function getProductPicklist($id_seller) {
        try {
            $connection = $this->getDb();
            $this->criteria='1=1';
            $this->employeeIds=$id_seller;
            $conditionalShopJoin='';
            if(ShopSession::shopSessionId())
            {
                $this->id_shop=ShopSession::shopSessionId();
                $conditionalShopJoin = 'LEFT JOIN ' . SHOP . ' shop1 ON (shop1.id_shop = ' . $this->id_shop . ')';
                $this->criteria.=' and a.id_shop=' . $this->id_shop . '';
            }

            $query = 'SELECT a.id_order,product.id_product,product_lang.name as product_name,product_quantity,detail.product_reference AS product_reference
            FROM ' . ORDERS . ' a 
            LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = a.id_order 
            LEFT JOIN ' . PRODUCT . ' product ON product.id_product = detail.product_id 
            LEFT JOIN ' . PRODUCT_LANG . ' product_lang ON product_lang.id_product = detail.product_id
            '.$conditionalShopJoin.'
            WHERE '.$this->criteria.' AND product.`id_employee` ='.$this->employeeIds.'  GROUP BY detail.id_order_detail ORDER BY a.`id_order` DESC';

            return $connection->createCommand($query)->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

}

?>