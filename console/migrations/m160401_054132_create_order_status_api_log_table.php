<?php

use yii\db\Migration;

class m160401_054132_create_order_status_api_log_table extends Migration
{
    public function up()
    {
        $this->createTable('order_status_api_log_table', [
            'id' => $this->primaryKey(),
            'id_employee' => $this->integer()->notNull(),
            'id_order' => $this->integer()->notNull(),
            'id_order_state' => $this->integer()->notNull(),
            'return_msg' => Schema::TYPE_STRING . ' NOT NULL',
            'individual_return_status'=>Schema::TYPE_STRING . ' NOT NULL',
            'overall_return_status'=>Schema::TYPE_STRING . ' NOT NULL',
            'date_add' => $this->datetime()->notNull()
        ]);
    }

    public function down()
    {
        $this->dropTable('order_status_api_log_table');
    }
}
