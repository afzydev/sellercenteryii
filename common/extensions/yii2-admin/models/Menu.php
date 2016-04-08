<?php

namespace common\extensions\admin\models;

use mdm\admin\models as BaseMenu;
/**
 * This is the model class for table "menu".
 *
 * @property integer $id Menu id(autoincrement)
 * @property string $name Menu name
 * @property integer $parent Menu parent
 * @property string $route Route for this menu
 * @property integer $order Menu order
 * @property string $data Extra information for this menu
 *
 * @property Menu $menuParent Menu parent
 * @property Menu[] $menus Menu children
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Menu extends BaseMenu
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_name'], 'filterParent'],
            [['parent_name'], 'in',
                'range' => static::find()->select(['name'])->column(),
                'message' => 'Menu "{value}" not found.'],
            [['parent', 'route', 'data', 'order','icon'], 'default'],
            [['order'], 'integer'],
            [['route'], 'in',
                'range' => static::getSavedRoutes(),
                'message' => 'Route "{value}" not found.']
        ];
    }

   
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac-admin', 'ID'),
            'name' => Yii::t('rbac-admin', 'Name'),
            'parent' => Yii::t('rbac-admin', 'Parent'),
            'parent_name' => Yii::t('rbac-admin', 'Parent Name'),
            'route' => Yii::t('rbac-admin', 'Route'),
            'order' => Yii::t('rbac-admin', 'Order'),
            'icon' => Yii::t('rbac-admin', 'Icon'),
            'data' => Yii::t('rbac-admin', 'Data'),
        ];
    }

    
}
