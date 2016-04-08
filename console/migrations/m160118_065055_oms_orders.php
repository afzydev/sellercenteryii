<?php

use yii\db\Schema;
use yii\db\Migration;

class m160118_065055_oms_orders extends Migration
{
    public function up()
    {
         $this->createTable('{{%oms_orders}}', [
            'id' => $this->primaryKey(),
            'id_order' => $this->integer(),
            'is_invoice_download' => $this->integer(5),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable('{{%oms_orders}}');
    }

}
