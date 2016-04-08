<?php

namespace backend\models;

use Yii;
use common\models\MyActiveRecordShop;
use yii\data\SqlDataProvider;
use common\components\Configuration;
use common\components\Session as ShopSession;
use common\components\ShopdevConfiguration;
use common\components\Helpers as Helper;

/**
 * Class name    : Order
 * Description      : This class is used to get the products from the product table.
 * Created By       : Preet Saxena
 * Created Date     : 08-02-2016
 * Modified By      : 
 * Modified Date    : 00-00-0000 */
class Product extends MyActiveRecordShop {

    public $id_product;
    public $name;
    public $name_category;
    public $base_price;
    public $sell_price;
    public $quantity;
    public $date_add;
    public $date_upd;
    public $active;
    public $criteria;
    public $perpage;
    public $from_date_add;
    public $to_date_add;
    public $from_date_upd;
    public $to_date_upd;
    public $id_category;
    public $search_radio;
    public $employeeIds;
    public $vendor  ;
    public $shop_reference  ;

    public static function tableName() {
        return '{{%ps_product}}';
    }

    public static function primaryKey() {
        return '{{%id_product}}';
    }

    public function setSearchAttributeValue($searchData) {
        // die('hiiiii');
        if (!empty($searchData['search_type']) && !empty($searchData['search_box'])) { // Filter by Order Id,Order Number,SUb-Order Number,Product Id,Waywill Number and Mode of Payment
            $this->$searchData['search_type'] = $searchData['search_box'];
        }

        if (!empty($searchData['page-count'])) {
                $this->perpage = $searchData['page-count'];
        }
        if (!empty($searchData['from_date_add']) && !empty($searchData['to_date_add'])) {
            $this->from_date_add = $searchData['from_date_add'];
            $this->to_date_add = $searchData['to_date_add'];
        }
        if (!empty($searchData['from_date_upd']) && !empty($searchData['to_date_upd'])) {
            $this->from_date_upd = $searchData['from_date_upd'];
            $this->to_date_upd = $searchData['to_date_upd'];
        }
        if (!empty($searchData['status'])) {
            $this->status = $searchData['status'];
        }
        if (!empty($searchData['cat'])) {
            $this->id_category = $searchData['cat'];
        }
        if (!empty($searchData['search_radio'])) {
            $this->search_radio = $searchData['search_radio'];
        }
    }

