<?php

namespace backend\models;
use common\components\Helpers as Helper;

use Yii;

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
class AssociateSeller extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static $associatedSellers;
    public static $search;

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
            'id_employee' => 'Employee ID',
            'id_profile' => 'Id Profile',
            'id_lang' => 'Id Lang',
            'lastname' => 'Last Name',
            'company' => 'Company Name',
            'city' => 'City',
            'firstname' => 'First Name',
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
            'active' => 'Active',
            'optin' => 'Optin',
            'id_last_order' => 'Id Last Order',
            'id_last_customer_message' => 'Id Last Customer Message',
            'id_last_customer' => 'Id Last Customer',
        ];
    }
    
    public static function addRemoveAssociateSeller($userID, $sellerID)
    {
        try{
            $exist_user = Yii::$app->get('db')->createCommand("SELECT id from ".USER_SELLER_MAPPING." WHERE id_user=:id_user and id_seller=:id_seller")
                ->bindValue(':id_user', $userID)
                ->bindValue(':id_seller', $sellerID)
                ->queryAll();
            if(!$exist_user)
            {
                Yii::$app->get('db')->createCommand()
                    ->insert(USER_SELLER_MAPPING, [
                    'id_user' => $userID,
                    'id_seller' => $sellerID
                    ])->execute();
            }
            else
            {
                
                $model = Yii::$app->get('db')->createCommand('DELETE FROM '.USER_SELLER_MAPPING.' WHERE id=:id');
                        $model->bindParam(':id', $exist_user[0]['id']);
                        $model->execute();
            }
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
        
    }

    public static function getAssociatedSeller(){
        try{
            $getAllUsers = Yii::$app->get('db')->createCommand("SELECT GROUP_CONCAT(id_seller) as id_seller from ".USER_SELLER_MAPPING." WHERE id_user=:id_user")
                ->bindValue(':id_user', Helper::getSessionId())
                ->queryAll();
            $employeeIds=Helper::getSessionId();
             if(isset($getAllUsers[0]['id_seller']))
               $employeeIds=$employeeIds.','.$getAllUsers[0]['id_seller'];
             
            return $employeeIds;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
    
    public function getAssociatedSellerList(){
        $getAllUsers = Yii::$app->get('db')->createCommand("SELECT usm.id_seller, si.company from " . USER_SELLER_MAPPING . " usm INNER JOIN " . SELLERINFO . " si ON usm.id_seller = si.id_seller WHERE id_user=:id_user")
            ->bindValue(':id_user',Helper::getSessionId())
            ->queryAll();
        return  $getAllUsers;
    }
    
    public static function getAllSellerList(){
        if(Helper::isSeller())
        {
            self::$associatedSellers=self::getAssociatedSeller();
            $exitSessionId=substr(str_replace(Helper::getSessionId(),'', self::$associatedSellers),1);
            if(!empty($exitSessionId))
                self::$search=' and emp.id_employee in ('.$exitSessionId.')';
        }
        if(!Helper::isSeller() &&  empty($exitSessionId))
        {
            $getAllUsers = Yii::$app->get('db')->createCommand("SELECT id_seller, company from " . SELLERINFO. " seller LEFT JOIN ".EMPLOYEE." emp ON seller.id_seller=emp.id_employee WHERE emp.active=1 GROUP BY company")
                ->queryAll();
            return  $getAllUsers;
        }
        else if (Helper::isSeller() &&  !empty($exitSessionId)) {
            $getAllUsers = Yii::$app->get('db')->createCommand("SELECT id_seller, company from " . SELLERINFO. " seller LEFT JOIN ".EMPLOYEE." emp ON seller.id_seller=emp.id_employee WHERE emp.active=1 ".self::$search." GROUP BY company")
                ->queryAll();
            return  $getAllUsers;
        }
    }
    
}
