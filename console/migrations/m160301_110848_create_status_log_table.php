<?php

use yii\db\Migration;

class m160301_110848_create_status_log_table extends Migration
{
    public function up()
    {
        $this->createTable('status_log_table', [
            'id_status_log' => $this->primaryKey(),
            'id_employee' => $this->integer()->notNull(),
            'id_shop' => $this->float()->notNull(),
            'id_product' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull(),
            'date_add' => $this->datetime()->notNull()->defaultValue(date('Y-m-d H:i:s'))
        ]);
    }

    public function down()
    {
        $this->dropTable('status_log_table');
    }
}