    public function search($params) {
        try {

            $connection = $this->getDb();
            $this->criteria = '1=1';
            $configValues = ShopdevConfiguration::getConfigValue('_SHOP_LIVE_DATE_TIME_');
            if(isset($this->active) && $this->active!=NULL){
                $this->criteria .= ' and sa.active in('.$this->active.')';
            }
            if(isset($params['bestSellingProduct']) && $params['bestSellingProduct']=="true")
            {
                $id='';
                $data = $this->getBestSellingProduct($this->employeeIds);
                $this->criteria = '1=1';
                if (count($data)) {
                    foreach ($data as $bestSellingProduct) {
                        $id.=$bestSellingProduct['id_product'].',';
                    }
                     $id = rtrim( $id, ',');
                     $this->criteria .= ' and a.`id_product` in ('. $id.')';
                }
            }
            
            if(isset($params['low_stock']) && $params['low_stock']=="true"){
                $this->criteria .= ' and sav.quantity <= 5';
            }else if(isset($params['in_stock']) && $params['in_stock']=="true"){
                $this->criteria .= ' and sav.quantity > 0';
            }else if(isset($params['out_stock']) && $params['out_stock']=="true"){
                $this->criteria .= ' and sav.quantity <= 0';
            }else if(isset($params['is_active']) && $params['is_active']=="true"){
                $this->criteria.=" and sa.active = 0";
            }else{
                if (!empty($this->quantity))
                    $this->criteria .= ' and a.quantity =' . $this->quantity;
                if (!empty($this->search_radio)) {
                    if ($this->search_radio == 'active') {
                        $this->search_radio = 1;
                    } else {
                        $this->search_radio = 0;
                    }
                    $this->criteria.=" and sa.active = '" . $this->search_radio . "'";
                }
            }
//            if(!empty($configValues))
//                $this->criteria.=' and a.date_add > "'.$configValues['value'].'"';
            if (isset($params['view']) && $params['view']==true){
                $this->criteria .= ' and a.`id_product` = ' . $params['id'];
            }else {

                if (!empty($this->name_category))
                    $this->criteria .= " and cl.`id_category` in (" . $this->name_category . ") ";
                if (!empty($this->base_price))
                    $this->criteria .= " and a.price LIKE '%" . trim($this->base_price) . "%'";
                if (!empty($this->sell_price))
                    $this->criteria .= " and sa.price LIKE '%" . trim($this->sell_price) . "%'";
                if (!empty($this->id_product))
                    $this->criteria .= ' and a.`id_product` = ' . $this->id_product;
                if (!empty($this->name))
                    $this->criteria .= " and b.`name` LIKE '%" . trim($this->name) . "%'";
                
                if (!empty($this->shop_reference))
                    $this->criteria .= " and a.reference LIKE '%" . trim($this->shop_reference) . "%'";
                
                if (!empty($this->from_date_add) && !empty($this->to_date_add)) {
                    $this->from_date_add = date("Y-m-d", strtotime($this->from_date_add));
                    $this->to_date_add = date("Y-m-d", strtotime($this->to_date_add));
                    $this->criteria.=" and date(a.`date_add`) BETWEEN  '" . $this->from_date_add . "' AND  '" . $this->to_date_add . "' ";
                }
                if (!empty($this->from_date_upd) && !empty($this->to_date_upd)) {
                    $this->from_date_upd = date("Y-m-d", strtotime($this->from_date_upd));
                    $this->to_date_upd = date("Y-m-d", strtotime($this->to_date_upd));
                    $this->criteria.=" and date(a.`date_upd`) BETWEEN  '" . $this->from_date_upd . "' AND  '" . $this->to_date_upd . "' ";
                }
                if (!empty($this->id_category)) {
                    $this->criteria.=' and cl.`id_category` in (' . $this->id_category . ')';
                }
                
            }
            if (!empty($this->id_shop))
                    $this->shopId = 'AND shop1.id_shop = ' . $this->id_shop . '';
            if (empty($this->perpage))
                $this->perpage = Configuration::get('PAGE_SIZE');
            $sellerCriteria = '';
            if (!empty($this->employeeIds) && empty($params['bestSellingProduct'])) {
                $sellerCriteria = 'LEFT JOIN ' . EMPLOYEE . ' as emp ON (emp.id_employee=a.id_employee)';
                $this->criteria.=' and a.`id_employee` in (' . $this->employeeIds . ')';
            }
            $condition1 = '';
            $condition2 = '';
            $condition3 = '';
            $condition4 = '';
            $condition5 = '';
            $shopId = ShopSession::shopSessionId();
            if ($shopId) {
                $condition1 = ' AND b.`id_shop` = ' . $shopId;
                $condition2 = ' AND sav.id_shop = ' . $shopId;
                $condition3 = ' AND sa.id_shop = ' . $shopId;
                $condition4 = ' AND cl.id_shop = ' . $shopId;
                $condition5 = ' AND image_shop.id_shop = ' . $shopId;
            } else{
                $condition1 = ' AND b.`id_shop` = 1';
                $condition2 = ' AND sav.id_shop = 1';
                $condition3 = ' AND sa.id_shop = 1';
                $condition4 = ' AND cl.id_shop = 1';
                $condition5 = ' AND image_shop.id_shop = 1';
            }

            $command = $connection->createCommand('SELECT SQL_CALC_FOUND_ROWS a.`id_product`'
                    . ' FROM `ps_product` a '
                    . 'LEFT JOIN `ps_product_lang` b ON (b.`id_product` = a.`id_product` ' . $condition1 . ') '
                    . 'LEFT JOIN `ps_image` i ON (i.`id_product` = a.`id_product`) '
                    . 'LEFT JOIN `ps_stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0 ' . $condition2 . ') '
                    . 'JOIN `ps_product_shop` sa ON (a.`id_product` = sa.`id_product` ' . $condition3 . ') LEFT JOIN `ps_category_lang` cl ON (sa.`id_category_default` = cl.`id_category` '
                    . 'AND b.`id_lang` = cl.`id_lang` ' . $condition4 . ')'
                    . 'LEFT JOIN `ps_image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 ' . $condition5 . ')'
                    . $sellerCriteria . ' WHERE ' . $this->criteria . ' GROUP BY sa.id_product');
            $reader = $command->query();
            $rowCount = $reader->rowCount;

       $Query = 'SELECT SQL_CALC_FOUND_ROWS a.shop_margin as shop_margin,a.shipping_charge as shipping_charge, a.pg_fee as pg_fee,a.reference as shop_reference,s.name as shop_name,si.company as vendor,a.supplier_reference as seller_reference,a.`id_product`,b.name as name,b.description as description,b.description_short as short_description,`reference`,format(a.price,2) as base_price,sa.active as active ,IF(sa.active =0,"Inactive","Active") as active1, MAX(image_shop.id_image) id_image, cl.name `name_category`, IF(sp.reduction_type="amount"
                   OR sp.reduction_type="percentage",CASE sp.reduction_type WHEN "amount" THEN FLOOR(sa.price-sp.reduction) ELSE FLOOR(sa.price-(sp.reduction*sa.price)) END, CASE sp1.reduction_type WHEN "amount" THEN FLOOR(sa.price-sp1.reduction) WHEN "percentage" THEN FLOOR(sa.price-(sp1.reduction*sa.price)) ELSE FLOOR(sa.price) END) as sell_price, 0 AS price_final, a.`is_virtual`,  sav.`quantity` as sav_quantity, IF(sav.`quantity`<=0, 1, 0) badge_danger, a.quantity, a.date_add, a.date_upd '
                    . 'FROM `ps_product` a '
                    . 'LEFT JOIN `ps_product_lang` b ON (b.`id_product` = a.`id_product` ' . $condition1 . ') '
                    . 'LEFT JOIN ps_specific_price sp ON (sp.id_product=a.id_product
                                   AND DATE(sp.from)<=DATE(NOW())
                                   AND DATE(sp.to)>=DATE(NOW())) '
                    .' LEFT JOIN ps_specific_price sp1 ON (sp1.id_product=a.id_product
                                    AND DATE(sp1.from)="0000-00-00"
                                    AND DATE(sp1.to)="0000-00-00") '
                    . 'LEFT JOIN `ps_image` i ON (i.`id_product` = a.`id_product`) '
                    . 'LEFT JOIN `ps_stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0 ' . $condition2 . ') '
                    . 'JOIN `ps_product_shop` sa ON (a.`id_product` = sa.`id_product` ' . $condition3 . ') LEFT JOIN `ps_category_lang` cl ON (sa.`id_category_default` = cl.`id_category` '
                    . 'AND b.`id_lang` = cl.`id_lang` ' . $condition4 . ')'
                    . 'LEFT JOIN `ps_image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 ' . $condition5 . ')'
                    .' LEFT JOIN ps_sellerinfo si ON a.id_employee = si.id_seller'
                    .' LEFT JOIN ps_employee_shop es ON a.id_employee = es.id_employee'
                    .' LEFT JOIN ps_shop s ON s.id_shop = es.id_shop '
                    . $sellerCriteria . ' WHERE ' . $this->criteria . ' GROUP BY sa.id_product';
            
            $provider = new SqlDataProvider([
                'db' => $connection,
                'sql' => $Query,
                'totalCount' => $rowCount,
                'pagination' => [
                    'pageSize' => $this->perpage,
                ],
                'sort' => [
                    'attributes' => [
                        'date_add',
                        'date_upd',
                        'base_price',
                        'sell_price',
                        'name_category',
                        'name',
                        'sav_quantity',
                        'shop_reference',
                        'active',
                        'vendor',
                        'id_product' => [
                            'asc' => ['id_product' => SORT_ASC],
                            'desc' => ['id_product' => SORT_DESC],
                            'default' => SORT_DESC,
                            'label' => 'Product Id',
                        ],
                        
                    ],
                    'defaultOrder' => ['id_product' => SORT_DESC],
                ],
                 
            ]);

            if (!empty($this->type) && $this->type == 'view'){
                return $provider->getModels(); // returns an array of data rows
            }
            else if (isset($params['view']) && $params['view']==true) {
                return $connection->createCommand($Query)->queryOne();
            } 
            else{
                return $provider;
            }
        } catch (Exception $e) {
            CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    public function getL3category() {
        $connection = $this->getDb();
        $conditionShop1 = '';
        $conditionShop2 = '';
        $shopId = ShopSession::shopSessionId();
        if ($shopId != '') {
            $conditionShop1 = ' AND cl.id_shop = ' . ShopSession::shopSessionId();
            $conditionShop2 = ' AND category_shop.id_shop = ' . ShopSession::shopSessionId();
        }
     $sql = 'SELECT c.`id_category`, cl.`name` FROM `ps_category` c LEFT JOIN `ps_category_lang` cl ON (c.`id_category` = cl.`id_category` ' . $conditionShop1 . ' ) '
                . 'INNER JOIN ps_category_shop category_shop ON (category_shop.id_category = c.id_category ' . $conditionShop2 . ' ) '
                . 'WHERE cl.`id_lang` = 1 AND c.level_depth=4 GROUP BY c.id_category ORDER BY cl.`name`, category_shop.`position`';
        return $connection->createCommand($sql)->queryAll();
    }
    
    public function getProductFeature($getParam)
    {
            $connection = $this->getDb();

            $this->criteria = '1=1';
            if (isset($params['view']) && $params['view']==true){
                $this->criteria .= ' and a.`id_product` = ' . $params['id'];
            }
            $sql = 'SELECT fl.name, GROUP_CONCAT( fvl.value ) AS values1, f.id_feature
                    FROM '.FEATURE_LANG.' fl
                    LEFT JOIN '.FEATURE_PRODUCT.' fp ON fl.id_feature = fp.id_feature AND fp.id_product =19569
                    LEFT JOIN '.FEATURE_VALUE_LANG.' fvl ON fp.id_feature_value = fvl.id_feature_value
                    LEFT JOIN '.FEATURE.' f ON f.id_feature = fl.id_feature
                    GROUP BY fl.name, fl.id_feature
                    ORDER BY f.position';
            return $connection->createCommand($sql)->queryAll();
    }

    public function getProductImages($productId){
            $connection = $this->getDb();
            $query='SELECT id_image from '.IMAGE.' WHERE id_product='.$productId.' ';
            return $connection->createCommand($query)->queryAll();
    }

    public function productDetails($idProduct){
        $connection = $this->getDb();
        $shopId = ShopSession::shopSessionId();
        $conditionShop='';
        if ($shopId != '') {
            $conditionShop = ' AND tl.id_shop = ' . $shopId;
        }
       $Query='SELECT  DISTINCT t.id_product,t.id_employee,tl.name as name,t.reference as shop_reference,t.supplier_reference as seller_reference,format(ps.price,0) as base_price,cl.name as `name_category`,ps.active as active ,tl.description as description,tl.description_short as short_description,t.date_add,t.date_upd,t.ean13, t.id_category_default, t.out_of_stock, psa.quantity as sav_quantity,i.id_image, IF(psa.quantity>0,1,0) having_stock, IF(sp.reduction_type="amount" or sp.reduction_type="percentage",CASE sp.reduction_type WHEN "amount" THEN FLOOR(ps.price-sp.reduction) ELSE FLOOR(ps.price-(sp.reduction*ps.price)) END, CASE sp1.reduction_type WHEN "amount" THEN FLOOR(ps.price-sp1.reduction) WHEN "percentage" THEN FLOOR(ps.price-(sp1.reduction*ps.price)) ELSE FLOOR(ps.price) END ) as sell_price,t.shop_margin as shop_margin,t.pg_fee as pg_fee,t.shipping_charge as shipping_charge
            FROM ps_product t 
            JOIN ps_stock_available psa ON psa.id_product = t.id_product 
            JOIN ps_product_lang tl ON t.id_product = tl.id_product 
            left join ps_specific_price sp on (sp.id_product=t.id_product and DATE(sp.from)<=DATE(NOW()) and DATE(sp.to)>=DATE(NOW())) 
            left join ps_specific_price sp1 on (sp1.id_product=t.id_product and DATE(sp1.from)="0000-00-00" and DATE(sp1.to)="0000-00-00") 
            left join ps_product_shop ps on ps.id_product=t.id_product 
            LEFT JOIN `ps_category_lang` cl ON (ps.`id_category_default` = cl.`id_category`) 
            LEFT JOIN ps_image i ON (i.id_product = t.id_product AND i.cover = 1) 
            INNER JOIN ps_category_product cp on t.id_product = cp.id_product
            WHERE t.id_product='.$idProduct.' '.$conditionShop.' ' ;

            return $connection->createCommand($Query)->queryOne();
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
    public function getProductInfo() {
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
    
    public function getBestSellingProduct($empid=null) {
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
    }
public function singleExport(){
             try {
                    $connection = $this->getDb();
                    $condition1 = '';
                    $condition2 = '';
                    $condition3 = '';
                    $condition4 = '';
                    $condition5 = '';
                    $shopId = ShopSession::shopSessionId();
                    if ($shopId) {
                        $condition1 = ' AND b.`id_shop` = ' . $shopId;
                        $condition2 = ' AND sav.id_shop = ' . $shopId;
                        $condition3 = ' AND sa.id_shop = ' . $shopId;
                        $condition4 = ' AND cl.id_shop = ' . $shopId;
                        $condition5 = ' AND image_shop.id_shop = ' . $shopId;
                    } else{
                        $condition1 = ' AND b.`id_shop` = 1';
                        $condition2 = ' AND sav.id_shop = 1';
                        $condition3 = ' AND sa.id_shop = 1';
                        $condition4 = ' AND cl.id_shop = 1';
                        $condition5 = ' AND image_shop.id_shop = 1';
                    }
                    $query = 'SELECT IF(sa.active =0,"Inactive","Active") as active ,b.name as name,a.`id_product`,cl.name `name_category`,a.reference as shop_reference, a.date_upd, sav.`quantity` as sav_quantity,format(a.price,2) as base_price,IF(sp.reduction_type="amount" OR sp.reduction_type="percentage",CASE sp.reduction_type WHEN "amount" THEN FLOOR(sa.price-sp.reduction) ELSE FLOOR(sa.price-(sp.reduction*sa.price)) END, CASE sp1.reduction_type WHEN "amount" THEN FLOOR(sa.price-sp1.reduction) WHEN "percentage" THEN FLOOR(sa.price-(sp1.reduction*sa.price)) ELSE FLOOR(sa.price) END) as sell_price,0 as vendor_payout,s.name as shop_name,si.company as vendor, MAX(image_shop.id_image) id_image,a.shop_margin as shop_margin,a.shipping_charge as shipping_charge, a.pg_fee as pg_fee,`reference` '
                        . 'FROM `ps_product` a '
                        . 'LEFT JOIN `ps_product_lang` b ON (b.`id_product` = a.`id_product` ' . $condition1 . ') '
                        . 'LEFT JOIN ps_specific_price sp ON (sp.id_product=a.id_product
                                       AND DATE(sp.from)<=DATE(NOW())
                                       AND DATE(sp.to)>=DATE(NOW())) '
                        .' LEFT JOIN ps_specific_price sp1 ON (sp1.id_product=a.id_product
                                        AND DATE(sp1.from)="0000-00-00"
                                        AND DATE(sp1.to)="0000-00-00") '
                        . 'LEFT JOIN `ps_image` i ON (i.`id_product` = a.`id_product`) '
                        . 'LEFT JOIN `ps_stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0 ' . $condition2 . ') '
                        . 'JOIN `ps_product_shop` sa ON (a.`id_product` = sa.`id_product` ' . $condition3 . ') LEFT JOIN `ps_category_lang` cl ON (sa.`id_category_default` = cl.`id_category` '
                        . 'AND b.`id_lang` = cl.`id_lang` ' . $condition4 . ')'
                        . 'LEFT JOIN `ps_image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` AND image_shop.`cover` = 1 ' . $condition5 . ')'
                        .' LEFT JOIN ps_sellerinfo si ON a.id_employee = si.id_seller'
                        .' LEFT JOIN ps_employee_shop es ON a.id_employee = es.id_employee'
                        .' LEFT JOIN ps_shop s ON s.id_shop = es.id_shop WHERE a.`id_product` in ('.$this->id_product.') GROUP BY sa.id_product';
                return $connection->createCommand($query)->queryAll();
            }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

}
?>