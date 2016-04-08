<?php

namespace backend\models;

use Yii;
use common\models\MyActiveRecordShop;
use yii\data\SqlDataProvider;

/**
* DashboardSite Model
* 
* PHP version 5+
*
* @category   Model
* @package    N/A
* @author     Preet Saxena<preet.saxena@girnarsoft.com>
* @since      20 Jan, 2105
*/
class DashboardSite extends MyActiveRecordShop {

    public $id_shop;

    /**
     * returns table name
     * @author Preet Saxena
     * @created 21 Jan, 2016
     * @return String
     */
    public static function tableName() {
        return '{{%ps_orders}}';
    }

    /**
     * Fetches order based in time filters[day/month/year]
     * @author Preet Saxena
     * @created 21 Jan, 2016
     * @param Date $fromDate Time Starting from
     * @param Date $toDate Time Ending To
     * @return Array
     */
    public function getOrder($fromDate, $toDate) {
        $connection = $this->getDb();
        $Query = 'SELECT count(*) as orders 
            FROM ps_orders as t1 
            LEFT JOIN ps_order_state as t2 ON t1.current_state = t2.id_order_state 
            WHERE invoice_date BETWEEN "' . $fromDate . ' 00:00:00" AND "' . $toDate . ' 23:59:59" 
            AND t2.logable = 1 
            AND t1.id_shop IN (' . $this->id_shop . ') 
            GROUP BY LEFT(invoice_date, 10)';
        $order = $connection->createCommand($Query)->queryOne();
        return $order;
    }

    public function getSale($from_date, $to_date) {
        $connection = $this->getDb();
        $Query = 'SELECT SUM(total_paid_tax_excl / o.conversion_rate) as sales FROM ps_orders o '
                . 'LEFT JOIN ps_order_state os '
                . 'ON o.current_state = os.id_order_state '
                . 'WHERE invoice_date '
                . 'BETWEEN "' . $from_date . ' 00:00:00" '
                . 'AND "' . $to_date . '" '
                . 'AND os.logable = 1 '
                . 'AND o.id_shop IN (' . $this->id_shop . ') '
                . 'GROUP BY LEFT(invoice_date, 10)';
        $sale = $connection->createCommand($Query)->queryOne();

        return $sale;
    }

}