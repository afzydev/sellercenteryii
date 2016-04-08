<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "ps_carrier".
 *
 * @property integer $id_carrier
 * @property integer $id_reference
 * @property integer $id_tax_rules_group
 * @property string $name
 * @property string $url
 * @property integer $active
 * @property integer $deleted
 * @property integer $shipping_handling
 * @property integer $range_behavior
 * @property integer $is_module
 * @property integer $is_free
 * @property integer $shipping_external
 * @property integer $need_range
 * @property string $external_module_name
 * @property integer $shipping_method
 * @property integer $position
 * @property integer $max_width
 * @property integer $max_height
 * @property integer $max_depth
 * @property string $max_weight
 * @property integer $grade
 */
class Carrier extends \common\models\MyActiveRecordShop
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ps_carrier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_reference', 'name'], 'required'],
            [['id_reference', 'id_tax_rules_group', 'active', 'deleted', 'shipping_handling', 'range_behavior', 'is_module', 'is_free', 'shipping_external', 'need_range', 'shipping_method', 'position', 'max_width', 'max_height', 'max_depth', 'grade'], 'integer'],
            [['max_weight'], 'number'],
            [['name', 'external_module_name'], 'string', 'max' => 64],
            [['url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_carrier' => 'Id Carrier',
            'id_reference' => 'Id Reference',
            'id_tax_rules_group' => 'Id Tax Rules Group',
            'name' => 'Name',
            'url' => 'Url',
            'active' => 'Active',
            'deleted' => 'Deleted',
            'shipping_handling' => 'Shipping Handling',
            'range_behavior' => 'Range Behavior',
            'is_module' => 'Is Module',
            'is_free' => 'Is Free',
            'shipping_external' => 'Shipping External',
            'need_range' => 'Need Range',
            'external_module_name' => 'External Module Name',
            'shipping_method' => 'Shipping Method',
            'position' => 'Position',
            'max_width' => 'Max Width',
            'max_height' => 'Max Height',
            'max_depth' => 'Max Depth',
            'max_weight' => 'Max Weight',
            'grade' => 'Grade',
        ];
    }

    public function getCarrier($idCarrier=null)
    {
        try{
            
            if(is_null($idCarrier))
                return static::find()->where(['deleted'=>0,'active'=>1])->asArray()->all();
            else
                return static::find()->where(['id_reference'=>$idCarrier,'deleted'=>0])->asArray()->one();
             
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
    public function getManifestCarrier($idCarrier)
    {
        try{
            $connection = $this->getDb();
            $id_carrier_details = $connection->createCommand('SELECT GROUP_CONCAT(id_carrier) as id_carrier FROM '.CARRIER.' where id_reference='.$idCarrier.' ')
                ->queryAll();
            $id_carrier=$id_carrier_details[0]['id_carrier'];
            return $id_carrier;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }


}
