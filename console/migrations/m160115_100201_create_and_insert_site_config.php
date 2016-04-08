<?php

use yii\db\Schema;
use yii\db\Migration;

class m160115_100201_create_and_insert_site_config extends Migration
{
    public function up()
    {
           $this->createTable('{{%site_config}}', [
            'id' => $this->primaryKey(),
            'id_shop_group' => $this->integer(),
            'id_shop' => $this->integer(),
            'name' => $this->string()->notNull(),
            'value' => $this->string()->notNull(),
            'date_add' => $this->integer()->notNull()->defaultValue(time())
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
		
          $this->batchInsert('site_config', ['name', 'value'],[
		        ['WEB_URL','http://localhost/seller_oms/backend/web/'],
			    ['API_URL','http://shopdev.gaadi.com/webservice/'],
			    ['DISPLAY_SELLER_SHOP',TRUE],
				['SHOP_DATABASE','shopdev'],
			    ['SHOP_OMS_DATABASE','shop_oms']
		   ]);
    }

    public function down()
    {
        echo "m160115_100201_create_and_insert_site_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
