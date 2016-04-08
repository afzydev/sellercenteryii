<?php

use yii\db\Migration;

class m160223_122135_create_price_log_table extends Migration
{
    public function up()
    {
       $this->createTable('{{%price_log}}', [
            'id_price_log' => $this->primaryKey(),
            'id_product' => $this->integer()->notNull(),
            'selling_price' => $this->float()->notNull(),
            'id_employee' => $this->integer()->notNull(),
            'id_shop' => $this->integer()->notNull(),
            'date_add' => $this->datetime()->notNull()->defaultValue(date('Y-m-d H:i:s')),
            'base_price'=>$this->float()->notNull()
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable('%price_log');
    }
}
