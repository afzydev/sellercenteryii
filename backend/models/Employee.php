<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use common\components\Session as ShopSession;

/**
 * This is the model class for table "ps_employee".
 *
 * @property string $id_employee
 * @property string $id_profile
 * @property string $id_lang
 * @property string $lastname
 * @property string $firstname
 * @property string $email
 * @property string $passwd
 * @property string $last_passwd_gen
 * @property string $stats_date_from
 * @property string $stats_date_to
 * @property string $stats_compare_from
 * @property string $stats_compare_to
 * @property string $stats_compare_option
 * @property string $preselect_date_range
 * @property string $bo_color
 * @property string $bo_theme
 * @property string $bo_css
 * @property string $default_tab
 * @property string $bo_width
 * @property integer $bo_menu
 * @property integer $active
 * @property integer $optin
 * @property string $id_last_order
 * @property string $id_last_customer_message
 * @property string $id_last_customer
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return EMPLOYEE;
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbshop');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_profile', 'lastname', 'firstname', 'email', 'passwd'], 'required'],
            [['id_profile', 'id_lang', 'stats_compare_option', 'default_tab', 'bo_width', 'bo_menu', 'active', 'optin', 'id_last_order', 'id_last_customer_message', 'id_last_customer'], 'integer'],
            [['last_passwd_gen', 'stats_date_from', 'stats_date_to', 'stats_compare_from', 'stats_compare_to'], 'safe'],
            [['lastname', 'firstname', 'passwd', 'preselect_date_range', 'bo_color', 'bo_theme'], 'string', 'max' => 32],
            [['email'], 'string', 'max' => 128],
            [['bo_css'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_employee' => 'ID',
            'id_profile' => 'Profile',
            'id_lang' => 'Id Lang',
            'lastname' => 'Lastname',
            'firstname' => 'Firstname',
            'email' => 'Email',
            'passwd' => 'Passwd',
            'last_passwd_gen' => 'Last Passwd Gen',
            'stats_date_from' => 'Stats Date From',
            'stats_date_to' => 'Stats Date To',
            'stats_compare_from' => 'Stats Compare From',
            'stats_compare_to' => 'Stats Compare To',
            'stats_compare_option' => 'Stats Compare Option',
            'preselect_date_range' => 'Preselect Date Range',
            'bo_color' => 'Bo Color',
            'bo_theme' => 'Bo Theme',
            'bo_css' => 'Bo Css',
            'default_tab' => 'Default Tab',
            'bo_width' => 'Bo Width',
            'bo_menu' => 'Bo Menu',
            'active' => 'Status',
            'optin' => 'Optin',
            'id_last_order' => 'Id Last Order',
            'id_last_customer_message' => 'Id Last Customer Message',
            'id_last_customer' => 'Id Last Customer',
        ];
    }
    
    public function relations()
    {
            return array(
                    'id_profile_lang' => array(self::MANY_MANY, 'id_profile_lang'),
            );
    }

    public static function getAllSellers(){
        try{
            $connection = self::getDb();
            $conditionJoin='';
            $criteria='1=1';
            $shopId = ShopSession::shopSessionId();
            if($shopId)
            {
                $conditionJoin='LEFT JOIN '.EMPLOYEE_SHOP.' es ON o.id_employee=es.id_employee ';
                $criteria='es.id_shop = '.$shopId;
            }

            $query = 'select o.id_employee as value,CONCAT(firstname," ",lastname) as label,o.id_employee as id_employee from '. EMPLOYEE.' o '.$conditionJoin.' WHERE '.$criteria.' ';
            return $connection->createCommand($query)->queryAll();

            // return static::find()->select(['id_employee as value','CONCAT(firstname," ",lastname) as label','id_employee as id_employee'])->asArray()->all();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
}
