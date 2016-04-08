<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\SqlDataProvider;
use backend\models\Order;
/**
* 
*/
class SearchOrder extends Order
{

    public $criteria, $id_order,$invoice_ref_key,$reference,$product_name,$customer,$cust_email,$response_waywill,$unit_price_tax_incl,$total_paid_tax_incl,$payment,$osname,$confirmed_by,$delivery_days,$shipped_date_add,$delivered_date_add,$invoice_number,$address1,$address2,$state_name,$city,$postcode;
    public $id_shop;
    public $orderStatus;
    public $id_order_state;
    public $employeeIds;

    public function search($params)
    {
        try{
        	$connection = Order::getDb();
               
            $this->criteria = " WHERE 1=1 ";

            if(!empty($this->id_order))
                $this->criteria .= " and a.`id_order` = '".$this->id_order."' ";
            if(!empty($this->invoice_ref_key))
                $this->criteria .= " and detail.invoice_ref_key LIKE '%".$this->invoice_ref_key."%' ";
            if(!empty($this->reference))
                $this->criteria .= " and a.reference LIKE '%".$this->reference."%' ";
            if(!empty($this->product_name))
                $this->criteria .= " and product_lang.name LIKE '%".$this->product_name."%' ";
            if(!empty($this->customer))
                $this->criteria .= " and CONCAT(LEFT(c.`firstname`, 1), c.`lastname`) LIKE '%".$this->customer."%' ";
            if(!empty($this->cust_email))
                $this->criteria .= " and c.email LIKE '%".$this->cust_email."%' ";
            if(!empty($this->response_waywill))
                $this->criteria .= " and response_waywill LIKE '%".$this->response_waywill."%' ";
            if(!empty($this->total_paid_tax_incl))
                $this->criteria .= " and a.total_paid_tax_incl = '".$this->total_paid_tax_incl."' ";
            if(!empty($this->payment))
                $this->criteria .= " and payment = '".$this->payment."' ";
            // if(!empty($this->osname))
            //     $this->criteria .= " and osl.`name` = '".$this->osname."' ";
            if(!empty($this->orderStatus))
                $this->criteria.=' and a.`current_state`='.$this->orderStatus;
            if(!empty($this->employeeIds))
                $this->criteria.=' and product.id_employee in ('.$this->employeeIds.')';

            $command = $connection->createCommand('
            SELECT COUNT(a.`id_order`) FROM '.ORDERS.' a LEFT JOIN '.CUSTOMERS.' c ON (c.`id_customer` = a.`id_customer`) INNER JOIN '.ADDRESS.' address ON address.id_address = a.id_address_delivery INNER JOIN '.COUNTRY.' country ON address.id_country = country.id_country INNER JOIN '.COUNTRY_LANG.' country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = 1) LEFT JOIN '.ORDER_STATE.' os ON (os.`id_order_state` = a.`current_state`) LEFT JOIN '.DELHIVERY_RESPONSE.' ds ON (ds.`reference` = a.`reference`) LEFT JOIN '.ORDER_HISTORY.' oh ON (oh.`id_order` = a.`id_order` and oh.id_order_state=13) LEFT JOIN '.ORDER_HISTORY.' oh1 ON (oh1.`id_order` = a.`id_order` and oh1.id_order_state=5) LEFT JOIN '.ORDER_DETAIL.' detail ON detail.id_order = a.id_order LEFT JOIN '.STATE.' state ON state.id_state = address.id_state LEFT JOIN '.CARRIER.' carrier ON a.id_carrier = carrier.id_carrier LEFT JOIN '.PRODUCT.' product ON product.id_product = detail.product_id LEFT JOIN '.PRODUCT_LANG.' product_lang ON product_lang.id_product = detail.product_id LEFT JOIN '.ORDER_INVOICE.' oi ON a.id_order = oi.id_order LEFT JOIN '.SHOP.' shop1 ON (shop1.id_shop = '.$this->id_shop.') LEFT JOIN '.SELLERINFO.' sellerinfo ON sellerinfo.id_seller = product.id_employee LEFT JOIN '.ORDER_STATE_LANG.' osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = 1) '.$this->criteria.' GROUP BY detail.id_order_detail ORDER BY a.`id_order` DESC
            ');
            $reader = $command->query();
            $rowCount = $reader->rowCount;
            $Query = 'SELECT a.`id_order`,`response_waywill`,`unit_price_tax_incl`,`cod_charge`,`total_shipping`,a.total_paid_tax_incl as total_paid_tax_incl,a.total_paid_tax_incl as total_paid_tax_inc2,`payment`,a.date_add as date_add,`product_quantity` , a.id_currency, a.reference as reference, a.confirmed_by as confirmed_by, product.id_employee as id_employee, product_lang.name as product_name, oh.date_add as shipped_date_add, oh1.date_add as delivered_date_add, detail.invoice_ref_key AS invoice_ref_key, IF(oh1.date_add!="NULL",(DATEDIFF(oh1.date_add, a.date_add)),"") as delivery_days, IF(carrier.name!="NULL",carrier.name,"Delhivery") as carrier_name, IF(address.phone="",address.phone_mobile,address.phone) AS mobile_number, a.id_order AS id_pdf, oi.number AS invoice_number, a.is_invoice_download, c.email as cust_email, sellerinfo.company AS company_name, state.name AS state_name, address.address1 AS address1, address.address2 AS address2, address.city AS city, address.postcode AS postcode, detail.product_reference AS product_reference, CONCAT(LEFT(c.`firstname`, 1), '. ' c.`lastname`) AS `customer`, osl.`name` AS `osname`,osl.`id_order_state`, os.`color`, IF((SELECT COUNT(so.id_order) FROM '.ORDERS.' so WHERE so.id_customer = a.id_customer) > 1, 0, 1) as new, country_lang.name as cname, IF(a.valid, 1, 0) badge_success FROM '.ORDERS.' a LEFT JOIN '.CUSTOMERS.' c ON (c.`id_customer` = a.`id_customer`) INNER JOIN '.ADDRESS.' address ON address.id_address = a.id_address_delivery INNER JOIN '.COUNTRY.' country ON address.id_country = country.id_country INNER JOIN '.COUNTRY_LANG.' country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = 1) LEFT JOIN '.ORDER_STATE.' os ON (os.`id_order_state` = a.`current_state`) LEFT JOIN '.DELHIVERY_RESPONSE.' ds ON (ds.`reference` = a.`reference`) LEFT JOIN '.ORDER_HISTORY.' oh ON (oh.`id_order` = a.`id_order` and oh.id_order_state=13) LEFT JOIN '.ORDER_HISTORY.' oh1 ON (oh1.`id_order` = a.`id_order` and oh1.id_order_state=5) LEFT JOIN '.ORDER_DETAIL.' detail ON detail.id_order = a.id_order LEFT JOIN '.STATE.' state ON state.id_state = address.id_state LEFT JOIN '.CARRIER.' carrier ON a.id_carrier = carrier.id_carrier LEFT JOIN '.PRODUCT.' product ON product.id_product = detail.product_id LEFT JOIN '.PRODUCT_LANG.' product_lang ON product_lang.id_product = detail.product_id LEFT JOIN '.ORDER_INVOICE.' oi ON a.id_order = oi.id_order LEFT JOIN '.SHOP.' shop1 ON (shop1.id_shop = '.$this->id_shop.') LEFT JOIN '.SELLERINFO.' sellerinfo ON sellerinfo.id_seller = product.id_employee LEFT JOIN '.ORDER_STATE_LANG.' osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = 1) '.$this->criteria.' GROUP BY detail.id_order_detail ORDER BY a.`id_order` DESC';
            //die;
            $provider = new SqlDataProvider([
                'db'  => $connection,   
                'sql' => $Query,
                'totalCount' => $rowCount,
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]);

            return $provider;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }


}

?>