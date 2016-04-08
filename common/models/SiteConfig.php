<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "site_config".
 *
 * @property integer $id
 * @property string $config_key
 * @property string $value
 * @property string $label
 * @property string $type
 * @property string $created_at
 * @property string $modified_at
 */
class SiteConfig extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'site_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['config_key', 'value', 'label'], 'required'],            
            ['config_key', 'filter', 'filter' => 'trim'],
            ['config_key', 'unique', 'message' => 'This config key has already been taken.'],            
            [['type'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['config_key', 'value'], 'string', 'max' => 255],
            [['label'], 'string', 'max' => 60]
        ];
    }
    
     public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'modified_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'modified_at',
                ],
                'value' => function($event) {
                    $format = "Y-m-d h:i:s"; // any format you wish
                    return date($format); 
                }
            ],
        ];
    }

    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'config_key' => 'Config Key',
            'value' => 'Value',
            'label' => 'Label',
            'type' => 'Type',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
    
    public function getConfigVal($key, $columns = 'value'){
        
        $data = SiteConfig::find()->select($columns)->where('config_key ="'.$key.'"')->one();
        if($data && isset($data->value)){
            return $data->value;
        }
    }
}
