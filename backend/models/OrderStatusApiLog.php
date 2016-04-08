<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_status_api_log".
 *
 * @property integer $id
 * @property integer $id_employee
 * @property integer $id_order
 * @property integer $id_order_state
 * @property string $return_msg
 * @property string $individual_return_status
 * @property string $overall_return_status
 * @property string $date_add
 */
class OrderStatusApiLog extends \common\models\MyActiveRecord
{
    /**
     * @inheritdoc
     */

    public $id_employee;
    public $id_order;
    public $id_order_state;
    public $date_add;
    public $returnData=[];
    public $overall_return_status;
    public static function tableName()
    {
        return 'order_status_api_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_employee', 'id_order', 'id_order_state', 'return_msg', 'individual_return_status', 'overall_return_status', 'date_add'], 'required'],
            [['id', 'id_employee', 'id_order', 'id_order_state'], 'integer'],
            [['date_add'], 'safe'],
            [['return_msg', 'individual_return_status', 'overall_return_status'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_employee' => 'Id Employee',
            'id_order' => 'Id Order',
            'id_order_state' => 'Id Order State',
            'return_msg' => 'Return Msg',
            'individual_return_status' => 'Individual Return Status',
            'overall_return_status' => 'Overall Return Status',
            'date_add' => 'Date Add',
        ];
    }

    public static function saveOrderStatusApiLog($id_employee,$id_order,$id_order_state,$overall_return_status,$date_add,$returnData){
        $connection = self::getDb();
        $queryString='';
        if(!empty($returnData) && is_array($returnData))
        {
            foreach ($returnData as $key=>$val) {
                if(is_numeric($key)){
                    if($val['status'])
                    {
                        $individual_return_status="true";
                    }
                    else{
                        $individual_return_status="false";
                    }
                    $api_response_msg=$val['msg'];
                    $queryString.='(';
                    $queryString.=$id_employee.',';
                    $queryString.=$key.',';
                    $queryString.=$id_order_state.',';
                    $queryString.="'".$api_response_msg."',";
                    $queryString.=$individual_return_status.',';
                    $queryString.=$overall_return_status.',';
                    $queryString.="'".$date_add."'";
                    $queryString.='),';
                }
            }

        }
        else
        {
            if(!empty($id_order))
            {
                $idOrderArr=explode(',',$id_order);
                foreach($idOrderArr as $id_order)
                {
                    $individual_return_status="false";
                    $api_response_msg='No response from API';

                    $queryString.="(";
                    $queryString.=$id_employee.',';
                    $queryString.=$id_order.',';
                    $queryString.=$id_order_state.',';
                    $queryString.="'".$api_response_msg."',";
                    $queryString.=$individual_return_status.',';
                    $queryString.=$overall_return_status.',';
                    $queryString.="'".$date_add."'";
                    $queryString.="),";
                }
            }
        }
        $queryString=substr($queryString,0,-1);
        //echo 'INSERT INTO '.OMS_STATUS_API_LOG.' (id_employee,id_order,id_order_state,return_msg,individual_return_status,overall_return_status,date_add) values'.$queryString.' ';die;
        $connection->createCommand('INSERT INTO '.OMS_STATUS_API_LOG.' (id_employee,id_order,id_order_state,return_msg,individual_return_status,overall_return_status,date_add) values'.$queryString.' ')->execute();


    }

}
